<?php
/**
 * Part of rad project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Windwalker\Router\Handler;

use Joomla\CMS\Component\Router\RouterViewConfiguration;

/**
 * The RouterRuleInterface class.
 *
 * @since  __DEPLOY_VERSION__
 */
interface RouterHandlerInterface
{
	/**
	 * Get resource name of this view.
	 *
	 * @return  string
	 */
	public function getName();

	/**
	 * Get view configuration object, should be singleton.
	 *
	 * @param   bool  $new  Return a new instance.
	 *
	 * @return RouterViewConfiguration
	 */
	public function getViewconfiguration($new = false);

	/**
	 * Configure view configuration.
	 *
	 * @param RouterViewConfiguration $view
	 *
	 * @return  void
	 */
	public function configure(\JComponentRouterViewconfiguration $view);

	/**
	 * Method to get the segment(s) for this view item.
	 *
	 * @param   string  $id     ID of the view item to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 */
	public function getSegment($id, $query);

	/**
	 * Method to get the id for this view item.
	 *
	 * @param   string  $segment Segment to retrieve the ID for view item.
	 * @param   array   $query   The request that is parsed right now
	 *
	 * @return  int|false  The id of this item or false
	 */
	public function getId($segment, $query);
}
