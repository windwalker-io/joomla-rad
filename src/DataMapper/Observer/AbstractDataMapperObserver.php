<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\DataMapper\Observer;

use Joomla\Registry\Registry;
use Windwalker\DataMapper\ObservableDataMapper;

/**
 * An observer for ObservableDataMapper
 * 
 * @since  {DEPLOY_VERSION}
 */
abstract class AbstractDataMapperObserver implements \JObserverInterface
{
	/**
	 * Property mapper.
	 *
	 * @var  ObservableDataMapper
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
	 * @param ObservableDataMapper $mapper
	 * @param array                        $params
	 */
	public function __construct(ObservableDataMapper $mapper, $params = array())
	{
		$mapper->attachObserver($this);
		$this->mapper = $mapper;
		$this->params = new Registry($params);
	}
}
