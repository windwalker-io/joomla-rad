<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Controller\Batch;

/**
 * Move Bath Controller.
 *
 * @since 2.0
 */
class MoveController extends AbstractBatchController
{
	/**
	 * Method to save item.
	 *
	 * @param int   $pk   The primary key value.
	 * @param array $data The item data.
	 *
	 * @return mixed
	 */
	protected function save($pk, $data)
	{
		$data[$this->urlVar] = $pk;

		if (!$this->allowEdit($data, $this->urlVar))
		{
			return false;
		}

        $this->model->set($this->model->getName() . '.id', null);

		return $this->model->save($data);
	}
}
