<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Script;

/**
 * The JqueryScript class.
 *
 * @since  {DEPLOY_VERSION}
 */
class JQueryScript extends AbstractScriptManager
{
	/**
	 * jquery
	 *
	 * @param   boolean $noConflict
	 * @param   boolean $debug
	 * @param   boolean $migrate
	 */
	public static function core($noConflict = true, $debug = null, $migrate = true)
	{
		\JHtmlJquery::framework($noConflict, $debug, $migrate);
	}

	/**
	 * ui
	 *
	 * @param array $components
	 *
	 * @return  void
	 */
	public static function ui(array $components)
	{
		static::core();
		$asset = static::getAsset();

		if (!static::inited(__METHOD__))
		{
			\JHtmlJquery::ui();
		}

		if (!static::inited(__METHOD__, func_get_args()))
		{
			$allowedComponents = array(
				'draggable',
				'droppable',
				'resizable',
				'selectable',
				'effect'
			);

			foreach ((array) $components as $component)
			{
				if (in_array($component, $allowedComponents))
				{
					$asset->addJS('jquery/jquery.ui.' . $component . '.min.js');
				}
			}
		}
	}
}
