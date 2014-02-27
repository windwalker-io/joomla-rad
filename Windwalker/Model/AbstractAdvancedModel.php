<?php

namespace Windwalker\Model;

use Joomla\Registry\Registry;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\System\ExtensionHelper;

/**
 * Class AbstractAdvancedModel
 *
 * @since 1.0
 */
abstract class AbstractAdvancedModel extends Model
{
	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = null;

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = null;

	/**
	 * Property params.
	 *
	 * @var  Registry
	 */
	protected $params = null;

	/**
	 * Property category.
	 *
	 * @var  \Windwalker\Data\Data
	 */
	protected $category = null;


	/**
	 * getParams
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
		$comParams  = ExtensionHelper::getParams('com_flower');
		$menuParams = new Registry;

		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}

		$menuParams->merge($comParams);

		return $this->params = $menuParams;
	}

	/**
	 * getCategory
	 *
	 * @return  \Windwalker\Data\Data
	 */
	public function getCategory()
	{
		if (!empty($this->category))
		{
			return $this->category;
		}

		$input  = $this->getContainer()->get('input');
		$pk     = $this->state->get('category.id', $input->get('id'));
		$mapper = new DataMapper('#__categories');

		$data = $mapper->findOne(array('id' => $pk));
		$data->params = new Registry($data);

		return $data;
	}
}
