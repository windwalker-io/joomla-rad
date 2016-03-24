<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Joomla\Registry;

use Windwalker\Registry\Registry;

/**
 * The DecoratingRegistry class.
 *
 * @since  2.1
 */
class DecoratingRegistry extends \Joomla\Registry\Registry
{
	/**
	 * Property registry.
	 *
	 * @var  Registry
	 */
	protected $registry;

	/**
	 * toWindwalkerRegistry
	 *
	 * @param   mixed  $config
	 *
	 * @return  Registry
	 */
	public static function toWindwalkerRegistry($config)
	{
		if ($config instanceof Registry)
		{
			return $config;
		}

		if ($config instanceof \Joomla\Registry\Registry)
		{
			$config = $config->toArray();
		}

		return new Registry($config);
	}

	/**
	 * toJoomlaRegistry
	 *
	 * @param   mixed  $config
	 *
	 * @return  \JRegistry
	 */
	public static function toJRegistry($config)
	{
		if ($config instanceof \Joomla\Registry\Registry)
		{
			return $config;
		}

		if ($config instanceof Registry)
		{
			$config = $config->toArray();
		}

		return new \JRegistry($config);
	}

	/**
	 * DecoratingRegistry constructor.
	 *
	 * @param Registry $registry
	 */
	public function __construct(Registry $registry = null)
	{
		$this->registry = $registry ? : new Registry;
	}

	/**
	 * Magic function to clone the registry object.
	 *
	 * @return  Registry
	 *
	 * @since   1.0
	 */
	public function __clone()
	{
		$this->registry = clone $this->registry;
	}

	/**
	 * Magic function to render this object as a string using default args of toString method.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function __toString()
	{
		return $this->registry->__toString();
	}

	/**
	 * Count elements of the data object
	 *
	 * @return  integer  The custom count as an integer.
	 *
	 * @link    http://php.net/manual/en/countable.count.php
	 * @since   1.3.0
	 */
	public function count()
	{
		return count($this->registry);
	}

	/**
	 * Implementation for the JsonSerializable interface.
	 * Allows us to pass Registry objects to json_encode.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @note    The interface is only present in PHP 5.4 and up.
	 */
	public function jsonSerialize()
	{
		return $this->registry->toArray();
	}

	/**
	 * Sets a default value if not already assigned.
	 *
	 * @param   string  $key      The name of the parameter.
	 * @param   mixed   $default  An optional value for the parameter.
	 *
	 * @return  mixed  The value set, or the default if the value was not previously set (or null).
	 *
	 * @since   1.0
	 */
	public function def($key, $default = '')
	{
		$this->registry->def($key, $default);

		return $this->registry->get($key);
	}

	/**
	 * Check if a registry path exists.
	 *
	 * @param   string  $path  Registry path (e.g. joomla.content.showauthor)
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function exists($path)
	{
		return $this->registry->exists($path);
	}

	/**
	 * Get a registry value.
	 *
	 * @param   string  $path     Registry path (e.g. joomla.content.showauthor)
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 *
	 * @return  mixed  Value of entry or null
	 *
	 * @since   1.0
	 */
	public function get($path, $default = null)
	{
		return $this->registry->get($path, $default);
	}

	/**
	 * Returns a reference to a global Registry object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * <pre>$registry = Registry::getInstance($id);</pre>
	 *
	 * @param   string  $id  An ID for the registry instance
	 *
	 * @return  Registry  The Registry object.
	 *
	 * @since   1.0
	 */
	public static function getInstance($id)
	{
		if (empty(static::$instances[$id]))
		{
			static::$instances[$id] = new static(new Registry);
		}

		return static::$instances[$id];
	}

	/**
	 * Gets this object represented as an ArrayIterator.
	 *
	 * This allows the data properties to be accessed via a foreach statement.
	 *
	 * @return  \ArrayIterator  This object represented as an ArrayIterator.
	 *
	 * @see     IteratorAggregate::getIterator()
	 * @since   1.3.0
	 */
	public function getIterator()
	{
		return $this->registry->getIterator();
	}

	/**
	 * Load a associative array of values into the default namespace
	 *
	 * @param   array    $array      Associative array of value to load
	 * @param   boolean  $flattened  Load from a one-dimensional array
	 * @param   string   $separator  The key separator
	 *
	 * @return  Registry  Return this object to support chaining.
	 *
	 * @since   1.0
	 */
	public function loadArray($array, $flattened = false, $separator = '.')
	{
		if (!$flattened)
		{
			$this->registry->load($array);

			return $this;
		}

		foreach ($array as $k => $v)
		{
			$bak = $this->registry->getSeparator();

			$this->registry->setSeparator($separator);
			$this->set($k, $v);

			$this->registry->setSeparator($bak);
		}

		return $this;
	}

	/**
	 * Load the public variables of the object into the default namespace.
	 *
	 * @param   object  $object  The object holding the publics to load
	 *
	 * @return  Registry  Return this object to support chaining.
	 *
	 * @since   1.0
	 */
	public function loadObject($object)
	{
		$this->registry->load($object);

		return $this;
	}

	/**
	 * Load the contents of a file into the registry
	 *
	 * @param   string  $file     Path to file to load
	 * @param   string  $format   Format of the file [optional: defaults to JSON]
	 * @param   array   $options  Options used by the formatter
	 *
	 * @return  Registry  Return this object to support chaining.
	 *
	 * @since   1.0
	 */
	public function loadFile($file, $format = 'JSON', $options = array())
	{
		$this->registry->loadFile($file, $format, $options);

		return $this;
	}

	/**
	 * Load a string into the registry
	 *
	 * @param   string  $data     String to load into the registry
	 * @param   string  $format   Format of the string
	 * @param   array   $options  Options used by the formatter
	 *
	 * @return  Registry  Return this object to support chaining.
	 *
	 * @since   1.0
	 */
	public function loadString($data, $format = 'JSON', $options = array())
	{
		$this->registry->loadString($data, $format, $options);

		return $this;
	}

	/**
	 * Merge a Registry object into this one
	 *
	 * @param   Registry  $source     Source Registry object to merge.
	 * @param   boolean   $recursive  True to support recursive merge the children values.
	 *
	 * @return  Registry  Return this object to support chaining.
	 *
	 * @since   1.0
	 */
	public function merge($source, $recursive = false)
	{
		if ($source instanceof \Joomla\Registry\Registry)
		{
			$source = $source->toArray();
		}

		$this->registry->merge($source, $recursive);

		return $this;
	}

	/**
	 * Method to extract a sub-registry from path
	 *
	 * @param   string  $path  Registry path (e.g. joomla.content.showauthor)
	 *
	 * @return  Registry|null  Registry object if data is present
	 *
	 * @since   1.2.0
	 */
	public function extract($path)
	{
		$data = $this->get($path);

		if (is_null($data))
		{
			return null;
		}

		return new static(new Registry($data));
	}

	/**
	 * Checks whether an offset exists in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 *
	 * @return  boolean  True if the offset exists, false otherwise.
	 *
	 * @since   1.0
	 */
	public function offsetExists($offset)
	{
		return $this->registry->offsetExists($offset);
	}

	/**
	 * Gets an offset in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 *
	 * @return  mixed  The array value if it exists, null otherwise.
	 *
	 * @since   1.0
	 */
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	/**
	 * Sets an offset in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 * @param   mixed  $value   The array value.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	/**
	 * Unsets an offset in the iterator.
	 *
	 * @param   mixed  $offset  The array offset.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function offsetUnset($offset)
	{
		$this->set($offset, null);
	}

	/**
	 * Set a registry value.
	 *
	 * @param   string  $path       Registry Path (e.g. joomla.content.showauthor)
	 * @param   mixed   $value      Value of entry
	 * @param   string  $separator  The key separator
	 *
	 * @return  mixed  The value of the that has been set.
	 *
	 * @since   1.0
	 */
	public function set($path, $value, $separator = '.')
	{
		$bak = $this->registry->getSeparator();
		$this->registry->setSeparator($separator);

		$this->registry->set($path, $value);

		$this->registry->setSeparator($bak);

		return $this->registry->get($path);
	}

	/**
	 * Append value to a path in registry
	 *
	 * @param   string  $path   Parent registry Path (e.g. joomla.content.showauthor)
	 * @param   mixed   $value  Value of entry
	 *
	 * @return  mixed  The value of the that has been set.
	 *
	 * @since   1.4.0
	 */
	public function append($path, $value)
	{
		$this->registry->push($path, $value);

		return $value;
	}

	/**
	 * Transforms a namespace to an array
	 *
	 * @return  array  An associative array holding the namespace data
	 *
	 * @since   1.0
	 */
	public function toArray()
	{
		return $this->registry->toArray();
	}

	/**
	 * Transforms a namespace to an object
	 *
	 * @return  object   An an object holding the namespace data
	 *
	 * @since   1.0
	 */
	public function toObject()
	{
		return $this->registry->toObject();
	}

	/**
	 * Get a namespace in a given string format
	 *
	 * @param   string  $format   Format to return the string in
	 * @param   mixed   $options  Parameters used by the formatter, see formatters for more info
	 *
	 * @return  string   Namespace in string format
	 *
	 * @since   1.0
	 */
	public function toString($format = 'JSON', $options = array())
	{
		$this->registry->toString($format, $options);
	}

	/**
	 * Dump to one dimension array.
	 *
	 * @param   string  $separator  The key separator.
	 *
	 * @return  string[]  Dumped array.
	 *
	 * @since   1.3.0
	 */
	public function flatten($separator = null)
	{
		return $this->registry->flatten($separator);
	}
}
