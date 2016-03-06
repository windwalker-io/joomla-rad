<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\View\Engine\Stub;

use Windwalker\View\Engine\AbstractEngine;

/**
 * The StubEngine class.
 * 
 * @since  2.1
 */
class StubEngine extends AbstractEngine
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
		return $templateFile . ($data ? json_encode($data) : '');
	}
}
