<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Api\Provider;

use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Windwalker\Api\ApiServer;
use Windwalker\Api\Listener\ApiListener;
use Windwalker\DI\ServiceProvider;

/**
 * Class ApiProvider
 *
 * @since 2.0
 */
class ApiProvider extends ServiceProvider
{
	/**
	 * Property option.
	 *
	 * @var  string
	 */
	protected $option;

	/**
	 * Property uri.
	 *
	 * @var  \JUri
	 */
	protected $uri;

	/**
	 * Property element.
	 *
	 * @var  string
	 */
	protected $element;

	/**
	 * Class init.
	 *
	 * @param string         $element
	 * @param \JUri          $uri
	 * @param Registry|array $option
	 */
	public function __construct($element, \JUri $uri = null, $option = array())
	{
		$this->option = ($option instanceof Registry) ? $option : new Registry($option);
		$this->uri = $uri ? : \JUri::getInstance();
		$this->element = $element;
	}

	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container $container The DI container.
	 *
	 * @return  Container  Returns itself to support chaining.
	 */
	public function register(Container $container)
	{
		$server = new ApiServer($this->element, $this->uri, $this->option);

		$container->set('api.server', $server);

		$server->register();

		// Listener
		$dispatcher = $container->get('event.dispatcher');

		/** @var $dispatcher \JEventDispatcher */
		$dispatcher->attach(new ApiListener($this->element, $dispatcher));
	}
}
