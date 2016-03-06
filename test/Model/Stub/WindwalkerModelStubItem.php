<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Model\Stub;

use Windwalker\Model\ItemModel;

/**
 * The WindwalkerModelStubItem class.
 * 
 * @since  2.1
 */
class WindwalkerModelStubItem extends ItemModel
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
	protected $name = 'stubitem';

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
	protected $context = 'com_windwalker.stubitem';

	/**
	 * Property eventCleanCache.
	 *
	 * @var  string
	 */
	protected $eventCleanCache = 'onTestCleanCache';
}
