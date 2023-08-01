<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;


/**
 * Controller for a single product
 *
 * @since  __BUMP_VERSION__
 */
class ProductController extends FormController
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
        $model = $this->getModel('Product', '', []);

        // Preset the redirect
        $this->setRedirect(Route::_('index.php?option=com_tswrent&view=products' . $this->getRedirectToListAppend(), false));

        return parent::batch($model);
    }
}
