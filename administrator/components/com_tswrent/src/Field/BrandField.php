<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Field;

use Joomla\CMS\Form\Field\ListField;
use TSWEB\Component\Tswrent\Administrator\Helper\TswrentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Bannerclient field.
 *
 * @since  1.6
 */
class BrandField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.6
     */
    protected $type = 'Brand';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   1.6
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), TswrentHelper::getBrandOptions());
    }
}
