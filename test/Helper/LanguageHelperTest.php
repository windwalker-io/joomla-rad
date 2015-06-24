<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use \Windwalker\Helper\LanguageHelper;

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
	 * @TODO   Implement testLoadAll().
	 */
	public function testLoadAll()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test loadLanguage().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\LanguageHelper::loadLanguage
	 * @TODO   Implement testLoadLanguage().
	 */
	public function testLoadLanguage()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
