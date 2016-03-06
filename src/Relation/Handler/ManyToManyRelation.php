<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Relation\Handler;

use Windwalker\Data\DataSet;
use Windwalker\Model\Helper\QueryHelper;
use Windwalker\Relation\Action;
use Windwalker\Table\Table;

/**
 * The OneToManyRelation class.
 * 
 * @since  2.1
 */
class ManyToManyRelation extends AbstractRelationHandler
{
	/**
	 * Property map.
	 *
	 * @var  \JTable
	 */
	protected $map;

	/**
	 * Property mapFks.
	 *
	 * @var  array
	 */
	protected $mapFks;

	/**
	 * Property mapTemps.
	 *
	 * @var  DataSet
	 */
	protected $mapTemps = array();

	/**
	 * Class init.
	 *
	 * @param  Table    $parent    The parent table od this relation.
	 * @param  string   $field     Field of parent table to store children.
	 * @param  \JTable  $map       The mapping table.
	 * @param  array    $mapFks    The mapping foreign keys.
	 * @param  \JTable  $table     The Table object of this relation child.
	 * @param  array    $fks       Foreign key mapping.
	 * @param  string   $onUpdate  The action of ON UPDATE operation.
	 * @param  string   $onDelete  The action of ON DELETE operation.
	 * @param  array    $options   Some options to configure this relation.
	 */
	public function __construct($parent, $field = null, $map = null, $mapFks = array(), $table = null, $fks = array(), $onUpdate = Action::NO_ACTION, $onDelete = Action::NO_ACTION,
		$options = array())
	{
		$this->mappingTable($map, $mapFks);

		parent::__construct($parent, $field, $table, $fks, $onUpdate, $onDelete, $options);
	}

	/**
	 * Load all relative children data.
	 *
	 * @return  void
	 */
	public function load()
	{
		$maps = $this->db->setQuery($this->buildMapQuery())->loadObjectList();

		$this->mapTemps = new DataSet($maps);

		$items = $maps ? $this->db->setQuery($this->buildTargetQuery($maps))->loadObjectList() : array();

		$this->setParentFieldValue($this->convertToDataSet($items));
	}

	/**
	 * Store all relative children data.
	 *
	 * The onUpdate option will work in this method.
	 *
	 * @return  void
	 */
	public function store()
	{
		if ($this->onUpdate == Action::NO_ACTION || $this->onUpdate == Action::RESTRICT)
		{
			return;
		}

		$items = $this->getParentFieldValue();

		if (!is_array($items) && !($items instanceof \Traversable))
		{
			throw new \InvalidArgumentException('Relation items should be array or iterator.');
		}

		foreach ($items as $item)
		{
			$itemTable = $this->convertToTable($item);
			$itemTable->check();
			$itemTable->store(true);
			$mapTableName = $this->map->getTableName();

			$map = new \stdClass;

			// Prepare parent table and map table mapping
			foreach ($this->mapFks as $field => $foreign)
			{
				$map->$foreign = $this->parent->$field;
			}

			// Prepare map table and target table mapping
			foreach ($this->fks as $field => $foreign)
			{
				$map->$field = $itemTable->$foreign;
			}

			// If map not empty, get the origin map from database.
			if ((array) $map)
			{
				$query = $this->db->getQuery(true)
					->select('*')
					->from($mapTableName);

				$originMap = $this->db->setQuery(QueryHelper::buildWheres($query, (array) $map))->loadObject();
			}
			else
			{
				$originMap = false;
			}

			// If flush set true, delete all maps then re-create new.
			if ($this->flush)
			{
				$query = $this->db->getQuery(true)
					->delete($mapTableName);

				foreach ($this->mapFks as $field => $foreign)
				{
					$query->where($query->format('%n = %q', $foreign, $this->parent->$field));
				}

				$this->db->setQuery($query)->execute();
			}
			// If not flush, we see actions to decide what should we do.
			else
			{
				// If action is CASCADE or SET NULL, delete origin map.
				// If CASCADE, we will create new map later.
				if ($this->onUpdate == Action::CASCADE || $this->onUpdate == Action::SET_NULL)
				{
					// Delete old same item
					$query = $this->db->getQuery(true)
						->delete($mapTableName);

					$query = QueryHelper::buildWheres($query, (array) $map);

					$this->db->setQuery($query)->execute();
				}

				// If parent changed and action is SET NULL, delete all old maps by temp
				if ($this->onUpdate == Action::SET_NULL && $this->changed($originMap))
				{
					foreach ($this->mapTemps as $mapTemp)
					{
						$query = $this->db->getQuery(true)
							->delete($mapTableName);

						$query = QueryHelper::buildWheres($query, $mapTemp->dump());

						$this->db->setQuery($query)->execute();
					}

					continue;
				}
			}

			// If action is CASCADE, create a new map.
			if ($this->onUpdate == Action::CASCADE)
			{
				$this->db->insertObject($mapTableName, $map);
			}
		}
	}

	/**
	 * Delete all relative children data.
	 *
	 * The onDelete option will work in this method.
	 *
	 * @return  void
	 */
	public function delete()
	{
		if ($this->onUpdate == Action::NO_ACTION || $this->onUpdate == Action::RESTRICT)
		{
			return;
		}

		$items = $this->getParentFieldValue();

		if (!is_array($items) && !($items instanceof \Traversable))
		{
			throw new \InvalidArgumentException('Relation items should be array or iterator.');
		}

		foreach ($items as $item)
		{
			$itemTable = $this->convertToTable($item);

			if ($this->onDelete == Action::CASCADE)
			{
				$itemTable->delete();
			}

			$mapTableName = $this->map->getTableName();

			$map = new \stdClass;

			// Prepare parent table and map table mapping
			foreach ($this->mapFks as $field => $foreign)
			{
				$map->$foreign = $this->parent->$field;
			}

			// If action is SET NULL, delete all old maps by temp
			if ($this->onDelete == Action::SET_NULL || $this->onDelete == Action::CASCADE)
			{
				$query = $this->db->getQuery(true)
					->delete($mapTableName);

				$query = QueryHelper::buildWheres($query, (array) $map);

				$this->db->setQuery($query)->execute();
			}
		}
	}

	/**
	 * Build query for load operation.
	 *
	 * @param   \JDatabaseQuery  $query  The query object to handle.
	 *
	 * @return  \JDatabaseQuery  Return handled query object.
	 */
	public function buildMapQuery(\JDatabaseQuery $query = null)
	{
		$conditions = array();

		foreach ($this->mapFks as $field => $foreign)
		{
			$conditions[$foreign] = $this->parent->$field;
		}

		$query = $query ? : $this->db->getQuery(true);

		QueryHelper::buildWheres($query, $conditions);

		$query->select('*')
			->from($this->map->getTableName());

		return $query;
	}

	/**
	 * Build query for load operation.
	 *
	 * @param   \stdClass[]     $mapping  The mapping data.
	 * @param   \JDatabaseQuery $query    The query object to handle.
	 *
	 * @return  \JDatabaseQuery Return handled query object.
	 */
	public function buildTargetQuery($mapping, \JDatabaseQuery $query = null)
	{
		$query = $query ? : $this->db->getQuery(true);
		$conditions = array();

		foreach ($mapping as $map)
		{
			$where = array();

			foreach ($this->fks as $field => $foreign)
			{
				$where[] = $query->format('%n = %q', $foreign, $map->$field);
			}

			$conditions[] = new \JDatabaseQueryElement('()', $where, ' AND ');
		}

		$conditions = new \JDatabaseQueryElement('', $conditions, ' OR ');

		$query->select('*')
			->from($this->table->getTableName())
			->where($conditions);

		return $query;
	}

	/**
	 * Is fields changed. If any field changed, means we have to do something to children.
	 *
	 * @param   \JTable  $map  The child table to be handled.
	 *
	 * @return  boolean  Something changed of not.
	 */
	public function changed($map)
	{
		if (!$map)
		{
			return true;
		}

		// If any key changed, set all fields as NULL.
		foreach ($this->mapFks as $field => $foreign)
		{
			if ($map->$foreign != $this->parent->$field)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Method to get property Map
	 *
	 * @return  \JTable
	 */
	public function getMappingTable()
	{
		return $this->map;
	}

	/**
	 * Method to set property map
	 *
	 * @param   \JTable $map
	 * @param   array   $mapFks
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function mappingTable($map, $mapFks)
	{
		if (!$map)
		{
			return $this;
		}

		if (!($map instanceof \JTable))
		{
			$map = $this->getTable($map, $this->prefix);
		}

		$this->map = $map;
		$this->mappingTableForeignKeys($mapFks);

		return $this;
	}

	/**
	 * Method to get property MapFks
	 *
	 * @return  array
	 */
	public function getMappingTableForeignKeys()
	{
		return $this->mapFks;
	}

	/**
	 * Method to set property mapFks
	 *
	 * @param   array $mapFks
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function mappingTableForeignKeys($mapFks)
	{
		if (!is_array($mapFks))
		{
			throw new \InvalidArgumentException('Argument $mapFks should be array');
		}

		$this->mapFks = $mapFks;

		return $this;
	}
}
