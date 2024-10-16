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
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Order ontroller class 
 *
 * @since  __BUMP_VERSION__
 */
class OrderController extends FormController
{
	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   __BUMP_VERSION__
	 */
    public function batch($model = null)
    {
        $this->checkToken();

        // Set the model
        $model = $this->getModel('Order', '', []);

        // Preset the redirect
        $this->setRedirect(Route::_('index.php?option=com_tswrent&view=orders' . $this->getRedirectToListAppend(), false));

        return parent::batch($model);
    }
    public function c_contact()
    {   
       // Get the model.
		$model  = $this->getModel();
		$id    = $this->input->get('id' , 'int');
		
		

            // Publish the items.
            if (!$model->c_contactid($id)) {
                $this->app->enqueueMessage($model->getError(), 'warning');
            };

           echo new JsonResponse($model->c_contactid($id));

     }

     public function getgraduationFactor()
    {   
       // Get the model.
		$model  = $this->getModel();
		$id    = $this->input->get('id' , 'int');
        $days    = $this->input->get('days' , 'int');
		
		

            // Publish the items.
            if (!$model->getgraduationFactor($id,$days)) {
                $this->app->enqueueMessage($model->getError(), 'warning');
            };

           echo new JsonResponse($model->getgraduationFactor($id,$days));

     }
}
