<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use \Windwalker\Helper\ModalHelper;
use \JApplicationCms;

/**
 * Test class of \Windwalker\Helper\ModalHelper
 *
 * @since {DEPLOY_VERSION}
 */
class ModalHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var \Windwalker\Helper\ModalHelper
	 */
	protected $instance;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
	}

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
	 * Method to test modal().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\ModalHelper::modal
	 */
	public function testModal()
	{
		$this->markTestSkipped('Skipped: Static proxy function of JHtmlBootstrap::modal()');
	}

	/**
	 * Method to test modalLink().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\ModalHelper::modalLink
	 */
	public function testModalLink()
	{
		$title    = 'test-modal-title';
		$selector = 'test-selector';
		$tag      = 'test';
		$id       = 'test-link';
		$class    = 'test-class';
		$onclick  = 'testClick()';
		$icon     = 'test-icon';

		// Test without $option
		$expected = <<<HTML
<a data-toggle="modal" data-target="#{$selector}" id="{$selector}_link" class="cursor-pointer">
	<i class="" title="{$title}"></i>
{$title}</a>
HTML;

		$this->assertEquals($expected, ModalHelper::modalLink($title, $selector));

		// Test with $option
		$option = array(
			'tag' => $tag,
			'id' => $id,
			'class' => $class,
			'onclick' => $onclick,
			'icon' => $icon
		);

		$expected = <<<HTML
<{$tag} data-toggle="modal" data-target="#{$selector}" id="{$id}" class="{$class} cursor-pointer" onclick="{$onclick}">
	<i class="{$icon}" title="{$title}"></i>
{$title}</{$tag}>
HTML;

		$this->assertEquals($expected, ModalHelper::modalLink($title, $selector, $option));
	}

	/**
	 * Method to test renderModal().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\ModalHelper::renderModal
	 */
	public function testRenderModal()
	{
		$selector = 'test-selector';
		$option = array(
			'title' => 'IM TITLE',
			'footer' => '<h1>IM FOOTER</h1>',
			'content' => '<h1>IM CONTENT</h1>'
		);

		$expected = <<<HTML
<div class="modal hide fade {$selector}" id="{$selector}">
<div class="modal-header">
    <button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
    <h3>{$option['title']}</h3>
</div>

<div id="{$selector}-container" class="modal-body">
    {$option['content']}
</div>

<div class="modal-footer">
    {$option['footer']}
</div>
</div>
HTML;

		$this->assertEquals($expected, ModalHelper::renderModal($selector, $option['content']), $option);
	}

	/**
	 * Method to test getQuickaddForm().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\ModalHelper::getQuickaddForm
	 * @TODO   Implement testGetQuickaddForm().
	 */
	public function testGetQuickaddForm()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
