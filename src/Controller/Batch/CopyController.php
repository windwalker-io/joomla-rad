<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Controller\Batch;

use Windwalker\String\StringHelper;

/**
 * Copy Batch Controller.
 *
 * @since 2.0
 */
class CopyController extends AbstractBatchController
{
	/**
	 * Which fields should increment.
	 *
	 * @var array
	 */
	protected $incrementFields = array(
		'title' => StringHelper::INCREMENT_STYLE_DEFAULT,
		'alias' => StringHelper::INCREMENT_STYLE_DASH
	);

	/**
	 * Method to save item.
	 *
	 * @param int   $pk   The primary key value.
	 * @param array $data The item data.
	 *
	 * @return mixed
	 */
	protected function save($pk, $data)
	{
		if (!$this->allowAdd($data))
		{
			return false;
		}

		// We load existing item first and bind data into it.
		$this->table->reset();

		$this->table->load($pk);

		$this->table->bind($data);

		// Dump as array
		$item = $this->table->getProperties(true);

		// Handle Title increment
		$table2 = $this->model->getTable();

		$condition = array();

		// Check table has increment fields, default is title and alias.
		foreach ($this->incrementFields as $field => $type)
		{
			if (property_exists($this->table, $field))
			{
				$condition[$field] = $item[$field];
			}
		}

		if (count($condition))
		{
			// Recheck item with same conditions(default is title & alias), if true, increment them.
			// If no item got, means it is the max number.
			while ($table2->load($condition))
			{
				foreach ($this->incrementFields as $field => $type)
				{
					if (property_exists($this->table, $field))
					{
						$item[$field] = $condition[$field] = StringHelper::increment($item[$field], $type);
					}
				}
			}
		}

		// Unset the primary key so that we can copy it.
		unset($item[$this->urlVar]);

        $this->model->set($this->model->getName() . '.id', null);

		return $this->model->save($item);
	}
}
