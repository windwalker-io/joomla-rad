<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;
use Windwalker\Compare\InCompare;
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
	protected function populateState($ordering = null, $direction = null)
	{
		$params = $this->getParams();
		$user   = $this->container->get('user');
		$input  = $this->container->get('input');
		$app    = $this->container->get('app');

		// Order
		// =====================================================================================
		$orderCol = $params->get('orderby', 'a.ordering');
		$this->state->set('list.ordering', $orderCol);

		// Order Dir
		// =====================================================================================
		$listOrder = $params->get('order_dir', 'asc');
		$this->state->set('list.direction', $listOrder);

		// Limitstart
		// =====================================================================================
		$this->state->set('list.start', $input->getInt('limitstart', 0));

		// Max Level
		// =====================================================================================
		$maxLevel = $params->get('maxLevel');

		if ($maxLevel)
		{
			$this->state->set('filter.max_category_levels', $maxLevel);
		}

		// Edit Access
		// =====================================================================================
		if (($user->authorise('core.edit.state', '{{extension.element.lower}}')) || ($user->authorise('core.edit', '{{extension.element.lower}}')))
		{
			// Filter on published for those who do not have edit or edit.state rights.
			$this->state->set('filter.unpublished', 1);
		}

		// View Level
		// =====================================================================================
		if (!$params->get('show_noauth'))
		{
			$this->state->set('filter.access', true);
		}
		else
		{
			$this->state->set('filter.access', false);
		}

		// Language
		// =====================================================================================
		$this->state->set('filter.language', $app->getLanguageFilter());

		parent::populateState($ordering, 'ASC');
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
		$user = $this->container->get('user');
		$db   = $this->container->get('db');
		$date = $this->container->get('date');

		// If no state filter, set published >= 0
		if (!isset($filters['{{controller.item.name.lower}}.state']) && property_exists($this->getTable(), 'state'))
		{
			$query->where($query->quoteName('{{controller.item.name.lower}}.state') . ' >= 0');
		}

		// Category
		// =====================================================================================
		$category = $this->getCategory();

		if ($category->id != 1 && in_array('category.lft', $this->filterFields) && in_array('category.rgt', $this->filterFields))
		{
			$query->where($query->format('(%n >= %a AND %n <= %a)', 'category.lft', $category->lft, 'category.rgt', $category->rgt));
		}

		// Max Level
		// =====================================================================================
		$maxLevel = $this->state->get('filter.max_category_levels', -1);

		if ($maxLevel > 0)
		{
			$query->where($query->quoteName('category.level') . " <= " . $maxLevel);
		}

		// Edit Access
		// =====================================================================================
		if ($this->state->get('filter.unpublished'))
		{
			$query->where('{{controller.item.name.lower}}.state >= 0');
		}
		else
		{
			$query->where('{{controller.item.name.lower}}.state > 0');

			$nullDate = $query->Quote($db->getNullDate());
			$nowDate  = $query->Quote($date->toSQL(true));

			if (in_array('{{controller.item.name.lower}}.publish_up', $this->filterFields) && in_array('{{controller.item.name.lower}}.publish_down', $this->filterFields))
			{
				$query->where('({{controller.item.name.lower}}.publish_up = ' . $nullDate . ' OR {{controller.item.name.lower}}.publish_up <= ' . $nowDate . ')');
				$query->where('({{controller.item.name.lower}}.publish_down = ' . $nullDate . ' OR {{controller.item.name.lower}}.publish_down >= ' . $nowDate . ')');
			}
		}

		// View Level
		// =====================================================================================
		if ($access = $this->state->get('filter.access') && in_array('{{controller.item.name.lower}}.access', $this->filterFields))
		{
			$query->where(new InCompare('{{controller.item.name.lower}}.access', $user->getAuthorisedViewLevels()));
		}

		// Language
		// =====================================================================================
		if ($this->state->get('filter.language') && in_array('a.language', $this->filterFields))
		{
			$lang_code = $db->quote(JFactory::getLanguage()->getTag());
			$query->where("a.language IN ('{$lang_code}', '*')");
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
