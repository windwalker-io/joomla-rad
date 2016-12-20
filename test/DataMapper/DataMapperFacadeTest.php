<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\DataMapper;

use Windwalker\Data\Data;
use Windwalker\Data\DataSet;
use Windwalker\DataMapper\DataMapperContainer;
use Windwalker\DataMapper\DataMapperFacade;

/**
 * Test class of \Windwalker\DataMapper\DataMapperFacade
 *
 * @since 2.1
 */
class DataMapperFacadeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test __callStatic().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\DataMapper\DataMapperFacade::__callStatic
	 */
	public function test__callStatic()
	{
		$mockDataMapper = $this->getMockBuilder('Windwalker\DataMapper\DataMapper')
			->disableOriginalConstructor()
			->setMethods(array('find', 'findOne'))
			->getMock();

		$mockDataMapper->expects($this->at(0))
			->method('find')
			->with(array('state' => 1), 'id desc', 0, 3)
			->willReturn(new DataSet);

		$mockDataMapper->expects($this->at(1))
			->method('findOne')
			->with(array('state' => 1), 'created desc')
			->willReturn(new Data);

		DataMapperContainer::setInstance('#__content', $mockDataMapper);

		DataMapperFacade::find('#__content', array('state' => 1), 'id desc', 0, 3);
		DataMapperFacade::findOne('#__content', array('state' => 1), 'created desc');
	}
}