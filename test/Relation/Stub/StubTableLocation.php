<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Relation\Stub;

use Windwalker\Relation\Action;
use Windwalker\Table\Table;

/**
 * The StubTableLocation class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class StubTableLocation extends Table
{
	/**
	 * Class init.
	 *
	 * @param \JDatabaseDriver $db
	 */
	public function __construct($db = null)
	{
		parent::__construct('#__testflower_locations', 'id', $db);
	}

	/**
	 * configure
	 *
	 * @return  void
	 */
	protected function configure()
	{

	}
}
