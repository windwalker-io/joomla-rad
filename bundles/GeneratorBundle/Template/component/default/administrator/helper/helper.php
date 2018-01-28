<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Windwalker\String\StringInflector;

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
	 * @param   string $vName The name of the active view.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 */
	public static function addSubmenu($vName)
	{
		$app       = Factory::getApplication();
		$inflector = StringInflector::getInstance(true);

		// Add Category Menu Item
		if ($app->isClient('administrator'))
		{
			JHtmlSidebar::addEntry(
				Text::_('JCATEGORY'),
				'index.php?option=com_categories&extension={{extension.element.lower}}',
				$vName === 'categories'
			);

			if (ComponentHelper::isEnabled('com_fields'))
			{
				JHtmlSidebar::addEntry(
					Text::_('JGLOBAL_FIELDS'),
					'index.php?option=com_fields&context={{extension.element.lower}}.{{controller.item.name.lower}}',
					$vName === 'fields.fields'
				);
				JHtmlSidebar::addEntry(
					Text::_('JGLOBAL_FIELD_GROUPS'),
					'index.php?option=com_fields&view=groups&context={{extension.element.lower}}.{{controller.item.name.lower}}',
					$vName === 'fields.groups'
				);
			}
		}

		foreach (new \DirectoryIterator(JPATH_ADMINISTRATOR . '/components/{{extension.element.lower}}/view') as $folder)
		{
			if ($folder->isDir() && $inflector->isPlural($view = $folder->getBasename()))
			{
				JHtmlSidebar::addEntry(
					Text::sprintf(sprintf('{{extension.element.upper}}_%s_TITLE_LIST', strtoupper($folder))),
					'index.php?option={{extension.element.lower}}&view=' . $view,
					$vName === $view
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
		$db = Factory::getDbo();

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
	 * @return  CMSObject
	 */
	public static function getActions($option = '{{extension.element.lower}}')
	{
		$user   = Factory::getUser();
		$result = new CMSObject;

		$actions = array(
			'core.admin',
			'core.options',
			'core.manage',
			'core.create',
			'core.delete',
			'core.edit',
			'core.edit.state',
			'core.edit.own',
			'core.edit.value',
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $option));
		}

		return $result;
	}

	/**
	 * Returns a valid section for articles. If it is not valid then null
	 * is returned.
	 *
	 * @param   string $section The section to get the mapping for
	 *
	 * @return  string|null  The new section
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 */
	public static function validateSection($section)
	{
		if (Factory::getApplication()->isClient('site'))
		{
			// On the front end we need to map some sections
			switch ($section)
			{
				// Map to {{controller.item.name.lower}}
				case '{{controller.item.name.lower}}':
				case '{{controller.list.name.lower}}':
					$section = '{{controller.item.name.lower}}';
					break;

				default:
					$section = null;
			}
		}

		return $section;
	}

	/**
	 * Returns valid contexts
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public static function getContexts()
	{
		Factory::getLanguage()->load('com_content', JPATH_ADMINISTRATOR);

		$contexts = array(
			'{{extension.element.lower}}.{{controller.item.name.lower}}'    => Text::_('{{extension.element.upper}}_VIEW_{{controller.item.name.upper}}'),
			'{{extension.element.lower}}.categories' => Text::_('JCATEGORY'),

			// Add more group here...
		);

		return $contexts;
	}
}
