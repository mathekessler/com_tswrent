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
class CustomerController extends FormController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * 
     * @since  __BUMP_VERSION__
     */
    protected $text_prefix = 'COM_TSWRENT_CUSTOMER';



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

        $this->setRedirect('index.php?option=com_tswrent&task=customer.edit&id='.$id);
    }
}