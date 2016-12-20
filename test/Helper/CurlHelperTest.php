<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Helper\CurlHelper;

/**
 * Test class of {className}
 *
 * @since 2.1
 */
class CurlHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var {className}
	 */
	protected $instance;

	/**
	 * Property options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		if (!defined('WINDWALKER_TEST_CURL_HELPER') || !WINDWALKER_TEST_CURL_HELPER)
		{
			$this->markTestSkipped('Do not test CURL in client computer.');
		}

		$this->processIsolation = true;

		$this->options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.163 Safari/535.1",
			CURLOPT_FOLLOWLOCATION => !ini_get('open_basedir') ? true : false,
			CURLOPT_SSL_VERIFYPEER => false
		);
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
	 * Method to test get().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\CurlHelper::get
	 */
	public function testGet()
	{
		$url = 'http://example.com/';

		$http = \JHttpFactory::getHttp(new \JRegistry($this->options), 'curl');

		// Test with Restful Api 'GET'
		$helperOutput = CurlHelper::get($url);
		$jHttpOutput = $http->get($url);

		$this->assertEquals($helperOutput->code, $jHttpOutput->code);
		$this->assertEquals($helperOutput->body, $jHttpOutput->body);

		// Test with Restful Api 'POST'
		$helperOutput = CurlHelper::get($url, 'post', array('key' => 'value'), array('testHeader'));
		$jHttpOutput = $http->post($url, array('key' => 'value'), array('testHeader'));

		$this->assertEquals($helperOutput->code, $jHttpOutput->code);
		$this->assertEquals($helperOutput->body, $jHttpOutput->body);
	}

	/**
	 * Method to test download().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\CurlHelper::download
	 */
	public function testDownload()
	{
		ini_set('allow_url_fopen', true);

		// We use static file README.md as our test file.
		$fileName = 'README.md';
		$testFileName = 'test' . $fileName;

		$filePath = __DIR__ . '/../../' . $fileName;
		$testFilePath = __DIR__ . '/../../' . $testFileName;

		$filePath = str_replace('\\', '/', $filePath);

		// Read file on local
		$oriFileContent = file_get_contents($filePath);

		// Download file by CurlHelper
		$success = CurlHelper::download('file://'. $filePath, $testFileName);

		if (!isset($success->errorCode))
		{
			// Read the downloaded file
			$downloadFileContent = file_get_contents($fileName);

			// Assert two file
			$this->assertEquals($oriFileContent, $downloadFileContent);

			// Remove temporary downloaded file.
			unlink($testFilePath);
		}
		else
		{
			$this->fail(sprintf('Download: %s fail', $filePath));
		}
	}
}
