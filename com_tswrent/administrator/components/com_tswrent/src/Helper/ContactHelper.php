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
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

/**
 * Tswrent component helper.
 *
 * @since  __BUMP_VERSION__
 */
class ContactHelper
{

    protected static function getRelationColumns(string $switch): ?array
    {
        return match ($switch) {
            'contactsupplier'   => ['supplier_id', 'contact_id', 'suppliers'],
            'contactcustomer'   => ['customer_id', 'contact_id', 'customers'],
            'tswrent'           => ['tswrent', 'contact_id', null],
            'suppliercontact'   => ['contact_id', 'supplier_id', 'contacts'],
            'customercontact'   => ['contact_id', 'customer_id', 'contacts'],
            default             => null,
        };
    }

	/**
     * Load Contact Relation Supplier/Customer
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function getInputContactRelation(int $id, string $switch): array
    {
        $columns = self::getRelationColumns($switch);

        if (!$columns || empty($columns[2])) {
            return [];
        }

        [$col1, $col2, $table] = $columns;
        try
        {
            $db    = Factory::getDbo();
            $query = $db->getQuery(true);

            $query->select('a.*, b.' . $db->quoteName($col1))
                ->from($db->quoteName("#__tswrent_{$table}", 'a'))
                ->join(
                    'INNER',
                    $db->quoteName('#__tswrent_contact_relation', 'b') . ' ON a.id = b.' . $db->quoteName($col1)
                )
                ->where('b.' . $db->quoteName($col2) . ' = ' . (int) $id);

            $db->setQuery($query);


            return $db->loadObjectList() ?: [];

        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }
    }

	/**
     * Load Employee Contact
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function getInputTswrentemployeeRelation(int $id): array
    {
        try
        {
            $db    = Factory::getDbo();

            $query = $db->getQuery(true);
            $query->select('a.tswrent')
                ->from($db->quoteName('#__tswrent_contact_relation', 'a'))
                ->where('a.contact_id = :contact_id')
                ->where('a.tswrent != 0')
                ->bind(':contact_id', $id, ParameterType::INTEGER);

            $db->setQuery($query);
            
            return $db->loadObjectList() ?: [];

        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }
    }

		/**  
     * Load if Contact is Tswrent Employee
     *
     * @return  array 
     *  
     *  @since   __BUMP_VERSION__  
     */
    public static function getInputTswrentemployees(): array
    {
        try{
            $db    = Factory::getDbo();

            $query = $db->getQuery(true);
            $query->select('a.*')
                ->from($db->quoteName('#__tswrent_contacts', 'a'))
                ->join('INNER', '#__tswrent_contact_relation AS b ON a.id = b.contact_id')
                ->where('b.tswrent != 0');

            $db->setQuery($query);

            return $db->loadObjectList() ?: [];
    
        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }
    }


    /**
     * Synchronisiert die Contact-Relation für tswrent-Employee (speichern/löschen)
     *
     * @param int $id Kontakt-ID
     * @param mixed $value Wert aus dem Formular (z.B. 1 für aktiv, leer/null für löschen)
     * @param string $switch Relationstyp ('tswrent')
     * @return bool
     */
    public static function syncContactRelationEmployee(int $id, $value, string $switch = 'tswrent'): bool
    {
        try {
            $db = Factory::getDbo();
            // Immer zuerst löschen
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__tswrent_contact_relation'))
                ->where($db->quoteName('contact_id') . ' = :contact_id')
                ->where($db->quoteName('tswrent') . ' = 1')
                ->bind(':contact_id', $id, ParameterType::INTEGER);
            $db->setQuery($query);
            $db->execute();

            // Nur speichern, wenn Wert gesetzt/aktiv
            if (!empty($value)) {
                $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__tswrent_contact_relation'))
                    ->columns([$db->quoteName('contact_id'), $db->quoteName('tswrent')])
                    ->values(':contact_id, 1')
                    ->bind(':contact_id', $id, ParameterType::INTEGER);
                $db->setQuery($query);
                $db->execute();
            }
            return true;
        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
    }
	

    /**
     * Synchronisiert Contact-Relationen (Supplier, Customer, etc.)
     *
     * @param int $id Kontakt-ID (oder Supplier/Customer, je nach Switch)
     * @param array $relatedIds Array der zu verknüpfenden IDs (z.B. aus Subform)
     * @param string $switch Relationstyp (z.B. 'contactsupplier', 'contactcustomer', ...)
     * @return bool
     */
    public static function syncContactRelation(int $id, array $relatedIds, string $switch): bool
    {
        $columns = self::getRelationColumns($switch);
        if (!$columns) {
            return false;
        }
        [$col1, $col2, $table] = $columns;
        if (!$col1 || !$col2) {
            return false;
        }
        $db = Factory::getDbo();
        // Subform-Array direkt filtern (z.B. ['supplier_id'=>1], ['customer_id'=>1], ...)
        if (!empty($relatedIds) && is_array($relatedIds) && is_array(reset($relatedIds))) {
            $relatedIds = array_filter(array_map(function($v) use ($col1) {
                return !empty($v[$col1]) ? (int)$v[$col1] : null;
            }, $relatedIds));
        } else {
            $relatedIds = array_filter(array_map('intval', $relatedIds));
        }
        // Aktuelle Relationen abrufen
        $query = $db->getQuery(true)
            ->select($db->quoteName($col1))
            ->from($db->quoteName('#__tswrent_contact_relation'))
            ->where($db->quoteName($col2) . ' = :id')
            ->where($db->quoteName($col1) . ' != 0')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->order($db->quoteName($col1));
        $db->setQuery($query);
        $current = $db->loadColumn() ?: [];
        $toDelete = array_diff($current, $relatedIds);
        $toInsert = array_diff($relatedIds, $current);
        $ret = true;
        // Löschen: Nur die jeweilige Relation entfernen
        if (!empty($toDelete)) {
            foreach ($toDelete as $deleteId) {
                $query = $db->getQuery(true)
                    ->delete($db->quoteName('#__tswrent_contact_relation'))
                    ->where($db->quoteName($col2) . ' = :id')
                    ->where($db->quoteName($col1) . ' = :deleteId')
                    ->bind(':id', $id, ParameterType::INTEGER)
                    ->bind(':deleteId', $deleteId, ParameterType::INTEGER);
                $db->setQuery($query);
                $ret = $ret && $db->execute();
            }
        }
        // Einfügen
        if (!empty($toInsert)) {
            foreach ($toInsert as $insertId) {
                $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__tswrent_contact_relation'))
                    ->columns([$db->quoteName($col1), $db->quoteName($col2)])
                    ->values((int)$insertId . ', ' . (int)$id);
                $db->setQuery($query);
                $ret = $ret && $db->execute();
            }
        }
        return $ret;
    }
}