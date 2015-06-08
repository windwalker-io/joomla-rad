<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Model\Stub;

require_once __DIR__ . '/WindwalkerModelStub.php';

/**
 * The StubModel class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class StubModel extends \WindwalkerModelStub
{
	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'windwalker';

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'stub';

	/**
	 * Property option.
	 *
	 * @var  string
	 */
	protected $option = 'com_windwalker';

	/**
	 * Property context.
	 *
	 * @var  string
	 */
	protected $context = 'com_windwalker.stub';

	/**
	 * Property eventCleanCache.
	 *
	 * @var  string
	 */
	protected $eventCleanCache = 'onTestCleanCache';
}
