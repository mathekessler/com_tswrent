<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Field;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * TswrentContact field.
 *
 * @since __BUMP_VERSION__
 * 
 */
class ContactField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * 
     * @since  __BUMP_VERSION__
     */
    protected $type = 'Contact';

	/**
	 * Create Input
	 * @see JFormFieldList::getInput()
	 */
	public function getInput()
	{
			return parent::getInput();
	
	}


	/**
	 * Retrieve Options
	 * @see JFormFieldList::getOptions()
	 */
	protected function getOptions()
	{
		$db    = Factory::getDbo();
        $contacttype = $this->element['contacttype'];
        $state = 1;
        switch($contacttype){
            case "tswrent";
            $query = $db->getQuery(true)
                    ->select(
                        [
                            $db->quoteName('a.id', 'value'),
                            $db->quoteName('a.title', 'text'),
                            $db->quoteName('b.tswrent')
                        ]
                    )
                    ->from($db->quoteName('#__tswrent_contacts', 'a'))
                    ->join('inner',$db->quoteName('#__tswrent_contact_relation', 'b').'on'.$db->quoteName('b.tswrent').'!= 0 and '.$db->quoteName('b.contact_id').'='.$db->quoteName('a.id') )
                    ->where($db->quoteName('a.state') . ' = :state')
                    ->bind(':state', $state, ParameterType::INTEGER)
                    ->order($db->quoteName('a.title'));
            
            break;
            case "order":
                $app = Factory::getApplication();
                $orderId = $app->input->getInt('id', 0);
                $customerId = 0;

                // If we are editing an existing order, find its customer
                if ($orderId) {
                    $orderQuery = $db->getQuery(true)
                        ->select($db->quoteName('customer_id'))
                        ->from($db->quoteName('#__tswrent_orders'))
                        ->where($db->quoteName('id') . ' = :orderId')
                        ->bind(':orderId', $orderId, ParameterType::INTEGER);
                    $customerId = (int) $db->setQuery($orderQuery)->loadResult() ?: 0;

                    $query = $db->getQuery(true)
                    ->select(
                        [
                            $db->quoteName('a.id', 'value'),
                            $db->quoteName('a.title', 'text'),
                        ]
                    )
                    ->from($db->quoteName('#__tswrent_contacts', 'a'));

                // Only show contacts for the selected customer
                $query->join('INNER', $db->quoteName('#__tswrent_contact_relation', 'b') . ' ON ' . $db->quoteName('b.contact_id') . ' = ' . $db->quoteName('a.id'));
                $query->where($db->quoteName('b.customer_id') . ' = :customerId')
                ->bind(':customerId', $customerId, ParameterType::INTEGER);
                $query->where($db->quoteName('a.state') . ' = :state')->bind(':state', $state, ParameterType::INTEGER);

                $query->order($db->quoteName('a.title'));
                }

                // If this is a new order (no id), only return the default select option
                if (!$orderId) {   
                    return parent::getOptions();
                }

                
            
            break;

             default:
                $query = $db->getQuery(true)
                    ->select(
                        [
                            $db->quoteName('a.id', 'value'),
                            $db->quoteName('a.title', 'text'),
                        ]
                    )
                    ->from($db->quoteName('#__tswrent_contacts', 'a'))
                    ->where($db->quoteName('a.state') . ' = :state')
                    ->bind(':state', $state, ParameterType::INTEGER)
                    ->order($db->quoteName('a.title'));
            break; 
        }
        
        // Get the options.
        $db->setQuery($query);

        try {
            $options = $db->loadObjectList();
            
        } catch (\RuntimeException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }


        //array_unshift($options, HTMLHelper::_('select.option', "0", Text::_('COM_TSWRENT_SELECT')));

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;

	}
}
