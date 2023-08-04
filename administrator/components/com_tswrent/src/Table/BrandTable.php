<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Table;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Brands Table class.
 *
 * @since  __BUMP_VERSION__
 * 
 */
class BrandTable extends Table implements VersionableTableInterface
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
		$this->typeAlias = 'com_tswrent.brand';
		parent::__construct('#__tswrent_brands', 'id', $db);
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

        // Set created date if not set.
        if (!(int) $this->created) {
            $this->created = Factory::getDate()->toSql();
        }

      // Set modified to created if not set
	  if (!$this->modified) {
		$this->modified = $this->created;
		}

		// Set modified_by to created_by if not set
		if (empty($this->modified_by)) {
			$this->modified_by = $this->created_by;
		}	

		return true;
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
        return parent::store($updateNulls);
	}

	/**
     * Get the type alias for the history table
     *
     * @return  string  The alias as described above
     *
     * @since   __BUMP_VERSION__
     * 
     */
    public function getTypeAlias()
    {
        return $this->typeAlias;
    }
}
