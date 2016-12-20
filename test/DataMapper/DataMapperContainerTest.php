<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\DataMapper;

use Windwalker\DataMapper\DataMapper;
use Windwalker\DataMapper\DataMapperContainer;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\DataMapper\DataMapperContainer
 *
 * @since 2.1
 */
class DataMapperContainerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test getInstance().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\DataMapper\DataMapperContainer::getInstance
	 */
	public function testGetInstance()
	{
		$mapper = DataMapperContainer::getInstance('#__content');

		$this->assertInstanceOf('Windwalker\DataMapper\DataMapper', $mapper);
		$this->assertEquals('#__content', $mapper->getTable());

		$this->assertSame($mapper, DataMapperContainer::getInstance('#__content'));
		$this->assertNotSame($mapper, DataMapperContainer::getInstance('#__categories'));
	}

	/**
	 * Method to test setInstance().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\DataMapper\DataMapperContainer::setInstance
	 */
	public function testSetInstance()
	{
		$mapperBackup = DataMapperContainer::getInstance('#__content');

		DataMapperContainer::setInstance('#__content', new DataMapper('#__users'));

		$this->assertNotSame($mapperBackup, DataMapperContainer::getInstance('#__content'));
		$this->assertEquals('#__users', DataMapperContainer::getInstance('#__content')->getTable());
	}

	/**
	 * Method to test removeInstance().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\DataMapper\DataMapperContainer::removeInstance
	 */
	public function testRemoveInstance()
	{
		$mapperBackup = DataMapperContainer::getInstance('#__content');

		DataMapperContainer::removeInstance('#__content');

		$mappers = TestHelper::getValue('Windwalker\DataMapper\DataMapperContainer', 'instances');

		$this->assertArrayNotHasKey('#__content', $mappers);

		$this->assertNotSame($mapperBackup, DataMapperContainer::getInstance('#__content'));
	}
}
