<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Registry\Registry;
use Windwalker\Data\Data;
use Windwalker\Helper\DateHelper;
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

		// Set Data
		// =====================================================================================
		foreach ($data->items as &$item)
		{
			$item = new Data($item);
			$item->params = $item->params = new JRegistry($item->params);

			// Link
			// =====================================================================================
			$item->link = new JUri("index.php?option={{extension.element.lower}}&view={{controller.item.name.lower}}&id={$item->id}");
			$item->link->setVar('alias', $item->alias);
			$item->link->setVar('catid', $item->catid);
			$item->link = JRoute::_((string) $item->link);

			// Publish Date
			// =====================================================================================
			$pup  = DateHelper::getDate($item->get('a_publish_up'))->toUnix(true);
			$pdw  = DateHelper::getDate($item->get('a_publish_down'))->toUnix(true);
			$now  = DateHelper::getDate('now')->toUnix(true);
			$null = DateHelper::getDate('0000-00-00 00:00:00')->toUnix(true);

			if (($now < $pup && $pup != $null) || ($now > $pdw && $pdw != $null))
			{
				$item->published = 0;
			}

			if ($item->modified == '0000-00-00 00:00:00')
			{
				$item->modified = '';
			}

			// Plugins
			// =====================================================================================
			$item->event = new stdClass;

			$dispatcher = $this->container->get('event.dispatcher');
			$item->text = $item->introtext;
			$results = $dispatcher->trigger('onContentPrepare', array('{{extension.element.lower}}.{{controller.item.name.lower}}', &$item, &$data->params, 0));

			$results = $dispatcher->trigger('onContentAfterTitle', array('{{extension.element.lower}}.{{controller.item.name.lower}}', &$item, &$data->params, 0));
			$item->event->afterDisplayTitle = trim(implode("\n", $results));

			$results = $dispatcher->trigger('onContentBeforeDisplay', array('{{extension.element.lower}}.{{controller.item.name.lower}}', &$item, &$data->params, 0));
			$item->event->beforeDisplayContent = trim(implode("\n", $results));

			$results = $dispatcher->trigger('onContentAfterDisplay', array('{{extension.element.lower}}.{{controller.item.name.lower}}', &$item, &$data->params, 0));
			$item->event->afterDisplayContent = trim(implode("\n", $results));
		}

		// Set title
		// =====================================================================================
		$app = $this->container->get('app');
		$active = $app->getMenu()->getActive();

		if ($active)
		{
			$currentLink = $active->link;

			if (!strpos($currentLink, 'view={{controller.list.name.lower}}') || !(strpos($currentLink, 'id=' . (string) $data->category->id)))
			{
				// If not Active, set Title
				$this->setTitle($data->category->title);
			}
		}
		else
		{
			$this->setTitle($data->category->title);
		}
	}
}
