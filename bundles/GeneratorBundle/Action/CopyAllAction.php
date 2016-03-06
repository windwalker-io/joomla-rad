<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace GeneratorBundle\Action;

/**
 * Class CopyAllAction
 *
 * @since 1.0
 */
class CopyAllAction extends AbstractAction
{
	/**
	 * doExecute
	 *
	 * @throws \RuntimeException
	 * @return  mixed
	 */
	public function doExecute()
	{
		$copyOperator = $this->container->get('operator.copy');

		$config = $this->config;

		if (!is_dir($config['dir.tmpl']))
		{
			throw new \RuntimeException(sprintf('Template "%s" of %s not exists', $config['template'], $config['extension']));
		}

		$copyOperator->copy($config['dir.src'], $config['dir.dest'], (array) $config['replace']);
	}
}
