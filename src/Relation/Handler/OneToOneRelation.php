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
 * The OneToOneRelation class.
 * 
 * @since  2.1
 */
class OneToOneRelation extends AbstractRelationHandler
{
	/**
	 * Load all relative children data.
	 *
	 * @return  void
	 */
	public function load()
	{
		$item = $this->db->setQuery($this->buildLoadQuery())->loadObject();

		$this->setParentFieldValue($this->convertToData($item));
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

		$item = $this->getParentFieldValue();

		$itemTable = $this->convertToTable($item);
		$itemTable = $this->handleUpdateRelations($itemTable);

		if ($this->flush)
		{
			$this->deleteAllRelatives();
			$itemTable = $this->clearPrimaryKeys($itemTable);
		}

		$itemTable->check();
		$itemTable->store(true);
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

		$item = $this->getParentFieldValue();

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
