<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace GeneratorBundle\Controller\Test;

use GeneratorBundle\Action\Test\GenClassAction;
use Muse\Filesystem\Path;
use Windwalker\Registry\Registry;
use Muse\Controller\AbstractTaskController;
use Muse\IO\IOInterface;
use Windwalker\Console\Prompter\ValidatePrompter;
use Windwalker\DI\Container;
use Windwalker\String\StringHelper;
use Windwalker\String\StringNormalise;

/**
 * The GenController class.
 * 
 * @since  2.1
 */
class GenController extends AbstractTaskController
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
//		// Set copy dir.
//		$config->set('dir.dest', PathHelper::get(strtolower($config['element']), $config['client']));
//
//		$config->set('dir.tmpl', GENERATOR_BUNDLE_PATH . '/Template/' . $config['extension'] . '/' . $config['template']);
//
//		$config->set('dir.src', $config->get('dir.tmpl') . '/' . $config['client']);
//
//		// Replace DS
//		$config['dir.dest'] = Path::clean($config['dir.dest']);
//
//		$config['dir.tmpl'] = Path::clean($config['dir.tmpl']);
//
//		$config['dir.src'] = Path::clean($config['dir.src']);

		// Push container
		$this->container = $container;

		parent::__construct($io, $config);
	}

	/**
	 * Execute the controller.
	 *
	 * @return  boolean  True if controller finished execution, false if the controller did not
	 *                   finish execution. A controller might return false if some precondition for
	 *                   the controller to run has not been satisfied.
	 *
	 * @throws  \LogicException
	 * @throws  \RuntimeException
	 */
	public function execute()
	{
		$package = $this->io->getArgument(0, new ValidatePrompter('Enter package name: '));
		$class   = $this->io->getArgument(1, new ValidatePrompter('Enter class name: '));
		$class   = StringNormalise::toClassNamespace($class);
		$target  = $this->io->getArgument(2, $package . '\\' . $class . 'Test');
		$target  = StringNormalise::toClassNamespace($target);
		$package = ucfirst($package);

		if (!class_exists($class))
		{
			$class = 'Windwalker\\' . $package . '\\' . $class;
		}

		if (!class_exists($class))
		{
			$this->out('Class not exists: ' . $class);

			exit();
		}

		$replace = $this->replace;

		$ref = new \ReflectionClass($class);

		$replace['origin.class.dir']  = dirname($ref->getFileName());
		$replace['origin.class.file'] = $ref->getFileName();
		$replace['origin.class.name'] = $ref->getName();
		$replace['origin.class.shortname'] = $ref->getShortName();
		$replace['origin.class.namespace'] = $ref->getNamespaceName();

		$replace['test.dir'] = WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'test';

		$replace['test.class.name'] = 'Windwalker\\Test\\' . $target;
		$replace['test.class.file'] = Path::clean($replace['test.dir'] . DIRECTORY_SEPARATOR . $target . '.php');
		$replace['test.class.dir']  = dirname($replace['test.class.file']);
		$replace['test.class.shortname'] = $this->getShortname(StringNormalise::toClassNamespace($replace['test.class.name']));
		$replace['test.class.namespace'] = $this->getNamespace($replace['test.class.name']);

		$this->replace = $replace;

		$config = new Registry;

		// Set replace to config.
		foreach ($this->replace as $key => $val)
		{
			$config->set('replace.' . $key, $val);
		}

		$methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
		$methodTmpl = file_get_contents(GENERATOR_BUNDLE_PATH . '/Template/test/testMethod.php');
		$methodCodes = array();

		foreach ($methods as $method)
		{
			$config['replace.origin.method'] = $method->getName();
			$config['replace.test.method'] = ucfirst($method->getName());

			$methodCodes[] = StringHelper::parseVariable($methodTmpl, $config->get('replace'));
		}

		$config['replace.test.methods'] = implode("", $methodCodes);

		$this->replace = $config->get('replace');
		$this->config = $config;

		$this->doAction(new GenClassAction);

		$this->out('Generate test class: ' . $replace['test.class.name'] . ' to file: ' . $replace['test.class.file'])->out();

		return true;
	}

	/**
	 * getShortname
	 *
	 * @param string $class
	 *
	 * @return  mixed
	 */
	protected function getShortname($class)
	{
		$class = explode('\\', $class);

		return array_pop($class);
	}

	/**
	 * getNamespace
	 *
	 * @param string $class
	 *
	 * @return  string
	 */
	protected function getNamespace($class)
	{
		$class = explode('\\', $class);

		array_pop($class);

		return implode('\\', $class);
	}
}
