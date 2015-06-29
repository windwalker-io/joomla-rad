<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Table;

use Joomla\Utilities\ArrayHelper;
use Windwalker\Relation\Action;
use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\Table\Stub\StubTableLocations;
use Windwalker\Test\Table\Stub\StubTableRoses;
use Windwalker\Test\Table\Stub\StubTableSakuras;

/**
 * The RelationHandlerTest class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class OneToManyRelationTest extends AbstractDatabaseTestCase
{
	public function testLoad()
	{
		$location = new StubTableLocations(\JFactory::getDbo());

		$location->_relation->addOneToMany('sakuras', new StubTableSakuras, array('id' => 'location'), Action::CASCADE, Action::CASCADE);
		$location->_relation->addOneToMany('roses', new StubTableRoses, array('id' => 'location'), Action::CASCADE, Action::CASCADE);

		$location->load(3);

		$sakuras = $location->sakuras;
		$roses = $location->roses;

		$this->assertInstanceOf('Windwalker\Table\Table', $sakuras[0]);
		$this->assertEquals(array(11, 12, 13, 14, 15), ArrayHelper::getColumn($sakuras, 'id'));
		$this->assertEquals(array(11, 12, 13, 14, 15), ArrayHelper::getColumn($roses, 'id'));
	}
}
