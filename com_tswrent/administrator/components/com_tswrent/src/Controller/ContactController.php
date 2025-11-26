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
 * Contact controller class
 *
 * @since  __BUMP_VERSION__
 */
class ContactController extends FormController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * 
     * @since  __BUMP_VERSION__
     */
    protected $text_prefix = 'COM_TSWRENT_CONTACT';


     	/**
	 * Method to remove supplier.
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function removesupplier()
	{     
		// Get the model.
		$model  = $this->getModel();
		$id    = $this->input->get('id' , 'int');
		$supplier_id    = $this->input->get('supplier_id' , 'int');
		
		if (!$this->app->getIdentity()->authorise('core.edit.state', 'com_contact.category.' . (int) $item->catid)) {
			// Prune items that you can't change.
			unset($id,$supplier_id);
			$this->app->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'notice');
		}

		// Publish the items.
		if (!$model->removesupplier($id,$supplier_id)) 
		{
			$this->app->enqueueMessage($model->getError(), 'warning');
		}
    
        $this->setRedirect('index.php?option=com_tswrent&view=contact&id='.$id);
    }

         	/**
	 * Method to remove customer.
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function removecustomer()
	{     
		// Get the model.
		$model  = $this->getModel();
		$id    = $this->input->get('id' , 'int');
		$customer_id    = $this->input->get('customer_id' , 'int');
		
		if (!$this->app->getIdentity()->authorise('core.edit.state', 'com_contact.category.' . (int) $item->catid)) {
			// Prune items that you can't change.
			unset($id,$customer_id);
			$this->app->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'notice');
		}

		// Publish the items.
		if (!$model->removecustomer($id,$customer_id)) {
			$this->app->enqueueMessage($model->getError(), 'warning');
	}
    
        $this->setRedirect('index.php?option=com_tswrent&view=contact&id='.$id);
    }
}
