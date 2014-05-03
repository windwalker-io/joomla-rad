<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\View\Json;

use Joomla\DI\Container;
use Windwalker\Model\Model;
use Joomla\Registry\Registry;
use Windwalker\View\AbstractView;

/**
 * Abstract JSON view.
 *
 * @since 2.0
 */
abstract class AbstractJsonView extends AbstractView
{
	/**
	 * The data object.
	 *
	 * @var Registry
	 */
	protected $data;

	/**
	 * Method to instantiate the view.
	 *
	 * @param Model      $model     The model object.
	 * @param Container  $container DI Container.
	 * @param array      $config    View config.
	 */
	public function __construct(Model $model = null, Container $container = null, $config = array())
	{
		parent::__construct($model, $container, $config);

		$this->data = new Registry;
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
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @throws  \RuntimeException
	 */
	public function doRender()
	{
		return JsonResponse::response($this->data->toArray());
	}

	/**
	 * getData
	 *
	 * @return \JData
	 */
	public function getData()
	{
		if (!$this->data)
		{
			$this->data = new Registry;
		}

		return $this->data;
	}
}
