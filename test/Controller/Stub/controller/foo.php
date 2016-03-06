<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\Controller;

/**
 * Class StubControllerFoo
 */
class StubControllerFoo extends Controller
{
	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'Stub';

	/**
	 * Property option.
	 *
	 * @var  string
	 */
	protected $option = 'com_test';

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'Foo';

	/**
	 * Property task.
	 *
	 * @var  string
	 */
	protected $task = 'default';

	/**
	 * Method to run this controller.
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		return 'foo controller test';
	}
}
