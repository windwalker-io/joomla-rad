<?php
/**
 * @package        {ORGANIZATION}.Module
 * @subpackage     {{extension.element.lower}}
 * @copyright      Copyright (C) 2016 {ORGANIZATION}, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later.
 */

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route as JRoute;
use Windwalker\Data\Data;

defined('_JEXEC') or die;

/**
 * The {{extension.name.cap}} model to get data.
 *
 * @since 1.0
 */
class Mod{{extension.name.cap}}Model extends \JModelDatabase
{
	/**
	 * Get item list.
	 *
	 * @return  mixed Item list.
	 * @throws Exception
	 */
	public function getItems()
	{
		// Prepare Joomla! API
		$app   = Factory::getApplication();
		$input = $app->input;

		// Get sample data.
		return $this->getSampleData();
	}

	// The following is example methods, please delete if you don't want them.
	// --------------------------------------------------------------------------------------------

	/**
	 * Get sample data.
	 *
	 * @return  mixed select list array.
	 *
	 * @throws  Exception
	 */
	protected function getSampleData()
	{
		$params = $this->state;

		// Init DB
		$db     = $this->db;
		$query  = $db->getQuery(true);

		// Get Joomla! API
		$app   = Factory::getApplication();
		$user  = Factory::getUser();
		$date  = Factory::getDate('now', $app->get('offset'));

		// Get Params and prepare data.
		$catid = $params->get('catid', 1);
		$order = $params->get('orderby', 'item.created');
		$dir   = $params->get('order_dir', 'DESC');
		$limit = $params->get('limit', 5);

		// Category

		// If Choose all category, select ROOT category.
		if (!in_array(1, $catid))
		{
			$query->where("item.catid " . new JDatabaseQueryElement('IN()', $catid));
		}

		// Published
		$query->where('item.state > 0');

		$nullDate = $db->Quote($db->getNullDate());
		$nowDate  = $db->Quote($date->toSql(true));

		$query->where('(item.publish_up = ' . $nullDate . ' OR item.publish_up <= ' . $nowDate . ')');
		$query->where('(item.publish_down = ' . $nullDate . ' OR item.publish_down >= ' . $nowDate . ')');

		// View Level
		$query->where('item.access ' . new JDatabaseQueryElement('IN()', $user->getAuthorisedViewLevels()));

		// Language
		if ($app instanceof SiteApplication && $app->getLanguageFilter())
		{
			$lang_code = $db->quote(Factory::getLanguage()->getTag());
			$query->where("item.language IN ({$lang_code}, '*')");
		}

		// Prepare Tables
		$table = array(
			'item' => '#__content',
			'cat'  => '#__categories'
		);

		try
		{
			$select = Mod{{extension.name.cap}}Helper::getSelectList($table);

			// Load Data
			$query->select($select)
				->from('#__content AS item')
				->join('LEFT', '#__categories AS cat ON item.catid = cat.id')
				->order("{$order} {$dir}");

			$items = (array) $db->setQuery($query, 0, $limit)->loadObjectList();

			foreach ($items as $key => &$item)
			{
				$item->link = JRoute::_("index.php?option=com_content&view=article&id={$item->id}:{$item->alias}&catid={$item->catid}");
			}
		}
		catch (\RuntimeException $e)
		{
			$items = range(1, 5);

			foreach ($items as $key => &$item)
			{
				$item = new Data;

				$item->item_title   = '{{extension.name.cap}} data - ' . ($key + 1);
				$item->link         = '#';
				$item->item_created = $date->toSql(true);
			}
		}

		return $items;
	}
}
