<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Helper\UriHelper;
use Windwalker\Test\TestHelper;

/**
 * Test class of UriHelper.
 *
 * @since 2.1
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

		\JFactory::getConfig()->set('live_site', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
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

		TestHelper::setValue('JUri', 'root', array());

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
		// Decode
		$this->assertSame($expected, UriHelper::base64('decode', $url));

		// Encode
		$this->assertNotSame($expected, UriHelper::base64('encode', $url));

		// Other actions
		$this->assertSame($url, UriHelper::base64('pardon?', $url));
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
		// Encode
		$this->assertSame($expected, UriHelper::base64('encode', $url));

		// Decode
		$this->assertNotSame($expected, UriHelper::base64('decode', $url));

		// Other actions
		$this->assertSame($url, UriHelper::base64('pardon?', $url));
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
	 * @param bool   $expected
	 * @param string $uri
	 * @param string $errMsg
	 *
	 * @return void
	 *
	 * @dataProvider isHomeDataProvider
	 * @covers       Windwalker\Helper\UriHelper::isHome
	 * @group        isHome
	 */
	public function testIsHome($expected, $uri, $errMsg)
	{
		$ref = new \ReflectionProperty('JUri', 'instances');
		$ref->setAccessible(true);
		$instances = $ref->getValue();

		$instances['SERVER'] = new \JUri($uri);

		$ref->setValue($instances);

		$this->assertEquals($expected, UriHelper::isHome(), 'Request: ' . $uri . ' ' . $errMsg);
	}

	/**
	 * isHomeDataProvider
	 *
	 * @return array
	 */
	public function isHomeDataProvider()
	{
		return array(
			array(true, $_SERVER['REQUEST_URI'], 'should be home.'),
			array(true, $_SERVER['REQUEST_URI'] . '/index.php', 'should be home.'),
			array(false, $_SERVER['REQUEST_URI'] . '/bloom.html', 'should not be home.'),
			array(false, $_SERVER['REQUEST_URI'] . '/beautiful', 'should not be home.'),
		);
	}

	/**
	 * The method to test UriHelper::download.
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\UriHelper::download
	 * @group  download
	 */
	public function testDownload()
	{
		UriHelper::setTestMode(true);

		// Test redirect download
		// Not absolute URL
		$this->assertEquals(\JUri::root() . 'foo/bar/file.io', UriHelper::download('foo/bar/file.io', false, false));

		// Absolute URL
		$this->assertNotEquals(\JUri::root() . 'foo/bar/file.io', UriHelper::download('foo/bar/file.io', true, false));

		// Test Streaming
		// Not absolute URL
		ob_start();
		UriHelper::download(__FILE__, false, true);
		$content = ob_get_contents();
		ob_end_clean();
		$this->assertStringNotEqualsFile(__FILE__, $content);

		// Absolute URL
		ob_start();
		UriHelper::download(__FILE__, true, true);
		$content = ob_get_contents();
		ob_end_clean();
		$this->assertStringEqualsFile(__FILE__, $content);

		// Test headers
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
		$expected = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/bloom.html';

		return array(
			// Not path
			array('', null),

			// Root relative path
			array($expected, '/flower/sakura/bloom.html'),

			// Base relative path
			array($expected, 'bloom.html'),

			// Full URL
			array($expected, 'http://rad.windwalker.io/flower/sakura/bloom.html'),
		);
	}
}
