<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

use Joomla\Application\AbstractCliApplication;
use Windwalker\Filesystem\Path;
use Windwalker\String\StringNormalise;
use Windwalker\Utilities\Reflection\ReflectionHelper;

$autoload = __DIR__ . '/../vendor/autoload.php';

if (!is_file($autoload))
{
	$autoload = __DIR__ . '/../../../autoload.php';
}

include_once $autoload;

define('WINDWALKER_ROOT', realpath(__DIR__ . '/..'));
define('WINDWALKER_CORE_ROOT', realpath(__DIR__ . '/../vendor/windwalker/core'));

/**
 * Class GenTest
 *
 * @since 1.0
 */
class GenTest extends AbstractCliApplication
{
	/**
	 * Property lastOutput.
	 *
	 * @var  mixed
	 */
	protected $lastOutput = null;

	/**
	 * Property lastReturn.
	 *
	 * @var  mixed
	 */
	protected $lastReturn = null;

	/**
	 * Method to run the application routines.  Most likely you will want to instantiate a controller
	 * and execute it, or perform some sort of task directly.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function doExecute()
	{
		$package = $this->io->getArgument(0);
		$class   = $this->io->getArgument(1);
		$class   = StringNormalise::toClassNamespace($class);

		if (!class_exists($class))
		{
			$class = 'Windwalker\\Core\\' . $package . '\\' . $class;
		}

		if (!class_exists($class))
		{
			$this->stop('Class not exists: ' . $class);
		}

		$classPath     = ReflectionHelper::getPath($class);
		$testPath      = WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'test';
		$testClass     = $this->io->getArgument(2, ReflectionHelper::getShortName($class) . 'Test');
		$testClass     = StringNormalise::toClassNamespace($testClass);
		$testFile      = $testPath . DIRECTORY_SEPARATOR . ucfirst($package) . DIRECTORY_SEPARATOR . Path::clean($testClass) . '.php';
		$realTestClass = 'Windwalker\\Core\\Test\\' . ucfirst($package) . '\\' . $testClass;
		$autoload      = WINDWALKER_ROOT . '/vendor/autoload.php';

		$skelgen = 'vendor/phpunit/phpunit-skeleton-generator/phpunit-skelgen';

		if (!is_file(WINDWALKER_ROOT . '/' . $skelgen))
		{
			$skelgen = '../../phpunit/phpunit-skeleton-generator/phpunit-skelgen';
		}

		$command = sprintf(
			$skelgen . ' generate-test --bootstrap="%s" %s %s %s %s',
			$autoload,
			$class,
			$classPath,
			$realTestClass,
			$testFile
		);

		$command = 'php ' . WINDWALKER_ROOT . '/' . $command;

		if (!defined('PHP_WINDOWS_VERSION_MAJOR'))
		{
			// Replace '\' to '\\' in MAC
			$command = str_replace('\\', '\\\\', $command);
		}

		\Windwalker\Filesystem\Folder::create(dirname($testFile));

		$this->exec($command);
	}

	/**
	 * getPackagePath
	 *
	 * @param string $class
	 * @param string $classPath
	 *
	 * @return  void
	 */
	protected function getPackagePath($class, $classPath)
	{
		$classFile = Path::clean($class) . '.php';
		$classFile = substr($classFile, 11);
		$this->out($classFile);
		$this->out($classPath);
		$packagePath = str_replace($classFile, '', $classPath);
		$this->out($packagePath);
		print_r($classFile);
	}

	/**
	 * Exec a command.
	 *
	 * @param string $command
	 * @param array  $arguments
	 * @param array  $options
	 *
	 * @return  string
	 */
	protected function exec($command, $arguments = array(), $options = array())
	{
		$arguments = implode(' ', (array) $arguments);
		$options   = implode(' ', (array) $options);
		$command   = sprintf('%s %s %s', $command, $arguments, $options);

		$this->out('>> ' . $command);

		$return = exec(trim($command), $this->lastOutput, $this->lastReturn);

		$this->out($return);
	}

	/**
	 * stop
	 *
	 * @param string $msg
	 *
	 * @return  void
	 */
	protected function stop($msg = null)
	{
		if ($msg)
		{
			$this->out($msg);
		}

		$this->close();
	}
}

$app = new GenTest;
$app->execute();
