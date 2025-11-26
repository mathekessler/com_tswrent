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
 * Suppliers model.
 *
 * @since  __BUMP_VERSION__
 * 
 */
class SuppliersModel extends ListModel
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
				'title', 'a.title',
				'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
				'created', 'a.created',                
				'state', 'a.state',
                'telephone', 'a.telephone',
                'mobile', 'a.mobile',
                'email_to', 'a.email_to',
                'webpage', 'a.webpage',

            ];
		}

		parent::__construct($config);
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
		$id .= ':' . $this->getState('filter.supplier_id');


		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \JDatabaseQuery
	 *
	 * @since   __BUMP_VERSION__
     * 
	 */
	protected function getListQuery()
	{
		// Create a new query object.
        $db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
                [
					$db->quoteName('a.id'),
                    $db->quoteName('a.title'),
                    $db->quoteName('a.state'),
                    $db->quoteName('a.telephone'),
                    $db->quoteName('a.mobile'),
                    $db->quoteName('a.webpage'),
                    $db->quoteName('a.email_to')
                ]		
			)					
        )
            ->select(
                [ 
                    $db->quoteName('uc.name', 'editor'),
                ]			
		)       

		->from($db->quoteName('#__tswrent_suppliers', 'a'))
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

		// Filter by brand.
		$brandId = $this->getState('filter.brand_id');
        
        if (is_numeric($brandId)) 
        {   
            $brandId = (int) $brandId;
            //$brandId =      TswrentHelper::getInputSupplierBrandRelation($brandId,'supplier');
            //$query->select(['b.*']);
            $query->join('INNER',$db->quoteName('#__tswrent_brand_supplier_relation','b').' ON ' . $db->quoteName('b.brand_id') . ' = :brandId')
            ->where($db->quoteName('b.supplier_id').'='.$db->quoteName('a.id'))
            ->bind(':brandId', $brandId, ParameterType::INTEGER);

		}
        
		// Filter by search in title
		if ($search = $this->getState('filter.search')) {
			if (stripos($search, 'id:') === 0) {
				$searchId = (int) substr($search, 3);
				$query->where($db->quoteName('a.id') . ' = :search_id')
					  ->bind(':search_id', $searchId, ParameterType::INTEGER);
			} else {
				$searchTerm = '%' . str_replace(' ', '%', trim($search)) . '%';
				$query->where('(' . $db->quoteName('a.title') . ' LIKE :search_title OR ' . $db->quoteName('a.alias') . ' LIKE :search_alias)')
					  ->bind(':search_title', $searchTerm, ParameterType::STRING)
					  ->bind(':search_alias', $searchTerm, ParameterType::STRING);
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

        // Get the brands in the list.
        $db        = $this->getDatabase();
        $supplierIds = array_column($items, 'id');

        $query = $db->getQuery(true)
            ->select(
                [
                    $db->quoteName('a.id'),
                    $db->quoteName('a.title'),
                    $db->quoteName('a.state'),
                    $db->quoteName('b.brand_id'),
                    $db->quoteName('b.supplier_id'),
                    'COUNT(' . $db->quoteName('a.id') . ') AS ' . $db->quoteName('count_published'),
                ]
            )
            ->from($db->quoteName('#__tswrent_brands','a'))
            ->where($db->quoteName('a.state') . ' = :published')
            ->join('INNER',$db->quoteName('#__tswrent_brand_supplier_relation','b').' ON ' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.brand_id'))
            ->whereIn($db->quoteName('b.supplier_id'), $supplierIds)
            ->group($db->quoteName('b.supplier_id'))
            ->bind(':published', $published, ParameterType::INTEGER);

        $db->setQuery($query);

        // Get the published banners count.
        try {
            $published          = 1;
            $countPublished = $db->loadAssocList('supplier_id', 'count_published');
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

}

