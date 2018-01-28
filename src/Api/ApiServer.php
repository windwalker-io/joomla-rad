<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Api;

use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Router;
use Joomla\CMS\Uri\Uri;
use Windwalker\Api\Authentication\Authentication;
use Windwalker\Api\Response\JsonResponse;
use Windwalker\Registry\Registry;
use Windwalker\System\ExtensionHelper;

/**
 * API server.
 *
 * @since 2.0
 *
 * @deprecated  API server will be re-write after Windwalker RAD 3.
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
	 * @var  \Joomla\CMS\Uri\Uri
	 */
	protected $uri = null;

	/**
	 * Class init.
	 *
	 * @param  string   $element The component option name.
	 * @param  Uri      $uri     The uri object.
	 * @param  Registry $option  The option of this server.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($element, Uri $uri, Registry $option = null)
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
		$uri = Uri::getInstance();

		if (!$this->isApi())
		{
			return false;
		}

		$app   = Factory::getApplication();
		$input = $app->input;

		// Restore Joomla handler and using our Json handler.
		JsonResponse::registerErrorHandler();

		// Authentication
		if (!$this->isUserOperation($uri) && $this->option['authorise'])
		{
			if (!Authentication::authenticate($input->get('session_key')))
			{
				throw new \Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
			}
		}

		// Set Format to JSON
		$input->set('format', 'json');

		// Store JDocumentJson to Factory
		Factory::$document = Document::getInstance('json');

		$router = $app::getRouter();

		// Attach a hook to Router
		$router->attachParseRule(array($this, 'parseRule'));

		return true;
	}

	/**
	 * Is the uri pattern match api rule?
	 *
	 * @param  Uri  $uri The Uri object.
	 *
	 * @return  boolean True is api server.
	 */
	public function isApi(Uri $uri = null)
	{
		$uri   = $uri ? : $this->uri;
		$path  = $uri->getPath();
		$root  = Uri::root(true);
		$route = substr($path, strlen($root));

		return (strpos($route, '/api') === 0);
	}

	/**
	 * isRoot
	 *
	 * @param Uri $uri
	 *
	 * @return  bool
	 */
	public function isRoot(Uri $uri = null)
	{
		$uri  = $uri ? : $this->uri;
		$path = $uri->getPath();

		return rtrim($path, '/') === '/api';
	}

	/**
	 * isUserOperation
	 *
	 * @param Uri $uri
	 *
	 * @return  boolean
	 */
	public function isUserOperation(Uri $uri)
	{
		$path  = $uri->getPath();
		$root  = Uri::root(true);
		$route = substr($path, strlen($root));

		return (strpos($route, '/api/user') === 0);
	}

	/**
	 * Parse rule hook.
	 *
	 * @param Router $router The router object.
	 * @param Uri    $uri    The uri object.
	 *
	 * @return  array
	 *
	 * @throws \InvalidArgumentException
	 */
	public function parseRule(Router $router, Uri $uri)
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
