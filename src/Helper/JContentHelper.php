<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Helper;

/**
 * Joomla Content Helper.
 *
 * @since 2.0
 */
class JContentHelper
{
	/**
	 * Property for article link route handler
	 *
	 * @var callable
	 */
	public static $articleRouteHandler = array('ContentHelperRoute', 'getArticleRoute');

	/**
	 * Property for category link route handler
	 *
	 * @var callable
	 */
	public static $categoryRouteHandler = array('ContentHelperRoute', 'getCategoryRoute');

	/**
	 * Get article link url by slug.
	 *
	 * @param  string  $slug     The id slug, eg: "43:artile-alias"
	 * @param  string  $catslug  The category slug, eg: "12:category-alias", can only include number.
	 * @param  boolean $absolute Ture to return whole absolute url.
	 *
	 * @return string Article link url.
	 */
	public static function getArticleLink($slug, $catslug = null, $absolute = false)
	{
		include_once JPATH_ROOT . '/components/com_content/helpers/route.php';

		$path = call_user_func(static::$articleRouteHandler, $slug, $catslug);

		if ($absolute)
		{
			return \JUri::root() . $path;
		}
		else
		{
			return $path;
		}
	}

	/**
	 * Get category link url by category id.
	 *
	 * @param   integer $catid    Category id to load Table.
	 * @param   bool    $absolute Ture to return whole absolute url.
	 *
	 * @return  string Category link url.
	 */
	public static function getCategoryLink($catid, $absolute = false)
	{
		include_once JPATH_ROOT . '/components/com_content/helpers/route.php';

		$path = call_user_func(static::$categoryRouteHandler, $catid);

		if ($absolute)
		{
			return \JUri::root() . $path;
		}
		else
		{
			return $path;
		}
	}
}
