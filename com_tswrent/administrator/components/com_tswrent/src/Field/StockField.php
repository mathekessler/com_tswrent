<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Field;

use Joomla\CMS\Form\Field\NumberField;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * TswrentBrand field.
 *
 * @since __BUMP_VERSION__
 * 
 */
class StockField extends NumberField
{
    /**
     * The form field type.
     *
     * @var    string
     * 
     * @since  __BUMP_VERSION__
     */
    protected $type = 'Stock';

    /**
     * The allowable maximum value of the field.
     *
     * @var    float
     * @since  4.1.0
     */
    protected $max;

    /**
     * The allowable minimum value of the field.
     *
     * @var    float
     * @since  4.1.0
     */
    protected $min;

    /**
     * The step by which value of the field increased or decreased.
     *
     * @var    float
     * @since  4.1.0
     */
    protected $step = 1;

	/**
     * Override the parent method to set deal with subtypes.
     *
     * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form
     *                                       field object.
     * @param   mixed              $value    The form field value to validate.
     * @param   string             $group    The field name group control value. This acts as an array container for
     *                                       the field. For example if the field has `name="foo"` and the group value is
     *                                       set to "bar" then the full field name would end up being "bar[foo]".
     *
     * @return  boolean  True on success.
     *
     * @since   4.1.0
     */
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if (!$return) {
            return false;
        }



        return true;
    }

}
