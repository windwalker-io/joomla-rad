<?php
/**
 * Part of rad project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Windwalker\Router\Handler;

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Windwalker\Router\ComponentViewRouter;

/**
 * The AbstractRouterRule class.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractRouterHandler implements RouterHandlerInterface
{
	/**
	 * Property name.
	 *
	 * @var string
	 */
	protected static $name;

	/**
	 * Property nestable.
	 *
	 * @var  bool
	 */
	protected static $nestable = false;

	/**
	 * Property key.
	 *
	 * @var  string
	 */
	protected static $key = null;

	/**
	 * Property parentClass.
	 *
	 * @var  string
	 */
	protected static $parent;

	/**
	 * Property parentKey.
	 *
	 * @var  string
	 */
	protected static $parentKey = 'parent_id';

	/**
	 * Property layouts.
	 *
	 * @var  array
	 */
	protected static $layouts = array();

	/**
	 * Property noID.
	 *
	 * @var  bool
	 */
	protected static $noIDs = false;

	/**
	 * Property router.
	 *
	 * @var ComponentViewRouter
	 */
	protected $router;

	/**
	 * Property view.
	 *
	 * @var RouterViewConfiguration
	 */
	protected $view;

	/**
	 * AbstractRouterHandler constructor.
	 *
	 * @param ComponentViewRouter $router
	 */
	public function __construct(ComponentViewRouter $router)
	{
		$this->router = $router;
	}

	/**
	 * Get resource name of this view.
	 *
	 * @return  string
	 */
	public function getName()
	{
		return static::$name;
	}

	/**
	 * Get view configuration object, should be singleton.
	 *
	 * @param   bool  $new  Return a new instance.
	 *
	 * @return RouterViewConfiguration
	 */
	public function getViewconfiguration($new = false)
	{
		if (!$this->view)
		{
			$this->view = new RouterViewConfiguration(static::getName());
		}

		return $this->view;
	}
}
