<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace GeneratorBundle\Action\Component;

use GeneratorBundle\Action\AbstractAction;

/**
 * Class CopyBasefilesAction
 *
 * @since 1.0
 */
class CopyAllAction extends AbstractAction
{
	/**
	 * doExecute
	 *
	 * @return  mixed
	 */
	public function doExecute()
	{
		$copyOperator = $this->container->get('operator.copy');

		$config = $this->config;

		$copyOperator->copy($config['dir.src'], $config['dir.dest'], (array) $config['replace']);
	}
}
