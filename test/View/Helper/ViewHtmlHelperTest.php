<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\View\Helper;

use Windwalker\View\Helper\ViewHtmlHelper;

/**
 * Test class of \Windwalker\View\Helper\ToolbarHelper
 *
 * @since {DEPLOY_VERSION}
 */
class ViewHtmlHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * testShowInfo
	 *
	 * @return  void
	 *
	 * @covers Windwalker\View\Helper\ViewHtmlHelper::showInfo
	 */
	public function testShowInfo()
	{
		$instance = new ViewHtmlHelper;

		$item = new \stdClass();

		$item->pk = 1;
		$item->title = 'itemFoo';

		$key = 'title';
		$label = 'fooLabel';
		$icon = 'barIcon';
		$link = 'http://link.foo';
		$class = 'barClass';

		$result = $instance->showInfo($item, $key, $label, $icon, $link, $class);

		$this->assertRegExp('(class="title barClass")', $result);

		$this->assertRegExp('(<span class="label">
            <i class="icon-barIcon"><\/i>
            fooLabel
            <\/span>)', $result);

		$this->assertRegExp('(<span class="value"><a href="http:\/\/link.foo" >itemFoo<\/a><\/span>)', $result);
	}
}
