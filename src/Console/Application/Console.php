<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Console\Application;

use Windwalker\Console\IO\IOInterface;
use Windwalker\DI\Container;
use Windwalker\Console\Descriptor\CommandDescriptor;
use Windwalker\Console\Descriptor\OptionDescriptor;
use Windwalker\Registry\Registry;

/**
 * Console Class.
 *
 * @since  2.0
 */
class Console extends \Windwalker\Console\Console
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
	protected $version = '2.1';

	/**
	 * The DI container.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Class init.
	 *
	 * @param   IOInterface $io      The Input and output handler.
	 * @param   Registry    $config  Application's config object.
	 */
	public function __construct(IOInterface $io = null, \Windwalker\Registry\Registry $config = null)
	{
		$this->loadDispatcher();

		$io = $io ? : $this->getContainer()->get('input');

		// Make Windows no ANSI color
		if (defined('PHP_WINDOWS_VERSION_BUILD'))
		{
			$io->setOption('ansi', true);
		}

		parent::__construct($io, $config);

		$this->rootCommand
			->help(
<<<HELP
Welcome to Windwalker Console.

HELP
			);

		$descriptorHelper = $this->getDescriptor();

		$descriptorHelper->setOptionDescriptor(new OptionDescriptor)
			->setCommandDescriptor(new CommandDescriptor);
		
		/*
		 * Note: The below code CANNOT change from instantiating a session via JFactory until there is a proper dependency injection container supported
		 * by the application. The current default behaviours result in this method being called each time an application class is instantiated.
		 * https://github.com/joomla/joomla-cms/issues/12108 explains why things will crash and burn if you ever attempt to make this change
		 * without a proper dependency injection container.
		 */
		$session = \JFactory::getSession();
		$session->initialise(new \JInput, $this->dispatcher);

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
	 * @return  static
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
