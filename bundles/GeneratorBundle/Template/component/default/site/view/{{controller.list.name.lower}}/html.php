<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\View\Html\ListHtmlView;

/**
 * Class {{extension.name.cap}}View{{controller.list.name.cap}}
 *
 * @since 1.0
 */
class {{extension.name.cap}}View{{controller.list.name.cap}}Html extends ListHtmlView
{
	/**
	 * prepareData
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
		$data = $this->getData();

		$data->params   = $this->get('Params');
		$data->category = $this->get('Category');
	}
}
