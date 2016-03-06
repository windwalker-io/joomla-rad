<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

use Windwalker\Data\Data;
use Windwalker\Model\Model;

/**
 * The WindwalkerModelStub class.
 * 
 * @since  2.1
 */
class WindwalkerModelStub extends Model
{
	/**
	 * Property populateState.
	 *
	 * @var  bool
	 */
	public $populateState = false;

	/**
	 * Property paths.
	 *
	 * @var  array
	 */
	public static $paths = array();

	/**
	 * getItem
	 *
	 * @param int $pk
	 *
	 * @return  Data
	 */
	public function getItem($pk = null)
	{
		$pk = $pk ? : $this->get('pk');

		$data = new Data(array('pk' => $pk));

		return $data;
	}

	/**
	 * populateState
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		$this->populateState = true;
	}

	/**
	 * Adds to the stack of model table paths in LIFO order.
	 *
	 * @param   mixed  $path  The directory as a string or directories as an array to add.
	 *
	 * @return  void
	 */
	public static function addTablePath($path)
	{
		static::$paths[] = $path;
	}
}
