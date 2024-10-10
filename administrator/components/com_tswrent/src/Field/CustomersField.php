<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ModalSelectField;
use Joomla\CMS\Language\Text; 
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\ParameterType;


// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Supports a modal customer picker.
 *
 * @since  1.6
 */
class CustomersField extends ModalSelectField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.6
     */
    protected $type = 'customers';

    /**
     * Method to attach a Form object to the field.
     *
     * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
     * @param   mixed              $value    The form field value to validate.
     * @param   string             $group    The field name group control value.
     *
     * @return  boolean  True on success.
     *
     * @see     FormField::setup()
     * @since   5.0.0
     */
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
       // Get the field
       $result = parent::setup($element, $value, $group);

		if (!$result)
		{
			return $result;
		}

		$app = Factory::getApplication();

		// We need the Url to get the list of products, 
		// getting an editing form, a creation form
		// entities. We indicate them here.
		// The result of accessing these URLs should return HTML,
		// which will include a small javascript,
		// transmitting the selected values - product id and product name.

      
		$urlSelect = (new Uri())->setPath(Uri::base(true) . '/index.php');
		$urlSelect->setQuery([
			'option'                => 'com_tswrent',
            'view'                  => 'customers',
            'layout'                => 'modal',
			'tmpl'                  => 'component',
			Session::getFormToken() => 1,
		]);

		$modalTitle = Text::_('COM_TSWEB_CUSTOMERS');
		$this->urls['select'] = (string) $urlSelect;

		// We comment on these lines, they are not needed. In the articles about JavaScript section
		// I’ll tell you why.
		// $wa = $app->getDocument()->getWebAssetManager();
		// $wa->useScript('field.modal-fields')->useScript('core');
		
		// Modal window title
		// To create and edit, respectively, you also need
		// individual headings
		$this->modalTitles['select'] = $modalTitle;

		// hint - the field placeholder in HTML.
		$this->hint = $this->hint ?: Text::_('COM_TSWEB_CUSTOMERS_CHOOSE_CUSTOMER');

		return $result;
	}
    

    /**
     * Method to retrieve the title of selected item.
     *
     * @return string
     *
     * @since   5.0.0
     */
    protected function getValueTitle()
    {
        $value = (int) $this->value ?: '';
        $title = '';

        if ($value) {
            try {
                $db    = $this->getDatabase();
                $query = $db->getQuery(true)
                    ->select($db->quoteName('title'))
                    ->from($db->quoteName('#__tswrent_customers'))
                    ->where($db->quoteName('id') . ' = :value')
                    ->bind(':value', $value, ParameterType::INTEGER);
                $db->setQuery($query);

                $title = $db->loadResult();
            } catch (\Throwable $e) {
                Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            }
        }

        return $title ?: $value;
    }



}
