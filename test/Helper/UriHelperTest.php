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
	 * Test instance.
	 *
	 * @var object
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
		$this->instance = null;
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
	 * Method to test UriHelper::base64 in decoding cases.
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
	 * Method to test UriHelper::base64 in encoding cases.
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
}
