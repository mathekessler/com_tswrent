<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_tswrent
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\View\Customer;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\AbstractView;
use Joomla\CMS\MVC\View\GenericDataException;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View to create a VCF for a contact item
 *
 * @since  1.6
 */
class VcfView extends AbstractView
{
    /**
     * The contact item
     *
     * @var   \Joomla\CMS\Object\CMSObject
     */
    protected $item;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  string  A string if successful
     *
     * @throws  GenericDataException
     */
    public function display($tpl = null)
    {
        // Get model data.
        $item = $this->get('Item');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->document->setMimeEncoding('text/directory', true);

        // Compute lastname, firstname and middlename
        $item->title = trim($item->title);

        // "Lastname, Firstname Middlename" format support
        // e.g. "de Gaulle, Charles"
        $namearray = explode(',', $item->title);

        if (count($namearray) > 1) {
            $lastname         = $namearray[0];
            $card_name        = $lastname;
            $name_and_midname = trim($namearray[1]);

            $firstname = '';

            if (!empty($name_and_midname)) {
                $namearray = explode(' ', $name_and_midname);

                $firstname  = $namearray[0];
                $middlename = (count($namearray) > 1) ? $namearray[1] : '';
                $card_name  = $firstname . ' ' . ($middlename ? $middlename . ' ' : '') . $card_name;
            }
        } else {
            // "Firstname Middlename Lastname" format support
            $namearray = explode(' ', $item->title);

            $middlename = (count($namearray) > 2) ? $namearray[1] : '';
            $firstname  = array_shift($namearray);
            $lastname   = count($namearray) ? end($namearray) : '';
            $card_name  = $firstname . ($middlename ? ' ' . $middlename : '') . ($lastname ? ' ' . $lastname : '');
        }

        $rev = date('c', strtotime($item->modified));

        Factory::getApplication()->setHeader('Content-disposition', 'attachment; filename="' . $card_name . '.vcf"', true);

        $vcard = [];
        $vcard[] .= 'BEGIN:VCARD';
        $vcard[] .= 'VERSION:3.0';
        $vcard[]  = 'N:' . $lastname . ';' . $firstname . ';' . $middlename;
        $vcard[]  = 'FN:' . $item->title;
        $vcard[]  = 'TEL;TYPE=WORK,VOICE:' . $item->telephone;
        $vcard[]  = 'TEL;TYPE=WORK,MOBILE:' . $item->mobile;
        $vcard[]  = 'ADR;TYPE=WORK:;;' . $item->address . ';' . $item->city . ';'  . $item->postalcode;
        $vcard[]  = 'LABEL;TYPE=WORK:' . $item->address . "\n" . $item->city . "\n" . $item->postalcode;
        $vcard[]  = 'EMAIL;TYPE=PREF,INTERNET:' . $item->email_to;
        $vcard[]  = 'URL:' . $item->webpage;
        $vcard[]  = 'REV:' . $rev . 'Z';
        $vcard[]  = 'END:VCARD';

        echo implode("\n", $vcard);
    }
}
