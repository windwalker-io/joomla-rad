<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\View\Helper;

use JToolbar;
use JToolbarHelper;
use Windwalker\Data\Data;
use Windwalker\DI\Container;
use Windwalker\Helper\ArrayHelper;
use Windwalker\Object\Object;
use Joomla\Registry\Registry;

/**
 * The toolbar helper.
 *
 * @since 2.0
 */
class ToolbarHelper
{
	/**
	 * The view data.
	 *
	 * @var  Data
	 */
	protected $data;

	/**
	 * Config object.
	 *
	 * @var  Registry
	 */
	protected $config;

	/**
	 * The access object.
	 *
	 * @var  Object
	 */
	protected $access;

	/**
	 * The button set.
	 *
	 * @var  array
	 */
	protected $buttonSet = array();

	/**
	 * Constructor.
	 *
	 * @param Data  $data      The view data.
	 * @param array $buttonSet The button set.
	 * @param array $config    The config object.
	 */
	public function __construct($data, array $buttonSet = array(), $config = array())
	{
		$this->data      = $data;
		$this->config    = $config ? : new Registry($config);
		$this->state     = $state = $data->state;
		$this->buttonSet = $buttonSet;

		// Access
		$access = (array) $this->config->get('access');

		$this->access = new Object($access);

	}

	/**
	 * Register a button.
	 *
	 * @param string   $button Button name.
	 * @param callable $value  The callback to raise this button.
	 *
	 * @return  void
	 */
	public function register($button, $value)
	{
		if (!$this->checkAccess($button, $value))
		{
			return;
		}

		$dispatcher = Container::getInstance()->get('event.dispatcher');

		$args = func_get_args();

		array_shift($args);
		array_shift($args);

		$callback = '';

		// (1) If is string, set callback as this string.
		if (is_string($value) || is_callable($value))
		{
			$value = array('handler' => $value);
		}

		// If value is array
		if (is_array($value) && !empty($value['handler']))
		{
			// (2) Set callback as handler in value array
			$callback = array($this, $value['handler']);

			if (!is_callable($callback))
			{
				// (3) If this object do not has this method, set callback direct to JToolbarHelper
				$callback = array('JToolbarHelper', $value['handler']);
			}

			// Prepare the arguments
			if (!empty($value['args']))
			{
				$args = (array) $value['args'];
			}
		}

		// (4) If value handler is a closure, we use this closure priority, give up other callback.
		if (is_callable($value['handler']))
		{
			$callback = $value['handler'];
		}

		// Now execute this callback or flash error message.
		if (is_callable($callback))
		{
			$dispatcher->trigger('onToolbarAppendButton', array($button, &$args));

			call_user_func_array($callback, $args);
		}
		else
		{
			$app = Container::getInstance()->get('app');
			$app->enqueueMessage(sprintf('%s not found', $button));
		}
	}

	/**
	 * Check button access.
	 *
	 * @param string $name   The button name.
	 * @param array  $button The button config array.
	 *
	 * @return  boolean Allow this button or not.
	 */
	protected function checkAccess($name, $button)
	{
		// No access set, means yes.
		if (!isset($button['access']))
		{
			return true;
		}

		// Then using access object to check access.
		elseif (is_string($button['access']))
		{
			return $this->access->get($button['access']);
		}

		// If we get FALSE, just return it.
		return $button['access'];
	}

	/**
	 * Register all buttons.
	 *
	 * @return  void
	 */
	public function registerButtons()
	{
		$buttons = $this->buttonSet;

		$queue = new \SplPriorityQueue;

		foreach ($buttons as $name => $button)
		{
			$priority = isset($priority) ? ArrayHelper::getValue($button, 'priority', $priority + 10) : 9999;

			$queue->insert($name, $priority);
		}

		foreach ($queue as $name)
		{
			$this->register($name, $buttons[$name]);
		}
	}

	/**
	 * Add Custom button by html.
	 *
	 * @param string $html The button html string.
	 *
	 * @return  void
	 */
	public function custom($html)
	{
		$bar = JToolbar::getInstance('toolbar');

		// Add a custom button.
		$bar->appendButton('Custom', $html);
	}

	/**
	 * Writes a common 'delete' button for a list of records.
	 *
	 * @param   string  $task  An override for the task.
	 * @param   string  $alt   An override for the alt text.
	 * @param   string  $msg   Postscript for the 'are you sure' message.
	 *
	 * @return  void
	 */
	public function deleteList($task = 'remove', $alt = 'JTOOLBAR_DELETE', $msg = '')
	{
		$bar = JToolbar::getInstance('toolbar');

		// Add a delete button.
		if ($msg)
		{
			$bar->appendButton('Confirm', $msg, 'delete', $alt, $task, true);
		}
		else
		{
			$bar->appendButton('Standard', 'delete', $alt, $task, true);
		}
	}

	/**
	 * Duplicate button.
	 *
	 * @param   string  $task  An override for the task.
	 * @param   string  $alt   An override for the alt text.
	 *
	 * @return  void
	 */
	public function duplicate($task = 'default.batch.copy', $alt = 'JTOOLBAR_DUPLICATE')
	{
		JToolBarHelper::custom($task, 'copy', 'copy_f2', 'JTOOLBAR_DUPLICATE', true);
	}

	/**
	 * Displays a modal button
	 *
	 * @param   string  $targetModalId  ID of the target modal box
	 * @param   string  $icon           Icon class to show on modal button
	 * @param   string  $alt            Title for the modal button
	 *
	 * @return  void
	 */
	public function modal($targetModalId = 'batchModal', $icon = 'icon-checkbox-partial', $alt = 'JTOOLBAR_BATCH')
	{
		JToolbarHelper::modal($targetModalId, $icon, $alt);
	}

	/**
	 * Writes a configuration button and invokes a cancel operation (eg a checkin).
	 *
	 * @param   string   $component  The name of the component, eg, com_content.
	 * @param   string   $alt        The name of the button.
	 * @param   string   $path       An alternative path for the configuation xml relative to JPATH_SITE.
	 *
	 * @return  void
	 */
	public function preferences($component = null, $alt = 'JToolbar_Options', $path = '')
	{
		$component = $component ? : $this->config->get('option', Container::getInstance()->get('input')->get('option'));
		$component = urlencode($component);

		JToolbarHelper::preferences($component, $alt, $path);
	}

	/**
	 * Set a link button.
	 *
	 * @param   string  $alt   An override for the alt text.
	 * @param   string  $href  The link url.
	 * @param   string  $icon  Icon class to show on modal button
	 *
	 * @return  void
	 */
	public function link($alt, $href = '#', $icon = 'asterisk')
	{
		$bar = JToolbar::getInstance('toolbar');

		// Add a back button.
		$bar->appendButton('Link', $icon, $alt, $href);
	}
}
