<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\View\Supplieremployee;

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use TSWEB\Component\Tswrent\Administrator\Model\SupplieremployeeModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View class to edit a supplieremployee.
 *
 * @since  __BUMP_VERSION__
 * 
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
	 */
	protected $state;

	/**
     * Object containing permissions for the item
     *
     * @var    CMSObject
     * 
     * @since  __BUMP_VERSION__
     */
    protected $canDo;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 * 
	 *  @since   __BUMP_VERSION__
	 * 
	 */
	public function display($tpl = null): void
	{
        /** @var SupplierModel $model */
        $model       = $this->getModel();
        $this->form  = $model->getForm();
        $this->item  = $model->getItem();
        $this->state = $model->getState();
        $this->canDo = ContentHelper::getActions('com_tswrent');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
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
	 * 
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

        $user       = $this->getCurrentUser();
		$userId 	= $user->id;
		$isNew 		= ($this->item->id == 0);
		$checkedOut = !(\is_null($this->item->checked_out) || $this->item->checked_out == $userId);
        $canDo      = $this->canDo;
		$toolbar    = Toolbar::getInstance();

		ToolbarHelper::title(
			$isNew ? Text::_('COM_TSWRENT_MANAGER_SUPPLIEREMPLOYEE_NEW') : Text::_('COM_TSWRENT_MANAGER_SUPPLIEREMPLOYEE_EDIT'), 
			'bookmark tswrent-supplieremployees');

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || \count($user->getAuthorisedCategories('com_tswrent', 'core.create')) > 0)) {
            $toolbar->apply('supplieremployee.apply');
        }
		
        $saveGroup = $toolbar->dropdownButton('save-group');

        $saveGroup->configure(
            function (Toolbar $childBar) use ($checkedOut, $canDo, $user, $isNew) {
                // If not checked out, can save the item.
                if (!$checkedOut && ($canDo->get('core.edit') || \count($user->getAuthorisedCategories('com_tswrent', 'core.create')) > 0)) {
                    $childBar->save('supplieremployee.save');

                    if ($canDo->get('core.create')) {
                        $childBar->save2new('supplieremployee.save2new');
                    }
                }

                // If an existing item, can save to a copy.
                if (!$isNew && $canDo->get('core.create')) {
                    $childBar->save2copy('supplieremployee.save2copy');
                }
            }
        );

        if (empty($this->item->id)) {
            $toolbar->cancel('supplieremployee.cancel', 'JTOOLBAR_CANCEL');
        } else {
            $toolbar->cancel('supplieremployee.cancel');

            if (ComponentHelper::isEnabled('com_contenthistory') && $this->state->params->get('save_history', 0) && $canDo->get('core.edit')) {
                $toolbar->versions('com_tswrent.supplieremployee', $this->item->id);
            }
        }


        $toolbar->divider();
        $toolbar->help('Banners:_New_or_Edit_Supplieremployee');
	}
}
