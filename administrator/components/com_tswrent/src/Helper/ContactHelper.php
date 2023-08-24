<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Tswrent component helper.
 *
 * @since  __BUMP_VERSION__
 */
class ContactHelper
{
	/**
     * Load Contact Relation
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function getInputContactRelation($id,$switch){
       
		$db   = Factory::getContainer()->get('DatabaseDriver');
		
		/** switch for different relations */
		switch($switch) {
			/** supplier relations */
			case "suppliercontact":
				$select01 = 'b.contact_id';
				$select02 = 'b.supplier_id';
				break;
			case "customercontact":
				$select01 = 'b.contact_id';
				$select02 = 'b.customer_id';
				break;
		}		
		// get previous entries
		$query = $db->getQuery(true);
		$query->select(['a.*', $select01])
			->from($db->quoteName('#__tswrent_contacts','a'))
			->join('inner',$db->quoteName('#__tswrent_contact_relation','b').'ON' .$db->quoteName($select02).'=' .$id)
			->where($db->quoteName('a.id'). '=' .$db->quoteName($select01));
		$db->setQuery($query);
		$input = $db->loadObjectList();
        return ($input);   
    }
	
    /**
     * Load Supplier Contact Relation
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function getInputSupplierRelation($id){
       
		$db   = Factory::getContainer()->get('DatabaseDriver');
			
		// get previous entries
		$query = $db->getQuery(true);
		$query->select(['a.supplier_id',])
			->from($db->quoteName('#__tswrent_contact_relation','a'))
			->where($db->quoteName('a.contact_id'). '=' .$id)
            ->where($db->quoteName('a.supplier_id'). '!= 0 ');
		$db->setQuery($query);
		$input = $db->loadObjectList();
        return ($input);   
    }

	/**
     * Load Supplier Contact Relation
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function getInputCustomerRelation($id){
       
		$db   = Factory::getContainer()->get('DatabaseDriver');
			
		// get previous entries
		$query = $db->getQuery(true);
		$query->select(['a.customer_id',])
			->from($db->quoteName('#__tswrent_contact_relation','a'))
			->where($db->quoteName('a.contact_id'). '=' .$id)
            ->where($db->quoteName('a.customer_id'). '!= 0 ');
		$db->setQuery($query);
		$input = $db->loadObjectList();
        return ($input);   
    }

	/**
     * Load Supplier Contact Relation
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function getInputTswrentemployeeRelation($id){
       
		$db   = Factory::getContainer()->get('DatabaseDriver');
			
		// get previous entries
		$query = $db->getQuery(true);
		$query->select(['a.tswrent',])
			->from($db->quoteName('#__tswrent_contact_relation','a'))
			->where($db->quoteName('a.contact_id'). '=' .$id)
            ->where($db->quoteName('a.tswrent'). '!= 0 ');
		$db->setQuery($query);
		$input = $db->loadObjectList();
        return ($input);   
    }

	    /**
     * delete Contact Relation
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function deleteContactRelation($id, $switch)
    {   
		$db   = Factory::getContainer()->get('DatabaseDriver');

		/** switch for different relations */
		switch($switch) {
			/** supplier relations */
			case "contactsupplier":
				$select01 = 'contact_id';
				$select01 = 'supplier_id';
				break;
			case "contactcustomer":
				$select01 = 'contact_id';
				$select02 = 'customer_id';
				break;
            case "tswrent":
				$select01 = 'contact_id';
                $select02 = 'tswrent';
            break;
			case "suppliercontact":
				$select01 = 'supplier_id';
				$select02 = 'contact_id';
				break;

		}	

		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__tswrent_contact_relation'));
		$query->where($select01.' = ' . $id);
		$query->where($select02.' != 0');
		$db->setQuery($query);
		
		try {
			$db->execute();
		}
		catch (Exception $e){
			echo $e->getMessage();
		}
	}
	

    /**
     * Check/Save/Update the Relation to the contact
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function saveContactRelation($id, $related_id,$switch)
    {

        $db   = Factory::getContainer()->get('DatabaseDriver');
		$id = (int)$id;
		if (empty($id) ){
			return false;
		}
		
		/** switch for different relations */
		switch($switch) {
			/** supplier relations */
			case "contactsupplier":
				$new_items = $related_id;
				$select01 = 'supplier_id';
				$select02 = 'contact_id';
				break;
			case "contactcustomer":
				$new_items = $related_id;
				$select01 = 'customer_id';
				$select02 = 'contact_id';
				break;
            case "tswrent":
                $new_items = $related_id;
                $select01 = 'tswrent';
				$select02 = 'contact_id';
            break;
			case "suppliercontact":
				$new_items = $related_id;
                $select01 = 'contact_id';
				$select02 = 'supplier_id';
            break;
			case "customercontact":
				$new_items = $related_id;
                $select01 = 'contact_id';
				$select02 = 'customer_id';
            break;
		}	

		// get previous entries
		$query = $db->getQuery(true);
		$query->select($select01);
		$query->from($db->quoteName('#__tswrent_contact_relation'));
		$query->where($db->quoteName($select02). ' = ' . $id);
		$query->where($select01.' != 0');
		$query->order($select01);
		$db->setQuery($query);
		$cur_reccur = $db->loadColumn();

		if (!is_array($cur_reccur)) {
			return false;
		}

		$del_reccur = array_diff($cur_reccur, $new_items);
		$add_reccur = array_diff($new_items, $cur_reccur);

		if (!empty($del_reccur)) {
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__tswrent_contact_relation'));
			$query->where($db->quoteName($select02).' = ' . $id);
			$query->whereIn($select01, $del_reccur);
			$db->setQuery($query);
			$ret &= ($db->execute() !== false);
		}

		if (!empty($add_reccur)) {
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__tswrent_contact_relation'))
			      ->columns($db->quoteName(array($select01, $select02)));
			foreach ($add_reccur as $reccurid) {
				$query->values((int)$reccurid . ',' . $id);
			}
			$db->setQuery($query);
			$ret &= ($db->execute() !== false);
		}

		return true;
    }

    /**
     * set TSW Rent Relation
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function setTswrentRelation($id, $related_id)
    {

    }
}