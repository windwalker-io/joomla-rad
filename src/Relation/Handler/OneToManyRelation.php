<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation\Handler;

use Windwalker\Model\Helper\QueryHelper;
use Windwalker\Relation\Action;

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

		$this->parent->{$this->field} = $this->convertToDataSet($items);
	}

	public function store()
	{
		if ($this->onUpdate == Action::NO_ACTION || $this->onUpdate == Action::RESTRICT)
		{
			return;
		}

		$items = $this->parent->{$this->field};

		if (!is_array($items) && !($items instanceof \Traversable))
		{
			throw new \InvalidArgumentException('Relation items should be array or iterator.');
		}

		foreach ($items as $item)
		{
			$itemTable = $this->convertToTable($item);
			$itemTable = $this->handleUpdateRelations($itemTable);

			$itemTable->check();
			$itemTable->store(true);
		}
	}

	/**
	 * delete
	 *
	 * @return  void
	 */
	public function delete()
	{
		if ($this->onUpdate == Action::NO_ACTION || $this->onUpdate == Action::RESTRICT)
		{
			return;
		}

		$items = $this->parent->{$this->field};

		if (!is_array($items) && !($items instanceof \Traversable))
		{
			throw new \InvalidArgumentException('Relation items should be array or iterator.');
		}

		foreach ($items as $item)
		{
			$itemTable = $this->convertToTable($item);
			$itemTable = $this->handleDeleteRelations($itemTable);

			if (empty($itemTable->_delete))
			{
				$itemTable->check();
				$itemTable->store(true);
			}
			else
			{
				$itemTable->delete();
			}
		}
	}
}
