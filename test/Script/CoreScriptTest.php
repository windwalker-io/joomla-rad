<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Script;

use Windwalker\Script\CoreScript;
use Windwalker\Test\Joomla\MockHtmlDocument;
use Windwalker\Test\TestCase\AbstractBaseTestCase;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Script\CoreScript
 *
 * @since 2.1
 */
class CoreScriptTest extends AbstractBaseTestCase
{
	/**
	 * Property doc.
	 *
	 * @var  MockHtmlDocument
	 */
	protected $doc;

	/**
	 * setUpBeforeClass
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		TestHelper::setValue('JUri', 'base', array());

		\JFactory::getConfig()->set('live_site', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	}

	/**
	 * setUp
	 *
	 * @return  void
	 */
	public function setUp()
	{
		CoreScript::getAsset()->setDoc($doc = new MockHtmlDocument);

		$this->doc = $doc;

		$doc->reset();
		CoreScript::reset(true);
	}

	/**
	 * Method to test underscore().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Script\CoreScript::underscore
	 */
	public function testUnderscore()
	{
		CoreScript::underscore(false);

		$url = \JUri::root(true) . '/libraries/windwalker/resource/asset/js/core/underscore.js';

		$this->assertEquals($url, $this->doc->getLastScript());

		$js = <<<JS
;
_.templateSettings = { interpolate: /\{\{(.+?)\}\}/g };;
JS;

		$this->assertStringDataEquals($js, $this->doc->_script['text/javascript']);

		CoreScript::underscore(true);

		$url = \JUri::root(true) . '/libraries/windwalker/resource/asset/js/core/underscore.js';

		$this->assertEquals($url, $this->doc->getLastScript());
		$this->assertEquals(1, count($this->doc->_scripts));

		$js = <<<JS
;
_.templateSettings = { interpolate: /\{\{(.+?)\}\}/g };;
;
var underscore = _.noConflict();;
JS;

		$this->assertStringDataEquals($js, $this->doc->_script['text/javascript']);
	}

	/**
	 * Method to test underscoreString().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Script\CoreScript::underscoreString
	 */
	public function testUnderscoreString()
	{
		CoreScript::underscoreString(false);

		$url = \JUri::root(true) . '/libraries/windwalker/resource/asset/js/core/underscore.string.js';

		$this->assertEquals($url, $this->doc->getLastScript());

		$this->assertEmpty($this->doc->_script);

		CoreScript::underscoreString(true);

		$url = \JUri::root(true) . '/libraries/windwalker/resource/asset/js/core/underscore.string.js';

		$this->assertEquals($url, $this->doc->getLastScript());
		$this->assertEquals(1, count($this->doc->_scripts));

		$js = <<<JS
; (function(s) {
	var us = function(underscore)
	{
		underscore.string = underscore.string || s;
	};
	us(window._ || (window._ = {}));
	us(window.underscore || (window.underscore = {}));
})(s);;
JS;

		$this->assertStringDataEquals($js, $this->doc->_script['text/javascript']);
	}

	/**
	 * Method to test backbone().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Script\CoreScript::backbone
	 */
	public function testBackbone()
	{
		$bakDoc = \JFactory::getDocument();

		\JFactory::$document = $this->doc;

		TestHelper::setValue('JHtmlJquery', 'loaded', array());

		CoreScript::backbone(false);

		$url = \JUri::root(true) . '/libraries/windwalker/resource/asset/js/core/backbone.js';

		$this->assertEquals($url, $this->doc->getLastScript());

		$this->assertEquals(5, count($this->doc->_scripts));

		$js = <<<JS
;
_.templateSettings = { interpolate: /\{\{(.+?)\}\}/g };;
;
var underscore = _.noConflict();;
JS;

		$this->assertStringDataEquals($js, $this->doc->_script['text/javascript']);

		CoreScript::backbone(true);

		$url = \JUri::root(true) . '/libraries/windwalker/resource/asset/js/core/backbone.js';

		$this->assertEquals($url, $this->doc->getLastScript());
		$this->assertEquals(5, count($this->doc->_scripts));

		$js = <<<JS
;
_.templateSettings = { interpolate: /\{\{(.+?)\}\}/g };;
;
var underscore = _.noConflict();;
;
var backbone = Backbone.noConflict();;
JS;

		$this->assertStringDataEquals($js, $this->doc->_script['text/javascript']);

		\JFactory::$document = $bakDoc;
	}

	/**
	 * Method to test windwalker().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Script\CoreScript::windwalker
	 * @TODO   Implement testWindwalker().
	 */
	public function testWindwalker()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getAsset().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Script\CoreScript::getAsset
	 * @TODO   Implement testGetAsset().
	 */
	public function testGetAsset()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setAsset().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Script\CoreScript::setAsset
	 * @TODO   Implement testSetAsset().
	 */
	public function testSetAsset()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}