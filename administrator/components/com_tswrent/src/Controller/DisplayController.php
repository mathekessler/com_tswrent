<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


/**
 * TSWRent display controller.
 *
 * @since   __BUMP_VERSION__
 */

class DisplayController extends BaseController
{
    /**
     * The default view.
     *
     * @var    string
     */
    protected $default_view = 'products';

    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
     *
     * @return  BaseController|boolean  This object to support chaining.
     *
     * @since   __BUMP_VERSION__
     */
    public function display($cachable = false, $urlparams = [])
    {

        return parent::display();
    }
}
