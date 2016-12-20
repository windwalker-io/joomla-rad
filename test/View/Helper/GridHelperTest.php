<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\View\Helper;

use Joomla\Registry\Registry;
use Windwalker\Dom\Test\AbstractDomTestCase;
use Windwalker\View\Helper\GridHelper;
use Windwalker\View\Html\GridView;

/**
 * Test class of \Windwalker\View\Helper\GridHelper
 *
 * @since 2.1
 */
class GridHelperTest extends AbstractDomTestCase
{

	/**
	 * Property instance.
	 *
	 * @var GridHelper
	 */
	protected $instance;

	/**
	 * Property view object.
	 *
	 * @var \Jview
	 */
	protected $view;

	/**
	 * Property config.
	 *
	 * @var mixed
	 */
	protected $config;

	/**
	 * Property current.
	 *
	 * @var object
	 */
	protected $current;

	/**
	 * setUp
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		$this->config = array(
			'option'    => 'testOption',
			'view_name' => 'testView',
			'view_item' => 'testViewItem',
			'view_list' => 'testViewList',
			'order_column'   => 'testTitle',
			'order_table_id' => 'test.order_table_id',
			'field' => array(
				'pk' => 'testId',
				'title' => 'testTitle',
				'date' => 'testDate'
			),
		);

		$this->view = new GridView();

		$this->view->state = new Registry();

		$this->view->state->set('list.ordering', 'testTitle');
		$this->view->state->set('list.orderCol', 'testTitle');
		$this->view->state->set('list.direction', 'asc');
	}

	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::__construct
	 */
	public function test__construct()
	{
		$grid = new GridHelper($this->view, $this->config);

		$this->assertSame('testOption.testViewItem', $grid->context);

		$expectedState = $this->view->state;

		$this->assertSame($expectedState, $grid->state);
		$this->assertTrue($expectedState->get('list.saveorder'));

		$field = $grid->config->get('field');

		$field = (array) $field;

		$this->assertSame('testId', $field['pk']);
		$this->assertSame('testTitle', $field['title']);
		$this->assertSame('testDate', $field['date']);
	}

	/**
	 * testRegisterTableSort
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::registerTableSort
	 */
	public function testRegisterTableSort()
	{
		$this->view->state->set('list.ordering', 'testTitle');
		$this->view->state->set('list.orderCol', 'testTitle');

		$grid = new GridHelper($this->view, $this->config);

		$this->assertTrue($grid->registerTableSort());

		$this->view->state->set('list.orderCol', 'testTitId');

		$grid = new GridHelper($this->view, $this->config);

		$this->assertFalse($grid->registerTableSort());
	}

	/**
	 * testSortTitle
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::sortTitle
	 */
	public function testSortTitle()
	{
		$this->view->state->set('list.direction', 'DESC');

		$grid = new GridHelper($this->view, $this->config);

		$result = $grid->sortTitle('Sort Test Label', 'testTitle');

		$this->assertRegExp('(data-direction="ASC")', $result);
		$this->assertRegExp('(data-name="Sort Test Label")', $result);
		$this->assertRegExp('(data-order="testTitle")', $result);

		$this->view->state->set('list.direction', 'ASC');

		$grid = new GridHelper($this->view, $this->config);

		$result = $grid->sortTitle('Sort Test Label', 'testTitle');

		$this->assertRegExp('(data-direction="DESC")', $result);
	}

	/**
	 * testOrderTitle
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::orderTitle
	 */
	public function testOrderTitle()
	{
		$this->config['order_column'] = 'testFoo';

		$this->view->state->set('list.direction', 'asc');
		$this->view->state->set('list.orderCol', '');

		$grid = new GridHelper($this->view, $this->config);

		$result = $grid->orderTitle();

		$this->assertRegExp('(data-direction="ASC")', $result);
		$this->assertRegExp('(data-order="testFoo")', $result);
	}

	/**
	 * testSetItem
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::setItem
	 */
	public function testSetItem()
	{
		$item = new \stdClass;
		$item->id = 138;
		$item->title = 'one three eight';

		$this->config['ignore_access'] = false;

		$grid = new GridHelper($this->view, $this->config);

		$result = $grid->setItem($item, 1);

		$this->assertTrue($result);

		$this->config['ignore_access'] = true;

		$grid = new GridHelper($this->view, $this->config);

		$result = $grid->setItem($item, 1);

		$this->assertFalse((bool) $result->state->get('access')->canEdit);
		$this->assertTrue($result->state->get('access')->canCheckin);
		$this->assertFalse($result->state->get('access')->canChange);
		$this->assertFalse($result->state->get('access')->canEditOwn);
	}

	/**
	 * testDragSort
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::dragSort
	 */
	public function testDragSort()
	{
		$this->config['field']['ordering'] = $ordering = 'foo';

		$grid = new GridHelper($this->view, $this->config);

		$item = new \stdClass;

		$item->$ordering = 'foobar';

		$grid->setItem($item, 1);

		$result = $grid->dragSort();

		$this->assertRegExp('(span class="sortable-handler")', $result);
		$this->assertRegExp('(input type="text".*value="foobar")', $result);

		$grid->state->set('access.canChange', false);

		$result = $grid->dragSort();

		$this->assertRegExp('(span class="sortable-handler inactive")', $result);
	}

	/**
	 * testCheckbox
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::checkbox
	 */
	public function testCheckbox()
	{
		$grid = new GridHelper($this->view, $this->config);

		$item = new \stdClass;
		$item->testId = 123;

		$grid->setItem($item, 1);

		$result = $grid->checkbox();

		$this->assertRegExp('(input type="checkbox" id="cb1".*value="123")', $result);
	}

	/**
	 * testEditTitle
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::editTitle
	 */
	public function testEditTitle()
	{
		$grid = new GridHelper($this->view, $this->config);

		$item = new \stdClass;
		$item->testId = 123;
		$item->testTitle = 'one two three';

		$grid->setItem($item, 1);

		$result = $grid->editTitle();

		$this->assertRegExp('(href="\?option=testOption&task=testViewItem.edit.edit&testId=123" >one two three)', $result);

		$grid->state->set('access.canEdit', false);
		$grid->state->set('access.canEditOwn', false);

		$result = $grid->editTitle();

		$this->assertSame('one two three', $result);
	}

	/**
	 * testEditButton
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::editEditButton
	 */
	public function testEditButton()
	{
		$grid = new GridHelper($this->view, $this->config);

		$item = new \stdClass;
		$item->testId = 123;
		$item->testTitle = 'one two three';

		$grid->setItem($item, 1);

		$result = $grid->editButton('foobar', 'FooEdit', $item->testId);

		$this->assertRegExp('(a href.*option=testOption&task=FooEdit&testId=123" class="btn btn-mini">foobar<\/a>)', $result);
	}

	/**
	 * testForeignLink
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::foreignLink
	 */
	public function testForeignLink()
	{
		$this->config['field']['pk'] = 'id';

		$grid = new GridHelper($this->view, $this->config);

		$item = new \stdClass;
		$item->id = 123;
		$item->fk = 1;
		$item->title = 'one two three';

		$grid->setItem($item, 1);

		$result = $grid->foreignLink('viewFK', 'editForeign', $item->fk);

		$this->assertRegExp('(option=testOption&task=editForeign&id=1" class="text-muted muted" target="_blank">viewFK)', $result);

		$result = $grid->foreignLink('viewFK', 'editForeign', '');

		$this->assertEmpty($result);
	}

	/**
	 * testState
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::state
	 */
	public function testState()
	{
		$grid = new GridHelper($this->view, $this->config);

		$item = new \stdClass;
		$item->id = 123;
		$item->title = 'one two three';

		$grid->setItem($item, 1);

		$result = $grid->state('testFoo.state.');

		$this->assertRegExp('(onclick="return listItemTask\(\'cb1\',\'testFoo.state.publish\'\)")', $result);
	}

	/**
	 * testCheckoutButton
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::checkoutButton
	 */
	public function testCheckoutButton()
	{
		$grid = new GridHelper($this->view, $this->config);

		$item = new \stdClass;
		$item->id = 123;
		$item->title = 'one two three';
		$item->author = 'Sakura';
		$item->checkout = '2015-08-24 08:00:00';

		$date = \JHtml::_('date', $item->checkout, 'l, d F Y') . '<br />' . \JHtml::_('date', $item->checkout, 'H:i');
		$date = htmlspecialchars($date);

		$grid->setItem($item, 1);

		$result = $grid->checkoutButton();

		$this->assertEmpty($result);

		$this->config['field']['pk'] = 'id';
		$this->config['field']['checked_out'] = 'id';
		$this->config['field']['author_name'] = 'author';
		$this->config['field']['checked_out_time'] = 'checkout';

		$grid = new GridHelper($this->view, $this->config);
		$grid->setItem($item, 1);
		$result = $grid->checkoutButton();

		$html = <<<HTML
<a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="return listItemTask('cb1','testViewList.check.checkin')" title="&lt;strong&gt;JLIB_HTML_CHECKIN&lt;/strong&gt;&lt;br /&gt;Sakura&lt;br /&gt;{$date}"><span class="icon-checkedout"></span></a>
HTML;
		
		$this->assertStringDataEquals($html, $result);

//		$this->assertRegExp('(onclick="return listItemTask\(\'cb1\',\'testViewList.check.checkin\'\)")', $result);
//		$this->assertRegExp('(Sakura)', $result);
//		$this->assertRegExp('(Monday, 24 August 2015.*16:00)', $result);
	}

	/**
	 * testCreatedDate
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::createdDate
	 */
	public function testCreatedDate()
	{
		$this->config['field']['created'] = 'createdDate';

		$grid = new GridHelper($this->view, $this->config);

		$item = new \stdClass;
		$item->id = 123;
		$item->title = 'one two three';
		$item->createdDate = '2015-01-01 08:00:00';

		$grid->setItem($item, 1);

		$result = $grid->createdDate('l, d F Y, g:i:s A');

		// Expected Date
		$date = new \JDate('2015-01-01 08:00:00');
		$date->setTimezone(new \DateTimeZone(\JFactory::getConfig()->get('offset', 'UTC')));

		$this->assertEquals($date->format('l, d F Y, g:i:s A', true), $result);
	}

	/**
	 * testLanguage
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::language
	 */
	public function testLanguage()
	{
		$this->config['field']['language'] = 'lang';
		$this->config['field']['lang_title'] = 'langTitle';

		$grid = new GridHelper($this->view, $this->config);

		$item = new \stdClass;

		$item->lang = 'Japan';
		$item->langTitle = 'nippon';

		$grid->setItem($item, 1);
		$result = $grid->language();

		$this->assertEquals('nippon', $result);

		$item->lang = '*';

		$grid->setItem($item, 1);
		$result = $grid->language();

		$this->assertEquals('All', $result);
	}

	/**
	 * testBooleanIcon
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::booleanIcon
	 */
	public function testBooleanIcon()
	{
		$grid = new GridHelper($this->view, $this->config);

		$result = $grid->booleanIcon(true);

		$this->assertEquals('<i class="icon-publish"></i>', $result);

		$result = $grid->booleanIcon(false);

		$this->assertEquals('<i class="icon-unpublish"></i>', $result);
	}

	/**
	 * testCan
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::can
	 */
	public function testCan()
	{
		$grid = new GridHelper($this->view, $this->config);

		$result = $grid->can('edit');

		$this->assertTrue($result);

		$this->view->state->set('access.canEdit', false);

		$grid = new GridHelper($this->view, $this->config);

		$result = $grid->can('edit');

		$this->assertFalse($result);
	}

	/**
	 * testEscape
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\GridHelper::escape
	 */
	public function testEscape()
	{
		$grid = new GridHelper($this->view, $this->config);

		$result = $grid->escape('<strong>考えすぎると、人間は臆病になる。</strong>');

		$this->assertEquals('&lt;strong&gt;考えすぎると、人間は臆病になる。&lt;/strong&gt;', $result);
	}
}
