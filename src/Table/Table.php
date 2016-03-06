<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Table;

use JTable;
use Windwalker\DI\Container;
use Windwalker\Relation\Observer\RelationObserver;
use Windwalker\Relation\Relation;

/**
 * Windwalker active record Table.
 *
 * @since 2.0
 */
class Table extends \JTable
{
	/**
	 * Property _relation.
	 *
	 * @var  Relation
	 */
	public $_relation;

	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   string           $table  Name of the table to model.
	 * @param   mixed            $key    Name of the primary key field in the table or array of field names that compose the primary key.
	 * @param   \JDatabaseDriver $db     JDatabaseDriver object.
	 */
	public function __construct($table, $key = 'id', $db = null)
	{
		$db = $db ?: Container::getInstance()->get('db');

		parent::__construct($table, $key, $db);

		// Prepare Relation handler
		$this->_relation = new Relation($this, $this->getPrefix());

		RelationObserver::createObserver($this);

		$this->configure();

		$this->_observers->update('onAfterConstruction', array());
	}

	/**
	 * Configure this table.
	 *
	 * This method will run after \Windwalker\Table\Table::__construct().
	 *
	 * @return  void
	 *
	 * @since   2.1
	 */
	protected function configure()
	{
		// Do some stuff
	}

	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 *
	 * @throws  \InvalidArgumentException
	 * @throws  \RuntimeException
	 * @throws  \UnexpectedValueException
	 */
	public function load($keys = null, $reset = true)
	{
		return parent::load($keys, $reset);
	}

	/**
	 * Method to store a row in the database from the JTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * JTable instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		if (property_exists($this, 'params') && !empty($this->params))
		{
			if (is_array($this->params) || is_object($this->params))
			{
				$this->params = json_encode($this->params);
			}
		}

		return parent::store($updateNulls);
	}

	/**
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed $src    An associative array or object to bind to the JTable instance.
	 * @param   mixed $ignore An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 * @throws  \InvalidArgumentException
	 */
	public function bind($src, $ignore = array())
	{
		if ($this->_trackAssets && isset($src['rules']) && is_array($src['rules']))
		{
			$this->setRules(new \JAccessRules($src['rules']));
		}

		return parent::bind($src, $ignore);
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  \UnexpectedValueException
	 */
	public function delete($pk = null)
	{
		return parent::delete($pk);
	}

	/**
	 * Magic method to get property and avoid errors.
	 *
	 * @param   string  $name  The property name to get.
	 *
	 * @return  mixed  Value of this property.
	 */
	public function __get($name)
	{
		if (property_exists($this, $name))
		{
			return $this->$name;
		}

		return null;
	}

	/**
	 * Get the columns from database table.
	 *
	 * @return  mixed  An array of the field names, or false if an error occurs.
	 *
	 * @since   11.1
	 * @throws  \UnexpectedValueException
	 */
	public function getFields()
	{
		return TableHelper::getFields($this);
	}

	/**
	 * getPrefix
	 *
	 * @return  string
	 */
	public function getPrefix()
	{
		$ref = new \ReflectionClass($this);

		$className = explode('Table', $ref->getShortName());

		if (count($className) >= 2)
		{
			$tablePrefix = $className[0] . 'Table';
		}
		else
		{
			$tablePrefix = 'JTable';
		}

		return $tablePrefix;
	}
}
