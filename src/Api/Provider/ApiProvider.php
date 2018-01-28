<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Api\Provider;

use Joomla\DI\Container;
use Windwalker\Api\ApiServer;
use Windwalker\Api\Listener\ApiListener;
use Windwalker\DI\ServiceProvider;
use Windwalker\Registry\Registry;

/**
 * Class ApiProvider
 *
 * @since 2.0
 *
 * @deprecated  API server will be re-write after Windwalker RAD 3.
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
	 * @var  \Joomla\CMS\Uri\Uri
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
	 * @param \Joomla\CMS\Uri\Uri          $uri
	 * @param Registry|array $option
	 */
	public function __construct($element, \Joomla\CMS\Uri\Uri $uri = null, $option = array())
	{
		$this->option = ($option instanceof Registry) ? $option : new Registry($option);
		$this->uri = $uri ? : \Joomla\CMS\Uri\Uri::getInstance();
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
