<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Script;

use Windwalker\Helper\ArrayHelper;

/**
 * The JqueryScript class.
 *
 * @since  2.1
 */
class JQueryScript extends AbstractScriptManager
{
	/**
	 * Add jQuery core script.
	 *
	 * @param   boolean  $noConflict  True to load jQuery in noConflict mode [optional]
	 * @param   boolean  $debug       Is debugging mode on? [optional]
	 * @param   boolean  $migrate     True to enable the jQuery Migrate plugin
	 */
	public static function core($noConflict = true, $debug = null, $migrate = true)
	{
		\JHtmlJquery::framework($noConflict, $debug, $migrate);
	}

	/**
	 * Method to load the jQuery UI framework.
	 *
	 * @param   array  $components  The jQuery UI components to load [optional]
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

	/**
	 * Add jQuery highlight plugin.
	 *
	 * @param  string  $selector  The selector to make highlight.
	 * @param  string  $text      The text to mark.
	 * @param  array   $options   The options of this script.
	 *
	 * @see  http://bartaz.github.io/sandbox.js/jquery.highlight.html
	 *
	 * @return  void
	 */
	public static function highlight($selector = '.hasHighlight', $text = null, $options = array())
	{
		$asset = static::getAsset();

		if (!static::inited(__METHOD__))
		{
			JQueryScript::core();

			$asset->addJS('jquery/jquery.highlight.js');
		}

		if (!static::inited(__METHOD__, func_get_args()) && $selector && $text)
		{
			if (is_array($text))
			{
				$text = implode(' ', $text);
			}

			$defaultOptions = array(
				'element' => 'mark',
				'className' => 'windwalker-highlight'
			);

			$options = $asset::getJSObject(ArrayHelper::merge($defaultOptions, $options));

			$js = <<<JS
// Highlight Text
jQuery(document).ready(function($)
{
	$('$selector').highlight('$text', $options);
});
JS;
			$asset->internalJS($js);
		}
	}
}
