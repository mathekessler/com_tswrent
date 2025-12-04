<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use TSWEB\Component\Tswrent\Administrator\Helper\TswrentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Methods supporting a list of foo records.
 *
 * @since  __BUMP_VERSION__
 */
class BrandsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JControllerLegacy
	 *
	 * @since   __BUMP_VERSION__
	 * 
	 */
	public function __construct($config = [])
    {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id', 'a.id',
				'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
				'title', 'a.title',
                'webpage', 'a.webpage',
				'brand_id', 'a.brand_id',
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
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				[
					$db->quoteName('a.id'),
                    $db->quoteName('a.checked_out'),
                    $db->quoteName('a.checked_out_time'),					
					$db->quoteName('a.title'),
					$db->quoteName('a.alias'),
					$db->quoteName('a.state'),
					$db->quoteName('a.webpage'),

				]
			)
		)
			->select(
				[
					$db->quoteName('s.supplier_id','supplier_id'),
					$db->quoteName('uc.name', 'editor'),
				]
			)
		->from($db->quoteName('#__tswrent_brands', 'a'))
		->join('LEFT', $db->quoteName('#__tswrent_brand_supplier_relation', 's'), $db->quoteName('a.id') . ' = ' . $db->quoteName('s.brand_id'))	
		->join('LEFT', $db->quoteName('#__users', 'uc'), $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.checked_out'))
		->group(
			[
				$db->quoteName('a.id'),
				$db->quoteName('a.checked_out'),
				$db->quoteName('a.checked_out_time'),
				$db->quoteName('a.title'),
				$db->quoteName('a.alias'),
				$db->quoteName('a.state'),
				$db->quoteName('a.webpage'),
				$db->quoteName('uc.name'),
			]
		);
		
		// Filter by published state
        $published = (string) $this->getState('filter.published');

        if (is_numeric($published)) {
            $published = (int) $published;
            $query->where($db->quoteName('a.state') . ' = :published')
                ->bind(':published', $published, ParameterType::INTEGER);
        } elseif ($published === '') {
            $query->whereIn($db->quoteName('a.state'), [0, 1]);
        }


		// Filter by supplier
		$supplierId = $this->getState('filter.supplier_id');

		if (is_numeric($supplierId)) {
			$supplierId = (int) $supplierId;
			$query->where($db->quoteName('supplier_id') . ' = :supplierId')
				->bind(':supplierId', $supplierId, ParameterType::INTEGER);
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
     * Overrides the getItems method to attach additional metrics to the list.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   __BUMP_VERSION__
     * 
     */
    public function getItems()
    {
        // Ensure local cache container exists
        if (!isset($this->cache) || !is_array($this->cache)) {
            $this->cache = [];
        }

        // Get a storage key.
        $store = $this->getStoreId('getItems');

        // Try to load the data from internal storage.
        if (!empty($this->cache[$store])) {
            return $this->cache[$store];
        }

        // Load the list items.
        $items = parent::getItems();

        // If empty or an error, just return.
        if (empty($items)) {
            return [];
        }

        // Get the clients in the list.
        $brandIds = array_column($items, 'id');

        // only run counts if we have brand IDs
        if (!empty($brandIds)) {
			$countProducts = [];
			$countSuppliers = [];
            
			$countProducts = $this->countProducts($brandIds) ?: [];
			$countSuppliers = $this->countSupplier($brandIds) ?: [];
        }

        // Inject the values back into the array.
        foreach ($items as $item) {
            $item->count_products    = isset($countProducts[$item->id]) ? $countProducts[$item->id] : 0;
            $item->count_suppliers =   isset($countSuppliers[$item->id]) ? $countSuppliers[$item->id] : 0;
			
		}

        // Add the items to the internal cache.
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
		$id .= ':' . $this->getState('filter.brand_id');
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
	 * 
	 */
	protected function populateState($ordering = 'a.title', $direction = 'asc')
	{	
		// Load the parameters.
        $this->setState('params', ComponentHelper::getParams('com_tswrent'));

		// List state information.
		parent::populateState($ordering, $direction);

	}

	protected function countSupplier($brandIds)
	{
		$db        = $this->getDatabase();
		$query = $db->getQuery(true)
		->select(
			[
				$db->quoteName('brand_id'),
				'COUNT(' . $db->quoteName('brand_id') . ') AS ' . $db->quoteName('count_suppliers'),
			]
		)
		->from($db->quoteName('#__tswrent_brand_supplier_relation'))
		->whereIn($db->quoteName('brand_id'), $brandIds)
		->group($db->quoteName('brand_id'));

		$db->setQuery($query);

		// Get the published banners count.
		try {
			$state          = 1;
			$countPublished = $db->loadAssocList('brand_id', 'count_suppliers');
		} catch (\RuntimeException $e) {
			$this->setError($e->getMessage());

			return false;
		}

		return ($countPublished);
	}

	protected function countProducts($brandIds)
	{
		$db        = $this->getDatabase();
		$query = $db->getQuery(true)
		->select(
			[
				$db->quoteName('a.brand_id'),
				$db->quoteName('a.state'),
				'COUNT(' . $db->quoteName('a.brand_id') . ') AS ' . $db->quoteName('count_products'),
			]
		)
		->from($db->quoteName('#__tswrent_products', 'a'))
		->whereIn($db->quoteName('a.brand_id'), $brandIds)
		->where($db->quoteName('a.state') . ' = 1')
		->group($db->quoteName('a.brand_id'));

		$db->setQuery($query);

		// Get the published banners count.
		try {
			$state          = 1;
			$countProducts = $db->loadAssocList('brand_id', 'count_products');
			return ($countProducts);
		} catch (\RuntimeException $e) {
			$this->setError($e->getMessage());

			return false;
		}
	}	

}
