<?php
/**
 * @version 4.0.0
 * @package JEM
 * @copyright (C) 2013-2021 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace TSWEB\Component\Tswrent\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;
use Joomla\Database\ParameterType;


/**
 * Model-Events
 **/
class ProductsModel extends ListModel
{

	
	/**
	 * Constructor.
	 */
	public function __construct($config = [])
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id', 'a.id',	
				'alias', 'a.alias',
				'title', 'a.title','name',
				'published', 'a.published',
				'catid', 'a.catid', 'category_id', 'category_title',
				'brand', 'a.brand', 'brand_id',
				'stock', 'a.stock',
			];
		}

		parent::__construct($config);
	}


	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return JDatabaseQuery
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
					$db->quoteName(	'a.published'),
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

		->from($db->quoteName('#__tswrent_product', 'a'))
		->join('LEFT', $db->quoteName('#__tswrent_brand', 'b'), $db->quoteName('b.id') . ' = ' . $db->quoteName('a.brand_id'))
		->join('LEFT', $db->quoteName('#__categories', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'));

		// Filter by search in title
		if ($search = $this->getState('filter.search')) {
			if (stripos($search, 'id:') === 0) {
				$search = (int) substr($search, 3);
				$query->where($db->quoteName('a.id') . ' = :search')
					->bind(':search', $search, ParameterType::INTEGER);
			} else {
				$search = '%' . str_replace(' ', '%', trim($search)) . '%';
				$query->where('(' . $db->quoteName('a.name') . ' LIKE :search1 OR ' . $db->quoteName('a.alias') . ' LIKE :search2)')
					->bind([':search1', ':search2'], $search);
			}
		}

		// Filter by published state
		$published = (string) $this->getState('filter.published');

		if (is_numeric($published)) {
			$query->where($db->quoteName('a.published') . ' = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(' . $db->quoteName('a.published') . ' = 0 OR ' . $db->quoteName('a.published') . ' = 1)');
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id');

		if (is_numeric($categoryId)) {
			$categoryId = (int) $categoryId;
			$query->where($db->quoteName('a.catid') . ' = :categoryId')
				->bind(':categoryId', $categoryId, ParameterType::INTEGER);
		}

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
    protected function populateState($ordering = 'a.name', $direction = 'asc')
    {
        // Load the parameters.
        $this->setState('params', ComponentHelper::getParams('com_tswrent'));

        // List state information.
        parent::populateState($ordering, $direction);
    }

}

