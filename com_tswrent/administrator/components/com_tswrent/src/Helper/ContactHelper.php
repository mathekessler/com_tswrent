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
     * delete Contact Relation suplier/customer
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
    */
    public static function deleteContactRelation(int $id, string $switch)
    {
        $columns = self::getRelationColumns($switch);
         if (!$columns || empty($id)) {
            return false;
        }
        [$col1, $col2] = $columns;
        
        try
        {
            $db    = Factory::getDbo();
        
            // Build delete query. We cast the id to int directly to avoid incorrect bind usage here.
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__tswrent_contact_relation'))
                ->where($db->quoteName($col2) . ' = ' . (int) $id)
                ->where($db->quoteName($col1) . ' != 0')
                ->where($db->quoteName('tswrent') . ' = 0');
            
            $db->setQuery($query); 
        
            $db->execute();

            return true ;

        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }      
    }

    /**
     * delete Contact Relation Employee
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
    */
    public static function deleteContactRelationEmployee(int $id)
    {
        try
        {
            $db    = Factory::getDbo();
        
            // Build delete query. We cast the id to int directly to avoid incorrect bind usage here.
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__tswrent_contact_relation'))
                ->where($db->quoteName('contact_id') . ' = :contact_id')
                ->where($db->quoteName('tswrent') . ' = 1')
                ->bind(':contact_id', $id, ParameterType::INTEGER);
            
            $db->setQuery($query);  
            
            $db->execute();
            
            return true ;

        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }        
    }
        /**
     * save Contact Relation Employee
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
    */
    public static function saveContactRelationEmployee(int $id)
    {
        self::deleteContactRelationEmployee($id);
        try{
            $db    = Factory::getDbo();
        
            // Build insert query.}
            $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__tswrent_contact_relation'))
                    ->columns([$db->quoteName('contact_id'), $db->quoteName('tswrent')])
                    ->values(':contact_id, 1')
                    ->bind(':contact_id', $id, ParameterType::INTEGER);
            $db->setQuery($query);
            $db->execute();
    
            return true ;
        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

    }
	

    /**
     * Check/Save/Update the Relation from Supplier/Customer to the contact
     *
     * @return  array
     * 
     *  @since   __BUMP_VERSION__
     */
    public static function saveContactRelation(int $id, array $relatedIds, string $switch): bool
    {
        $columns = self::getRelationColumns($switch);

        if (!$columns || empty($id)) {
            return false;
        }

        [$col1, $col2] = $columns;
        $db    = Factory::getDbo();

        $query = $db->getQuery(true);
        $query->select($col1)
            ->from($db->quoteName('#__tswrent_contact_relation'))
            ->where($col2 . ' = ' . (int) $id)
            ->where($col1 . ' != 0');

        $db->setQuery($query);
        $current = $db->loadColumn() ?: [];

        $toDelete = array_diff($current, $relatedIds);
        $toInsert = array_diff($relatedIds, $current);
        $success = true;

        if (!empty($toDelete)) {
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__tswrent_contact_relation'))
                ->where($col2 . ' = ' . (int) $id)
                ->whereIn($col1, $toDelete);
            $db->setQuery($query);
            $success &= $db->execute() !== false;
        }

        if (!empty($toInsert)) {
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__tswrent_contact_relation'))
                ->columns([$col1, $col2]);

            foreach ($toInsert as $relatedId) {
                $query->values((int) $relatedId . ', ' . $id);
            }

            $db->setQuery($query);
            $success &= $db->execute() !== false;
        }

        return $success;
    }
}