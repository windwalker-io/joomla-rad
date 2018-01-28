<?php
/**
 * Part of rad project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Windwalker\Router\Handler;

/**
 * The AbstractItemRule class.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractItemHandler extends AbstractRouterHandler
{
	/**
	 * Property table.
	 *
	 * @var string
	 */
	protected static $table;

	/**
	 * Property aliasField.
	 *
	 * @var  string
	 */
	protected static $aliasField = 'alias';

	/**
	 * Method to get the segment(s) for this view item.
	 *
	 * @param   string $id    ID of the view item to retrieve the segments for
	 * @param   array  $query The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 *
	 * @throws \RuntimeException
	 * @throws \LogicException
	 */
	public function getSegment($id, $query)
	{
		if (!static::$table)
		{
			throw new \LogicException('No table name in ' . get_called_class());
		}

		$view = $this->getViewconfiguration();

		if (!strpos($id, ':'))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$dbquery = $db->getQuery(true);

			$dbquery->select($dbquery->qn('alias'))
				->from($dbquery->qn(static::$table))
				->where($dbquery->qn($view->key) . ' = ' . $dbquery->q($id));

			$db->setQuery($dbquery);

			$id .= ':' . $db->loadResult();
		}

		if (static::$noIDs)
		{
			list($void, $segment) = explode(':', $id, 2);

			return array($void => $segment);
		}

		return array((int) $id => $id);
	}

	/**
	 * Method to get the id for this view item.
	 *
	 * @param   string $segment Segment to retrieve the ID for view item.
	 * @param   array  $query   The request that is parsed right now
	 *
	 * @return  int|false  The id of this item or false
	 *
	 * @throws \RuntimeException
	 */
	public function getId($segment, $query)
	{
		$view = $this->getViewconfiguration();

		if (static::$noIDs)
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$dbquery = $db->getQuery(true);

			$dbquery->select($dbquery->qn($view->key))
				->from($dbquery->qn(static::$table))
				->where($dbquery->qn($view->key) . ' = ' . $dbquery->q($segment));

			if ($view->parent_key)
			{
				$dbquery->where($dbquery->qn($view->parent_key) . ' = ' . $dbquery->q($query[$view->key]));
			}

			$db->setQuery($dbquery);

			return (int) $db->loadResult();
		}

		return (int) $segment;
	}
}
