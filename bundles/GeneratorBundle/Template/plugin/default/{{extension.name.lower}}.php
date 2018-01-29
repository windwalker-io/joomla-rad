<?php
/**
 * @package        {ORGANIZATION}.Plugin
 * @subpackage     {{plugin.group.lower}}.plg_{{extension.name.lower}}
 * @copyright      Copyright (C) 2012 {ORGANIZATION}.com, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later.
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Plugin\CMSPlugin;

defined('_JEXEC') or die;

/**
 * {{extension.name.cap}} {{plugin.group.cap}} Plugin
 *
 * @package        Joomla.Plugin
 * @subpackage     {{plugin.group.cap}}.{{extension.name.lower}}
 * @since          1.0
 *
 * @see https://docs.joomla.org/Plugin/Events
 * @see https://gist.github.com/asika32764/67c9894c9cee758b7d64d9a72fa70fe3
 */
class Plg{{plugin.group.cap}}{{extension.name.cap}} extends CMSPlugin
{
	/**
	 * Property self.
	 *
	 * @var  static
	 */
	public static $instance;

	/**
	 * The property exists will auto load application instance.
	 *
	 * @var  CMSApplication
	 */
	protected $app;

	/**
	 * Set to TRUE to autoload languages.
	 *
	 * @var  bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param  object $subject The object to observe
	 * @param  array  $config  An array that holds the plugin configuration
	 *
	 * @throws Exception
	 */
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);

		self::$instance = $this;
	}

	/**
	 * Get self object.
	 *
	 * @return  static
	 */
	public static function getInstance()
	{
		return self::$instance;
	}

	/**
	 * onAfterInitialise
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
	}

	/**
	 * onAfterRoute
	 *
	 * @return  void
	 */
	public function onAfterRoute()
	{
	}

	/**
	 * onAfterDispatch
	 *
	 * @return  void
	 */
	public function onAfterDispatch()
	{
	}

	/**
	 * onAfterRender
	 *
	 * @return  void
	 */
	public function onAfterRender()
	{
	}
}
