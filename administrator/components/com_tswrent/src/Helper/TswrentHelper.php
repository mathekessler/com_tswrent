<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Helper;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Banners component helper.
 *
 * @since  1.6
 */
class TswrentHelper extends ContentHelper
{
    
    /**
     * Get supliers list in text/value format for a select field
     *
     * @return  array
     */
    public static function getBrandOptions()
    {
        $options = [];

        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select(
                [
                    $db->quoteName('id', 'value'),
                    $db->quoteName('title', 'text'),
                ]
            )
            ->from($db->quoteName('#__tswrent_brand', 'a'))
            ->where($db->quoteName('a.published') . ' = 1')
            ->order($db->quoteName('a.title'));

        // Get the options.
        $db->setQuery($query);

        try {
            $options = $db->loadObjectList();
        } catch (\RuntimeException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

        array_unshift($options, HTMLHelper::_('select.option', '0', Text::_('COM_TSWRENT_NO_BRANDS')));

        return $options;
    }
}
