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
 * @since  {DEPLOY_VERSION}
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
		$this->_script = '';
		$this->_styleSheets = array();
		$this->_style = '';

		return $this;
	}
}
