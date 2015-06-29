<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Helper\UriHelper;
use Windwalker\Test\TestHelper;

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
		TestHelper::setValue('JUri', 'base', array());

		\JFactory::getConfig()->set('live_site', 'http://php.localhost/flower/sakura');

		$_SERVER['HTTP_HOST'] = 'php.localhost';
		$_SERVER['REQUEST_URI'] = '/flower/sakura';
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		TestHelper::setValue('Juri', 'base', array());

		unset($_SERVER['HTTP_HOST']);
		unset($_SERVER['REQUEST_URI']);

		\JFactory::getConfig()->set('live_site', null);
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
	 * @dataProvider  isHomeProvider
	 *
	 * @covers Windwalker\Helper\UriHelper::isHome
	 * @group  isHome
	 */
	public function testIsHome($uri, $expected, $errMsg)
	{
		$ref = new \ReflectionProperty('JUri', 'instances');
		$ref->setAccessible(true);
		$instances = $ref->getValue();

		$instances['SERVER'] = null;

		$_SERVER['REQUEST_URI'] = $uri;

		$ref->setValue($instances);

		$this->assertEquals($expected, UriHelper::isHome($expected), 'Request: ' . $uri . ' ' . $errMsg);
	}

	/**
	 * isHomeProvider
	 *
	 * @return  array
	 */
	public function isHomeProvider()
	{
		return array(
			array('/flower/sakura', true, 'should be home.'),
			array('/flower/sakura/index.php', true, 'should be home.'),
			array('/flower/sakura/bloom.html', false, 'should not be home.'),
			array('/flower/sakura/beautiful', false, 'should not be home.'),
		);
	}

	/**
	 * The method to test UriHelper::download.
	 *
	 * @return void
	 *
	 * @covers Windwalker\Helper\UriHelper::download
	 * @group  download
	 */
	public function testDownload()
	{
		// Test redirect download with no absolute
		$this->assertEquals(\JUri::root() . 'foo/bar/file.io', UriHelper::download('foo/bar/file.io', false, false, array('test' => true)));

		// Test Streaming
		ob_start();

		UriHelper::download(__FILE__, true, true, array('test' => true));

		$content = ob_get_contents();

		ob_end_clean();

		$this->assertStringEqualsFile(__FILE__, $content);

		$headers = array(
			'Content-Type: application/octet-stream',
			'Cache-Control: no-store, no-cache, must-revalidate',
			'Cache-Control: pre-check=0, post-check=0, max-age=0',
			'Content-Transfer-Encoding: binary',
			'Content-Encoding: none',
			'Content-type: application/force-download',
			'Content-length: ' . filesize(__FILE__),
			'Content-Disposition: attachment; filename="' . basename(__FILE__) . '"'
		);

		$this->assertEquals($headers, UriHelper::$headerBuffer);
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
		$this->assertSame($expected, UriHelper::pathAddHost($path));
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

			// Root relative path
			array('http://php.localhost/flower/sakura/bloom.html', '/flower/sakura/bloom.html'),

			// Base relative path
			array('http://php.localhost/flower/sakura/bloom.html', 'bloom.html'),

			// Full URL
			array('http://php.localhost/flower/sakura/bloom.html', 'http://php.localhost/flower/sakura/bloom.html'),
		);
	}
}
