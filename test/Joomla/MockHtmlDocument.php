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
	 * @param array $data
	 *
	 * @return  string|array
	 */
	public function getLastStylesheet(&$data = null)
	{
		$keys = array_keys($this->_styleSheets);

		$key = array_pop($keys);

		$data = $this->_styleSheets[$key];

		return $key;
	}

	/**
	 * getLastScript
	 *
	 * @param array $data
	 *
	 * @return  string|array
	 */
	public function getLastScript(&$data = null)
	{
		$keys = array_keys($this->_scripts);

		$key = array_pop($keys);

		$data = $this->_scripts[$key];

		return $key;
	}
}
