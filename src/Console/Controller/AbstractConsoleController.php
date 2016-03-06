<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Console\Controller;

use Windwalker\Console\Application\Console;
use Windwalker\Console\Command\Command;
use Windwalker\Console\IO\IO;

/**
 * Windwalker Console Base Controller Class
 *
 * @since  2.1
 */
abstract class AbstractConsoleController implements \JController
{
	/**
	 * The application object.
	 *
	 * @var  Console
	 */
	protected $app;

	/**
	 * The input object.
	 *
	 * @var  IO
	 */
	protected $input;

	/**
	 * Property io.
	 *
	 * @var IO
	 */
	protected $io;

	/**
	 * Property command.
	 *
	 * @var  Command
	 */
	protected $command;

	/**
	 * Instantiate the controller.
	 *
	 * @param   Command  $command  The command object.
	 */
	public function __construct(Command $command)
	{
		// Setup dependencies.
		$this->app     = $command->getApplication() ? : $this->loadApplication();
		$this->input   = $this->io = $command->getIO();
		$this->command = $command;
	}

	/**
	 * Get the application object.
	 *
	 * @return  Console  The application object.
	 */
	public function getApplication()
	{
		return $this->app;
	}

	/**
	 * Get the input object.
	 *
	 * @return  IO  The input object.
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * Serialize the controller.
	 *
	 * @return  string  The serialized controller.
	 */
	public function serialize()
	{
		return serialize($this->input);
	}

	/**
	 * Unserialize the controller.
	 *
	 * @param   string  $input  The serialized controller.
	 *
	 * @return  AbstractConsoleController  Supports chaining.
	 *
	 * @throws  \UnexpectedValueException if input is not the right class.
	 */
	public function unserialize($input)
	{
		// Setup dependencies.
		$this->app = $this->loadApplication();

		// Unserialize the input.
		$this->input = unserialize($input);

		if (!($this->input instanceof IO))
		{
			throw new \UnexpectedValueException(sprintf('%s::unserialize would not accept a `%s`.', get_class($this), gettype($this->input)));
		}

		return $this;
	}

	/**
	 * Load the application object.
	 *
	 * @return  Console  The application object.
	 */
	protected function loadApplication()
	{
		return \JFactory::getApplication();
	}

	/**
	 * Method to get property Io
	 *
	 * @return  IO
	 */
	public function getIO()
	{
		return $this->io;
	}

	/**
	 * Method to set property io
	 *
	 * @param   IO $io
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setIO($io)
	{
		$this->io = $io;

		return $this;
	}

	/**
	 * Load the input object.
	 *
	 * @return  IO  The input object.
	 */
	protected function loadInput()
	{
		return $this->app->getIO();
	}

	/**
	 * Write a string to standard output.
	 *
	 * @param   string   $text  The text to display.
	 * @param   boolean  $nl    True (default) to append a new line at the end of the output string.
	 *
	 * @return  Command  Instance of $this to allow chaining.
	 */
	public function out($text = '', $nl = true)
	{
		$this->command->out($text, $nl);

		return $this;
	}

	/**
	 * Write a string to standard error output.
	 *
	 * @param   string   $text  The text to display.
	 * @param   boolean  $nl    True (default) to append a new line at the end of the output string.
	 *
	 * @return  Command  Instance of $this to allow chaining.
	 */
	public function err($text = '', $nl = true)
	{
		$this->command->err($text, $nl);

		return $this;
	}

	/**
	 * Get a value from standard input.
	 *
	 * @param   string  $question  The question you want to ask user.
	 *
	 * @return  string  The input string from standard input.
	 */
	public function in($question = '')
	{
		return $this->command->in($question);
	}

	/**
	 * Close this application.
	 *
	 * @param   string $text
	 * @param   bool   $nl
	 *
	 * @return  void
	 */
	public function close($text = '', $nl = false)
	{
		$this->out($text, $nl);

		die;
	}
}
