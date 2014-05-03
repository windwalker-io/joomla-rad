<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use {{extension.name.cap}}\Router\Route;
use Joomla\Registry\Registry;
use Windwalker\Data\Data;
use Windwalker\Helper\DateHelper;
use Windwalker\View\Html\ListHtmlView;

// No direct access
defined('_JEXEC') or die;

/**
 * {{extension.name.cap}} {{controller.list.name.cap}} View
 *
 * @since 1.0
 */
class {{extension.name.cap}}View{{controller.list.name.cap}}Html extends ListHtmlView
{
	/**
	 * The component prefix.
	 *
	 * @var  string
	 */
	protected $prefix = '{{extension.name.lower}}';

	/**
	 * The component option name.
	 *
	 * @var string
	 */
	protected $option = '{{extension.element.lower}}';

	/**
	 * The text prefix for translate.
	 *
	 * @var  string
	 */
	protected $textPrefix = '{{extension.element.upper}}';

	/**
	 * The item name.
	 *
	 * @var  string
	 */
	protected $name = '{{controller.list.name.lower}}';

	/**
	 * The item name.
	 *
	 * @var  string
	 */
	protected $viewItem = '{{controller.item.name.lower}}';


	/**
	 * Prepare data hook.
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
			$query = array(
				'id'    => $item->id,
				'alias' => $item->alias,
				// 'catid' => $item->catid
			);
			$item->link = Route::_('{{extension.element.lower}}.{{controller.item.name.lower}}', $query);

			// Publish Date
			// =====================================================================================
			$pup  = DateHelper::getDate($item->publish_up)->toUnix(true);
			$pdw  = DateHelper::getDate($item->publish_down)->toUnix(true);
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
