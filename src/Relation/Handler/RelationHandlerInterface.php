<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation\Handler;


/**
 * Interface RelationHandlerInterface
 *
 * @since  {DEPLOY_VERSION}
 */
interface RelationHandlerInterface
{
	public function load();

	public function store();

	public function delete();
}
