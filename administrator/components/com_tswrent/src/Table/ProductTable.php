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

        // Set created date if not set.
        if (!(int) $this->created) {
            $this->created = Factory::getDate()->toSql();
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


    /**
     * Method to store a row
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success, false on failure.
     * 
     * @since   __BUMP_VERSION__
     * 
     */
    public function store($updateNulls = true)
    {
        $date = Factory::getDate()->toSql();
		$user = Factory::getApplication()->getIdentity();

		$this->modified = $date;

		if ($this->id)
		{
			// Existing item
			$this->modified_by = $user->id;
			$this->modified    = $date;
		}
		else
		{
			// New weblink. A weblink created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created)
			{
				$this->created = $date;
			}

			if (empty($this->created_by))
			{
				$this->created_by = $user->id;
			}

			if (!(int) $this->modified)
			{
				$this->modified = $date;
			}

			if (empty($this->modified_by))
			{
				$this->modified_by = $user->id;
			}
            // Verify that the alias is unique
            $table = new ProductTable($this->getDbo());

            if ($table->load(array('alias' => $this->alias))
                && ($table->id != $this->id || $this->id == 0))
            {
                $this->setError(Text::_('COM_TSWRENT_ERROR_UNIQUE_ALIAS'));

                return false;
            }

        }
        
        return parent::store($updateNulls);
    }
}
