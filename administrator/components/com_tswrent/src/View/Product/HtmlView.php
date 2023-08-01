<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\View\Product;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

\defined('_JEXEC') or die;

/**
 * View to edit a product.
 *
 * @since  __BUMP_VERSION__
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The \JForm object
	 *
	 * @var  \JForm
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var  object
	 */
	protected $item;
	
	/**
	 * The model state
	 *
	 * @var    object
	 * @since  1.5
	 */
	protected $state;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->state = $this->get('State');
		$this->item	 = $this->get('Item');
		$this->form	 = $this->get('Form');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}


        $this->addToolbar();


		return parent::display($tpl);
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
		Factory::getApplication()->input->set('hidemainmenu', true);

        $user       = $this->getCurrentUser();
		$userId 	= $user->id;
		$isNew 		= ($this->item->id == 0);
		$checkedOut = !(\is_null($this->item->checked_out) || $this->item->checked_out == $userId);
        $toolbar    = Toolbar::getInstance();

        // Since we don't track these assets at the item level, use the category id.
        $canDo = ContentHelper::getActions('com_tswrent', 'category', $this->item->catid);

		ToolbarHelper::title($isNew ? Text::_('COM_TSWRENT_MANAGER_PRODUCT_NEW') : Text::_('COM_TSWRENT_MANAGER_PRODUCT_EDIT'), 'bookmark products');

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || \count($user->getAuthorisedCategories('com_tswrent', 'core.create')) > 0)) {
            $toolbar->apply('product.apply');
        }
		
        $saveGroup = $toolbar->dropdownButton('save-group');

        $saveGroup->configure(
            function (Toolbar $childBar) use ($checkedOut, $canDo, $user, $isNew) {
                // If not checked out, can save the item.
                if (!$checkedOut && ($canDo->get('core.edit') || \count($user->getAuthorisedCategories('com_tswrent', 'core.create')) > 0)) {
                    $childBar->save('product.save');

                    if ($canDo->get('core.create')) {
                        $childBar->save2new('product.save2new');
                    }
                }

                // If an existing item, can save to a copy.
                if (!$isNew && $canDo->get('core.create')) {
                    $childBar->save2copy('product.save2copy');
                }
            }
        );

        if (empty($this->item->id)) {
            $toolbar->cancel('product.cancel', 'JTOOLBAR_CANCEL');
        } else {
            $toolbar->cancel('product.cancel');

            if (ComponentHelper::isEnabled('com_contenthistory') && $this->state->params->get('save_history', 0) && $canDo->get('core.edit')) {
                $toolbar->versions('com_tswrent.product', $this->item->id);
            }
        }


		ToolbarHelper::divider();
		ToolbarHelper::help('', false, 'http://example.org');
	}
}
