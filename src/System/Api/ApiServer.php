<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\System\Api;

use Windwalker\System\ExtensionHelper;
use Windwalker\View\Json\JsonResponse;

/**
 * API server.
 *
 * @since 2.0
 */
class ApiServer
{
	/**
	 * The component name.
	 *
	 * @var  string
	 */
	protected $component = null;

	/**
	 * Class init.
	 *
	 * @param  string $option The component option name.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($option)
	{
		$extracted = ExtensionHelper::extractElement($option);

		if ($extracted['type'] !== 'component')
		{
			throw new \InvalidArgumentException('Please give me a component name like `com_flower`.');
		}

		$this->component = $extracted['name'];
	}

	/**
	 * Register the API server.
	 *
	 * @return  boolean
	 */
	public function register()
	{
		if (!$this->isApi(\JURI::getInstance()))
		{
			return false;
		}

		$app   = \JFactory::getApplication();
		$input = $app->input;

		// Restore Joomla handler and using our Json handler.
		JsonResponse::registerErrorHandler();

		// Set Format to JSON
		$input->set('format', 'json');

		// Store JDocumentJson to Factory
		\JFactory::$document = \JDocument::getInstance('json');

		$router = $app::getRouter();

		// Attach a hook to Router
		$router->attachParseRule(array($this, 'parseRule'));

		return true;
	}

	/**
	 * Is the uri pattern match api rule?
	 *
	 * @param  \JUri  $uri The Uri object.
	 *
	 * @return  boolean True is api server.
	 */
	public function isApi(\JUri $uri)
	{
		$path  = $uri->getPath();
		$root  = \JUri::root(true);
		$route = substr($path, strlen($root));

		return (strpos($route, '/api') === 0);
	}

	/**
	 * Parse rule hook.
	 *
	 * @param \JRouter $router The router object.
	 * @param \JUri    $uri    The uri object.
	 *
	 * @return  array
	 *
	 * @throws \InvalidArgumentException
	 */
	public function parseRule(\JRouter $router, \JUri $uri)
	{
		$path = $uri->getPath();

		// No path & method, return 404.
		if (trim($path, '/') == 'api')
		{
			throw new \InvalidArgumentException('No method.', 404);
		}

		// Direct our URI to iCRM
		$path = 'component/' . $this->component . '/' . $path;
		$uri->setPath($path);
		$uri->setVar('format', 'json');

		return array();
	}
}
