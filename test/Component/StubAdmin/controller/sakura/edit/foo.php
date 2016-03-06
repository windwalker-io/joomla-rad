<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

/**
 * The FooController class.
 * 
 * @since  2.1
 */
class StubControllerSakuraEditFoo extends \Windwalker\Controller\Controller
{
	/**
	 * Method to run this controller.
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		return 'foo controller data: ' . $this->input->get('data');
	}
}
