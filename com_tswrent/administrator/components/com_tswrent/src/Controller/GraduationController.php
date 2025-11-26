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
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Graduation ontroller class 
 *
 * @since  __BUMP_VERSION__
 */
class GraduationController extends FormController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * 
     * @since  __BUMP_VERSION__
     */
    protected $text_prefix = 'COM_TSWRENT_GRADUATION';
    


    public function save($key = null, $urlVar = null)
    {
        $app   = Factory::getApplication();
        $model = $this->getModel();

        // Speichern der Daten
        $return = parent::save($key, $urlVar);

        if ($return) {
            // Prüfen, ob ein Return-Parameter gesetzt wurde
            $returnUrl = $app->input->get('return', '', 'base64');

            if ($returnUrl) {
                // Zurück zur ursprünglichen Seite
                $this->setRedirect(base64_decode($returnUrl), Text::_('COM_TSWRENT_GRADUATION_SAVED'));
            } 
        }

        return $return;
    }
}