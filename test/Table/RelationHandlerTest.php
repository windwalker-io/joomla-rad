<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Table;

use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\Table\Stub\StubTableLocations;

/**
 * The RelationHandlerTest class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class RelationHandlerTest extends AbstractDatabaseTestCase
{
	public function testRelation()
	{
		$location = new StubTableLocations(\JFactory::getDbo());


	}
}
