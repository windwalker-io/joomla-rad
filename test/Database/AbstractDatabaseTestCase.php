<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Database;

use Windwalker\Helper\DatabaseHelper;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Test\TestCase\AbstractBaseTestCase;

/**
 * The AbstractDatabaseTestCase class.
 * 
 * @since  2.1
 */
abstract class AbstractDatabaseTestCase extends AbstractBaseTestCase
{
	const TABLE_LOCATIONS        = '#__testflower_locations';
	const TABLE_LOCATION_DATA    = '#__testflower_location_data';
	const TABLE_ROSES            = '#__testflower_roses';
	const TABLE_SAKURAS          = '#__testflower_sakuras';
	const TABLE_SAKURA_ROSE_MAPS = '#__testflower_sakura_rose_maps';

	/**
	 * setUpBeforeClass
	 *
	 * @throws \LogicException
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		$queries = file_get_contents(static::getInstallSql());

		DatabaseHelper::batchQuery($queries);
	}

	/**
	 * tearDownAfterClass
	 *
	 * @return  void
	 */
	public static function tearDownAfterClass()
	{
		$queries = file_get_contents(static::getUninstallSql());

		DatabaseHelper::batchQuery($queries);
	}

	/**
	 * Install test sql when setUp.
	 *
	 * @return  string
	 */
	public static function getInstallSql()
	{
		return __DIR__ . '/fixtures/testflower.sql';
	}

	/**
	 * Uninstall test sql when tearDown.
	 *
	 * @return  string
	 */
	public static function getUninstallSql()
	{
		return __DIR__ . '/fixtures/drop_testflower.sql';
	}
}
