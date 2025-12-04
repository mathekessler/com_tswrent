<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Helper;

use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

/**
 * Tswrent component helper.
 * Tswrent helper.
 *
 * @since  __BUMP_VERSION__
 */
class TswrentHelper
{
    protected static function getDb(): DatabaseDriver
    {
        return Factory::getContainer()->get('DatabaseDriver');
    }
    
	/**
     * Load supplier Brand Relation
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function getInputSupplierBrandRelation(int $id, string $switch): array
    {
        [$col1, $col2, $table] = match ($switch) { // phpcs:ignore Generic.Formatting.MultipleStatementAlignment.NotSame
            'supplierbrand' => ['b.brand_id', 'b.supplier_id', 'brands'],
            'brandsupplier' => ['b.supplier_id', 'b.brand_id', 'suppliers'],
            default => [null, null, null]
        };

        if (!$col1 || !$col2 || !$table || empty($id)) {
            return [];
        }

        $db = self::getDb();
        $query = $db->getQuery(true)
            ->select(['a.*', $db->quoteName($col1)])
            ->from($db->quoteName("#__tswrent_{$table}", 'a'))
            ->join('INNER', $db->quoteName('#__tswrent_brand_supplier_relation', 'b') . ' ON ' . $db->quoteName($col1) . ' = a.id')
            ->where($db->quoteName($col2) . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->order($db->quoteName('a.title'));
        $db->setQuery($query);

        try {
            return $db->loadObjectList() ?: [];
        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Synchronisiert die Supplier-Brand-Relation, filtert leere Einträge und nutzt Switch-Logik
     *
     * @param int $id Brand- oder Supplier-ID
     * @param array $relatedIds Array der Supplier- oder Brand-IDs (z.B. aus Subform)
     * @param string $switch 'supplierbrand' oder 'brandsupplier'
     * @return bool
     */
    public static function syncSupplierBrandRelation(int $id, array $relatedIds, string $switch): bool
    {
        if (empty($id)) {
            return false;
        }
        // Switch-Logik
        [$col1, $col2] = match ($switch) {
            'supplierbrand' => ['brand_id', 'supplier_id'],
            'brandsupplier' => ['supplier_id', 'brand_id'],
            default => [null, null]
        };
        if (!$col1 || !$col2) {
            return false;
        }
        // Subform-Array direkt filtern (z.B. ['brand_id'=>1], ['supplier_id'=>1], ...)
        if (!empty($relatedIds) && is_array($relatedIds) && is_array(reset($relatedIds))) {
            $relatedIds = array_filter(array_map(function($v) use ($col1) {
                return !empty($v[$col1]) ? (int)$v[$col1] : null;
            }, $relatedIds));
        } else {
            $relatedIds = array_filter(array_map('intval', $relatedIds));
        }
        $db = self::getDb();
        // Aktuelle Relationen abrufen
        $query = $db->getQuery(true)
            ->select($db->quoteName($col1))
            ->from($db->quoteName('#__tswrent_brand_supplier_relation'))
            ->where($db->quoteName($col2) . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->order($db->quoteName($col1));
        $db->setQuery($query);
        $current = $db->loadColumn() ?: [];
        $toDelete = array_diff($current, $relatedIds);
        $toInsert = array_diff($relatedIds, $current);
        $ret = true;
        if (!empty($toDelete)) {
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__tswrent_brand_supplier_relation'))
                ->where($db->quoteName($col2) . ' = :id')
                ->whereIn($db->quoteName($col1), $toDelete)
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($query);
            $ret = $ret && $db->execute();
        }
        if (!empty($toInsert)) {
            foreach ($toInsert as $insertId) {
                $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__tswrent_brand_supplier_relation'))
                    ->columns([$db->quoteName($col1), $db->quoteName($col2)])
                    ->values((int)$insertId . ', ' . (int)$id);
                $db->setQuery($query);
                $ret = $ret && $db->execute();
            }
        }
        return $ret;
    }
}
