<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace GeneratorBundle\Controller;

use Muse\Controller\AbstractTaskController;
use Muse\IO\IOInterface;
use Windwalker\Filesystem\Path;
use Windwalker\Registry\Registry;
use Windwalker\Console\Command\Command;
use Windwalker\DI\Container;
use Windwalker\Helper\PathHelper;
use Windwalker\String\StringInflector;

/**
 * Class AbstractJExtensionController
 *
 * @since 1.0
 */
abstract class AbstractJExtensionController extends AbstractTaskController
{
	/**
	 * Property container.
	 *
	 * @var  Container
	 */
	protected $container;

	/**
	 * Constructor.
	 *
	 * @param   \Windwalker\DI\Container      $container
	 * @param   \Muse\IO\IOInterface $io
	 * @param   Registry                      $config
	 */
	public function __construct(Container $container, IOInterface $io, Registry $config = null)
	{
		// Get item & list name
		$ctrl = $config['ctrl'] ? : $io->getArgument(1);

		$ctrl = explode('.', $ctrl);

		$inflector = StringInflector::getInstance();

		if (empty($ctrl[0]))
		{
			$ctrl[0] = 'item';
		}

		if (empty($ctrl[1]))
		{
			$ctrl[1] = $inflector->toPlural($ctrl[0]);
		}

		list($itemName, $listName) = $ctrl;

		$replace['extension.element.lower'] = strtolower($config['element']);
		$replace['extension.element.upper'] = strtoupper($config['element']);
		$replace['extension.element.cap']   = ucfirst($config['element']);

		$replace['extension.name.lower']    = strtolower($config['name']);
		$replace['extension.name.upper']    = strtoupper($config['name']);
		$replace['extension.name.cap']      = ucfirst($config['name']);

		$replace['controller.list.name.lower'] = strtolower($listName);
		$replace['controller.list.name.upper'] = strtoupper($listName);
		$replace['controller.list.name.cap']   = ucfirst($listName);

		$replace['controller.item.name.lower'] = strtolower($itemName);
		$replace['controller.item.name.upper'] = strtoupper($itemName);
		$replace['controller.item.name.cap']   = ucfirst($itemName);

		// Set replace to config.
		foreach ($replace as $key => $val)
		{
			$config->set('replace.' . $key, $val);
		}

		// Set copy dir.
		$config->set('dir.dest', PathHelper::get(strtolower($config['element']), $config['client']));

		$config->set('dir.tmpl', GENERATOR_BUNDLE_PATH . '/Template/' . $config['extension'] . '/' . $config['template']);

		$config->set('dir.src', $config->get('dir.tmpl') . '/' . $config['client']);

		// Replace DS
		$config['dir.dest'] = Path::clean($config['dir.dest']);

		$config['dir.tmpl'] = Path::clean($config['dir.tmpl']);

		$config['dir.src'] = Path::clean($config['dir.src']);

		// Push container
		$this->container = $container;

		parent::__construct($io, $config, $replace);
	}
}
