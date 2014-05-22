<?php

namespace Windwalker\Model;

use Joomla\DI\Container as JoomlaContainer;
use JTable;

/**
 * The basic Crud Model
 *
 * @since 2.0
 */
class CrudModel extends FormModel
{
	/**
	 * Item cache.
	 *
	 * @var  array
	 */
	protected $item = null;

	/**
	 * The prefix to use with messages.
	 *
	 * @var  string
	 */
	protected $textPrefix = null;

	/**
	 * The event to trigger after deleting the data.
	 *
	 * @var  string
	 */
	protected $eventAfterDelete = null;

	/**
	 * The event to trigger after saving the data.
	 *
	 * @var  string
	 */
	protected $eventAfterSave = null;

	/**
	 * The event to trigger before deleting the data.
	 *
	 * @var  string
	 */
	protected $eventBeforeDelete = null;

	/**
	 * The event to trigger before saving the data.
	 *
	 * @var  string
	 */
	protected $eventBeforeSave = null;

	/**
	 * The event to trigger after changing the published state of the data.
	 *
	 * @var  string
	 */
	protected $eventChangeState = null;

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
		parent::__construct($config, $container, $state, $db);

		$this->eventAfterDelete  = $this->eventAfterDelete  ? : \JArrayHelper::getValue($config, 'event_after_delete', 'onContentAfterDelete');
		$this->eventBeforeDelete = $this->eventBeforeDelete ? : \JArrayHelper::getValue($config, 'event_before_delete', 'onContentBeforeDelete');
		$this->eventAfterSave    = $this->eventAfterSave    ? : \JArrayHelper::getValue($config, 'event_after_save', 'onContentAfterSave');
		$this->eventBeforeSave   = $this->eventAfterSave    ? : \JArrayHelper::getValue($config, 'event_before_save', 'onContentBeforeSave');
		$this->eventChangeState  = $this->eventAfterSave    ? : \JArrayHelper::getValue($config, 'event_change_state', 'onContentChangeState');

		// @TODO: Check is needed or not.
		$this->textPrefix = $this->textPrefix ? : strtoupper(\JArrayHelper::getValue($config, 'text_prefix', $this->option));
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method will only called in constructor. Using `ignore_request` to ignore this method.
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		$table = $this->getTable();
		$key   = $table->getKeyName();

		// Get the pk of the record from the request.
		$pk = $this->getContainer()->get('input')->get($key);
		$this->state->set($this->getName() . '.id', $pk);

		// Load the parameters.
		$value = \JComponentHelper::getParams($this->option);
		$this->state->set('params', $value);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : $this->state->get($this->getName() . '.id');
		$table = $this->getTable();

		if (!empty($pk))
		{
			// Attempt to load the row.
			$table->load($pk);
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = \JArrayHelper::toObject($properties, 'stdClass');

		if (property_exists($item, 'params'))
		{
			$registry = new \JRegistry;

			$registry->loadString($item->params);

			$item->params = $registry->toArray();
		}

		return $item;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @throws  \Exception
	 * @return  boolean  True on success, False on error.
	 */
	public function save($data)
	{
		$container  = $this->getContainer();
		$table      = $this->getTable();
		$dispatcher = $container->get('event.dispatcher');

		if ((!empty($data['tags']) && $data['tags'][0] != ''))
		{
			$table->newTags = $data['tags'];
		}

		$key = $table->getKeyName();
		$pk  = \JArrayHelper::getValue($data, $key, $this->getState($this->getName() . '.id'));

		$isNew = true;

		// Include the content plugins for the on save events.
		\JPluginHelper::importPlugin('content');

		// Load the row if saving an existing record.
		if ($pk)
		{
			$table->load($pk);
			$isNew = false;
		}

		// Bind the data.
		$table->bind($data);

		// Prepare the row for saving
		$this->prepareTable($table);

		// Check the data.
		if (!$table->check())
		{
			throw new \Exception($table->getError());
		}

		// Trigger the onContentBeforeSave event.
		$result = $dispatcher->trigger($this->eventBeforeSave, array($this->option . '.' . $this->name, $table, $isNew));

		if (in_array(false, $result, true))
		{
			throw new \Exception($table->getError());
		}

		// Store the data.
		if (!$table->store())
		{
			throw new \Exception($table->getError());
		}

		// Clean the cache.
		$this->cleanCache();

		// Trigger the onContentAfterSave event.
		$dispatcher->trigger($this->eventAfterSave, array($this->option . '.' . $this->name, $table, $isNew));

		$pkName = $table->getKeyName();

		if (isset($table->$pkName))
		{
			$this->state->set($this->getName() . '.id', $table->$pkName);
		}

		$this->state->set($this->getName() . '.new', $isNew);

		$this->postSaveHook($table);

		return true;
	}

	/**
	 * Post save hook.
	 *
	 * @param JTable $table The table object.
	 *
	 * @return  void
	 */
	public function postSaveHook(\JTable $table)
	{
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	protected function prepareTable(\JTable $table)
	{
		// Please override this method.
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 */
	protected function loadFormData()
	{
		$container = $this->getContainer();
		$app   = $container->get('app');
		$input = $container->get('input');

		// Check the session for previously entered form data.
		$data = $app->getUserState("{$this->option}.edit.{$this->getName()}.data", array());

		if (empty($data))
		{
			$data = $this->getItem();
		}
		else
		{
			// If Error occured and resend, just return data.
			return $data;
		}

		// If page reload, retain data
		// ==========================================================================================
		$retain = $input->get('retain', 0);

		// Set Change Field Type Retain Data
		if ($retain)
		{
			$data = $input->getVar('jform');
		}

		return $data;
	}

	/**
	 * Method to update a field of items.
	 *
	 * @param string $pks  The ids we want to update.
	 * @param mixed  $data The data to update.
	 *
	 * @return boolean True if update success.
	 */
	public function updateState($pks, $data = array())
	{
		$dispatcher = $this->getContainer()->get('event.dispatcher');
		$errors  = array();
		$success = 0;
		$table   = $this->getTable();
		$pks     = (array) $pks;

		if (!count($pks))
		{
			return false;
		}

		// Include the content plugins for the change of state event.
		\JPluginHelper::importPlugin('content');

		$key = $table->getKeyName();

		// Update the state for rows with the given primary keys.
		foreach ($pks as $pk)
		{
			$table->reset();

			// Set primary
			$table->$key = $pk;

			// Bind data
			$table->bind($data);

			// Do save
			if (!$table->store())
			{
				$errors[] = $table->getError();

				continue;
			}

			$success++;
		}

		$this->state->set('error.message',  $errors);
		$this->state->set('success.number', $success);

		$context = $this->option . '.' . $this->name;

		// Trigger the onContentChangeState event.
		$dispatcher->trigger($this->eventChangeState, array($context, $pks, $data));

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function delete(&$pks)
	{
		$dispatcher = $this->getContainer()->get('event.dispatcher');
		$errors  = array();
		$success = 0;
		$pks     = (array) $pks;
		$table   = $this->getTable();

		// Include the content plugins for the on delete events.
		\JPluginHelper::importPlugin('content');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if (!$table->load($pk))
			{
				$errors[] = $table->getError();

				continue;
			}

			$context = $this->option . '.' . $this->name;

			// Trigger the onContentBeforeDelete event.
			$result = $dispatcher->trigger($this->eventBeforeDelete, array($context, $table));

			if (in_array(false, $result, true))
			{
				$errors[] = $table->getError();

				continue;
			}

			if (!$table->delete($pk))
			{
				$errors[] = $table->getError();

				continue;
			}

			// Trigger the onContentAfterDelete event.
			$dispatcher->trigger($this->eventAfterDelete, array($context, $table));

			$success++;
		}

		$this->state->set('error.message',  $errors);
		$this->state->set('success.number', $success);

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
