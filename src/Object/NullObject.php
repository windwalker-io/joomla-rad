<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Object;

/**
 * Null Object
 *
 * @since 2.0
 */
class NullObject extends \JObject implements NullObjectInterface
{
	/**
	 * Is this object not contain any values.
	 *
	 * @return boolean
	 */
	public function isNull()
	{
		return true;
	}

	/**
	 * Magic method to convert the object to a string gracefully.
	 *
	 * @return  string  Empty string.
	 */
	public function __toString()
	{
		return '';
	}

	/**
	 * Sets a default value if not alreay assigned
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 *
	 * @return  mixed
	 */
	public function def($property, $default = null)
	{
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 *
	 * @return  mixed  The value of the property.
	 */
	public function get($property, $default = null)
	{
		return $default;
	}

	/**
	 * Returns an associative array of object properties.
	 *
	 * @param   boolean  $public  If true, returns only the public properties.
	 *
	 * @return  array
	 */
	public function getProperties($public = true)
	{
		return array();
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set.
	 *
	 * @return  void
	 */
	public function set($property, $value = null)
	{
	}

	/**
	 * Set the object properties based on a named array/hash.
	 *
	 * @param   mixed  $properties  Either an associative array or another object.
	 *
	 * @return  boolean
	 */
	public function setProperties($properties)
	{
		return false;
	}


	/**
	 * Call magic.
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return  void
	 */
	public function __call($name, $args)
	{
		return;
	}

	/**
	 * Get magic.
	 *
	 * @param string $name
	 *
	 * @return  null
	 */
	public function __get($name)
	{
		return null;
	}
}
