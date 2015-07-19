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
 * @since  {DEPLOY_VERSION}
 */
abstract class AbstractDatabaseTestCase extends AbstractBaseTestCase
{
	const TABLE_LOCATIONS        = '#__testflower_locations';
	const TABLE_LOCATION_DATA    = '#__testflower_location_data';
	const TABLE_ROSES            = '#__testflower_roses';
	const TABLE_SAKURAS          = '#__testflower_sakuras';
	const TABLE_SAKURA_ROSE_MAPS = '#__testflower_sakura_rose_maps';

	/**
	 * Property mappers.
	 *
	 * @var  DataMapper[]
	 */
	protected $mappers = array();

	/**
	 * setUpBeforeClass
	 *
	 * @throws \LogicException
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		$queries = file_get_contents(__DIR__ . '/fixtures/testflower.sql');

		DatabaseHelper::batchQuery($queries);
	}

	/**
	 * tearDownAfterClass
	 *
	 * @return  void
	 */
	public static function tearDownAfterClass()
	{
		$queries = file_get_contents(__DIR__ . '/fixtures/drop_testflower.sql');

		DatabaseHelper::batchQuery($queries);
	}
}
