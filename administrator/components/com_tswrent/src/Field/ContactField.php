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
                    ->where($db->quoteName('a.published') . ' = 1')
                    ->order($db->quoteName('a.title'));
            
            break;
            case "order";
            $query = $db->getQuery(true)
                    ->select(
                        [
                            $db->quoteName('a.id', 'value'),
                            $db->quoteName('a.title', 'text'),
                        ]
                    )
                    ->from($db->quoteName('#__tswrent_contacts', 'a'))
                    ->join('inner',$db->quoteName('#__tswrent_contact_relation', 'b').'on'.$db->quoteName('b.customer_id').'!= 0 and '.$db->quoteName('b.contact_id').'='.$db->quoteName('a.id') )
                    ->join('inner',$db->quoteName('#__tswrent_orders', 'o').'on'.$db->quoteName('o.customer').'='.$db->quoteName('b.customer_id') )
                    ->where($db->quoteName('a.published') . ' = 1')
                    ->order($db->quoteName('a.title'));
            
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
                    ->where($db->quoteName('a.published') . ' = 1')
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


        array_unshift($options, HTMLHelper::_('select.option', "0", Text::_('COM_TSWRENT_SELECT')));

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;

	}
}
