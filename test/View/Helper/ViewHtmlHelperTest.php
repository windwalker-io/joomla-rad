<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\View\Helper;

use Windwalker\Dom\Test\AbstractDomTestCase;
use Windwalker\View\Helper\ViewHtmlHelper;

/**
 * Test class of \Windwalker\View\Helper\ViewHtmlHelper
 *
 * @since 2.1
 */
class ViewHtmlHelperTest extends AbstractDomTestCase
{
	/**
	 * testShowInfo
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\View\Helper\ViewHtmlHelper::showInfo
	 */
	public function testShowInfo()
	{
		$instance = new ViewHtmlHelper;

		$item = new \stdClass;

		$item->pk = 1;
		$item->title = 'itemFoo';

		$key   = 'title';
		$label = 'fooLabel';
		$icon  = 'barIcon';
		$link  = 'http://link.foo';
		$class = 'barClass';

		$result = $instance->showInfo($item, $key, $label, $icon, $link, $class);

		$html = <<<HTML
<div class="title barClass">
	<span class="label">
	<i class="icon-barIcon"></i>
	fooLabel
	</span>
	<span class="value"><a href="http://link.foo" >itemFoo</a></span>
</div>
HTML;

		$this->assertDomStringEqualsDomString($html, $result);
	}
}
