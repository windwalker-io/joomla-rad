<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\View\Engine;

use Joomla\DI\ContainerAwareInterface;
use SplPriorityQueue;
use Windwalker\DI\Container;

/**
 * The php engine.
 *
 * @since 2.0
 */
class PhpEngine extends AbstractEngine
{
	/**
	 * Execute a template and return to loadTemplate() method.
	 *
	 * @param string $templateFile The template file name.
	 * @param array  $data         The data to push into layout.
	 *
	 * @return  mixed
	 */
	protected function execute($templateFile, $data = null)
	{
		// Keep data and the template can get this variable as global variable.
		$data = $this->data->bind((array) $data);

		// Start capturing output into a buffer
		ob_start();

		// Include the requested template filename in the local scope
		// (this will execute the view logic).
		include $templateFile;

		// Done with the requested template; get the buffer and
		// clear it.
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
