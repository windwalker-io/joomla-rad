<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Dom\Format\HtmlFormatter;
use Windwalker\Dom\Test\AbstractDomTestCase;
use Windwalker\Helper\ModalHelper;

/**
 * Test class of \Windwalker\Helper\ModalHelper
 *
 * @since 2.1
 */
class ModalHelperTest extends AbstractDomTestCase
{
	/**
	 * Original loaded array
	 *
	 * @var  boolean[]
	 */
	protected $originalLoaded;

	/**
	 * Property reflectedLoaded.
	 *
	 * @var \ReflectionProperty
	 */
	protected $reflectedLoaded;

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
		$this->reflectedLoaded = new \ReflectionProperty('JHtmlBootstrap', 'loaded');

		$this->reflectedLoaded->setAccessible(true);

		$this->originalLoaded = $this->reflectedLoaded->getValue();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		$this->reflectedLoaded->setValue($this->originalLoaded);

		$this->reflectedLoaded->setAccessible(false);
	}

	/**
	 * Method to test modalLink().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\ModalHelper::modalLink
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
	{$title}
</a>
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
	{$title}
</{$tag}>
HTML;

		$this->assertEquals($expected, ModalHelper::modalLink($title, $selector, $option));
	}

	/**
	 * Method to test renderModal().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\ModalHelper::renderModal
	 */
	public function testRenderModal()
	{
		// Reset Loaded
		$this->reflectedLoaded->setValue($this->originalLoaded);

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

		$this->assertEquals($expected, ModalHelper::renderModal($selector, $option['content'], $option));
	}

	/**
	 * Method to test getQuickaddForm().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\ModalHelper::getQuickaddForm
	 */
	public function testGetQuickaddForm()
	{
		$id = 'stubQuickaddForm';

		$pathToQuickaddForm = 'libraries/windwalker/test/Helper/stub/stubQuickaddForm.xml';

		$text = \JText::_('LIB_WINDWALKER_QUICKADD_HOTKEY_DESC');

		$expectedForm = <<<HTML
<div class="alert alert-info">$text</div><div class="control-group" id="{$id}_title-wrap">
	<div class="control-label">
		<label id="{$id}_title-lbl" for="{$id}_title" class="required"> 
stub_label<span class="star">&#160;*</span></label>
	</div>
	<div class="controls">
		<input type="text" name="{$id}[title]" id="{$id}_title" value="" class="input-xlarge required" required aria-required="true" />
	</div>
</div>
HTML;

		$this->assertStringDataEquals($expectedForm, ModalHelper::getQuickaddForm($id, $pathToQuickaddForm));
	}
}
