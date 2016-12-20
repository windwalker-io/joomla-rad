<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

use Windwalker\String\StringInflector;

// No direct access
defined('_JEXEC') or die;

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';

/**
 * {{extension.name.cap}} helper.
 *
 * @since 1.0
 */
abstract class {{extension.name.cap}}Helper
{
	/**
	 * Configure the Link bar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 */
	public static function addSubmenu($vName)
	{
		$app       = \JFactory::getApplication();
		$inflector = StringInflector::getInstance(true);

		// Add Category Menu Item
		if ($app->isAdmin())
		{
			JHtmlSidebar::addEntry(
				JText::_('JCATEGORY'),
				'index.php?option=com_categories&extension={{extension.element.lower}}',
				($vName == 'categories')
			);
		}

		foreach (new \DirectoryIterator(JPATH_ADMINISTRATOR . '/components/{{extension.element.lower}}/view') as $folder)
		{
			if ($folder->isDir() && $inflector->isPlural($view = $folder->getBasename()))
			{
				JHtmlSidebar::addEntry(
					JText::sprintf(sprintf('{{extension.element.upper}}_%s_TITLE_LIST', strtoupper($folder))),
					'index.php?option={{extension.element.lower}}&view=' . $view,
					($vName == $view)
				);
			}
		}

		$dispatcher = \JEventDispatcher::getInstance();
		$dispatcher->trigger('onAfterAddSubmenu', array('{{extension.element.lower}}', $vName));
	}

	/**
	 * Adds Count Items for Category Manager.
	 *
	 * @param   stdClass[] &$items The banner category objects
	 *
	 * @return  stdClass[]
	 *
	 * @throws  \RuntimeException
	 *
	 * @since   1.0
	 */
	public static function countItems(&$items)
	{
		$db = JFactory::getDbo();

		foreach ($items as $item)
		{
			$item->count_trashed = 0;
			$item->count_archived = 0;
			$item->count_unpublished = 0;
			$item->count_published = 0;

			$query = $db->getQuery(true);

			$query->select('state, count(*) AS count')
				->from($query->quoteName('#__{{extension.name.lower}}_{{controller.list.name.lower}}'))
				->where('catid = ' . (int) $item->id)
				->group('state');

			$db->setQuery($query);

			$elements = (array) $db->loadObjectList();

			foreach ($elements as $element)
			{
				switch ($element->state) {
					case 0:
						$item->count_unpublished = $element->count;
						break;

					case 1:
						$item->count_published = $element->count;
						break;

					case 2:
						$item->count_archived = $element->count;
						break;

					case -2:
						$item->count_trashed = $element->count;
						break;
				}
			}
		}

		return $items;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string  $option  Action option.
	 *
	 * @return  JObject
	 */
	public static function getActions($option = '{{extension.element.lower}}')
	{
		$user   = JFactory::getUser();
		$result = new \JObject;

		$actions = array(
			'core.admin',
			'core.manage',
			'core.create',
			'core.edit',
			'core.edit.own',
			'core.edit.state',
			'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $option));
		}

		return $result;
	}
}
