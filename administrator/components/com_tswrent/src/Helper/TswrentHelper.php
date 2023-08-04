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
    public static function saveSupplierBrandRelation($supplier_id, $brand_id)
    {

        $db   = Factory::getContainer()->get('DatabaseDriver');


        $supplier_id = (int)$supplier_id;
		if (empty($supplier_id) || !is_array($brand_id)) {
			return false;
		}

		// get previous entries
		$query = $db->getQuery(true);
		$query->select('brand_id')
		      ->from('#__tswrent_brand_supplier_relation')
		      ->where('supplier_id = ' . $supplier_id)
		      ->order('brand_id');
		$db->setQuery($query);
		$cur_brand = $db->loadColumn();

		if (!is_array($cur_brand)) {
			return false;
		}

		$ret = true;
		$del_brand = array_diff($cur_brand, $brand_id);
		$add_brand = array_diff($brand_id, $cur_brand);

		if (!empty($del_brand)) {
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__tswrent_brand_supplier_relation'));
			$query->where('supplier_id = ' . $supplier_id);
			$query->where('brand_id IN (' . implode(',', $del_brand) . ')');
			$db->setQuery($query);
			$ret &= ($db->execute() !== false);
		}

		if (!empty($add_brand)) {
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__tswrent_brand_supplier_relation'))
			      ->columns($db->quoteName(array('brand_id', 'supplier_id')));
			foreach ($add_brand as $brandid) {
				$query->values((int)$brandid . ',' . $supplier_id);
			}
			$db->setQuery($query);
			$ret &= ($db->execute() !== false);
		}

		return true;
    }
    public static function getInputSupplierBrandRelation($supplier_id){
        $db   = Factory::getContainer()->get('DatabaseDriver');
	// get previous entries
	$query = $db->getQuery(true);
	$query->select('brand_id')
		  ->from('#__tswrent_brand_supplier_relation')
		  ->where('supplier_id = ' . $supplier_id)
		  ->order('brand_id');
	$db->setQuery($query);
	$input = $db->loadColumn();
		


        return ($input);   
    }



}
