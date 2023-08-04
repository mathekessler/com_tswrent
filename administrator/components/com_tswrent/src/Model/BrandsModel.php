<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Model;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Methods supporting a list of foo records.
 *
 * @since  __BUMP_VERSION__
 * 
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
	public function __construct($config = array(), MVCFactoryInterface $factory = null)
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
                'website', 'a.website',
				'published', 'a.published',
			);
		}

		parent::__construct($config);
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

		return parent::getStoreId($id);
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
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
						'a.id, a.title,'.
						'a.published, a.website'
					)
				)
			

		->from($db->quoteName('#__tswrent_brands', 'a'));

		// Filter by published state
		$published = (string) $this->getState('filter.published');

		if (is_numeric($published)) {
			$query->where($db->quoteName('a.published') . ' = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(' . $db->quoteName('a.published') . ' = 0 OR ' . $db->quoteName('a.published') . ' = 1)');
		}

		// Filter by supplier
		$supplier = (string) $this->getState('filter.supplier_id');

		if (is_numeric($supplier)) {

			$subQuery = $db->getQuery(true);
			$subQuery->select('s.brand_id')
			->from($db->quoteName('#__tswrent_brand_supplier_relation','s'))
			->where($db->quoteName('supplier_id') . ' = ' . (int)$supplier);
			
			$query->where($db->quoteName('a.id') . ' IN (' . $subQuery . ')');
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int) substr($search, 3));
			} else {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where(
					'(' . $db->quoteName('a.title') . ' LIKE ' . $search . ')'
				);
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

        // Getting the following metric by joins is WAY TOO SLOW.
        // Faster to do three queries for very large banner trees.

        // Get the brands in the list.
        $db        = $this->getDatabase();
        $brandIds = array_column($items, 'id');

        $query = $db->getQuery(true)
            ->select(
                [
                    $db->quoteName('brand_id'),
                    'COUNT(' . $db->quoteName('brand_id') . ') AS ' . $db->quoteName('count_published'),
                ]
            )
            ->from($db->quoteName('#__tswrent_products'))
            ->where($db->quoteName('published') . ' = :published')
            ->whereIn($db->quoteName('brand_id'), $brandIds)
            ->group($db->quoteName('brand_id'))
            ->bind(':published', $published, ParameterType::INTEGER); 

        $db->setQuery($query);

        // Get the published banners count.
        try {
            $published          = 1;
            $countPublished = $db->loadAssocList('brand_id', 'count_published');
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());

            return false;
        }

        // Get the unpublished banners count.
        try {
            $published            = 0;
            $countUnpublished = $db->loadAssocList('supplier_id', 'count_published');
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());

            return false;
        }

        // Get the trashed banners count.
        try {
            $published        = -2;
            $countTrashed = $db->loadAssocList('supplier_id', 'count_published');
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());

            return false;
        }

        // Get the archived banners count.
        try {
            $published         = 2;
            $countArchived = $db->loadAssocList('supplier_id', 'count_published');
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());

            return false;
        }

        // Inject the values back into the array.
        foreach ($items as $item) {
            $item->count_published   = isset($countPublished[$item->id]) ? $countPublished[$item->id] : 0;
            $item->count_unpublished = isset($countUnpublished[$item->id]) ? $countUnpublished[$item->id] : 0;
            $item->count_trashed     = isset($countTrashed[$item->id]) ? $countTrashed[$item->id] : 0;
            $item->count_archived    = isset($countArchived[$item->id]) ? $countArchived[$item->id] : 0;
        }

        // Add the items to the internal cache.
        $this->cache[$store] = $items;

        return $this->cache[$store];
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
		// List state information.
		parent::populateState($ordering, $direction);

	}
}
