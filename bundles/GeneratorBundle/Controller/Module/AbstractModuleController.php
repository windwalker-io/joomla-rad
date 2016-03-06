<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace GeneratorBundle\Controller\Module;

use Muse\IO\IOInterface;
use GeneratorBundle\Controller\AbstractJExtensionController;
use Windwalker\Registry\Registry;
use Windwalker\DI\Container;

/**
 * Class AbstractPluginController
 *
 * @since 1.0
 */
abstract class AbstractModuleController extends AbstractJExtensionController
{
	/**
	 * Constructor.
	 *
	 * @param   \Windwalker\DI\Container      $container
	 * @param   \Muse\IO\IOInterface $io
	 * @param   Registry                      $config
	 */
	public function __construct(Container $container, IOInterface $io, Registry $config = null)
	{
		$config['client'] = $config['client'] ? : 'site';

		$this->replace['module.client'] = 'client="' . $config['client'] . '"';

		parent::__construct($container, $io, $config);

		// Set copy dir.
		$config->set('dir.src', $config->get('dir.tmpl'));
	}
}
