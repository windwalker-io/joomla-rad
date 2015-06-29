<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation\Handler;

use Windwalker\Model\Helper\QueryHelper;

/**
 * The OneToManyRelation class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class OneToManyRelation extends AbstractRelationHandler
{
	/**
	 * load
	 *
	 * @return  void
	 */
	public function load()
	{
		$conditions = array();

		foreach ($this->fks as $field => $foreign)
		{
			$conditions[$foreign] = $this->parent->$field;
		}

		$query = $this->db->getQuery(true);

		QueryHelper::buildWheres($query, $conditions);

		$query->select('*')
			->from($this->tableName);

		$items = $this->db->setQuery($query)->loadObjectList();

		$table = clone $this->table;
		$table->reset();

		$results = array();

		foreach ($items as $item)
		{
			$itemTable = clone $table;
			$itemTable->bind($item);

			$results[] = $itemTable;
		}

		$this->parent->{$this->field} = $results;
	}

	public function store()
	{
		$items = $this->parent->{$this->field};

		if ($items instanceof \Traversable)
		{
			$items = iterator_to_array($items);
		}

		if (!is_array($items))
		{
			throw new \InvalidArgumentException('Relation items should be array');
		}

		$table = clone $this->table;
		$table->reset();

		foreach ($items as $item)
		{
			if (!($item instanceof \JTable))
			{
				$itemTable = clone $table;
				$itemTable->bind($item);
			}
			else
			{
				$itemTable = $table;
			}

			$itemTable->check();
			$itemTable->store(true);
		}
	}

	public function delete()
	{

	}
}
