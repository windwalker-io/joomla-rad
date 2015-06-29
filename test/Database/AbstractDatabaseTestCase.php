<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Database;

use Windwalker\Test\TestCase\AbstractBaseTestCase;

/**
 * The AbstractDatabaseTestCase class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class AbstractDatabaseTestCase extends AbstractBaseTestCase
{
	/**
	 * setUpBeforeClass
	 *
	 * @throws \LogicException
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		$queries = file_get_contents(__DIR__ . '/fixtures/testflower.sql');

		static::batchQuery($queries);
	}

	/**
	 * tearDownAfterClass
	 *
	 * @return  void
	 */
	public static function tearDownAfterClass()
	{
		$queries = file_get_contents(__DIR__ . '/fixtures/drop_testflower.sql');

		static::batchQuery($queries);
	}

	/**
	 * batchQuery
	 *
	 * @param string $queries
	 *
	 * @return  void
	 */
	public static function batchQuery($queries)
	{
		$db = \JFactory::getDbo();

		$queries = $db->splitSql($queries);

		foreach ($queries as $query)
		{
			$query = trim($query);

			if ($query)
			{
				$db->setQuery($query)->execute();
			}
		}
	}
}
