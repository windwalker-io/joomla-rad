<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Relation\Stub;

use Windwalker\Relation\Handler\AbstractRelationHandler;

/**
 * The StubRelationHandler class.
 * 
 * @since  2.1
 */
class StubRelationHandler extends AbstractRelationHandler
{
	/**
	 * Load all relative children data.
	 *
	 * @return  void
	 */
	public function load()
	{

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

	}
}
