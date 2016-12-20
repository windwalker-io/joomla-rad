<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Data\DataSet;
use Windwalker\DataMapper\DataMapperFacade;
use Windwalker\DI\Container;
use Windwalker\Helper\LanguageHelper;
use Windwalker\Model\Model;
use Windwalker\System\JClient;
use Windwalker\Test\Joomla\MockLanguage;

/**
 * Test class of \Windwalker\Helper\LanguageHelper
 *
 * @since 2.1
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
	 * setUpBeforeClass
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-us,en;q=0.7,ja;q=0.3';
	}

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
	 * testGetLocale
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Helper\LanguageHelper::getLocale
	 */
	public function testGetLocale()
	{
		$this->assertEquals('en-GB', LanguageHelper::getLocale());
	}

	/**
	 * testGetCurrentLanguage
	 *
	 * @return  void
	 */
	public function testGetCurrentLanguage()
	{
		$langs = \JLanguageHelper::getLanguages('lang_code');

		$this->assertEquals($langs[\JFactory::getLanguage()->getTag()], LanguageHelper::getCurrentLanguage());
	}

	/**
	 * testGetLanguageProfile
	 *
	 * @return  void
	 */
	public function testGetLanguageProfile()
	{
		$langs = \JLanguageHelper::getLanguages('lang_code');

		$expected = $langs[\JFactory::getLanguage()->getTag()];

		$this->assertEquals((object) $expected, LanguageHelper::getContentLanguage('en-GB'));
		$this->assertEquals((object) $expected, LanguageHelper::getContentLanguage('en', 'sef'));
		$this->assertEquals((object) $expected, LanguageHelper::getContentLanguage(0, 'default'));
		$this->assertNull(LanguageHelper::getContentLanguage('zh-TW'));
	}

	/**
	 * testDetectLanguageFromBrowser
	 *
	 * @return  void
	 */
	public function testDetectLanguageFromBrowser()
	{
		$this->assertEquals('en-GB', LanguageHelper::detectLanguageFromBrowser());
	}

	/**
	 * testGetContentLanguages
	 *
	 * @return  void
	 */
	public function testGetContentLanguages()
	{
		$this->assertEquals(\JLanguageHelper::getLanguages(), LanguageHelper::getContentLanguages());
	}

	/**
	 * testGetSefPath
	 *
	 * @return  void
	 */
	public function testGetSefPath()
	{
		$this->assertEquals('en', LanguageHelper::getSefPath());
	}

	/**
	 * testGetInstalledLanguages
	 *
	 * @return  void
	 */
	public function testGetInstalledLanguages()
	{
		$langs = DataMapperFacade::find('#__extensions', array('type' => 'language'));

		$this->assertEquals($langs, LanguageHelper::getInstalledLanguages());

		$langs = DataMapperFacade::find('#__extensions', array('type' => 'language', 'client_id' => 0));

		$this->assertEquals($langs, LanguageHelper::getInstalledLanguages(JClient::SITE));

		$langs = DataMapperFacade::find('#__extensions', array('type' => 'language', 'client_id' => 1));

		$this->assertEquals($langs, LanguageHelper::getInstalledLanguages(JClient::ADMINISTRATOR));
	}

	/**
	 * Method to test translate().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\LanguageHelper::translate
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
	 * @covers \Windwalker\Helper\LanguageHelper::gTranslate
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
	 * @covers \Windwalker\Helper\LanguageHelper::loadAll
	 */
	public function testLoadAll()
	{
		$container = Container::getInstance();

		$mock = new MockLanguage;

		// Set Mock object into container
		$container->share('mock.language', $mock);

		// Change container key to use mock object
		LanguageHelper::setDIKey('mock.language');

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
	 * @covers \Windwalker\Helper\LanguageHelper::loadLanguage
	 */
	public function testLoadLanguage()
	{
		$container = Container::getInstance();

		// Set Mock object into container
		$mock = new MockLanguage;

		// Set Mock object into container
		$container->share('mock.language', $mock);

		// Change container key to use mock object
		LanguageHelper::setDIKey('mock.language');

		LanguageHelper::loadLanguage('com_content');
		$this->assertEquals(true, $mock->loadExecuted);
	}
}
