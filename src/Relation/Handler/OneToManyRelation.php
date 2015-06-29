<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation\Handler;

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
	 * @param array $conditions
	 *
	 * @return  \JTable[]
	 */
	public function load($conditions)
	{
		$query = $this->db->getQuery(true);

		$query->select('*')
			->from($this->tableName)
			->where($conditions);

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

		return $results;
	}

	public function update()
	{

	}

	public function create()
	{

	}

	public function delete()
	{

	}
}
