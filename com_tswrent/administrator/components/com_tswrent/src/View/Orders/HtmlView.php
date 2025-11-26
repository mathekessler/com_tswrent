<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\View\Orders;

\defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Form\Form;

/**
 * View class for a list of orders.
 *
 * @since  __BUMP_VERSION__
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * An array of items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var  \JPagination
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * Form object for search filters
	 *
	 * @var  \JForm
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var  array
	 */
	public $activeFilters;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function display($tpl = null): void
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		// --- Start: Prepare the orderstate field for each item ---
		if (!empty($this->items)) {
			// Get the form definition
			$form = Form::getInstance('order', JPATH_COMPONENT_ADMINISTRATOR . '/forms/order.xml');

			foreach ($this->items as $item) {
				// Get the raw XML definition for the 'orderstate' field
				$fieldXml = clone $form->getFieldXml('orderstate');

				if ($fieldXml) {
					// Create a *new* field instance from the XML for each item
					$field = new \Joomla\CMS\Form\Field\ListField($form);

					// Set the specific attributes for this row
					$fieldXml['name']         = 'orderstate_' . (int) $item->id;
					$fieldXml['id']           = 'orderstate_' . (int) $item->id;
					$fieldXml['class']        = 'orderstate';
					$fieldXml['data-orderid'] = (int) $item->id;
					$fieldXml['onchange']     = 'window.updateOrderState(event)';

					// Setup the new field instance with the modified XML and the correct value
					$field->setup($fieldXml, $item->orderstate ?? 0);

					// Attach the rendered HTML to the item object
					$item->orderstate_input = $field->input;
				}
			}
		}
		// --- End: Prepare the orderstate field ---

		if (!count($this->items) && $this->get('IsEmptyState')) {
			$this->setLayout('emptystate');
		}

		$this->addToolbar();


		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function addToolbar()
	{
		$this->sidebar = \JHtmlSidebar::render();

		$canDo = ContentHelper::getActions('com_tswrent', 'category', $this->state->get('filter.category_id'));
		$user  = Factory::getUser();

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title(Text::_('COM_TSWRENT_MANAGER_ORDERS'), 'address order');

		if ($canDo->get('core.create') || count($user->getAuthorisedCategories('com_tswrent', 'core.create')) > 0) {
			$toolbar->addNew('order.add');
		}

		if ($canDo->get('core.edit.state')) {
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fa fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);
			$childBar = $dropdown->getChildToolbar();
			$childBar->publish('orders.publish')->listCheck(true);
			$childBar->unpublish('orders.unpublish')->listCheck(true);

			$childBar->standardButton('featured')
				->text('JFEATURE')
				->task('orders.featured')
				->listCheck(true);
			$childBar->standardButton('unfeatured')
				->text('JUNFEATURE')
				->task('orders.unfeatured')
				->listCheck(true);

			$childBar->archive('orders.archive')->listCheck(true);

			if ($user->authorise('core.admin')) {
				$childBar->checkin('orders.checkin')->listCheck(true);
			

			
				$childBar->delete('orders.delete')->listCheck(true);
			}

			// Add a batch button
			if ($user->authorise('core.create', 'com_tswrent')
				&& $user->authorise('core.edit', 'com_tswrent')
				&& $user->authorise('core.edit.state', 'com_tswrent')) {
				$childBar->popupButton('batch')
					->text('JTOOLBAR_BATCH')
					->selector('collapseModal')
					->listCheck(true);
			}
		}

		if ($user->authorise('core.admin', 'com_tswrent') || $user->authorise('core.options', 'com_tswrent')) {
			$toolbar->preferences('com_tswrent');
		}
		ToolbarHelper::divider();
		ToolbarHelper::help('', false, 'http://example.org');
	}
}
