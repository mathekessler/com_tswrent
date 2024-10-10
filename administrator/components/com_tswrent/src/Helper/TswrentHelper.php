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
class TswrentHelper
{

    /**
     * Check/Save/Update supplier Brand Relation
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function saveSupplierBrandRelation($id, $related_id,$switch)
    {

        $db   = Factory::getContainer()->get('DatabaseDriver');
		$id = (int)$id;
		if (empty($id) ){
			return false;
		}
		
		/** switch for different relations */
		switch($switch) {
			/** supplier relations */
			case "supplierbrand":
				$search = $id;
				$new_items = $related_id;
				$select01 = 'brand_id';
				$select02 = 'supplier_id';
				break;
			case "brandsupplier":
				$search = $id;
				$new_items = $related_id;
				$select01 = 'supplier_id';
				$select02 = 'brand_id';
				break;
		}	

		// get previous entries
		$query = $db->getQuery(true);
		$query->select($select01)
		      ->from('#__tswrent_brand_supplier_relation')
		      ->where($select02.' = ' . $search)
		      ->order($select01);
		$db->setQuery($query);
		$cur_reccur = $db->loadColumn();

		if (!is_array($cur_reccur)) {
			return false;
		}

		$del_reccur = array_diff($cur_reccur, $new_items);
		$add_reccur = array_diff($new_items, $cur_reccur);

		if (!empty($del_reccur)) {
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__tswrent_brand_supplier_relation'));
			$query->where($select02.' = ' . $search);
			$query->whereIn($select01, $del_reccur);
			$db->setQuery($query);
			$ret &= ($db->execute() !== false);
		}

		if (!empty($add_reccur)) {
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__tswrent_brand_supplier_relation'))
			      ->columns($db->quoteName(array($select01, $select02)));
			foreach ($add_reccur as $reccurid) {
				$query->values((int)$reccurid . ',' . $search);
			}
			$db->setQuery($query);
			$ret &= ($db->execute() !== false);
		}

		return true;
    }

    /**
     * delete supplier Brand Relation
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function deleteSupplierBrandRelation($id,$switch)
    {   
		$db   = Factory::getContainer()->get('DatabaseDriver');

		/** switch for different relations */
		switch($switch) {
			/** supplier relations */
			case "supplierbrand":
				$search = $id;
				$select01 = 'brand_id';
				$select02 = 'supplier_id';
				break;
			case "brandsupplier":
				$search = $id;
				$select01 = 'supplier_id';
				$select02 = 'brand_id';
				break;
		}	
		
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__tswrent_brand_supplier_relation'));
		$query->where($select02.' = ' . $search);
		$db->setQuery($query);
		
		try {
			$db->execute();
		}
		catch (Exception $e){
			echo $e->getMessage();
		}
	}


	/**
     * Load supplier Brand Relation
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function getInputSupplierBrandRelation($id,$switch){
       
		$db   = Factory::getContainer()->get('DatabaseDriver');

		/** switch for different relations */
		switch($switch) {
			/** supplier relations */
			case "supplierbrand":
				$id = (int)$id;
				if (empty($id) ){
					return false;
				}
				$select01 = 'b.brand_id';
				$select02 = 'b.supplier_id';
				$table = 'brands';
				break;

				/** brand relations */
			case "brandsupplier":
				$id = (int)$id;
				if (empty($id) ){
					return false;
				}
				$select01 = 'b.supplier_id';
				$select02 = 'b.brand_id';
				$table = 'suppliers';
				break;
		}
			
		// get previous entries
		$query = $db->getQuery(true);
		$query->select(['a.*',$select01,])
			->from($db->quoteName('#__tswrent_'.$table,'a'))
			->join('INNER',$db->quoteName('#__tswrent_brand_supplier_relation','b').' ON ' . $db->quoteName($select01) . ' = ' . $db->quoteName('a.id'))
			->where($select02.'='.$id)
			->order('a.title');
		$db->setQuery($query);
		$input = $db->loadObjectList();
		


        return ($input);   
    }
    
	/**
     * Load supplier Employees
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function getContacts($id,$type){
	

		$db   = Factory::getContainer()->get('DatabaseDriver');
			// get previous entries
			$query = $db->getQuery(true);
			$query->select( 'a.*')
			->from($db->quoteName('#__tswrent_contact','a'));
		
		switch($type){
		case "Supplier":
			
			$query->join('INNER',$db->quoteName('#__tswrent_contact_relation','b').' ON ' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.contact_id'))
				->whereIn($db->quoteName('b.supplier_id'),$id);

			$db->setQuery($query);
			$input = $db->loadObjectList();
			break;
	}
	return ($input);   
    }



}
