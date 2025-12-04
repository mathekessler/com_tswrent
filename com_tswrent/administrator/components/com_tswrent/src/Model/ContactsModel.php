<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;


// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Methods supporting a list of foo records.
 *
 * @since  __BUMP_VERSION__
 */
class ContactsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JControllerLegacy
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function __construct($config = [])
	{

		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id', 'a.id',
				'title', 'a.title',
				'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
				'created', 'a.created',
				'state', 'a.state',
			];
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \JDatabaseQuery
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db= $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
        $query->select(
			$this->getState(
				'list.select',
				[
					$db->quoteName(	'a.id'),
					$db->quoteName(	'a.title'),
					$db->quoteName(	'a.state'),
					$db->quoteName(	'a.checked_out'),
					$db->quoteName(	'a.checked_out_time'),
				]					
           	)
		)
            ->select(
                [ 
                    $db->quoteName('uc.name', 'editor'),
                ]			
		)

		->from($db->quoteName('#__tswrent_contacts', 'a'))
		->join('LEFT', $db->quoteName('#__users', 'uc'), $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.checked_out'));
		
		// Filter by published state
        $published = (string) $this->getState('filter.published');

        if (is_numeric($published)) {
            $published = (int) $published;
            $query->where($db->quoteName('a.state') . ' = :published')
                ->bind(':published', $published, ParameterType::INTEGER);
        } elseif ($published === '') {
            $query->whereIn($db->quoteName('a.state'), [0, 1]);
        }

		// Filter by search in title
        if ($search = $this->getState('filter.search')) {
            if (stripos($search, 'id:') === 0) {
                $search = (int) substr($search, 3);
                $query->where($db->quoteName('a.id') . ' = :search')
                    ->bind(':search', $search, ParameterType::INTEGER);
            } else {
                $search = '%' . str_replace(' ', '%', trim($search)) . '%';
                $query->where('(' . $db->quoteName('a.title') . ' LIKE :search1 OR ' . $db->quoteName('a.alias') . ' LIKE :search2)')
                    ->bind([':search1', ':search2'], $search);
            }
        }


		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.title');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Overrides getItems to attach supplier counts (by product's brand) and
	 * to provide a request-local cache and guarded queries.
	 *
	 * @return array
	 */
	public function getItems()
	{
		// Ensure local cache container exists
		if (!isset($this->cache) || !is_array($this->cache)) {
			$this->cache = [];
		}

		$store = $this->getStoreId('getItems');

		// Return cached if available
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the base items
		$items = parent::getItems();

		if (empty($items)) {
			return [];
		}

		// Collect contact IDs
		$contactIds = array_column($items, 'id');
		sort($contactIds);
		
		if (!empty($contactIds)) {
			$countSuppliers = [];
			$countCustomers = [];

			$countSuppliers = $this->countSupplier($contactIds) ?: [];
			$countCustomers = $this->countCustomer($contactIds) ?: [];

		}

		// Inject counts back into products
		foreach ($items as $item) {
			if (isset($item->id)) {
				$item->count_suppliers = isset($countSuppliers[$item->id]) ? $countSuppliers[$item->id] : 0;
				$item->count_customers = isset($countCustomers[$item->id]) ? $countCustomers[$item->id] : 0;
			} else {
				$item->count_suppliers = 0;
				$item->count_customers = 0;
			}
		}

		// Cache and return
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}


	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.customer_id');
		$id .= ':' . $this->getState('filter.supplier_id');


		return parent::getStoreId($id);
	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
    protected function populateState($ordering = 'a.title', $direction = 'asc')
    {
		// Load the parameters.
        $this->setState('params', ComponentHelper::getParams('com_tswrent'));

        // List state information.
        parent::populateState($ordering, $direction);
    }

	protected function countSupplier($contactIds)
	{
		$db        = $this->getDatabase();
		$query = $db->getQuery(true)
			->select([
				$db->quoteName('contact_id'),
				'COUNT(' . $db->quoteName('contact_id') . ') AS ' . $db->quoteName('count_suppliers'),
			])
			->from($db->quoteName('#__tswrent_contact_relation'))
			->whereIn($db->quoteName('contact_id'), $contactIds)
			->where($db->quoteName('supplier_id') . ' > 0')
			->group($db->quoteName('contact_id'));

		$db->setQuery($query);		
		try {
			return $db->loadAssocList('contact_id', 'count_suppliers');
		} catch (\RuntimeException $e) {
			$this->setError($e->getMessage());
			return false;
		}

	}
	protected function countCustomer($contactIds)
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select(
				[
					$db->quoteName('contact_id'),
					'COUNT(' . $db->quoteName('customer_id') . ') AS ' . $db->quoteName('count_customers'),
				]
			)
			->from($db->quoteName('#__tswrent_contact_relation'))
			->whereIn($db->quoteName('contact_id'), $contactIds)
			->where($db->quoteName('customer_id') . ' > 0')
			->group($db->quoteName('contact_id'));

		$db->setQuery($query);

		try {
			return $db->loadAssocList('contact_id', 'count_customers');
		} catch (\RuntimeException $e) {
			$this->setError($e->getMessage());
			return false;
		}

	}
}
