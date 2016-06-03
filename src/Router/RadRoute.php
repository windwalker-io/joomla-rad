<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Router;

use Joomla\Uri\Uri;
use Windwalker\Helper\ArrayHelper;

/**
 * Route class to handle single route pattern.
 *
 * @since 2.1
 */
class RadRoute
{
	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = null;

	/**
	 * Property pattern.
	 *
	 * @var  string
	 */
	protected $pattern = null;

	/**
	 * Property regex.
	 *
	 * @var  string
	 */
	protected $regex = null;

	/**
	 * Property vars.
	 *
	 * @var  array
	 */
	protected $vars = array();

	/**
	 * Property allowMethods.
	 *
	 * @var  array
	 */
	protected $allowMethods = array();

	/**
	 * Property variables.
	 *
	 * @var  array
	 */
	protected $variables = array();

	/**
	 * Property requirements.
	 *
	 * @var  array
	 */
	public $requirements = array();

	/**
	 * Property host.
	 *
	 * @var string
	 */
	protected $host;

	/**
	 * Property scheme.
	 *
	 * @var  string
	 */
	protected $scheme = '';

	/**
	 * Property port.
	 *
	 * @var integer
	 */
	protected $port;

	/**
	 * Property sslPort.
	 *
	 * @var integer
	 */
	protected $sslPort;

	/**
	 * Property options.
	 *
	 * @var  array
	 */
	protected $options = array();

	/**
	 * Property ssl.
	 *
	 * @var boolean
	 */
	protected $ssl = false;

	/**
	 * Property extra.
	 *
	 * @var  array
	 */
	protected $extra = array();

	/**
	 * Property defaultOption.
	 *
	 * @var  string
	 */
	protected static $defaultOption;

	/**
	 * Class init.
	 *
	 * @param string       $name
	 * @param string       $pattern
	 * @param array        $variables
	 * @param array|string $allowMethods
	 * @param array        $options
	 */
	public function __construct($name, $pattern, $variables = array(), $allowMethods = array(), $options = array())
	{
		$this->name = $name;
		$this->variables = $variables;

		$this->setPattern($pattern);
		$this->setOptions($options);
		$this->setAllowMethods($allowMethods);
	}

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

		return \JRoute::_((string) $url, $xhtml, $ssl);
	}

	/**
	 * Build route.
	 *
	 * @param   array  &$data The query data to build route.
	 *
	 * @return  string Route url.
	 */
	public static function build(&$data = array())
	{
		$menu = \JFactory::getApplication()->getMenu('site');

		$items = $menu->getMenu();

		$Itemid = isset($data['Itemid']) ? $data['Itemid'] : null;

		$data['view'] = isset($data['view']) ? $data['view'] : null;

		// If itemid exists and view not, use itemid as menu item
		if (isset($data['Itemid']) && empty($data['view']))
		{
			if ($item = $menu->getItem($data['Itemid']))
			{
				$data['Itemid'] = $item->id;

				return $data;
			}
		}

		// Find option, view and id
		if (!empty($data['id']))
		{
			foreach ($items as $item)
			{
				$option = ArrayHelper::getValue($item->query, 'option');
				$view   = ArrayHelper::getValue($item->query, 'view');
				$id     = ArrayHelper::getValue($item->query, 'id');

				if ($option == $data['option'] && $view == $data['view'] && $id == $data['id'])
				{
					$data['view'] = null;
					$data['id'] = null;

					$data['Itemid'] = $item->id;

					return $data;
				}
			}
		}

		// Find option and view
		if (!empty($data['view']))
		{
			foreach ($items as $item)
			{
				$option = ArrayHelper::getValue($item->query, 'option');
				$view   = ArrayHelper::getValue($item->query, 'view');

				if ($option == $data['option'] && $view == $data['view'])
				{
					$data['view'] = null;

					$data['Itemid'] = $item->id;

					return $data;
				}
			}
		}

		// Find option
		if (!$Itemid && empty($data['view']))
		{
			foreach ($items as $item)
			{
				$option = ArrayHelper::getValue($item->query, 'option');

				if ($option == $data['option'])
				{
					$data['Itemid'] = $item->id;

					return $data;
				}
			}
		}

		return $data;
	}

	/**
	 * Method to get property DefaultOption
	 *
	 * @return  string
	 */
	public static function getDefaultOption()
	{
		return static::$defaultOption;
	}

	/**
	 * Method to set property defaultOption
	 *
	 * @param   string $defaultOption
	 *
	 * @return  void
	 */
	public static function setDefaultOption($defaultOption)
	{
		static::$defaultOption = $defaultOption;
	}

	/**
	 * getPattern
	 *
	 * @return  string
	 */
	public function getPattern()
	{
		return $this->pattern;
	}

	/**
	 * setPattern
	 *
	 * @param   string $pattern
	 *
	 * @return  Route  Return self to support chaining.
	 */
	public function setPattern($pattern)
	{
		$this->pattern = RouteHelper::normalise($pattern);

		return $this;
	}

	/**
	 * getRegex
	 *
	 * @return  string
	 */
	public function getRegex()
	{
		return $this->regex;
	}

	/**
	 * setRegex
	 *
	 * @param   string $regex
	 *
	 * @return  Route  Return self to support chaining.
	 */
	public function setRegex($regex)
	{
		$this->regex = $regex;

		return $this;
	}

	/**
	 * getVars
	 *
	 * @return  array
	 */
	public function getVars()
	{
		return $this->vars;
	}

	/**
	 * setVars
	 *
	 * @param   array $vars
	 *
	 * @return  Route  Return self to support chaining.
	 */
	public function setVars($vars)
	{
		$this->vars = $vars;

		return $this;
	}

	/**
	 * getMethod
	 *
	 * @return  string
	 */
	public function getAllowMethods()
	{
		return $this->allowMethods;
	}

	/**
	 * setMethod
	 *
	 * @param   array|string $methods
	 *
	 * @return  Route  Return self to support chaining.
	 */
	public function setAllowMethods($methods)
	{
		$methods = (array) $methods;

		$methods = array_map('strtoupper', $methods);

		$this->allowMethods = $methods;

		return $this;
	}

	/**
	 * getVariables
	 *
	 * @return  array
	 */
	public function getVariables()
	{
		return $this->variables;
	}

	/**
	 * setVariables
	 *
	 * @param   array $variables
	 *
	 * @return  Route  Return self to support chaining.
	 */
	public function setVariables($variables)
	{
		$this->variables = $variables;

		return $this;
	}

	/**
	 * getName
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * setName
	 *
	 * @param   string $name
	 *
	 * @return  Route  Return self to support chaining.
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Method to get property Options
	 *
	 * @return  array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Method to set property options
	 *
	 * @param   array $options
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setOptions($options)
	{
		$options = $this->prepareOptions($options);

		$this->setHost($options['host']);
		$this->setScheme($options['scheme']);
		$this->setPort($options['port']);
		$this->setSslPort($options['sslPort']);
		$this->setRequirements($options['requirements']);
		$this->setExtra($options['extra']);

		return $this;
	}

	/**
	 * prepareOptions
	 *
	 * @param   array $options
	 *
	 * @return  array
	 */
	public function prepareOptions($options)
	{
		$defaultOptions = array(
			'requirements' => array(),
			'options' => array(),
			'host' => null,
			'scheme' => null,
			'port' => null,
			'sslPort' => null,
			'extra' => array()
		);

		return array_merge($defaultOptions, (array) $options);
	}

	/**
	 * Method to get property Options
	 *
	 * @param   string $name
	 * @param   mixed  $default
	 *
	 * @return  mixed
	 */
	public function getOption($name, $default = null)
	{
		if (array_key_exists($name, $this->options))
		{
			return $this->options[$name];
		}

		return $default;
	}

	/**
	 * Method to set property options
	 *
	 * @param   string  $name
	 * @param   mixed   $value
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setOption($name, $value)
	{
		$this->options[$name] = $value;

		return $this;
	}

	/**
	 * Method to get property SslPort
	 *
	 * @return  int
	 */
	public function getSslPort()
	{
		return $this->sslPort;
	}

	/**
	 * Method to set property sslPort
	 *
	 * @param   int $sslPort
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setSslPort($sslPort)
	{
		$this->sslPort = (int) $sslPort;

		return $this;
	}

	/**
	 * Method to get property Port
	 *
	 * @return  int
	 */
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * Method to set property port
	 *
	 * @param   int $port
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setPort($port)
	{
		$this->port = (int) $port;

		return $this;
	}

	/**
	 * Method to get property Scheme
	 *
	 * @return  string
	 */
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * Method to set property scheme
	 *
	 * @param   string $scheme
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setScheme($scheme)
	{
		$this->scheme = strtolower($scheme);

		$this->ssl = ($this->scheme == 'https');

		return $this;
	}

	/**
	 * Method to get property Host
	 *
	 * @return  string
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Method to set property host
	 *
	 * @param   string $host
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setHost($host)
	{
		$this->host = strtolower($host);

		return $this;
	}

	/**
	 * Method to get property Requirements
	 *
	 * @return  array
	 */
	public function getRequirements()
	{
		return $this->requirements;
	}

	/**
	 * Method to set property requirements
	 *
	 * @param   array $requirements
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setRequirements($requirements)
	{
		$this->requirements = (array) $requirements;

		return $this;
	}

	/**
	 * Method to get property Ssl
	 *
	 * @return  boolean
	 */
	public function getSSL()
	{
		return $this->ssl;
	}

	/**
	 * Method to set property ssl
	 *
	 * @param   boolean $ssl
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setSSL($ssl)
	{
		$this->ssl = $ssl;

		return $this;
	}

	/**
	 * Method to get property Extra
	 *
	 * @return  array
	 */
	public function getExtra()
	{
		return $this->extra;
	}

	/**
	 * Method to set property extra
	 *
	 * @param   array $extra
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setExtra($extra)
	{
		$this->extra = (array) $extra;

		return $this;
	}
}
