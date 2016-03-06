<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\DataMapper\Stub;

use Windwalker\DataMapper\Adapter\DatabaseAdapterInterface;
use Windwalker\DataMapper\ObservableDataMapper;
use Windwalker\Test\Database\AbstractDatabaseTestCase;

/**
 * The LocationDataMapper class.
 * 
 * @since  2.1
 */
class StubLocationDataMapper extends ObservableDataMapper
{
	/**
	 * Constructor.
	 *
	 * @param   DatabaseAdapterInterface  $db     Database adapter.
	 */
	public function __construct(DatabaseAdapterInterface $db = null)
	{
		parent::__construct(AbstractDatabaseTestCase::TABLE_LOCATIONS, 'id', $db);
	}
}
