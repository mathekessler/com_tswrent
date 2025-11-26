<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Table;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Product table
 *
 * @since  __BUMP_VERSION__
 * 
 */
class ProductTable extends Table
{
    	/**
     * Indicates that columns fully support the NULL value in the database
     *
     * @var    boolean
     * @since  __BUMP_VERSION__
     * 
     */
    protected $_supportNullValue = true;
    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  Database connector object
     *
     * @since   __BUMP_VERSION__
     * 
     */
    public function __construct(DatabaseDriver $db)
    {
        $this->typeAlias = 'com_tswrent.product';

        $this->setColumnAlias('published', 'state');

        parent::__construct('#__tswrent_products', 'id', $db);

    }

    /**
     * Overloaded check function
     *
     * @return  boolean
     *
     * @see     Table::check
     * 
     * @since   __BUMP_VERSION__
     * 
     */
    public function check()
    {
        try {
            parent::check();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        // Set name
        $this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);

        // Set alias
        if (trim($this->alias) == '') {
            $this->alias = $this->title;
        }
		$this->alias = ApplicationHelper::stringURLSafe($this->alias);

        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
        }

        // Check for a valid category.
        if (!$this->catid = (int) $this->catid) {
            $this->setError(Text::_('JLIB_DATABASE_ERROR_CATEGORY_REQUIRED'));

            return false;
        }

        return true;
    }

 /**
     * Overloaded bind function
     *
     * @param   mixed  $array   An associative array or object to bind to the \JTable instance.
     * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
     *
     * @return  boolean  True on success
     *
     * @since   __BUMP_VERSION__
     * 
     */
    public function bind($array, $ignore = [])
    {

        if (isset($array['productimage']) && is_array($array['productimage']))
        {
        // Convert the imageinfo array to a string.
        $parameter = new Registry;
        $parameter->loadArray($array['productimage']);
        $array['productimage'] = (string)$parameter;
        }
        return parent::bind($array, $ignore);
    }

}
