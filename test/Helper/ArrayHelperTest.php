<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Helper\ArrayHelper;

/**
 * Test class of Windwalker\Helper\ArrayHelper
 *
 * @since 2.0
 */
class ArrayHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
	}
	
	/**
	 * Method to test repair().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\ArrayHelper::getByPath
	 */
	public function testGetByPath()
	{
        $object = new \stdClass;
        $object->Alice = 'Julia';
        $object->Johnny = array(
            'David' => 123,
            'Peter' => 'John',
        );
        $object->Vanessa = new \stdClass;
        $object->Vanessa->Maria = 'Catherine';

        $data = array(
            'Jones' => array(
                'Sakura' => 223
            ),
            'Arthur' => array(
                'Lancelot' => array(
                    'Jessica' => $object,
                    'Rose' => array(
                        'Taylor' => 323
                    )
                )
            )
        );

		// Test null return
        $this->assertEquals(null, ArrayHelper::getByPath($data, ''));
        $this->assertEquals(null, ArrayHelper::getByPath($data, null));
        $this->assertEquals(null, ArrayHelper::getByPath($data, []));
        $this->assertEquals(null, ArrayHelper::getByPath($data, new \stdClass));

        // Test paths
        $this->assertEquals(223, ArrayHelper::getByPath($data, 'Jones.Sakura'));
        $this->assertEquals(array('Taylor' => 323), ArrayHelper::getByPath($data, 'Arthur.Lancelot.Rose'));
        $this->assertEquals(323, ArrayHelper::getByPath($data, 'Arthur.Lancelot.Rose.Taylor'));
        $this->assertEquals('Julia', ArrayHelper::getByPath($data, 'Arthur.Lancelot.Jessica.Alice'));
        $this->assertEquals(array('David' => 123,'Peter' => 'John'), ArrayHelper::getByPath($data, 'Arthur.Lancelot.Jessica.Johnny'));
        $this->assertEquals(123, ArrayHelper::getByPath($data, 'Arthur.Lancelot.Jessica.Johnny.David'));
        $this->assertEquals('John', ArrayHelper::getByPath($data, 'Arthur.Lancelot.Jessica.Johnny.Peter'));
	}

	/**
	 * Method to test getJSObject().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\HtmlHelper::getJSObject
	 * @TODO   Implement testGetJSObject().
	 */
	public function testPivot()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

    public function testPivotByKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testPivotBySort()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testPivotFromPrefix()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testPivotToPrefix()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testPivotFromTwoDimension()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testPivotToTwoDimension()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testQuery()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testSetValue()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testGetValue()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testMapKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testMerge()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
