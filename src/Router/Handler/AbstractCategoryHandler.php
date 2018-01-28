<?php
/**
 * Part of rad project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Windwalker\Router\Handler;

use Joomla\CMS\Categories\Categories;

/**
 * The AbstractListRule class.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractCategoryHandler extends AbstractRouterHandler
{
	/**
	 * Method to get the segment(s) for this view item.
	 *
	 * @param   string $id    ID of the view item to retrieve the segments for
	 * @param   array  $query The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 * @throws \Exception
	 */
	public function getSegment($id, $query)
	{
		$category = Categories::getInstance($this->router->getName())->get($id);

		if (!$category)
		{
			return array();
		}

		$path = array_reverse($category->getPath(), true);
		$path[0] = '1:root';

		if (static::$noIDs)
		{
			foreach ($path as &$segment)
			{
				list($id, $segment) = explode(':', $segment, 2);
			}
		}

		return $path;
	}

	/**
	 * Method to get the id for this view item.
	 *
	 * @param   string $segment Segment to retrieve the ID for view item.
	 * @param   array  $query   The request that is parsed right now
	 *
	 * @return  int|false  The id of this item or false
	 * @throws \Exception
	 */
	public function getId($segment, $query)
	{
		$key = $this->getViewconfiguration()->key;

		if (isset($query[$key]))
		{
			$category = Categories::getInstance($this->router->getName())->get();

			foreach ($category->getChildren(true) as $child)
			{
				if (static::$noIDs)
				{
					if ($child->alias === $segment)
					{
						return $child->$key;
					}
				}
				else
				{
					if ((int) $child->$key === (int) $segment)
					{
						return $child->$key;
					}
				}
			}
		}

		return false;
	}
}
