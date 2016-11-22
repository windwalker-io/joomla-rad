<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Model;

use Joomla\DI\Container as JoomlaContainer;
use Windwalker\String\StringInflector as Inflector;
use Windwalker\Helper\ArrayHelper;
use Windwalker\Helper\PathHelper;
use Windwalker\Helper\ProfilerHelper;
use Windwalker\Model\Filter\FilterHelper;
use Windwalker\Model\Filter\SearchHelper;
use Windwalker\Model\Helper\AdminListHelper;
use Windwalker\Model\Helper\QueryHelper;
use Windwalker\Model\Provider\GridProvider;
use Windwalker\String\StringHelper;

defined('_JEXEC') or die;

/**
 * Model class for handling lists of items.
 *
 * @since  2.0
 */
class ListModel extends AbstractFormModel
{
	/**
	 * Only allow this fields to set in query.
	 *
	 * Override this property at component layer.
	 *
	 * @var  array
	 *
	 * @since  2.1
	 */
	protected $allowFields = array();

	/**
	 * Set field aliases to make correct query columns.
	 *
	 * Override this property at component layer.
	 *
	 * @var  array
	 *
	 * @since  2.1
	 */
	protected $fieldMapping = array();

	/**
	 * Valid filter fields or ordering.
	 *
	 * Override this property at component layer.
	 *
	 * @var array
	 *
	 * @deprecated  Use $this->allowFields instead.
	 */
	protected $filterFields = array();

	/**
	 * An internal cache for the last query used.
	 *
	 * @var \JDatabaseQuery
	 */
	protected $query = array();

	/**
	 * Name of the filter form to load
	 *
	 * @var string
	 */
	protected $formPath = null;

	/**
	 * Cache of forms.
	 *
	 * @var \JForm[]
	 */
	protected $forms;

	/**
	 * Ordering field.
	 *
	 * @var string
	 */
	protected $orderCol = null;

	/**
	 * Search fields.
	 *
	 * @var array
	 */
	protected $searchFields = array();

	/**
	 * Property selectType.
	 *
	 * @var  integer
	 */
	protected $selectType = null;

	/**
	 * Property queryHelper.
	 *
	 * @var  QueryHelper
	 */
	protected $queryHelper;

	/**
	 * Property filterHelper.
	 *
	 * @var  FilterHelper
	 */
	protected $filterHelper;

	/**
	 * Property searchHelper.
	 *
	 * @var  SearchHelper
	 */
	protected $searchHelper;

	/**
	 * Constructor
	 *
	 * @param   array              $config    An array of configuration options (name, state, dbo, table_path, ignore_request).
	 * @param   JoomlaContainer    $container Service container.
	 * @param   \JRegistry         $state     The model state.
	 * @param   \JDatabaseDriver   $db        The database adapter.
	 */
	public function __construct($config = array(), JoomlaContainer $container = null, \JRegistry $state = null, \JDatabaseDriver $db = null)
	{
		// These need before parent constructor.
		$this->orderCol = $this->orderCol ? : ArrayHelper::getValue($config, 'order_column', null);

		// This block should be remove after 3.0, use allowFields instead
		if (!$this->filterFields)
		{
			$this->filterFields = ArrayHelper::getValue($config, 'filter_fields', array());

			$this->filterFields[] = '*';
		}

		if (!$this->allowFields)
		{
			$this->allowFields = ArrayHelper::getValue($config, 'allow_fields', array());

			$this->allowFields[] = '*';
		}

		$this->prefix = $this->getPrefix($config);
		$this->option = 'com_' . $this->prefix;

		// Guess name for container
		$this->name = $this->name ? : ArrayHelper::getValue($config, 'name', $this->getName());

		$this->container = $container ? : $this->getContainer();

		$this->container->registerServiceProvider(new GridProvider($this->name, $this));

		$this->configureTables();

		parent::__construct($config, $container, $state, $db);

		// Guess the item view as the context.
		$this->viewList = $this->viewList ? : ArrayHelper::getValue($config, 'view_list', $this->getName());

		// Guess the list view as the plural of the item view.
		$this->viewItem = $this->viewItem ? : ArrayHelper::getValue($config, 'view_item');

		if (empty($this->viewItem))
		{
			$inflector = Inflector::getInstance();

			$this->viewItem = $inflector->toSingular($this->viewList);
		}
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  \JTable  A JTable object
	 *
	 * @throws  \Exception
	 */
	public function getTable($name = '', $prefix = '', $options = array())
	{
		$name = $name ? : $this->viewItem;

		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is constructed only once for a given state of the model.
	 *
	 * @return  \JDatabaseQuery  A JDatabaseQuery object
	 */
	protected function _getListQuery()
	{
		// Capture the last store id used.
		static $lastStoreId;

		// Compute the current store id.
		$currentStoreId = $this->getStoreId('getItems');

		// If the last store id is different from the current, refresh the query.
		if ($lastStoreId != $currentStoreId || empty($this->query))
		{
			$lastStoreId = $currentStoreId;
			$this->query = $this->getListQuery();
		}

		return $this->query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if ($this->hasCache(__FUNCTION__))
		{
			return $this->getCache(__FUNCTION__);
		}

		// Load the list items.
		$query = $this->_getListQuery();

		$items = $this->getList($query, $this->getStart(), $this->state->get('list.limit'));

		// Add the items to the internal cache.
		return $this->setCache(__FUNCTION__, $items);
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  \JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 */
	protected function getListQuery()
	{
		$query       = $this->db->getQuery(true);
		$queryHelper = $this->getQueryHelper();

		// Prepare
		$this->prepareGetQuery($query);

		// Build filter query
		$this->processFilters($query, ArrayHelper::flatten((array) $this->get('filter')));

		// Build search query
		$this->processSearches($query, ArrayHelper::flatten((array) $this->get('search')));

		// Ordering
		$this->processOrdering($query);

		// Custom Where
		foreach ((array) $this->state->get('query.where', array()) as $k => $v)
		{
			$query->where($v);
		}

		// Custom Having
		foreach ((array) $this->state->get('query.having', array()) as $k => $v)
		{
			$query->having($v);
		}

		// Build query
		// ========================================================================

		// Get select columns
		$select = $this->state->get('query.select');

		if (!$select)
		{
			$select = $queryHelper->getSelectFields();
		}

		$query->select($select);

		// Build Selected tables query
		$queryHelper->registerQueryTables($query);

		$this->postGetQuery($query);

		// Debug
		if (JDEBUG)
		{
			ProfilerHelper::mark(QueryHelper::highlightQuery($this->db->replacePrefix((string) $query)));
		}

		return $query;
	}

	/**
	 * The prepare getQuery hook
	 *
	 * @param \JDatabaseQuery $query The db query object.
	 *
	 * @return  void
	 */
	protected function prepareGetQuery(\JDatabaseQuery $query)
	{
	}

	/**
	 * The post getQuery object.
	 *
	 * @param \JDatabaseQuery $query The db query object.
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  \JPagination  A JPagination object for the data set.
	 */
	public function getPagination()
	{
		$self = $this;
		$state = $this->state;

		// Get a storage key.
		return $this->fetch(__FUNCTION__, function() use ($self, $state)
		{
			// Create the pagination object.
			$limit = (int) $state->get('list.limit') - (int) $state->get('list.links');

			 return new \JPagination($self->getTotal(), $self->getStart(), $limit);
		});
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Add the list state to the store id.
		$id .= ':' . json_encode($this->allowFields);
		$id .= ':' . json_encode($this->state);

		return md5($this->context . ':' . $id);
	}

	/**
	 * Gets an array of objects from the results of database query.
	 *
	 * @param   string   $query       The query.
	 * @param   integer  $limitstart  Offset.
	 * @param   integer  $limit       The number of records.
	 *
	 * @return  array  An array of results.
	 *
	 * @throws  \RuntimeException
	 */
	public function getList($query, $limitstart = 0, $limit = 0)
	{
		$this->db->setQuery($query, $limitstart, $limit);

		$result = $this->db->loadObjectList();

		return $result;
	}

	/**
	 * Returns a record count for the query.
	 *
	 * @param   \JDatabaseQuery|string  $query  The query.
	 *
	 * @return  integer  Number of rows for query.
	 */
	public function getListCount($query)
	{
		// Use fast COUNT(*) on JDatabaseQuery objects if there no GROUP BY or HAVING clause:
		if ($query instanceof \JDatabaseQuery
			&& $query->type == 'select'
			&& $query->group === null
			&& $query->having === null)
		{
			$query = clone $query;
			$query->clear('select')->clear('order')->select('COUNT(*)');

			$this->db->setQuery($query);

			return (int) $this->db->loadResult();
		}

		// Otherwise fall back to inefficient way of counting all results.
		$this->db->setQuery($query);
		$this->db->execute();

		return (int) $this->db->getNumRows();
	}

	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return  integer  The total number of items available in the data set.
	 */
	public function getTotal()
	{
		// Get a storage key.
		if ($this->hasCache(__FUNCTION__))
		{
			return $this->getCache(__FUNCTION__);
		}

		// Load the total.
		$query = $this->_getListQuery();

		$total = (int) $this->getListCount($query);

		// Add the total to the internal cache.
		return $this->setCache(__FUNCTION__, $total);
	}

	/**
	 * Method to get the starting number of items for the data set.
	 *
	 * @return  integer  The starting number of items available in the data set.
	 */
	public function getStart()
	{
		if ($this->hasCache(__FUNCTION__))
		{
			return $this->getCache(__FUNCTION__);
		}

		$start = (int) $this->state->get('list.start');
		$limit = (int) $this->state->get('list.limit');
		$total = (int) $this->getTotal();

		if ($limit === 0)
		{
			$start = 0;
		}
		elseif ($start > $total - $limit)
		{
			$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
		}

		// Add the total to the internal cache.
		return $this->setCache(__FUNCTION__, $start);
	}

	/**
	 * Get the filter form
	 *
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load current data
	 *
	 * @return  \JForm|false  the JForm object or false
	 */
	public function getBatchForm($data = array(), $loadData = false)
	{
		try
		{
			return $this->loadForm($this->context . '.batch', 'batch', array('control' => '', 'load_data' => $loadData));
		}
		catch (\RuntimeException $e)
		{
			// Return Null Form
			return new \JForm($this->context . '.batch');
		}
	}

	/**
	 * Get the filter form
	 *
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load current data
	 *
	 * @return  \JForm|false  the JForm object or false
	 */
	public function getFilterForm($data = array(), $loadData = true)
	{
		try
		{
			return $this->loadForm($this->context . '.filter', 'filter', array('control' => '', 'load_data' => $loadData));
		}
		catch (\RuntimeException $e)
		{
			// Return Null Form
			return new \JForm($this->context . '.filter');
		}
	}

	/**
	 * Get cached query.
	 *
	 * @return  \JDatabaseQuery The db query object.
	 */
	public function getQuery()
	{
		return $this->query;
	}

	/**
	 * Set a query to cache.
	 *
	 * @param   \JDatabaseQuery $query The db query object.
	 *
	 * @return  ListModel  Return self to support chaining.
	 */
	public function setQuery($query)
	{
		$this->query = $query;

		return $this;
	}

	/**
	 * Method to get property AllowFields and merge with auto-generated fields.
	 *
	 * @return  array
	 */
	public function getAllowFields()
	{
		if ($this->hasCache('allow.fields'))
		{
			return $this->getCache('allow.fields');
		}

		$queryHelper = $this->getQueryHelper();

		// Add $this->filterFields to support B/C
		$this->allowFields = array_merge($this->allowFields, $this->filterFields, $queryHelper->getFilterFields());

		$this->filterFields = $this->allowFields;

		return $this->setCache('allow.fields', $this->allowFields);
	}

	/**
	 * Method to set property allowFields
	 *
	 * @param   array $allowFields
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setAllowFields($allowFields)
	{
		$this->allowFields = $allowFields;

		return $this;
	}

	/**
	 * Method to get property FieldAliases
	 *
	 * @return  array
	 */
	public function getFieldMapping()
	{
		return $this->fieldMapping;
	}

	/**
	 * Method to set property fieldAliases
	 *
	 * @param   array $fieldMapping
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setFieldMapping($fieldMapping)
	{
		$this->fieldMapping = $fieldMapping;

		return $this;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = $this->getContainer()->get('app');
		$data = $app->getUserState($this->context, new \stdClass);

		// Pre-fill the list options
		if (!property_exists($data, 'list'))
		{
			$data->list = array(
				'direction' => $this->state->get('list.direction'),
				'limit'     => $this->state->get('list.limit'),
				'ordering'  => $this->state->get('list.ordering'),
				'start'     => $this->state->get('list.start')
			);
		}

		$data->filter = $this->state->get('filter');

		return $data;
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
		$config = $this->getContainer()->get('joomla.config');

		// Set default ordering
		$this->state->set('list.direction', $direction);
		$this->state->set('list.ordering',  $ordering);

		// If the context is set, assume that stateful lists are used.
		if ($this->context)
		{
			// Receive & set filters
			if ($filters = (array) $this->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array'))
			{
				$filters = array_filter($filters, 'strlen');

				$this->state->set('filter', $filters);
			}

			// Receive & set searches
			if ($searches = (array) $this->getUserStateFromRequest($this->context . '.search', 'search', array(), 'array'))
			{
				$searches = AdminListHelper::handleSearches($searches, $this->getSearchFields());

				$this->state->set('search', $searches);
			}

			$limit = null;

			// Receive & set list options
			if ($list = $this->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
			{
				foreach ($list as $name => $value)
				{
					// Extra validations
					switch ($name)
					{
						case 'fullordering':
							$orderConfig = array(
								'ordering'  => $ordering,
								'direction' => $direction
							);

							$orderConfig = AdminListHelper::handleFullordering($value, $orderConfig, $this);

							$this->state->set('list.direction', $orderConfig['direction']);
							$this->state->set('list.ordering',  $orderConfig['ordering']);
							break;

//						case 'ordering':
//							$value = $this->filterField($value);
//							break;

						case 'direction':
							if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
							{
								$value = $direction;
							}
							break;

						case 'limit':
							$limit = $value;
							break;

						// Just to keep the default case
						default:
							$value = $value;
							break;
					}

					$this->state->set('list.' . $name, $value);
				}
			}

			// Fill the limits and start
			if ('' === (string) $limit)
			{
				$limit = $this->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit'), 'uint');
				$this->state->set('list.limit', $limit);
			}

			$value = $this->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);
			$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
			$this->state->set('list.start', $limitstart);
		}
		else
		{
			$this->state->set('list.start', 0);
			$this->state->set('list.limit', 0);
		}
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   \JForm   $form   A JForm object.
	 * @param   mixed    $data   The data expected for the form.
	 * @param   string   $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @throws  \Exception if there is an error in the form event.
	 */
	protected function preprocessForm(\JForm $form, $data, $group = 'content')
	{
		// Import the appropriate plugin group.
		\JPluginHelper::importPlugin($group);

		// Get the dispatcher.
		$dispatcher = $this->getContainer()->get('event.dispatcher');

		// Trigger the form preparation event.
		$results = $dispatcher->trigger('onContentPrepareForm', array($form, $data));

		// Check for errors encountered while preparing the form.
		if (count($results) && in_array(false, $results, true))
		{
			// Get the last error.
			$error = $dispatcher->getError();

			if (!($error instanceof \Exception))
			{
				throw new \Exception($error);
			}
		}
	}

	/**
	 * Process the query filters.
	 *
	 * @param \JDatabaseQuery $query   The query object.
	 * @param array          $filters The filters values.
	 *
	 * @return  \JDatabaseQuery The db query object.
	 */
	protected function processFilters(\JDatabaseQuery $query, $filters = array())
	{
		$filters = $filters ? : $this->get('filter', array());

		$filters = ArrayHelper::flatten($filters);

		$filters = $this->filterDataFields($filters);
		$filters = $this->mapDataFields($filters);

		$filterHelper = $this->getFilterHelper();

		$this->configureFilters($filterHelper);

		$query = $filterHelper->execute($query, $filters);

		return $query;
	}

	/**
	 * Configure the filter handlers.
	 *
	 * Example:
	 * ``` php
	 * $filterHelper->setHandler(
	 *     'sakura.date',
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
		// Override this method.
	}

	/**
	 * Process the search query.
	 *
	 * @param \JDatabaseQuery $query    The query object.
	 * @param array           $searches The search values.
	 *
	 * @return  \JDatabaseQuery The db query object.
	 */
	protected function processSearches(\JDatabaseQuery $query, $searches = array())
	{
		$searches = $searches ? : $this->state->get('search', array());

		$searches = ArrayHelper::flatten($searches);

		$searches = $this->filterDataFields($searches);
		$searches = $this->mapDataFields($searches);

		$searchHelper = $this->getSearchHelper();

		$this->configureSearches($searchHelper);

		$query = $searchHelper->execute($query, $searches);

		return $query;
	}

	/**
	 * Configure the search handlers.
	 *
	 * Example:
	 * ``` php
	 * $searchHelper->setHandler(
	 *     'sakura.title',
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
		// Override this method.
	}

	/**
	 * Process ordering query.
	 *
	 * @param \JDatabaseQuery $query     The query object.
	 * @param string          $ordering  The ordering string.
	 * @param string          $direction ASC or DESC.
	 *
	 * @return  void
	 */
	protected function processOrdering(\JDatabaseQuery $query, $ordering = null, $direction = null)
	{
		$ordering  = $ordering ? : $this->get('list.ordering');

		// If no ordering set, ignore this function.
		if (!$ordering)
		{
			return;
		}

		$direction = $direction ? : $this->get('list.direction', 'ASC');
		$ordering  = explode(',', $ordering);

		// Add quote
		foreach ($ordering as $key => &$value)
		{
			// Remove extra spaces
			$value = preg_replace('/\s+/', ' ', trim($value));

			$value = StringHelper::explode(' ', $value);

			if (!$this->filterField($value[0]))
			{
				unset($ordering[$key]);

				continue;
			}

			$value[0] = $this->mapField($value[0]);

			// Ignore expression
			if (!empty($value[0]) && $value[0][strlen($value[0]) - 1] != ')')
			{
				$value[0] = $query->quoteName($value[0]);
			}

			$value = implode(' ', $value);
		}

		$ordering = implode(', ', $ordering);

		if (!$ordering)
		{
			return;
		}

		$query->order($ordering . ' ' . $direction);
	}

	/**
	 * Get search fields from form xml.
	 *
	 * @return  array Search fields.
	 */
	public function getSearchFields()
	{
		if ($this->searchFields)
		{
			return $this->searchFields;
		}

		$file = PathHelper::get($this->option) . '/model/form/' . $this->name . '/filter.xml';

		if (!is_file($file))
		{
			return array();
		}

		$xml     = simplexml_load_file($file);
		$field   = $xml->xpath('//fields[@name="search"]/field[@name="field"]');

		$options = $field[0]->option;
		$fields  = array();

		foreach ($options as $option)
		{
			$attr = $option->attributes();

			if ('*' == (string) $attr['value'])
			{
				continue;
			}

			$fields[] = (string) $attr['value'];
		}

		return $this->searchFields = $fields;
	}

	/**
	 * Gets the value of a user state variable and sets it in the session
	 *
	 * This is the same as the method in JApplication except that this also can optionally
	 * force you back to the first page when a filter has changed
	 *
	 * @param   string   $key        The key of the user state variable.
	 * @param   string   $request    The name of the variable passed in a request.
	 * @param   string   $default    The default value for the variable if not found. Optional.
	 * @param   string   $type       Filter for the variable, for valid values see {@link \JFilterInput::clean()}. Optional.
	 * @param   boolean  $resetPage  If true, the limitstart in request is set to zero
	 *
	 * @return  array The request user state.
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
	{
		/** @var \JApplicationCms $app */
		$app       = $this->container->get('app');
		$input     = $app->input;
		$oldState  = $app->getUserState($key);
		$currentState = (!is_null($oldState)) ? $oldState : $default;
		$newState     = $input->get($request, null, $type);

		/*
		 * In RAD, filter & search is array with default elements,
		 * so we can't directly compare them with empty value.
		 * Here prepare some default value to compare them.
		 */

		// Remove empty values from input, because session registry will remove empty values too.
		if ($request == 'filter' && is_array($newState))
		{
			$newState = ArrayHelper::filterRecursive($newState, 'strlen');
		}

		// Add default field name '*' if we clear filter bar.
		if ($request == 'search' && '' === (string) ArrayHelper::getValue($currentState, 'field'))
		{
			$currentState['field'] = '*';
		}

		// Now compare them, and set start to 0 if there has any differences.
		if ($request !== 'limitstart' && $newState && ($currentState != $newState) && ($resetPage))
		{
			$input->set('limitstart', 0);
		}

		// Save the new value only if it is set in this request.
		if ($newState !== null)
		{
			$app->setUserState($key, $newState);
		}
		else
		{
			$newState = $currentState;
		}

		return $newState;
	}

	/**
	 * Configure tables through QueryHelper.
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
	}

	/**
	 * Add a table into QueryHelper.
	 *
	 * @param string $alias     Table select alias.
	 * @param string $table     Table name.
	 * @param mixed  $condition Join conditions, use string or array.
	 * @param string $joinType  The Join type.
	 *
	 * @return  ListModel Return self to support chaining.
	 */
	public function addTable($alias, $table, $condition = null, $joinType = 'LEFT')
	{
		$queryHelper = $this->getQueryHelper();

		$queryHelper->addTable($alias, $table, $condition, $joinType);

		return $this;
	}

	/**
	 * Remove a table from storage.
	 *
	 * @param string $alias Table alias.
	 *
	 * @return  ListModel Return self to support chaining.
	 */
	public function removeTable($alias)
	{
		$queryHelper = $this->getQueryHelper();

		$queryHelper->removeTable($alias);

		return $this;
	}

	/**
	 * Method to get property QueryHelper
	 *
	 * @return  QueryHelper
	 */
	public function getQueryHelper()
	{
		if (!$this->queryHelper)
		{
			$this->queryHelper = new QueryHelper;
		}

		return $this->queryHelper;
	}

	/**
	 * Method to set property queryHelper
	 *
	 * @param   QueryHelper $queryHelper
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setQueryHelper(QueryHelper $queryHelper)
	{
		$this->queryHelper = $queryHelper;

		return $this;
	}

	/**
	 * Method to get property FilterHelper
	 *
	 * @return  FilterHelper
	 */
	public function getFilterHelper()
	{
		if (!$this->filterHelper)
		{
			$this->filterHelper = new FilterHelper;
		}

		return $this->filterHelper;
	}

	/**
	 * Method to set property filterHelper
	 *
	 * @param   FilterHelper $filterHelper
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setFilterHelper($filterHelper)
	{
		$this->filterHelper = $filterHelper;

		return $this;
	}

	/**
	 * Method to get property SearchHelper
	 *
	 * @return  SearchHelper
	 */
	public function getSearchHelper()
	{
		if (!$this->searchHelper)
		{
			$this->searchHelper = new SearchHelper;
		}

		return $this->searchHelper;
	}

	/**
	 * Method to set property searchHelper
	 *
	 * @param   SearchHelper $searchHelper
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setSearchHelper($searchHelper)
	{
		$this->searchHelper = $searchHelper;

		return $this;
	}

	/**
	 * filterField
	 *
	 * @param string $field
	 * @param mixed  $default
	 *
	 * @return  string
	 *
	 * @since  2.1
	 */
	public function filterField($field, $default = null)
	{
		if (in_array($field, $this->getAllowFields()))
		{
			return $field;
		}

		return $default;
	}

	/**
	 * filterFields
	 *
	 * @param  array $data
	 *
	 * @return  array
	 *
	 * @since  2.1
	 */
	public function filterDataFields(array $data)
	{
		$allowFields = $this->getAllowFields();

		$return = array();

		foreach ($data as $field => $value)
		{
			if (in_array($field, $allowFields))
			{
				$return[$field] = $value;
			}
		}

		return $return;
	}

	/**
	 * mapField
	 *
	 * @param string $field
	 * @param mixed  $default
	 *
	 * @return  string
	 *
	 * @since  2.1
	 */
	public function mapField($field, $default = null)
	{
		if (isset($this->fieldMapping[$field]))
		{
			return $this->fieldMapping[$field];
		}

		return ($default === null) ? $field : $default;
	}

	/**
	 * mapDataFields
	 *
	 * @param array $data
	 *
	 * @return  array
	 */
	public function mapDataFields(array $data)
	{
		$return = array();

		foreach ($data as $field => $value)
		{
			$return[$this->mapField($field)] = $value;
		}

		return $return;
	}

	/**
	 * addFilter
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 *
	 * @return  static
	 *
	 * @since  2.1
	 */
	public function addFilter($key, $value)
	{
		$this->set('filter.' . $key, $value);

		return $this;
	}

	/**
	 * addSearch
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 *
	 * @return  static
	 *
	 * @since  2.1
	 */
	public function addSearch($key, $value)
	{
		$this->set('search.' . $key, $value);

		return $this;
	}

	/**
	 * setOrdering
	 *
	 * @param  string      $order
	 * @param  bool|false  $direction
	 *
	 * @return  static
	 *
	 * @since  2.1
	 */
	public function setOrdering($order, $direction = false)
	{
		$this->set('list.ordering', $order);

		if ($direction !== false)
		{
			$this->set('list.direction', $direction);
		}

		return $this;
	}
}
