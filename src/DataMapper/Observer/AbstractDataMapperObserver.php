<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\DataMapper\Observer;

use Joomla\Registry\Registry;
use Windwalker\DataMapper\Observer\AbstractObservableDataMapper;

/**
 * The AbstractDataMapperObserver class.
 * 
 * @since  {DEPLOY_VERSION}
 */
abstract class AbstractDataMapperObserver implements \JObserverInterface
{
	/**
	 * Property mapper.
	 *
	 * @var  AbstractObservableDataMapper
	 */
	protected $mapper;

	/**
	 * Property params.
	 *
	 * @var  array
	 */
	protected $params;

	/**
	 * Class init.
	 *
	 * @param AbstractObservableDataMapper $mapper
	 * @param array                        $params
	 */
	public function __construct(AbstractObservableDataMapper $mapper, $params = array())
	{
		$this->mapper = $mapper;
		$this->params = new Registry($params);
	}
}
