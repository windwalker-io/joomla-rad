<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Helper\JContentHelper;

/**
 * Test class of Windwalker\Helper\JContentHelper
 *
 * @since 2.0
 */
class JContentHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, open a network connection.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['REQUEST_URI'] = '/index.php';
		$_SERVER['SCRIPT_NAME'] = '/index.php';
	}

	/**
	 * Method to test getArticleLink().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\JContentHelper::getArticleLink
	 */
	public function testGetArticleLink()
	{
		JContentHelper::$articleRouteHandler = array($this, 'getArticleRoute');

		$slug = '12:article-foobar';
		$categorySlug = '34:category-foobar';

		$this->assertSame('article/foobar', JContentHelper::getArticleLink($slug, $categorySlug));
		$this->assertSame('http://example.com/article/foobar', JContentHelper::getArticleLink($slug, $categorySlug, true));
	}

	/**
	 * Method to test getCategoryLink().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\JContentHelper::getCategoryLink
	 */
	public function testGetCategoryLink()
	{
		JContentHelper::$categoryRouteHandler = array($this, 'getCategoryRoute');

		$categoryId = '34';

		$this->assertSame('category/foobar', JContentHelper::getCategoryLink($categoryId));
		$this->assertSame('http://example.com/category/foobar', JContentHelper::getCategoryLink($categoryId, true));
	}

	public function getArticleRoute($slug, $categorySlug)
	{
		return 'article/foobar';
	}

	public function getCategoryRoute($categoryId)
	{
		return 'category/foobar';
	}
}
