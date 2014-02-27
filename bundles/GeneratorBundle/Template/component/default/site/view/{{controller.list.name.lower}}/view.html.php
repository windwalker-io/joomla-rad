<?php
/**
 * @package     Joomla.Site
 * @subpackage  {{extension.element.lower}}
 * @author      Simon ASika <asika32764@gmail.com>
 * @copyright   Copyright (C) 2013 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

include_once AKPATH_COMPONENT . '/viewlist.php';

/**
 * View class for a list of {{extension.name.cap}}.
 *
 * @package     Joomla.Site
 * @subpackage  {{extension.element.lower}}
 */
class {{extension.name.cap}}View{{controller.list.name.cap}} extends AKViewList
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var     ing
	 * @since    1.6
	 */
	protected $text_prefix = '{{extension.element.upper}}';

	/**
	 * Item list of table view.
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * Pagination object of table view
	 *
	 * @var JPagination
	 */
	protected $pagination;

	/**
	 * Model state to get some configuration.
	 *
	 * @var JRegistry
	 */
	protected $state;

	/**
	 * The Component option name.
	 *
	 * @var    ing
	 */
	protected $option = '{{extension.element.lower}}';

	/**
	 * The URL view list variable.
	 *
	 * @var    ing
	 */
	protected $list_name = '{{controller.list.name.lower}}';

	/**
	 * The URL view item variable.
	 *
	 * @var    ing
	 */
	protected $item_name = '{{controller.item.name.lower}}';

	/**
	 * Sort fields of table view.
	 *
	 * @var array
	 */
	protected $sort_fields;

	/**
	 * Leading items number.
	 *
	 * @var int
	 */
	protected $lead_items;

	/**
	 * Intro items number.
	 *
	 * @var int
	 */
	protected $intro_items;

	/**
	 * Link items number.
	 *
	 * @var int
	 */
	protected $link_items;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @see     JViewLegacy::loadTemplate()
	 * @since   12.2
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$this->state      = $this->get('State');
		$this->params     = $this->state->get('params');
		$this->category   = $this->get('Category');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filter     = $this->get('Filter');

		// Check for errors.
		// =====================================================================================
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Set Data
		// =====================================================================================
		foreach ($this->items as &$item)
		{
			$item         = new JObject($item);
			$item->params = $item->a_params = new JRegistry($item->a_params);

			// Link
			// =====================================================================================
			$item->link = new JURI("index.php?option={{extension.element.lower}}&view={{controller.item.name.lower}}&id={$item->a_id}");
			$item->link->setVar('alias', $item->get('a_alias'));
			$item->link->setVar('catid', $item->get('a_catid'));
			$item->link = JRoute::_((string) $item->link);

			// Publish Date
			// =====================================================================================
			$pup  = JFactory::getDate($item->get('a_publish_up'), JFactory::getConfig()->get('offset'))->toUnix(true);
			$pdw  = JFactory::getDate($item->get('a_publish_down'), JFactory::getConfig()->get('offset'))->toUnix(true);
			$now  = JFactory::getDate('now', JFactory::getConfig()->get('offset'))->toUnix(true);
			$null = JFactory::getDate('0000-00-00 00:00:00', JFactory::getConfig()->get('offset'))->toUnix(true);

			if (($now < $pup && $pup != $null) || ($now > $pdw && $pdw != $null))
			{
				$item->a_published = 0;
			}

			if ($item->a_modified == '0000-00-00 00:00:00')
			{
				$item->a_modified = '';
			}

			// Plugins
			// =====================================================================================
			$item->event = new stdClass;

			$dispatcher = JDispatcher::getInstance();
			$item->text = $item->a_introtext;
			$results    = $dispatcher->trigger('onContentPrepare', array('{{extension.element.lower}}.{{controller.item.name.lower}}', &$item, &$this->params, 0));

			$results                        = $dispatcher->trigger('onContentAfterTitle', array('{{extension.element.lower}}.{{controller.item.name.lower}}', &$item, &$item->params, 0));
			$item->event->afterDisplayTitle = trim(implode("\n", $results));

			$results                           = $dispatcher->trigger('onContentBeforeDisplay', array('{{extension.element.lower}}.{{controller.item.name.lower}}', &$item, &$item->params, 0));
			$item->event->beforeDisplayContent = trim(implode("\n", $results));

			$results                          = $dispatcher->trigger('onContentAfterDisplay', array('{{extension.element.lower}}.{{controller.item.name.lower}}', &$item, &$item->params, 0));
			$item->event->afterDisplayContent = trim(implode("\n", $results));
		}

		// Category Params
		// =====================================================================================
		$registry = new JRegistry;
		$registry->loadString($this->category->params);
		$this->category->params = $registry;

		// Set title
		// =====================================================================================
		$active = $app->getMenu()->getActive();

		if ($active)
		{
			$currentLink = $active->link;

			if (!strpos($currentLink, 'view={{controller.list.name.lower}}') || !(strpos($currentLink, 'id=' . (string) $this->category->id)))
			{
				// If not Active, set Title
				$this->setTitle($this->category->title);
			}
		}
		else
		{
			$this->setTitle($this->category->title);
		}

		// Count Leading, Items & Links Number
		// =====================================================================================
		$numLeading = $this->params->def('num_leading_articles', $this->state->get('list.num_leading'));
		$numIntro   = $this->params->def('num_intro_articles', $this->state->get('list.num_intro'));
		$numLinks   = $this->params->def('num_links', $this->state->get('list.num_links'));

		// For blog layouts, preprocess the breakdown of leading, intro and linked articles.
		// This makes it much easier for the designer to just interrogate the arrays.
		$max = count($this->items);

		// The first group is the leading articles.
		$limit = $numLeading;

		for ($i = 0; $i < $limit && $i < $max; $i++)
		{
			$this->lead_items[$i] = & $this->items[$i];
		}

		// The second group is the intro articles.
		$limit = $numLeading + $numIntro;

		// Order articles across, then down (or single column mode)
		for ($i = $numLeading; $i < $limit && $i < $max; $i++)
		{
			$this->intro_items[$i] = & $this->items[$i];
		}

		$this->columns = max(1, $this->params->def('num_columns', 2));
		$order         = $this->params->def('multi_column_order', 1);

		$limit = $numLeading + $numIntro + $numLinks;

		// The remainder are the links.
		for ($i = $numLeading + $numIntro; $i < $limit && $i < $max; $i++)
		{
			$this->link_items[$i] = & $this->items[$i];
		}

		parent::display($tpl);
	}
}
