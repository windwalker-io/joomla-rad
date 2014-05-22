<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Api;

use Joomla\Registry\Registry;
use Windwalker\Api\Authentication\Authentication;
use Windwalker\System\ExtensionHelper;
use Windwalker\Api\Response\JsonResponse;

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
	 * Property option.
	 *
	 * @var  \Joomla\Registry\Registry
	 */
	protected $option = null;

	/**
	 * Property uri.
	 *
	 * @var  \JUri
	 */
	protected $uri = null;

	/**
	 * Class init.
	 *
	 * @param  string   $element The component option name.
	 * @param  \JUri    $uri     The uri object.
	 * @param  Registry $option  The option of this server.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($element, \JUri $uri, Registry $option = null)
	{
		$extracted = ExtensionHelper::extractElement($element);
		$this->option = $option ? : new Registry;
		$this->uri = $uri;

		if ($extracted['type'] !== 'component')
		{
			throw new \InvalidArgumentException('Please give me a component name like `com_flower`.');
		}

		$this->component = $extracted['name'];
	}

	/**
	 * Register the API server.
	 *
	 * @throws \Exception
	 * @return  boolean
	 */
	public function register()
	{
		$uri = \JURI::getInstance();

		if (!$this->isApi())
		{
			return false;
		}

		$app   = \JFactory::getApplication();
		$input = $app->input;

		// Restore Joomla handler and using our Json handler.
		JsonResponse::registerErrorHandler();

		// Authentication
		if (!$this->isUserOperation($uri) && $this->option['authorise'])
		{
			if (!Authentication::authenticate($input->get('session_key')))
			{
				throw new \Exception(\JText::_('JERROR_ALERTNOAUTHOR'), 403);
			}
		}

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
	public function isApi(\JUri $uri = null)
	{
		$uri   = $uri ? : $this->uri;
		$path  = $uri->getPath();
		$root  = \JUri::root(true);
		$route = substr($path, strlen($root));

		return (strpos($route, '/api') === 0);
	}

	/**
	 * isRoot
	 *
	 * @param \JUri $uri
	 *
	 * @return  bool
	 */
	public function isRoot(\JUri $uri = null)
	{
		$uri  = $uri ? : $this->uri;
		$path = $uri->getPath();

		return rtrim($path, '/') == '/api';
	}

	/**
	 * isUserOperation
	 *
	 * @param \JUri $uri
	 *
	 * @return  boolean
	 */
	public function isUserOperation(\JUri $uri)
	{
		$path  = $uri->getPath();
		$root  = \JUri::root(true);
		$route = substr($path, strlen($root));

		return (strpos($route, '/api/user') === 0);
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
		if ($this->isRoot($uri))
		{
			throw new \InvalidArgumentException('No method.', 404);
		}

		// Direct our URI to component
		$path = 'component/' . $this->component . '/' . $path;
		$uri->setPath($path);
		$uri->setVar('format', 'json');

		return array();
	}
}
