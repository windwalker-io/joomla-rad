<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Model;

use Joomla\Registry\Registry;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\System\ExtensionHelper;

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
		$comParams  = ExtensionHelper::getParams($this->option);
		$menuParams = new Registry;

		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}

		$menuParams->merge($comParams);

		return $this->params = $menuParams;
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

		$data = $mapper->findOne(array('id' => $pk));
		$data->params = new Registry($data->params);

		return $data;
	}
}
