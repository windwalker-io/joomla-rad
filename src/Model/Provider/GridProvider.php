<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Model\Provider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Windwalker\Model\Filter\FilterHelper;
use Windwalker\Model\Filter\SearchHelper;
use Windwalker\Model\Helper\QueryHelper;

/**
 * Class FilterProvider
 *
 * @since 1.0
 */
class GridProvider implements ServiceProviderInterface
{
	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = null;

	/**
	 * Constructor
	 *
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->name = strtolower($name);
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
		// QueryHelper
		$container->share(
			'model.' . $this->name . '.helper.query',
			function($container)
			{
				return new QueryHelper;
			}
		);

		// Filter
		$container->share(
			'model.' . $this->name . '.filter',
			function($container)
			{
				return new FilterHelper;
			}
		)->alias('model.' . $this->name . '.helper.filter', 'model.' . $this->name . '.filter');

		// Search
		$container->share(
			'model.' . $this->name . '.search',
			function($container)
			{
				return new SearchHelper;
			}
		)->alias('model.' . $this->name . '.helper.search', 'model.' . $this->name . '.search');
	}
}
