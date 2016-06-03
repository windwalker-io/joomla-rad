<?php
/**
 * Part of rad project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Router;

use Joomla\Uri\Uri;
use Windwalker\Test\TestHelper;

/**
 * The SiteRoute class.
 *
 * @since  2.1.5
 */
class SiteRoute
{
	/**
	 * Property defaultOption.
	 *
	 * @var  string
	 */
	protected static $defaultOption;

	/**
	 * Property router.
	 *
	 * @var  \JRouterSite
	 */
	protected static $router;

	/**
	 * Build by resource.
	 *
	 * @param   string   $resource The resource key to find our route.
	 * @param   array    $data     The url query data.
	 * @param   boolean  $xhtml    Replace & by &amp; for XML compilance.
	 * @param   integer  $ssl      Secure state for the resolved URI.
	 *                             1: Make URI secure using global secure site URI.
	 *                             2: Make URI unsecure using the global unsecure site URI.
	 *
	 * @return  string Route url.
	 */
	public static function _($resource, $data = array(), $xhtml = true, $ssl = null)
	{
		// Replace all '.' and ':' to '@' to make it B/C
		$resource = str_replace(array('.', ':'), '@', $resource);

		if (static::$defaultOption && strpos($resource, '@') === false)
		{
			$resource = static::$defaultOption . '@' . $resource;
		}

		$resource = explode('@', $resource, 2);

		if (count($resource) == 2)
		{
			$data['option']    = $resource[0];
			$data['_resource'] = $resource[1];
		}
		elseif (count($resource) == 1)
		{
			$data['option']    = $resource[0];
			$data['_resource'] = null;
		}

		$url = new Uri;

		$url->setQuery($data);

		$url->setPath('index.php');

		return static::jroute((string) $url, $xhtml, $ssl);
	}

	/**
	 * Translates an internal Joomla URL to a humanly readable URL.
	 *
	 * @param   string   $url    Absolute or Relative URI to Joomla resource.
	 * @param   boolean  $xhtml  Replace & by &amp; for XML compliance.
	 * @param   integer  $ssl    Secure state for the resolved URI.
	 *                             0: (default) No change, use the protocol currently used in the request
	 *                             1: Make URI secure using global secure site URI.
	 *                             2: Make URI unsecure using the global unsecure site URI.
	 *
	 * @return string The translated humanly readable URL.
	 */
	public static function jroute($url, $xhtml = true, $ssl = null)
	{
		if (!static::$router)
		{
			static::$router = static::getRouter();
		}

		if (!is_array($url) && (strpos($url, '&') !== 0) && (strpos($url, 'index.php') !== 0))
		{
			return $url;
		}

		// Backup base with frontend root
		$base = TestHelper::getValue('JUri', 'base');
		TestHelper::setValue('JUri', 'base', TestHelper::getValue('JUri', 'root'));

		// Build route.
		/** @var Uri $uri */
		$uri = static::$router->build($url);

		// Restore base
		TestHelper::setValue('JUri', 'base', $base);

		$scheme = array('path', 'query', 'fragment');

		/*
		 * Get the secure/unsecure URLs.
		 *
		 * If the first 5 characters of the BASE are 'https', then we are on an ssl connection over
		 * https and need to set our secure URL to the current request URL, if not, and the scheme is
		 * 'http', then we need to do a quick string manipulation to switch schemes.
		 */
		if ((int) $ssl || $uri->isSSL())
		{
			static $host_port;

			if (!is_array($host_port))
			{
				$uri2 = \JUri::getInstance();
				$host_port = array($uri2->getHost(), $uri2->getPort());
			}

			// Determine which scheme we want.
			$uri->setScheme(((int) $ssl === 1 || $uri->isSSL()) ? 'https' : 'http');
			$uri->setHost($host_port[0]);
			$uri->setPort($host_port[1]);
			$scheme = array_merge($scheme, array('host', 'port', 'scheme'));
		}

		$url = $uri->toString($scheme);

		// Replace spaces.
		$url = preg_replace('/\s/u', '%20', $url);

		if ($xhtml)
		{
			$url = htmlspecialchars($url);
		}

		return $url;
	}

	/**
	 * getRouter
	 *
	 * @return  \JRouterSite
	 */
	protected static function getRouter()
	{
		return \JRouter::getInstance('site', ['mode' => JROUTER_MODE_SEF]);
	}
}
