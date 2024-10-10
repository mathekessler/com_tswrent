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
use Joomla\CMS\Date\Date;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Tswrent component helper.
 *
 * @since  __BUMP_VERSION__
 */
class OrderHelper
{

    /**
     * calculate the Graduation Factor
     *
     * @param   array $array  Graduation array.
     * 
     * @param   object  $day  Numbers of day.
     *
     * @return  factor
     *
     *  @since   __BUMP_VERSION__
     * 
     */
    public static function graduationFactor($array,$day) {

        $nextSmaller = null;

        foreach ($array as $item) {
            if ($item <= $day && ($nextSmaller === null || $item > $nextSmaller)) {
                $nextSmaller = $item;
            }
        }

        return $nextSmaller;
    }

    public static function countDays($startDate,$endDate){

        // Convert dates to JDate objects
        $startDate = Factory::getDate($startDate);
        $endDate = Factory::getDate($endDate);

        // Calculate the difference in days
        $diffInDays = $endDate->toUnix() - $startDate->toUnix();
        $days = floor($diffInDays / (60 * 60 * 24)) + 1;

        return ($days);
    }

    
    public static function countHours($startDate,$endDate){

        // Convert dates to JDate objects
        $startDate = Factory::getDate($startDate);
        $endDate = Factory::getDate($endDate);
    
        // Calculate the difference in days
        $diffTime = $endDate->toUnix() - $startDate->toUnix();
        $hours = floor($diffTime / 3600);
    
        return ($hours);
        }
}