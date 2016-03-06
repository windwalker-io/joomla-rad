<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Relation\Handler;

/**
 * Interface RelationHandlerInterface
 *
 * @since  2.1
 */
interface RelationHandlerInterface
{
	/**
	 * Load all relative children data.
	 *
	 * @return  void
	 */
	public function load();

	/**
	 * Store all relative children data.
	 *
	 * The onUpdate option will work in this method.
	 *
	 * @return  void
	 */
	public function store();

	/**
	 * Delete all relative children data.
	 *
	 * The onDelete option will work in this method.
	 *
	 * @return  void
	 */
	public function delete();
}
