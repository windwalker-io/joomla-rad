<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Model\Exception;

use Windwalker\Model\Exception\ValidateFailException;
use Windwalker\Test\TestHelper;

/**
 * Test class of ValidateFailException
 *
 * @since 2.1
 */
class ValidateFailExceptionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test ValidateFailException::getErrors.
	 *
	 * @param array $data
	 *
	 * @return void
	 *
	 * @dataProvider getErrorsDataProvider
	 * @covers       Windwalker\Model\Exception\ValidateFailException::getErrors
	 * @group        getErrors
	 */
	public function testGetErrors($data)
	{
		$obj = new ValidateFailException($data);

		$this->assertEquals($data, $obj->getErrors());
	}

	/**
	 * getErrorsDataProvider
	 *
	 * @return array
	 */
	public function getErrorsDataProvider()
	{
		return array(
			array(array()),
			array(array(false)),
			array(array('Warning')),
		);
	}

	/**
	 * Method to test ValidateFailException::setErrors.
	 *
	 * @param array $data
	 *
	 * @return void
	 *
	 * @dataProvider setErrorsDataProvider
	 * @covers       Windwalker\Model\Exception\ValidateFailException::setErrors
	 * @group        setErrors
	 */
	public function testSetErrors($data)
	{
		$obj = new ValidateFailException($data);

		$this->assertSame($obj, $obj->setErrors($data));

		$this->assertSame($data, TestHelper::getValue($obj, 'errors'));
	}

	/**
	 * setErrorsDataProvider
	 *
	 * @return array
	 */
	public function setErrorsDataProvider()
	{
		return array(
			array(array()),
			array(array(false)),
			array(array('Warning')),
		);
	}
}
