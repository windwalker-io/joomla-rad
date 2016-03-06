<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Relation\Handler;

use Windwalker\Relation\Action;

/**
 * The OneToManyRelation class.
 * 
 * @since  2.1
 */
class OneToManyRelation extends AbstractRelationHandler
{
	/**
	 * Load all relative children data.
	 *
	 * @return  void
	 */
	public function load()
	{
		$items = $this->db->setQuery($this->buildLoadQuery())->loadObjectList();

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

		if ($this->flush)
		{
			$this->deleteAllRelatives();
		}

		foreach ($items as $item)
		{
			$itemTable = $this->convertToTable($item);
			$itemTable = $this->handleUpdateRelations($itemTable);

			if ($this->flush)
			{
				$itemTable = $this->clearPrimaryKeys($itemTable);
			}

			$itemTable->check();
			$itemTable->store(true);
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
