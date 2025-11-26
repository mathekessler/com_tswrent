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
use Joomla\CMS\Date\Date;
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

/**
 * Tswrent component helper.
 *
 * @since  __BUMP_VERSION__
 */
class OrderHelper
{

    /**
     * Finds the applicable graduation tier (days) for a given number of rental days.
     *
     * @param   array  $array  The graduation tiers.
     * @param   int    $days   The number of rental days.
     *
     * @return  int|null  The closest lower or equal day threshold, or null if none matched.
     *
     * @since   __BUMP_VERSION__
     */
     public static function graduationFactor(array $array, int $days): ?int
    {
        $nextSmaller = null;

        foreach ($array as $item) {
            if ($item <= $days && ($nextSmaller === null || $item > $nextSmaller)) {
                $nextSmaller = $item;
            }
        }

        return $nextSmaller;
    }
    
    /**
     * Counts full rental days between two dates (inclusive).
     *
     * @param   string  $startDate  e.g. '2025-07-01 10:00:00'
     * @param   string  $endDate    e.g. '2025-07-05 18:00:00'
     *
     * @return  int  Number of full days (minimum 1).
     */
    public static function countDays(string $startDate, string $endDate): int
    {
        // Convert dates to JDate objects
        $startDate = Factory::getDate($startDate);
        $endDate = Factory::getDate($endDate);

        // Calculate the difference in days
        $diffInDays = $endDate->toUnix() - $startDate->toUnix();
        $days = floor($diffInDays / (60 * 60 * 24)) + 1;

        return ($days);
    }

    /**
     * Counts the total hours between two dates.
     *
     * @param   string  $startDate  The start date.
     * @param   string  $endDate    The end date.
     *
     * @return  int  The total number of hours.
     */
    public static function countHours(string $startDate, string $endDate): int{

        // Convert dates to JDate objects
        $startDate = Factory::getDate($startDate);
        $endDate = Factory::getDate($endDate);
    
        // Calculate the difference in days
        $diffTime = $endDate->toUnix() - $startDate->toUnix();
        $hours = floor($diffTime / 3600);
    
        return ($hours);
        }


    /**
     * Saves the products associated with an order. This method first deletes all
     * existing product relations for the order and then inserts the new ones.
     *
     * @param   int    $orderId       The ID of the order.
     * @param   array  $productsData  An array of product data from the form.
     *
     * @return  bool   True on success, false on failure.
     * @since   __BUMP_VERSION__
     */
    public static function saveOrderProducts(int $orderId, array $productsData): bool{
         
        if ($orderId <= 0) {
            return false;
        }
        
        $db = Factory::getDbo();

        try {
            $db->transactionStart();

            // Delete all existing product associations for this order
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__tswrent_order_product'))
                ->where($db->quoteName('order_id') . ' = :orderId')
                ->bind(':orderId', $orderId, ParameterType::INTEGER);
            $db->setQuery($query);
            $db->execute();

            // Insert new product associations if any exist
            if (!empty($productsData)) {
                $columns = [
                    'order_id', 'product_id', 'description', 'reserved', 'productdiscount', 'price', 'price_total'
                ];
    
                foreach ($productsData as $product) {
                    // Skip if there is no product ID
                    if (empty($product['product_id'])) {
                        continue;
                    }

                    $insertQuery = $db->getQuery(true)
                        ->insert($db->quoteName('#__tswrent_order_product'))
                        ->columns($db->quoteName($columns));

                    // Sanitize and prepare values for insertion
                    $values = [
                        $orderId,
                        (int) ($product['product_id'] ?? 0),
                        $db->quote($product['product_description'] ?? ''),
                        (int) ($product['reserved_quantity'] ?? 0),
                        (float) ($product['productdiscount'] ?? 0.0),
                        (float) ($product['product_price'] ?? 0.0),
                        (float) ($product['product_price_total'] ?? 0.0)
                    ];
                    
                    $insertQuery->values(implode(',', $values));
                    $db->setQuery($insertQuery)->execute();
                }
            }

            $db->transactionCommit();
            return true;
        
        } catch (\Exception $e) {
            $db->transactionRollback();
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Loads all products associated with a given order.
     *
     * @param   int  $orderId  The ID of the order.
     *
     * @return  array|false  An array of product objects on success, false on failure.
     */
    public static function LoadOrderProducts(int $orderId)
    {
        
        if ($orderId <= 0) {
            return [];
        }
     //$app = Factory::getApplication();    $app->enqueueMessage(print_r( $orderId), true) ;

        $db = Factory::getDbo();
        
        try {    
            $query = $db->getQuery(true) 
                 ->select([
                        $db->quoteName('prod.id'),
                        $db->quoteName('prod.title', 'product_title'),
                        $db->quoteName('p.product_id'),
                        $db->quoteName('p.order_id'),
                        $db->quoteName('p.description', 'product_description'),
                        $db->quoteName('p.reserved', 'reserved_quantity'),
                        $db->quoteName('p.productdiscount'),
                        $db->quoteName('p.price', 'product_price'),
                        $db->quoteName('p.price_total', 'product_price_total'),
                    ])
                ->from($db->quoteName('#__tswrent_order_product', 'p'))
                ->join('LEFT', $db->quoteName('#__tswrent_products', 'prod') . ' ON ' . $db->quoteName('prod.id') . ' = ' . $db->quoteName('p.product_id'))
                ->where($db->quoteName('p.order_id') . ' = :orderId')
                ->bind(':orderId', $orderId, ParameterType::INTEGER);
            $db->setQuery($query);
            $result = $db->loadObjectList();
            return $result;
        } catch (\RuntimeException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return [];
        }
    }
}