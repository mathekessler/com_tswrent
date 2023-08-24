<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Supplier controller class
 *
 * @since  __BUMP_VERSION__
 */
class SupplierController extends FormController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * 
     * @since  __BUMP_VERSION__
     */
    protected $text_prefix = 'COM_TSWRENT_SUPPLIER';


    	/**
	 * Method to edit supplier.
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function edit2()
	{
        $id    = $this->input->get('id');

        $this->setRedirect('index.php?option=com_tswrent&task=supplier.edit&id='.$id);
    }

    
    	/**
	 * Method to remove brand.
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function removebrand()
	{     
		// Get the model.
		$model  = $this->getModel();
		$id    = $this->input->get('id' , 'int');
		$brand_id    = $this->input->get('brand_id' , 'int');
		
		if (!$this->app->getIdentity()->authorise('core.edit.state', 'com_contact.category.' . (int) $item->catid)) {
			// Prune items that you can't change.
			unset($id,$brand_id);
			$this->app->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'notice');
		}

		// Publish the items.
		if (!$model->removebrand($id,$brand_id)) {
			$this->app->enqueueMessage($model->getError(), 'warning');
	}
    
        $this->setRedirect('index.php?option=com_tswrent&view=supplier&id='.$id);
    }

	    	/**
	 * Method to remove brand.
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function removecontact()
	{     
		// Get the model.
		$model  = $this->getModel();
		$id    = $this->input->get('id' , 'int');
		$contact_id    = $this->input->get('contact_id' , 'int');
		
		if (!$this->app->getIdentity()->authorise('core.edit.state', 'com_contact.category.' . (int) $item->catid)) {
			// Prune items that you can't change.
			unset($id,$contact_id);
			$this->app->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'notice');
		}

		// Publish the items.
		if (!$model->removecontact($id,$contact_id)) {
			$this->app->enqueueMessage($model->getError(), 'warning');
	}
    
        $this->setRedirect('index.php?option=com_tswrent&view=supplier&id='.$id);
    }
}
