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
 * The ContextHelper to auto get context string.
 *
 * @since  2.1
 */
abstract class ContextHelper
{
	/**
	 * Get context from controller.
	 *
	 * @param  Controller  $controller  Controller object.
	 * @param  string      $suffix      Add suffix or not.
	 *
	 * @return  string  The generated context string.
	 */
	public static function fromController(Controller $controller, $suffix = null)
	{
		$suffix = $suffix ? '.' . trim($suffix, '.') : null;

		return $controller->getOption() . '.' . $controller->getName() . $suffix;
	}

	/**
	 * Get context from model.
	 *
	 * @param   Model   $model   The model object.
	 * @param   string  $suffix  Add suffix or not.
	 *
	 * @return  string  The generated context string.
	 *
	 * @throws \Exception
	 */
	public static function fromModel(Model $model, $suffix = null)
	{
		$suffix = $suffix ? '.' . trim($suffix, '.') : null;

		return $model->getOption() . '.' . $model->getName() . $suffix;
	}

	/**
	 * Get context from view.
	 *
	 * @param   AbstractView  $view    The view object.
	 * @param   string        $suffix  Add suffix or not.
	 *
	 * @return  string  The generated context string.
	 *
	 * @throws \Exception
	 */
	public static function fromView(AbstractView $view, $suffix = null)
	{
		$suffix = $suffix ? '.' . trim($suffix, '.') : null;

		return $view->getOption() . '.' . $view->getName() . $suffix;
	}
}
