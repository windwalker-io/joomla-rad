<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Model;

use Windwalker\DataMapper\DataMapper;
use Windwalker\Registry\Registry;

/**
 * Advanced Model.
 *
 * @since 2.0
 */
abstract class AbstractAdvancedModel extends Model
{
	/**
	 * Item name.
	 *
	 * @var  string
	 */
	protected $viewItem = null;

	/**
	 * List name.
	 *
	 * @var  string
	 */
	protected $viewList = null;

	/**
	 * Params of component.
	 *
	 * @var  Registry
	 */
	protected $params = null;

	/**
	 * Category object of item or items.
	 *
	 * @var  \Windwalker\Data\Data
	 */
	protected $category = null;

	/**
	 * Get component params.
	 *
	 * @return  Registry
	 */
	public function getParams()
	{
		if ($this->params)
		{
			return $this->params;
		}

		$app = $this->getContainer()->get('app');

		return $app->getParams();
	}

	/**
	 * Get category object.
	 *
	 * @param integer $pk Category id.
	 *
	 * @return  \Windwalker\Data\Data
	 */
	public function getCategory($pk = null)
	{
		if (!empty($this->category))
		{
			return $this->category;
		}

		$input  = $this->getContainer()->get('input');
		$pk     = $pk ? : $this->state->get('category.id', $input->get('id'));
		$mapper = new DataMapper('#__categories');

		$data = $mapper->findOne($pk);
		$data->params = new Registry($data->params);

		return $data;
	}
}
