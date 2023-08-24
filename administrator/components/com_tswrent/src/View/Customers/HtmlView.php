<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\View\Customers;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use TSWEB\Component\Tswrent\Administrator\Model\CustomersModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View class for a list of customers.
 *
 * @since  __BUMP_VERSION__
 */
class HtmlView extends BaseHtmlView
{
	/**
     * The search tools form
     *
     * @var    Form
     * @since  __BUMP_VERSION__
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var    array
     * @since  __BUMP_VERSION__
     */
    public $activeFilters = [];

    /**
     * An array of items
     *
     * @var    array
     * @since  __BUMP_VERSION__
     */
    protected $items = [];

    /**
     * The pagination object
     *
     * @var    Pagination
     * @since  __BUMP_VERSION__
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var    CMSObject
     * @since  __BUMP_VERSION__
     */
    protected $state;

    /**
     * Is this view an Empty State
     *
     * @var  boolean
     * @since 4.0.0
     */
    private $isEmptyState = false;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @since   __BUMP_VERSION__
     *
     * @throws  Exception
     */
    public function display($tpl = null): void
    {
        /** @var CustomersModel $model */
        $model               = $this->getModel();
        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->state         = $model->getState();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();

        if (!\count($this->items) && $this->isEmptyState = $this->get('IsEmptyState')) {
            $this->setLayout('emptystate');
        }

        // Check for errors.
        if (\count($errors = $this->get('Errors')) || $this->transitions === false) {
            throw new GenericDataException(implode("\n", $errors), 500);
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

		$canDo = ContentHelper::getActions('com_tswrent', 'category', $this->state->get('filter.category_id'));
		$user    = Factory::getApplication()->getIdentity();
		$toolbar = Toolbar::getInstance();

		ToolbarHelper::title(Text::_('COM_TSWRENT_MANAGER_CUSTOMERS'), 'address customers');

		if ($canDo->get('core.create') ) {
			$toolbar->addNew('customer.add');
		}
		if (!$this->isEmptyState && ($canDo->get('core.edit.state') || ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')))) {
            /** @var  DropdownButton $dropdown */
            $dropdown = $toolbar->dropdownButton('status-group', 'JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if ($canDo->get('core.edit.state')) {
                if ($this->state->get('filter.published') != 2) {
                    $childBar->publish('customers.publish')->listCheck(true);

                    $childBar->unpublish('customers.unpublish')->listCheck(true);
                }

                if ($this->state->get('filter.published') != -1) {
                    if ($this->state->get('filter.published') != 2) {
                        $childBar->archive('customers.archive')->listCheck(true);
                    } elseif ($this->state->get('filter.published') == 2) {
                        $childBar->publish('publish')->task('customers.publish')->listCheck(true);
                    }
                }

                $childBar->checkin('customers.checkin');

                if ($this->state->get('filter.published') != -2) {
                    $childBar->trash('customers.trash')->listCheck(true);
                }
            }

            if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
                $toolbar->delete('customers.delete', 'JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }

            // Add a batch button
           
        }

        if ($user->authorise('core.admin', 'com_tswrent') || $user->authorise('core.options', 'com_tswrent')) {
            $toolbar->preferences('com_tswrent');
        }

        $toolbar->help('Tswrent');
    }
}
