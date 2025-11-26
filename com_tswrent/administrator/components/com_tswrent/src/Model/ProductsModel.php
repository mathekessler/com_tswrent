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
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Products model
 * 
 * @since  __BUMP_VERSION__
 * 
 **/
class ProductsModel extends ListModel
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
	public function __construct($config = [], ?MVCFactoryInterface $factory = null)
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id', 'a.id',	
				'alias', 'a.alias',
				'title', 'a.title',
				'state', 'a.state',
				'catid', 'a.catid', 'category_title',
				'brand', 'a.brand', 
				'stock', 'a.stock',
				'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'created', 'a.created',
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
	 * 
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
					$db->quoteName(	'a.id'),
					$db->quoteName(	'a.title'),
					$db->quoteName(	'a.alias'),
					$db->quoteName(	'a.catid'),
					$db->quoteName(	'a.state'),
					$db->quoteName(	'a.brand_id'),
					$db->quoteName(	'a.price'),
				]					
           	)
		)
		->select(
			[
				$db->quoteName('b.title', 'brand_title'),
				$db->quoteName('c.title', 'category_title'),
				
			]
		)

		->from($db->quoteName('#__tswrent_products', 'a'))
		->join('LEFT', $db->quoteName('#__tswrent_brands', 'b'), $db->quoteName('b.id') . ' = ' . $db->quoteName('a.brand_id'))
		->join(
			'LEFT', 
			$db->quoteName('#__categories', 'c'),
			$db->quoteName('c.id') . '=' . $db->quoteName('a.catid')
			.'OR'.$db->quoteName('c.parent_id'). '=' . $db->quoteName('a.catid'));

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

		// Filter by published state
        $published = (string) $this->getState('filter.published');

        if (is_numeric($published)) {
            $published = (int) $published;
            $query->where($db->quoteName('a.state') . ' = :published')
                ->bind(':published', $published, ParameterType::INTEGER);
        } elseif ($published === '') {
            $query->whereIn($db->quoteName('a.state'), [0, 1]);
        }

		// Filter by category.
		$categoryId = $this->getState('filter.category_id');

		if (is_numeric($categoryId)) {
			$categoryId = (int) $categoryId;
			$query->where($db->quoteName('c.id') . '= :categoryId OR'.$db->quoteName('c.parent_id') . '= :cparentId')
				->bind([':categoryId',':cparentId'], $categoryId, ParameterType::INTEGER);

		}
		// Filter by brand.
		$brandId = $this->getState('filter.brand_id');

		if (is_numeric($brandId)) {
			$brandId = (int) $brandId;
			$query->where($db->quoteName('a.brand_id') . ' = :brandId')
				->bind(':brandId', $brandId, ParameterType::INTEGER);
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.title');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
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
        // List state information.
        parent::populateState($ordering, $direction);
    }

}

