<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Helper;

use Windwalker\Controller\Controller;
use Windwalker\Model\Model;

/**
 * The ContextHelper class.
 *
 * @since  {DEPLOY_VERSION}
 */
abstract class ContextHelper
{
	/**
	 * fromController
	 *
	 * @param Controller $controller
	 * @param string     $suffix
	 *
	 * @return  string
	 */
	public static function fromController(Controller $controller, $suffix = null)
	{
		$suffix = $suffix ? '.' . trim($suffix, '.') : null;

		return $controller->getOption() . '.' . $controller->getName() . $suffix;
	}

	/**
	 * fromModel
	 *
	 * @param  Model   $model
	 * @param   string  $suffix
	 *
	 * @return  string
	 *
	 * @throws \Exception
	 */
	public static function fromModel(Model $model, $suffix = null)
	{
		$suffix = $suffix ? '.' . trim($suffix, '.') : null;

		return $model->getOption() . '.' . $model->getName() . $suffix;
	}
}
