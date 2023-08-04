<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Service\HTML;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseAwareTrait;


// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


/**
 * Tswrent HTML class.
 *
 * @since  __BUMP_VERSION__
 * 
 */
class AdministratorService
{
    use DatabaseAwareTrait;
	
    /**
     * Display a batch widget for the brand selector.
     *
     * @return  string  The necessary HTML for the widget.
     *
     * @since   __BUMP_VERSION__
     * 
     */

    public function brands()
    {
        $brandname= 'Brand';
        // Create the batch selector to change the brand on a selection list.
        return implode(
            "\n",
            [
                '<label id="batch-brand-lbl" for="batch-brand_id">',
                Text::_('COM_TSWRENT_BATCH_BRAND_LABEL'),
                '</label>',
                '<select class="form-select" name="batch[brand_id]" id="batch-brand_id">',
                '<option value="">' . Text::_(Text::sprintf('COM_TSWRENT_BATCH_NOCHANGE', $brandname)) . '</option>',
                '<option value="0">' . Text::_(Text::sprintf('COM_TSWRENT_NO_VALUE', $brandname)) . '</option>',
                HTMLHelper::_('select.options', static::brandslist(), 'value', 'text'),
                '</select>',
            ]
        );
    }

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   __BUMP_VERSION__
     * 
     */
    public function brandslist()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select(
                [
                    $db->quoteName('id', 'value'),
                    $db->quoteName('title', 'text'),
                ]
            )
            ->from($db->quoteName('#__tswrent_brands'))
            ->order($db->quoteName('title'));

        // Get the options.
        $db->setQuery($query);

        try {
            $options = $db->loadObjectList();
        } catch (\RuntimeException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

        return $options;
    }

}
