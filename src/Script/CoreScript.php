<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Script;

/**
 * The CoreScript class.
 *
 * @since  2.1
 */
abstract class CoreScript extends AbstractScriptManager
{
	/**
	 * Load underscore.
	 *
	 * @param boolean $noConflict Enable underscore no conflict mode.
	 *
	 * @return  void
	 */
	public static function underscore($noConflict = true)
	{
		$asset = static::getAsset();

		if (!static::inited(__METHOD__))
		{
			$asset->addJS('core/underscore.js');

			$asset->internalJS('_.templateSettings = { interpolate: /\{\{(.+?)\}\}/g };');
		}

		if (!static::inited(__METHOD__, (bool) $noConflict) && $noConflict)
		{
			$asset->internalJS('var underscore = _.noConflict();');
		}
	}

	/**
	 * Load Underscore.String.
	 *
	 * @param   boolean  $noConflict  Enable underscore no conflict mode.
	 *
	 * @return  void
	 */
	public static function underscoreString($noConflict = true)
	{
		$asset = static::getAsset();

		if (!static::inited(__METHOD__))
		{
			$asset->addJS('core/underscore.string.min.js');
		}

		if (!static::inited(__METHOD__, (bool) $noConflict) && $noConflict)
		{
			$js = <<<JS
(function(s) {
	var us = function(underscore)
	{
		underscore.string = underscore.string || s;
	};
	us(window._ || (window._ = {}));
	us(window.underscore || (window.underscore = {}));
})(s);
JS;

			$asset->internalJS($js);
		}
	}

	/**
	 * Include Backbone. Note this library may not support old IE browser.
	 *
	 * Please see: http://backbonejs.org/
	 *
	 * @param   boolean $noConflict
	 *
	 * @return  void
	 */
	public static function backbone($noConflict = false)
	{
		$asset = static::getAsset();

		if (!static::inited(__METHOD__))
		{
			// Dependency
			\JHtmlJquery::framework(true);
			static::underscore();

			$asset->addJS('core/backbone.js');
		}

		if (!static::inited(__METHOD__, (bool) $noConflict) && $noConflict)
		{
			$asset->internalJS('var backbone = Backbone.noConflict();');
		}
	}

	/**
	 * Load Windwalker script.
	 *
	 * @return  void
	 */
	public static function windwalker()
	{
		if (!static::inited(__METHOD__))
		{
			static::getAsset()->windwalker();
		}
	}
}
