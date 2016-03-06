<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace GeneratorBundle\Action;

use Windwalker\DI\Container;

/**
 * Class AbstractAction
 *
 * @since 1.0
 */
abstract class AbstractAction extends \Muse\Action\AbstractAction
{
	/**
	 * Constructor.
	 *
	 * @param Container $container
	 */
	public function __construct(Container $container = null)
	{
		$this->container = $container ? : Container::getInstance();
	}
}
