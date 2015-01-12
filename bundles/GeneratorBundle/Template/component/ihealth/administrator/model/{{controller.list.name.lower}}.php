<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\DI\Container;
use Windwalker\Model\Filter\FilterHelper;
use Windwalker\Model\ListModel;

// No direct access
defined('_JEXEC') or die;

/**
 * {{extension.name.cap}} {{controller.list.name.cap}} model
 *
 * @since 1.0
 */
class {{extension.name.cap}}Model{{controller.list.name.cap}} extends ListModel
{
	/**
	 * Component prefix.
	 *
	 * @var  string
	 */
	protected $prefix = '{{extension.name.lower}}';

	/**
	 * The URL option for the component.
	 *
	 * @var  string
	 */
	protected $option = '{{extension.element.lower}}';

	/**
	 * The prefix to use with messages.
	 *
	 * @var  string
	 */
	protected $textPrefix = '{{extension.element.upper}}';

	/**
	 * The model (base) name
	 *
	 * @var  string
	 */
	protected $name = '{{controller.list.name.lower}}';

	/**
	 * Item name.
	 *
	 * @var  string
	 */
	protected $viewItem = '{{controller.item.name.lower}}';

	/**
	 * List name.
	 *
	 * @var  string
	 */
	protected $viewList = '{{controller.list.name.lower}}';

	/**
	 * Configure tables through QueryHelper.
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.{{controller.list.name.lower}}.helper.query', Container::FORCE_NEW);

		$queryHelper->addTable('{{controller.item.name.lower}}', '#__{{extension.name.lower}}_{{controller.list.name.lower}}')
			->addTable('category',  '#__categories', '{{controller.item.name.lower}}.catid      = category.id')
			->addTable('user',      '#__users',      '{{controller.item.name.lower}}.created_by = user.id')
			->addTable('viewlevel', '#__viewlevels', '{{controller.item.name.lower}}.access     = viewlevel.id')
			->addTable('lang',      '#__languages',  '{{controller.item.name.lower}}.language   = lang.lang_code');

		$this->filterFields = array_merge($this->filterFields, $queryHelper->getFilterFields());
	}

	/**
	 * The post getQuery object.
	 *
	 * @param JDatabaseQuery $query The db query object.
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method will only called in constructor. Using `ignore_request` to ignore this method.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = 'ASC')
	{
		// Build ordering prefix
		if (!$ordering)
		{
			$table = $this->getTable('{{controller.item.name.cap}}');

			$ordering = property_exists($table, 'ordering') ? '{{controller.item.name.lower}}.ordering' : '{{controller.item.name.lower}}.id';

			$ordering = property_exists($table, 'catid') ? '{{controller.item.name.lower}}.catid, ' . $ordering : $ordering;
		}

		parent::populateState($ordering, $direction);
	}

	/**
	 * Process the query filters.
	 *
	 * @param JDatabaseQuery $query   The query object.
	 * @param array          $filters The filters values.
	 *
	 * @return  JDatabaseQuery The db query object.
	 */
	protected function processFilters(\JDatabaseQuery $query, $filters = array())
	{
		// If no state filter, set published >= 0
		if (!isset($filters['{{controller.item.name.lower}}.state']) && property_exists($this->getTable(), 'state'))
		{
			$query->where($query->quoteName('{{controller.item.name.lower}}.state') . ' >= 0');
		}

		return parent::processFilters($query, $filters);
	}

	/**
	 * Configure the filter handlers.
	 *
	 * Example:
	 * ``` php
	 * $filterHelper->setHandler(
	 *     '{{controller.item.name.lower}}.date',
	 *     function($query, $field, $value)
	 *     {
	 *         $query->where($field . ' >= ' . $value);
	 *     }
	 * );
	 * ```
	 *
	 * @param FilterHelper $filterHelper The filter helper object.
	 *
	 * @return  void
	 */
	protected function configureFilters($filterHelper)
	{
	}

	/**
	 * Configure the search handlers.
	 *
	 * Example:
	 * ``` php
	 * $searchHelper->setHandler(
	 *     '{{controller.item.name.lower}}.title',
	 *     function($query, $field, $value)
	 *     {
	 *         return $query->quoteName($field) . ' LIKE ' . $query->quote('%' . $value . '%');
	 *     }
	 * );
	 * ```
	 *
	 * @param SearchHelper $searchHelper The search helper object.
	 *
	 * @return  void
	 */
	protected function configureSearches($searchHelper)
	{
	}
}
