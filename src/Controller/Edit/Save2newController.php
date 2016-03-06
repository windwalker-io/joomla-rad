<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Controller\Edit;

use Windwalker\Controller\Admin\AbstractItemController;
use Windwalker\Model\Exception\ValidateFailException;

/**
 * Save2new Controller
 *
 * @since 2.0
 */
class Save2newController extends SaveController
{
	/**
	 * Set redirect URL for action success.
	 *
	 * @return  string  Redirect URL.
	 */
	public function getSuccessRedirect()
	{
		// Redirect back to the edit screen.
		$this->input->set('layout', 'edit');
		$this->input->set($this->urlVar, null);
		$this->recordId = null;

		return \JRoute::_($this->getRedirectItemUrl($this->recordId, $this->urlVar), false);
	}
}
