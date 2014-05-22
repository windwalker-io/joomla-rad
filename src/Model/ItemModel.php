<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Model;

/**
 * The model for single item.
 *
 * @since 2.0
 */
class ItemModel extends AbstractAdvancedModel
{
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

		if ($pk > 0)
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
}
