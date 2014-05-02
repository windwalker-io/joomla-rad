<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Console\Application;

use Joomla\Application\Cli\CliOutput;
use Joomla\Application\Cli\Output;
use Joomla\Console\Console as JoomlaConsole;
use Joomla\Input;
use Joomla\Registry\Registry;

use Windwalker\Console\Descriptor\CommandDescriptor;
use Windwalker\DI\Container;
use Windwalker\Console\Descriptor\OptionDescriptor;

/**
 * Console Class.
 *
 * @since  2.0
 */
class Console extends JoomlaConsole
{
	/**
	 * The application dispatcher object.
	 *
	 * @var    \JEventDispatcher
	 */
	protected $dispatcher;

	/**
	 * The Console title.
	 *
	 * @var  string
	 */
	protected $name = 'Windwalker Console';

	/**
	 * Version of this application.
	 *
	 * @var string
	 */
	protected $version = '2.0';

	/**
	 * The DI container.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Class constructor.
	 *
	 * @param   Input\Cli  $input   An optional argument to provide dependency injection for the application's
	 *                              input object.  If the argument is a InputCli object that object will become
	 *                              the application's input object, otherwise a default input object is created.
	 *
	 * @param   Registry   $config  An optional argument to provide dependency injection for the application's
	 *                              config object.  If the argument is a Registry object that object will become
	 *                              the application's config object, otherwise a default config object is created.
	 *
	 * @param   CliOutput  $output  The output handler.
	 */
	public function __construct(Input\Cli $input = null, Registry $config = null, CliOutput $output = null)
	{
		$this->loadDispatcher();

		$input = $input ? : $this->getContainer()->get('input');

		// Make Windows no ANSI color
		if (defined('PHP_WINDOWS_VERSION_BUILD'))
		{
			$input->set('no-ansi', true);
		}

		\JFactory::$application = $this;

		parent::__construct($input, $config, $output);

		$this->rootCommand
			->setHelp(
<<<HELP
Welcome to Windwalker Console.

HELP
			);

		$descriptorHelper = $this->rootCommand->getChild('help')
			->getDescriptor();

		$descriptorHelper->setOptionDescriptor(new OptionDescriptor)
			->setCommandDescriptor(new CommandDescriptor);

		$this->loadFirstlevelCommands();
	}

	/**
	 * Auto load the first level commands.
	 *
	 * @return void
	 */
	protected function loadFirstlevelCommands()
	{
		try
		{
			\JPluginHelper::importPlugin('windwalker');
		}
		catch (\RuntimeException $e)
		{
			// Do nothing
		}

		foreach ($this->get('bundle') as $bundle)
		{
			$bundle::registerCommands($this);
		}

		$context = get_class($this->rootCommand);

		$this->triggerEvent('onWindwalkerLoadCommand', array($context, $this->rootCommand));
	}

	/**
	 * Allows the application to load a custom or default dispatcher.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create event
	 * dispatchers, if required, based on more specific needs.
	 *
	 * @param   \JEventDispatcher  $dispatcher  An optional dispatcher object. If omitted, the factory dispatcher is created.
	 *
	 * @return  Console This method is chainable.
	 */
	public function loadDispatcher(\JEventDispatcher $dispatcher = null)
	{
		$this->dispatcher = ($dispatcher === null) ? \JEventDispatcher::getInstance() : $dispatcher;

		return $this;
	}

	/**
	 * Calls all handlers associated with an event group.
	 *
	 * @param   string  $event  The event name.
	 * @param   array   $args   An array of arguments (optional).
	 *
	 * @return  array   An array of results from each function call, or null if no dispatcher is defined.
	 */
	public function triggerEvent($event, array $args = null)
	{
		if ($this->dispatcher instanceof \JEventDispatcher)
		{
			return $this->dispatcher->trigger($event, $args);
		}

		return null;
	}

	/**
	 * Is site interface?
	 *
	 * @return  boolean  True if this application is site.
	 */
	public function isSite()
	{
		return true;
	}

	/**
	 * Is admin interface?
	 *
	 * @return  boolean  True if this application is administrator.
	 */
	public function isAdmin()
	{
		return false;
	}

	/**
	 * Enqueue a system message.
	 *
	 * @param   string  $msg   The message to enqueue.
	 * @param   string  $type  The message type. Default is message.
	 *
	 * @return  void
	 */
	public function enqueueMessage($msg, $type = 'message')
	{
		$this->out($msg);

		return $this;
	}

	/**
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @throws  \UnexpectedValueException May be thrown if the container has not been set.
	 */
	public function getContainer()
	{
		if (!$this->container)
		{
			$this->container = Container::getInstance();
		}

		return $this->container;
	}

	/**
	 * Set the DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Console Return self to support chaining.
	 */
	public function setContainer(Container $container)
	{
		$this->container = $container;

		return $this;
	}
}
