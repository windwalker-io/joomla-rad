<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\View\Html;

use Windwalker\DI\Container;
use Windwalker\Model\Model;
use Windwalker\View\AbstractView;
use Windwalker\View\Engine\EngineInterface;
use Windwalker\View\Engine\PhpEngine;

/**
 * Abstract Html view.
 *
 * @since 2.0
 */
abstract class AbstractHtmlView extends AbstractView
{
	/**
	 * The view layout.
	 *
	 * @var  string
	 */
	protected $layout = 'default';

	/**
	 * The paths queue.
	 *
	 * @var  \SplPriorityQueue
	 */
	protected $paths = null;

	/**
	 * The list name.
	 *
	 * @var  string
	 */
	protected $viewList = null;

	/**
	 * The item name.
	 *
	 * @var  string
	 */
	protected $viewItem = null;

	/**
	 * The engine object.
	 *
	 * @var  EngineInterface
	 */
	protected $engine = null;

	/**
	 * Method to instantiate the view.
	 *
	 * @param Model             $model     The model object.
	 * @param Container         $container DI Container.
	 * @param array             $config    View config.
	 * @param \SplPriorityQueue $paths     Paths queue.
	 */
	public function __construct(Model $model = null, Container $container = null, $config = array(), \SplPriorityQueue $paths = null)
	{
		if (!empty($config['engine']) && $config['engine'] instanceof EngineInterface)
		{
			$this->engine = $config['engine'];
		}

		parent::__construct($model, $container, $config);

		// Setup dependencies.
		$this->paths = $paths ? : $this->loadPaths();
	}

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return  string  The escaped output.
	 */
	public function escape($output)
	{
		// Escape the output.
		return htmlspecialchars($output, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Set the flash message.
	 *
	 * @param string $msgs The message list.
	 * @param string $type The message type.
	 *
	 * @return AbstractHtmlView Return self to support chaining.
	 */
	public function flash($msgs, $type = 'message')
	{
		$app  = $this->getContainer()->get('app');
		$msgs = (array) $msgs;

		foreach ($msgs as $msg)
		{
			$app->enqueueMessage($msg, $type);
		}

		return $this;
	}

	/**
	 * Method to get the view paths.
	 *
	 * @return  \SplPriorityQueue  The paths queue.
	 */
	public function getPaths()
	{
		return $this->paths;
	}

	/**
	 * Method to render the view.
	 *
	 * @return  string The output of rendered.
	 *
	 * @throws \RuntimeException
	 */
	protected function doRender()
	{
		$engine = $this->getEngine();

		$engine->setPaths($this->paths)
			->setContainer($this->container);

		return $engine->render($this->layout, $this->data);
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return  HtmlView  Method supports chaining.
	 */
	public function setLayout($layout)
	{
		$this->layout = $layout;

		return $this;
	}

	/**
	 * Get layout.
	 *
	 * @return  string Layout name.
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * Method to set the view paths.
	 *
	 * @param   \SplPriorityQueue  $paths  The paths queue.
	 *
	 * @return  HtmlView  Method supports chaining.
	 */
	public function setPaths(\SplPriorityQueue $paths)
	{
		$this->paths = $paths;

		return $this;
	}

	/**
	 * Method to load the paths queue.
	 *
	 * @return  \SplPriorityQueue  The paths queue.
	 */
	protected function loadPaths()
	{
		return new \SplPriorityQueue;
	}

	/**
	 * Get engine object.
	 *
	 * @return  EngineInterface The engine object
	 */
	public function getEngine()
	{
		if (!($this->engine instanceof EngineInterface))
		{
			$this->engine = new PhpEngine;
		}

		return $this->engine;
	}

	/**
	 * Set engine.
	 *
	 * @param   EngineInterface $engine The engine object.
	 *
	 * @return  AbstractHtmlView  Return self to support chaining.
	 */
	public function setEngine(EngineInterface $engine)
	{
		$this->engine = $engine;

		return $this;
	}
}
