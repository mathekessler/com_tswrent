<?php

/**
 * @package     STWRent
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Service\HTML;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseAwareTrait;


defined('_JEXEC') or die;


/**
 * TSWRent HTML class.
 *

 */
class Tswrent
{
    use DatabaseAwareTrait;
	    /**
     * Display a batch widget for the brand selector.
     *
     * @return  string  The necessary HTML for the widget.
     *
     * @since   2.5
     */
    public function brand()
    {
        
        // Create the batch selector to change the brand on a selection list.
        return implode(
            "\n",
            [
                '<label id="batch-brand-lbl" for="batch-brand_id">',
                Text::_('COM_TSWRENT_BATCH_BRAND_LABEL'),
                '</label>',
                '<select class="form-select" name="batch[brand_id]" id="batch-brand_id">',
                '<option value="">' . Text::_('COM_TSWRENT_BATCH_BRAND_NOCHANGE') . '</option>',
                '<option value="0">' . Text::_('COM_TSWRENT_NO_BRAND') . '</option>',
                HTMLHelper::_('select.options', static::brandlist(), 'value', 'text'),
                '</select>',
            ]
        );
    }

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   1.6
     */
    public function brandlist()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select(
                [
                    $db->quoteName('id', 'value'),
                    $db->quoteName('title', 'text'),
                ]
            )
            ->from($db->quoteName('#__tswrent_brand'))
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
