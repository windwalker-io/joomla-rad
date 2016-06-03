<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model;

use Windwalker\Data\Data;
use Windwalker\Test\Model\Stub\StubModelAdvanced;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Model\AbstractAdvancedModel
 *
 * @since 2.1
 */
class AbstractAdvancedModelTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Property originComponents.
	 *
	 * @var  \stdClass[]
	 */
	protected $originComponents;

	/**
	 * Property reflectedJComponentHelper.
	 *
	 * @var \ReflectionProperty
	 */
	protected $reflectedJComponentHelper;

	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$option = 'com_advencedmodeltest';

		$this->reflectedJComponentHelper = new \ReflectionProperty('JComponentHelper', 'components');

		$this->reflectedJComponentHelper->setAccessible(true);

		$this->originComponents = $this->reflectedJComponentHelper->getValue();

		$newComponents = $this->originComponents;

		$newComponents[$option] = (object) array(
			'id' => 9999999,
			'option' => $option,
			'params' => new \JRegistry(array(
				'foo' => 'bar',
				'bar' => 'foo',
				'foobar' => 123,
			)),
			'enabled' => true,
		);

		$this->reflectedJComponentHelper->setValue($newComponents);
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		$this->reflectedJComponentHelper->setValue($this->originComponents);
		$this->reflectedJComponentHelper->setAccessible(false);

		$db = \JFactory::getDbo();

		// Remove test data
		$sql = 'DELETE FROM `#__categories` WHERE `id` IN (10008, 10009)';

		$db->setQuery($sql)->execute();
	}
	
	/**
	 * Method to test getParams().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\AbstractAdvancedModel::getParams
	 */
	public function testGetParams()
	{
		$model = new StubModelAdvanced;

		// Test case #1: Make sure initial params is null
		$this->assertNull(TestHelper::getValue($model, 'params'));

		$params = $model->getParams();

		$expectParams = new \JRegistry(array(
			'foo' => 'bar',
			'bar' => 'foo',
			'foobar' => 123,
		));

		// Test case #2: Test given component params
		$this->assertEquals($expectParams, $params);
	}

	/**
	 * Method to test getCategory().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\AbstractAdvancedModel::getCategory
	 */
	public function testGetCategory()
	{
		// Install test data
		$db = \JFactory::getDbo();
		$sqls = file_get_contents(__DIR__ . '/sql/install.advanced-model.sql');

		foreach ($db->splitSql($sqls) as $sql)
		{
			$sql = trim($sql);

			if (!empty($sql))
			{
				$db->setQuery($sql)->execute();
			}
		}

		$model = new StubModelAdvanced();

		$category = $model->getCategory(10008);

		$this->assertEquals('10008', $category->id);
		$this->assertEquals('foo', $category->title);

		$category = $model->getCategory(10009);

		$this->assertEquals('10009', $category->id);
		$this->assertEquals('bar', $category->title);

		$category = $model->getCategory(20000);

		$this->assertNull($category->id);
		$this->assertNull($category->title);
	}

	/**
	 * testGetCategoryWithAssignedProperty
	 *
	 * @return  void
	 */
	public function testGetCategoryWithAssignedProperty()
	{
		$model = new StubModelAdvanced;

		$category = new Data(array('id' => 30001, 'title' => 'foobar'));

		TestHelper::setValue($model, 'category', $category);

		$this->assertSame($category, $model->getCategory());
	}
}
