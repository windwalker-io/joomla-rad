<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Api\Helper;

/**
 * Class RoutingHelper
 *
 * @since 2.0
 */
class RoutingHelper
{
	/**
	 * parseTask
	 *
	 * @param string  $controller
	 * @param \JInput $input
	 *
	 * @return  void
	 */
	public static function parseUserTask($controller, $input)
	{
		$task = $input->get('task');

		if (is_numeric($task))
		{
			$id = $task;

			$input->set('id', $id);

			$input->set('task', null);
		}
		else
		{
			$input->set('task', 'user.' . $input->get('task'));
		}
	}
}
