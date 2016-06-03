<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Component;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Windwalker\Asset\AssetManager;

/**
 * Component Provider class.
 *
 * @since 2.0
 */
class ComponentProvider implements ServiceProviderInterface
{
	/**
	 * Component name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Component object.
	 *
	 * @var
	 */
	private $component;

	/**
	 * Constructor.
	 *
	 * @param string    $name      Component name.
	 * @param Component $component Component object.
	 */
	public function __construct($name, Component $component)
	{
		$this->name      = $name;
		$this->component = $component;
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
		$name = $this->name;

		// Component
		$container->alias('component', ucfirst($name) . 'Component')
			->share(ucfirst($name) . 'Component', $this->component);

		// ControllerResolver
		$resolverClass = '\\Windwalker\\Controller\\Resolver\\ControllerResolver';

		$container->alias('controller.resolver', $resolverClass)
			->share(
				$resolverClass,
				function($container) use($resolverClass)
				{
					return new $resolverClass($container->get('app'), $container);
				}
			);

		// Asset Helper
		$container->share(
			'helper.asset',
			function($container) use ($name)
			{
				$asset = AssetManager::getInstance('com_' . strtolower($name));

				$asset->setContainer($container);

				return $asset;
			}
		);
	}
}
