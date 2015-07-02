<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\DI\Container;
use Windwalker\Helper\LanguageHelper;
use Windwalker\Model\Model;
use Windwalker\Test\Mock\MockLanguage;

/**
 * Test class of \Windwalker\Helper\LanguageHelper
 *
 * @since {DEPLOY_VERSION}
 */
class LanguageHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var \Windwalker\Helper\LanguageHelper
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
	 * Method to test translate().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\LanguageHelper::translate
	 */
	public function testTranslate()
	{
		if (!defined('WINDWALKER_TEST_GOOGLE_TRANSLATE') || !WINDWALKER_TEST_GOOGLE_TRANSLATE)
		{
			$this->markTestSkipped('Skip Google Translate test.');
		}

		$testParagraph = 'admin article news';

		$expectedResult = '管理員文章新聞';

		$gTranslated = LanguageHelper::translate($testParagraph, 'en', 'zh-TW');

		$this->assertEquals($expectedResult, $gTranslated);
	}

	/**
	 * Method to test gTranslate().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\LanguageHelper::gTranslate
	 */
	public function testGTranslate()
	{
		if (!defined('WINDWALKER_TEST_GOOGLE_TRANSLATE') || !WINDWALKER_TEST_GOOGLE_TRANSLATE)
		{
			$this->markTestSkipped('Skip Google Translate test.');
		}

		$testDictionary = array(
			'admin' => '管理員',
			'article' => '文章',
			'news' => '新聞'
		);

		foreach($testDictionary as $word => $translation)
		{
			$gTranslated = LanguageHelper::gTranslate($word, 'en', 'zh-TW');

			$this->assertEquals($translation, $gTranslated);
		}
	}

	/**
	 * Method to test loadAll().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\LanguageHelper::loadAll
	 */
	public function testLoadAll()
	{
		$container = Container::getInstance();

		$mock = new MockLanguage;

		// Set Mock object into container
		$container->share('mock.language', $mock);

		// Change container key to use mock object
		LanguageHelper::setKey('mock.language');

		// Test loadAll method
		LanguageHelper::loadAll('en-GB', 'com_blog');
		$this->assertEquals(true,  $mock->loadExecuted);

		// Reset load flag
		$mock->loadExecuted = false;

		// Test is folder not exist
		LanguageHelper::loadAll('unknown-lang', 'com_blog');
		$this->assertEquals(false, $mock->loadExecuted);
	}

	/**
	 * Method to test loadLanguage().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\LanguageHelper::loadLanguage
	 */
	public function testLoadLanguage()
	{
		$container = Container::getInstance();

		// Set Mock object into container
		$mock = new MockLanguage;

		// Set Mock object into container
		$container->share('mock.language', $mock);

		// Change container key to use mock object
		LanguageHelper::setKey('mock.language');

		LanguageHelper::loadLanguage('com_content');
		$this->assertEquals(true, $mock->loadExecuted);
	}
}
