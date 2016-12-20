<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model;

use Windwalker\DI\Container;
use Windwalker\Model\ListModel;
use Windwalker\String\StringInflector;
use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\Model\Stub\WindwalkerModelStubList;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Model\ListModel
 *
 * @since 2.1
 */
class ListModelTest extends AbstractDatabaseTestCase
{
	/**
	 * Install test sql when setUp.
	 *
	 * @return  string
	 */
	public static function getInstallSql()
	{
		return __DIR__ . '/sql/install.listmodel.sql';
	}

	/**
	 * Uninstall test sql when tearDown.
	 *
	 * @return  string
	 */
	public static function getUninstallSql()
	{
		return __DIR__ . '/sql/install.listmodel.sql';
	}

	/**
	 * setUpBeforeClass
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		// Write filter.xml
		$formPath = JPATH_BASE . '/components/com_stub/model/form/posts';

		if (!is_dir($formPath))
		{
			mkdir($formPath, 0777, true);
		}

		copy(__DIR__ . '/form/posts/filter.xml', $formPath . '/filter.xml');
	}

	/**
	 * tearDownAfterClass
	 *
	 * @return  void
	 */
	public static function tearDownAfterClass()
	{
		parent::tearDownAfterClass();

		// Remove filter.xml
		$formPath = JPATH_BASE . '/components/com_stub/model/form/posts';

		unlink($formPath . '/filter.xml');
		static::removeDirectory(JPATH_BASE . '/components/com_stub');
	}
	
	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::__construct
	 */
	public function test__construct()
	{
		$listModel = new ListModel;
		$option = TestHelper::getValue($listModel, 'option');
		$viewList = TestHelper::getValue($listModel, 'viewList');
		$viewItem = TestHelper::getValue($listModel, 'viewItem');
		$expectedViewItem = StringInflector::getInstance()->toSingular($viewList);

		$this->assertNull(TestHelper::getValue($listModel, 'orderCol'));
		$this->assertEquals(array('*'), TestHelper::getValue($listModel, 'filterFields'));
		$this->assertSame(Container::getInstance($option), TestHelper::getValue($listModel, 'container'));
		$this->assertEquals($listModel->getName(), $viewList);
		$this->assertEquals($expectedViewItem, $viewItem);
	}

	/**
	 * Method to test __construct() with given mock container instance.
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::__construct
	 */
	public function test__constructWithGivenContainer()
	{
		$config = array(
			'name' => 'foo',
		);

		$model = new ListModel($config, $this->getConstructContainer($config));

		$this->assertEquals(Container::getInstance()->get('joomla.config')->get('list_limit'), $model->get('list.limit'));
	}

	/**
	 * Method to test __construct() with given mock container instance.
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::__construct
	 */
	public function test__constructWithGivenConfigAndContainer()
	{
		$config = array(
			'name' => 'foo',
			'order_column' => array('foobar'),
			'filter_fields' => array('foo', 'bar'),
			'view_list' => 'foobars',
			'view_item' => 'foobar',
		);
		$orderCol = array_merge($config['filter_fields'], array('*'));

		$listModel = new ListModel($config, $this->getConstructContainer($config));
		$viewList = TestHelper::getValue($listModel, 'viewList');
		$viewItem = TestHelper::getValue($listModel, 'viewItem');

		$this->assertEquals($config['order_column'], TestHelper::getValue($listModel, 'orderCol'));
		$this->assertEquals($orderCol, TestHelper::getValue($listModel, 'filterFields'));
		$this->assertEquals($config['view_list'], $viewList);
		$this->assertEquals($config['view_item'], $viewItem);
	}

	/**
	 * getConstructContainer
	 *
	 * @param array $config
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getConstructContainer(array $config = null)
	{
		$jconfig = Container::getInstance()->get('joomla.config');

		$container = $this->getMockBuilder('Windwalker\DI\Container')
			->disableOriginalConstructor()
			->setMethods(array('get'))
			->getMock();

		$config = $this->getMockBuilder('JRegistry')
			->disableOriginalConstructor()
			->setMethods(array('get'))
			->getMock();

		$config->expects($this->any())
			->method('get')
			->with('list_limit')
			->will($this->returnValue($jconfig->get('list_limit')));

		$container->expects($this->any())
			->method('get')
			->will($this->returnCallback(function($arguments)
			{
				$result = Container::getInstance()->get($arguments);

				return $result;
			}));

		return $container;
	}

	/**
	 * Method to test getTable().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::getTable
	 */
	public function testGetTable()
	{
		$listModel = new ListModel;

		$this->assertInstanceOf('JTableContent', $listModel->getTable('Content', 'JTable'));

		$config = array(
			'view_item' => 'content',
			'prefix' => 'J',
		);

		$listModel = new ListModel($config);

		$this->assertInstanceOf('JTableContent', $listModel->getTable());
	}

	/**
	 * Method to test getItems().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::getItems
	 */
	public function testGetItems()
	{
		$listModel = new WindwalkerModelStubList;
		$query = $listModel->getDb()->getQuery(true);

		$query->select('`id`, `foo`')
			->from('#__test_table')
			->where('`type` = "animal"');

		$listModel->setListQuery($query);
		$listModel->quickCleanCache();

		$expected = array(
			(object) array('id' => '2', 'foo' => 'bag'),
			(object) array('id' => '3', 'foo' => 'bah'),
			(object) array('id' => '8', 'foo' => 'bar'),
			(object) array('id' => '11', 'foo' => 'bax'),
		);

		$this->assertEquals($expected, $listModel->getItems());

		// Test cached items
		$query->clear()->select('`id`, `foo`, `type`')->from('#__test_table');
		$listModel->setListQuery($query);

		$this->assertEquals($expected, $listModel->getItems());
	}

	/**
	 * testPopulateState
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::populateState
	 */
	public function testPopulateState()
	{
		$model = new WindwalkerModelStubList(array('name' => 'stubs'), Container::getInstance());
		$model->userState = array(
			'com_windwalker.stublist.list' => array(
				'limit' => 100,
				'ordering' => 'foo',
				'direction' => 'DESC'
			),
			'com_windwalker.stublist.limitstart' => 200,
			'com_windwalker.stublist.filter' => array(
				'flower.sakura' => 'foo',
				'test.foo' => 'bar'
			)
		);

		TestHelper::invoke($model, 'populateState');

		$this->assertEquals(100, $model['list.limit']);
		$this->assertEquals(200, $model['list.start']);
		$this->assertEquals('foo', $model['list.ordering']);
		$this->assertEquals('DESC', $model['list.direction']);

		$model->userState['com_windwalker.stublist.list']['limit'] = null;

		TestHelper::invoke($model, 'populateState');

		$config = Container::getInstance()->get('joomla.config');

		$this->assertEquals($config->get('list_limit'), $model['list.limit']);

		// Set limit 0 should get NULL
		$model->userState['com_windwalker.stublist.list']['limit'] = 0;

		TestHelper::invoke($model, 'populateState');

		$this->assertEquals(null, $model['list.limit']);
	}

	/**
	 * Method to test getPagination().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::getPagination
	 */
	public function testGetPagination()
	{
		$listModel = new WindwalkerModelStubList;
		$expected = new \JPagination(4, 0, 3);
		$db = $listModel->getDb();
		$query = $db->getQuery(true);

		TestHelper::setValue($expected, 'app', null);

		$query->select('*')
			->from('#__test_table')
			->where('`type` = "animal"');

		$listModel->setListQuery($query);

		$listModel->set('list.limit', 3);
		$listModel->quickCleanCache();
		$pagination = $listModel->getPagination();

		// There is something weird that we cannot compare JPagination::$app->input
		// So we remove it and just compare other properties
		TestHelper::setValue($pagination, 'app', null);

		$this->assertEquals($expected, $pagination);

		// Test cached result
		$newData = (object) array('foo' => 'bar2', 'type' => 'exception');
		$db->insertObject('#__test_table', $newData);

		$pagination = $listModel->getPagination();
		TestHelper::setValue($pagination, 'app', null);

		$this->assertEquals($expected, $pagination);

		$db->setQuery(sprintf('DELETE FROM #__test_table WHERE id = %d', $db->insertid()))->execute();
	}

	/**
	 * Method to test getList().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::getList
	 */
	public function testGetList()
	{
		$query = \JFactory::getDbo()->getQuery(true);

		$query->select('`foo`')
			->from('#__test_table')
			->where('`type` = "fruit"');

		$expected = array(
			(object) array('foo' => 'bad'),
			(object) array('foo' => 'bak'),
			(object) array('foo' => 'bal'),
			(object) array('foo' => 'bat'),
			(object) array('foo' => 'bay'),
			(object) array('foo' => 'baz'),
		);

		$listModel = new ListModel;
		$testQuery = clone $query;

		$this->assertEquals($expected, $listModel->getList($testQuery));

		$expected3 = array_slice($expected, 2, 3);
		$testQuery = clone $query;

		$this->assertEquals($expected3, $listModel->getList($testQuery, 2, 3));
	}

	/**
	 * Method to test getListCount().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::getListCount
	 */
	public function testGetListCount()
	{
		$listModel = new ListModel;
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('`id`, `foo`, `type`')
			->from('#__test_table')
			->where('`type` = "fruit"');

		$count = $listModel->getListCount($query);

		$this->assertEquals(6, $count);

		$query = $db->getQuery(true);

		$query->select('`id`, `foo`, `type`')
			->from('#__test_table')
			->group('type');

		$count = $listModel->getListCount($query);

		$this->assertEquals(3, $count);
	}

	/**
	 * Method to test getTotal().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::getTotal
	 */
	public function testGetTotal()
	{
		$listModel = new WindwalkerModelStubList;
		$query = $listModel->getDb()->getQuery(true);

		$query->select('*')->from('#__test_table');

		$listModel->setListQuery($query);

		$this->assertEquals(13, $listModel->getTotal());
	}

	/**
	 * Method to test getStart().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::getStart
	 */
	public function testGetStart()
	{
		$listModel = new WindwalkerModelStubList;
		$db = $listModel->getDb();
		$query = $db->getQuery(true);

		$query->select('*')->from('#__test_table');

		$listModel->setListQuery($query);

		$listModel->quickCleanCache();

		// Test case #1: 0 < start < total - limit
		$listModel->getState()->set('list.start', 5);
		$listModel->getState()->set('list.limit', 7);

		$this->assertEquals(5, $listModel->getStart());

		// Test case #2: Test cached result
		$newData = (object) array('foo' => 'bar2', 'type' => 'exception');
		$db->insertObject('#__test_table', $newData);
		$this->assertEquals(5, $listModel->getStart());

		$listModel->quickCleanCache();

		// Test case #3: total - limit < start < total
		$listModel->getState()->set('list.start', 9);

		$this->assertEquals(7, $listModel->getStart());
	}

	/**
	 * Method to test getBatchForm().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::getBatchForm
	 */
	public function testGetBatchForm()
	{
		$listModel = new WindwalkerModelStubList;
		$form = new \JForm('stublistmodel');

		TestHelper::setValue($listModel, 'context', 'model.foo.bar');

		$listModel->setLoadedForm($form);

		$this->assertSame($form, $listModel->getBatchForm());

		$listModel->setLoadedForm(new \RuntimeException('test runtime exception'));

		$this->assertEquals(new \JForm('model.foo.bar.batch'), $listModel->getBatchForm());
	}

	/**
	 * Method to test getFilterForm().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::getFilterForm
	 */
	public function testGetFilterForm()
	{
		$listModel = new WindwalkerModelStubList;
		$form = new \JForm('stublistmodel');

		TestHelper::setValue($listModel, 'context', 'model.foo.bar');

		$listModel->setLoadedForm($form);

		$this->assertSame($form, $listModel->getFilterForm());

		$listModel->setLoadedForm(new \RuntimeException('test runtime exception'));

		$this->assertEquals(new \JForm('model.foo.bar.filter'), $listModel->getFilterForm());
	}

	/**
	 * Method to test getSearchFields().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::getSearchFields
	 */
	public function testGetSearchFields()
	{
		$config = array(
			'prefix' => 'stub',
			'name' => 'posts',
		);
		$listModel = new ListModel($config);
		$expected = array('post.title', 'post.category_title');

		$this->assertEquals($expected, $listModel->getSearchFields());
	}

	/**
	 * Method to test getUserStateFromRequest().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::getUserStateFromRequest
	 */
	public function testGetUserStateFromRequest()
	{
		$config = array(
			'prefix' => 'foo',
			'name' => 'bar',
		);
		$listModel = new ListModel($config);
		$app = $listModel->getContainer()->get('app');
		$input = $listModel->getContainer()->get('app')->input;

		$app->clearUserState();

		# Test case #1: get user state with input value
		$input->set('list-model-foo-xyz-1', 123);
		$input->set('limitstart', 100);

		$value = $listModel->getUserStateFromRequest('test.foo.xyz', 'list-model-foo-xyz-1');

		$this->assertEquals(123, $value);
		$this->assertEquals(123, $app->getUserState('test.foo.xyz'));
		$this->assertEquals(0, $input->get('limitstart'));

		$app->clearUserState();

		# Test case #2: get user state without input value (use default value)
		$value = $listModel->getUserStateFromRequest('test.foo.xyz', 'list-model-foo-xyz-2', 223);

		$this->assertEquals(223, $value);
		$this->assertNull($app->getUserState('test.foo.xyz'));

		$app->clearUserState();

		# Test case #3: get user state without input value (use old user state value)
		$app->setUserState('test.foo.xyz', 323);

		$value = $listModel->getUserStateFromRequest('test.foo.xyz', 'list-model-foo-xyz-3', 423);

		$this->assertEquals(323, $value);
	}

	/**
	 * Method to test addTable().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::addTable
	 */
	public function testAddTable()
	{
		$config = array(
			'prefix' => 'foo',
			'name'   => 'bar',
		);

		$listModel = new ListModel($config);

		$mock = $this->getMockBuilder('\Windwalker\Model\Helper\QueryHelper')
			->setMethods(array('addTable'))
			->getMock();

		$mock->expects($this->once())
			->method('addTable')
			->with('alias', '#__foobar', 'foo=123', 'LEFT');

		$listModel->setQueryHelper($mock);

		$listModel->removeTable('alias');

		$listModel->addTable('alias', '#__foobar', 'foo=123', 'LEFT');
	}

	/**
	 * Method to test removeTable().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\ListModel::removeTable
	 */
	public function testRemoveTable()
	{
		$config = array(
			'prefix' => 'foo',
			'name' => 'bar',
		);
		$listModel = new ListModel($config);

		$mock = $this->getMockBuilder('\Windwalker\Model\Helper\QueryHelper')
			->setMethods(array('removeTable'))
			->getMock();

		$listModel->setQueryHelper($mock);

		$mock->expects($this->once())
			->method('removeTable')
			->with('alias');

		$listModel->removeTable('alias');
	}

	/**
	 * Delete directory recursively (will delete all files in the directory)
	 *
	 * @param string $dir
	 *
	 * @return bool
	 */
	protected static function removeDirectory($dir)
	{
		$files = array_diff(scandir($dir), array('.', '..'));

		foreach ($files as $file)
		{
			(is_dir("$dir/$file")) ? static::removeDirectory("$dir/$file") : unlink("$dir/$file");
		}

		return rmdir($dir);
	}
}
