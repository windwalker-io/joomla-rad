<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Model\Provider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Windwalker\Model\Filter\FilterHelper;
use Windwalker\Model\Filter\SearchHelper;
use Windwalker\Model\Helper\QueryHelper;
use Windwalker\Model\ListModel;

/**
 * Class GridProvider
 *
 * @since 2.0
 *
 * @deprecated  3.0  Decouple container from ListModel.
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
	 * Inject ListModel to make this class backward compatible.
	 *
	 * @var  ListModel
	 */
	private $model;

	/**
	 * Constructor
	 *
	 * @param string    $name
	 * @param ListModel $model
	 */
	public function __construct($name, ListModel $model = null)
	{
		$this->name = strtolower($name);
		$this->model = $model;
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
		$model = $this->model;

		// QueryHelper
		$container->share(
			'model.' . $this->name . '.helper.query',
			function($container) use ($model)
			{
				if ($model instanceof ListModel)
				{
					return $model->getQueryHelper();
				}
				else
				{
					return new QueryHelper;
				}
			}
		);

		// Filter
		$container->share(
			'model.' . $this->name . '.filter',
			function($container) use ($model)
			{
				if ($model instanceof ListModel)
				{
					return $model->getFilterHelper();
				}
				else
				{
					return new FilterHelper;
				}
			}
		)->alias('model.' . $this->name . '.helper.filter', 'model.' . $this->name . '.filter');

		// Search
		$container->share(
			'model.' . $this->name . '.search',
			function($container) use ($model)
			{
				if ($model instanceof ListModel)
				{
					return $model->getSearchHelper();
				}
				else
				{
					return new SearchHelper;
				}
			}
		)->alias('model.' . $this->name . '.helper.search', 'model.' . $this->name . '.search');
	}
}
