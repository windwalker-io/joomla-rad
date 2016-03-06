<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Helper;

use Windwalker\Controller\Controller;
use Windwalker\Model\Model;
use Windwalker\View\AbstractView;

/**
 * The ContextHelper class.
 *
 * @since  2.1
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
	 * @param   Model   $model
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

	/**
	 * fromView
	 *
	 * @param AbstractView $view
	 * @param string       $suffix
	 *
	 * @return  string
	 *
	 * @throws \Exception
	 */
	public static function fromView(AbstractView $view, $suffix = null)
	{
		$suffix = $suffix ? '.' . trim($suffix, '.') : null;

		return $view->getOption() . '.' . $view->getName() . $suffix;
	}
}
