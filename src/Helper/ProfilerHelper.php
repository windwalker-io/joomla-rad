<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Helper;

use JProfiler;
use Windwalker\DI\Container;

/**
 * Profiler Helper
 *
 * @since 2.0
 */
class ProfilerHelper
{
	/**
	 * The profiler instances storage.
	 *
	 * @var  JProfiler[]
	 */
	protected static $profiler = array();

	/**
	 * State buffer.
	 *
	 * @var mixed
	 */
	protected static $stateBuffer = array();

	/**
	 * A helper to add JProfiler log mark. Need to turn on the debug mode.
	 *
	 * @param   string $text      Log text.
	 * @param   string $namespace The JProfiler instance ID. Default is the core profiler "Application".
	 *
	 * @return  void
	 */
	public static function mark($text, $namespace = 'Windwalker')
	{
		$container = Container::getInstance();
		$app = $container->get('app');

		if ($namespace == 'core' || !$namespace)
		{
			$namespace = 'Application';
		}

		if (!$container->get('joomla.config')->get('debug'))
		{
			return;
		}

		if (!isset(self::$profiler[$namespace]))
		{
			self::$profiler[$namespace] = JProfiler::getInstance($namespace);

			// Get last page logs.
			self::$stateBuffer[$namespace] = $app->getUserState('windwalker.system.profiler.' . $namespace);
		}

		self::$profiler[$namespace]->mark($text);

		// Save in session
		$app->setUserState('windwalker.system.profiler.' . $namespace, self::$profiler[$namespace]->getBuffer());
	}

	/**
	 * Render the profiler log data, and echo it..
	 *
	 * @param   string   $namespace The JProfiler instance ID. Default is the core profiler "Application".
	 * @param   boolean  $asString  Return as string.
	 *
	 * @return  string
	 */
	public static function render($namespace = 'Windwalker', $asString = false)
	{
		$app = Container::getInstance()->get('app');

		if ($namespace == 'core' || !$namespace)
		{
			$namespace = 'Application';
		}

		if (isset(self::$profiler[$namespace]))
		{
			$_PROFILER = self::$profiler[$namespace];

			$buffer = $_PROFILER->getBuffer();
			$buffer = implode("\n<br />\n", $buffer);
		}
		else
		{
			$buffer = $app->getUserState('windwalker.system.profiler.' . $namespace, array());
			$buffer = $buffer ? implode("\n<br />\n", $buffer) : '';
		}

		$buffer = $buffer ? $buffer : 'No Profiler data.';

		// Get last page logs
		$state_buffer = ArrayHelper::getValue(self::$stateBuffer, $namespace);

		if ($state_buffer)
		{
			$state_buffer = implode("\n<br />\n", $state_buffer);
			$buffer       = $state_buffer . "\n<br />---------<br />\n" . $buffer;
		}

		// Render
		$buffer = "<pre><h3>WindWalker Debug [namespace: {$namespace}]: </h3>" . $buffer . '</pre>';

		$app->setUserState('windwalker.system.profiler.' . $namespace, '');

		if ($asString)
		{
			return $buffer;
		}

		echo $buffer;

		return '';
	}

	/**
	 * Get a profiler instance with a namespace
	 *
	 * @param   string  $namespace The JProfiler instance ID. Default is the core profiler "Application".
	 *
	 * @return  JProfiler|null
	 */
	public static function getProfiler($namespace = 'Windwalker')
	{
		if (isset(static::$profiler[$namespace]))
		{
			return static::$profiler[$namespace];
		}

		return null;
	}

	/**
	 * Set a profiler instance with a namespace
	 *
	 * @param   string    $namespace The JProfiler instance ID. Default is the core profiler "Application".
	 * @param   JProfiler $profiler  The JProfiler instance.
	 *
	 * @return  void
	 */
	public static function setProfiler($namespace = 'Windwalker', JProfiler $profiler)
	{
		static::$profiler[$namespace] = $profiler;
	}
}
