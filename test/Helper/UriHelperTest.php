<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Helper\UriHelper;

/**
 * Test class of UriHelper.
 *
 * @since {DEPLOY_VERSION}
 */
class UriHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$_SERVER['HTTP_HOST'] = 'php.localhost';
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		unset($_SERVER['HTTP_HOST']);
	}

	/**
	 * The method to test UriHelper::base64 in decoding cases.
	 *
	 * @param string $expected
	 * @param string $url
	 *
	 * @return void
	 *
	 * @dataProvider decodedUrlDataProvider
	 * @covers       Windwalker\Helper\UriHelper::base64
	 * @group        base64
	 */
	public function testBase64Decode($expected, $url)
	{
		$this->assertSame($expected, UriHelper::base64('decode', $url));
	}

	/**
	 * The method to test UriHelper::base64 in encoding cases.
	 *
	 * @param string $expected
	 * @param string $url
	 *
	 * @return void
	 *
	 * @dataProvider encodedUrlDataProvider
	 * @covers       Windwalker\Helper\UriHelper::base64
	 * @group        base64
	 */
	public function testBase64Encode($expected, $url)
	{
		$this->assertSame($expected, UriHelper::base64('encode', $url));
	}

	/**
	 * encodedUrlDataProvider
	 *
	 * @return array
	 */
	public function encodedUrlDataProvider()
	{
		return array(
			array('d3d3Lmdvb2dsZS5jb20=', 'www.google.com'),
			array('d3d3LmJtLXNtcy5jb20udHc=', 'www.bm-sms.com.tw'),
			array('dHcudGVzdC5jb20vZHVtbXkvcGF0aC90bz9hcmc9MSZhcmc9Mg==', 'tw.test.com/dummy/path/to?arg=1&arg=2'),
		);
	}

	/**
	 * decodedUrlDataProvider
	 *
	 * @return array
	 */
	public function decodedUrlDataProvider()
	{
		return array(
			array('www.google.com', 'd3d3Lmdvb2dsZS5jb20='),
			array('www.bm-sms.com.tw', 'd3d3LmJtLXNtcy5jb20udHc='),
			array('tw.test.com/dummy/path/to?arg=1&arg=2', 'dHcudGVzdC5jb20vZHVtbXkvcGF0aC90bz9hcmc9MSZhcmc9Mg=='),
		);
	}

	/**
	 * The method to test UriHelper::safe.
	 *
	 * @param string $expected
	 * @param string $uri
	 *
	 * @return void
	 *
	 * @dataProvider uriDataProvider
	 * @covers       Windwalker\Helper\UriHelper::safe
	 * @group        safe
	 */
	public function testSafe($expected, $uri)
	{
		$this->assertSame($expected, UriHelper::safe($uri));
	}

	/**
	 * uriDataProvider
	 *
	 * @return array
	 */
	public function uriDataProvider()
	{
		return array(
			array('www.dummy.com/?page=2&title=test%20page', 'www.dummy.com/?page=2&title=test page'),
			array('^&=#%20$%!~', '^&=# $%!~'),
			array('\x20%20\x20', '\x20 \x20'),
		);
	}

	/**
	 * The method to test UriHelper::isHome.
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\UriHelper::isHome
	 * @group  isHome
	 */
	public function testIsHome()
	{
		if (php_sapi_name() === 'cli')
		{
			$this->markTestSkipped(
				'It is better to test UriHelper::isHome through HTTP request.'
			);
		}
	}

	/**
	 * The method to test UriHelper::pathAddHost.
	 *
	 * @param string $expected
	 * @param string $path
	 *
	 * @return void
	 *
	 * @dataProvider pathDataProvider
	 * @covers       Windwalker\Helper\UriHelper::pathAddHost
	 * @group        pathAddHost
	 */
	public function testPathAddHost($expected, $path)
	{
		if (php_sapi_name() === 'cli')
		{
			$this->assertSame($expected, UriHelper::pathAddHost($path));
			echo 'It is better to test UriHelper::pathAddHost through HTTP request.';
		}
	}

	/**
	 * pathDataProvider
	 *
	 * @return array
	 */
	public function pathDataProvider()
	{
		return array(
			// Not path
			array('', null),

			// No host
			array('http://php.localhost' . dirname($_SERVER['SCRIPT_NAME']) . '/www.bm-sms.com.tw', 'www.bm-sms.com.tw'),

			// Normal case
			array('http://www.bm-sms.com.tw/', 'http://www.bm-sms.com.tw/'),
		);
	}
}
