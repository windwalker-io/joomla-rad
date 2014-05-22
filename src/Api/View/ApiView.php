<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Api\View;

use Joomla\Registry\Registry;
use Windwalker\Api\Response\JsonResponse;
use Windwalker\View\Json\AbstractJsonView;

/**
 * Class ApiView
 *
 * @since 2.0
 */
class ApiView extends AbstractJsonView
{
	/**
	 * Method to render the view.
	 *
	 * We just return JSON string for Joomla to respond it.
	 *
	 * @return  string  The rendered view.
	 *
	 * @throws  \RuntimeException
	 */
	public function doRender()
	{
		return (string) JsonResponse::response($this->data->toArray());
	}
}
