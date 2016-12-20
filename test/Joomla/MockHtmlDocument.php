<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Test\Joomla;

\JLoader::register('JDocumentHTML', JPATH_LIBRARIES . '/joomla/document/html/html.php');

/**
 * The MockDocument class.
 *
 * @since  2.1
 */
class MockHtmlDocument extends \JDocumentHTML
{
	const TYPE_ARRAY = 1;
	const TYPE_KEY = 2;

	/**
	 * reset
	 *
	 * @return  static
	 */
	public function reset()
	{
		$this->_scripts = array();
		$this->_script = array();
		$this->_styleSheets = array();
		$this->_style = array();

		return $this;
	}

	/**
	 * getLastStylesheet
	 *
	 * @param int $type
	 *
	 * @return  string|array
	 */
	public function getLastStylesheet($type = self::TYPE_KEY)
	{
		if ($type == static::TYPE_KEY)
		{
			$keys = array_keys($this->_styleSheets);

			return array_pop($keys);
		}

		return array_pop($this->_styleSheets);
	}

	/**
	 * getLastScript
	 *
	 * @param int $type
	 *
	 * @return  string|array
	 */
	public function getLastScript($type = self::TYPE_KEY)
	{
		if ($type == static::TYPE_KEY)
		{
			$keys = array_keys($this->_scripts);

			return array_pop($keys);
		}

		return array_pop($this->_styleSheets);
	}
}
