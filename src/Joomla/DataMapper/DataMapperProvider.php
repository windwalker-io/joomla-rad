<?php
/**
 * Part of joomla336 project. 
 *
 * @copyright  Copyright (C) 2014 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Joomla\DataMapper;

use Joomla\DI\Container;
use Windwalker\DataMapper\Adapter\AbstractDatabaseAdapter;
use Windwalker\DI\ServiceProvider;
use Windwalker\Joomla\Database\JoomlaAdapter;

/**
 * The DataMapperProvider class.
 * 
 * @since  2.1
 */
class DataMapperProvider extends ServiceProvider
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container $container The DI container.
	 *
	 * @return  Container  Returns itself to support chaining.
	 *
	 * @since   1.0
	 */
	public function register(Container $container)
	{
		AbstractDatabaseAdapter::setInstance(new JoomlaAdapter($container->get('db')));
	}
}
