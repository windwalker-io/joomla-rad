<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace {{extension.name.cap}}\Component;

use {{extension.name.cap}}\Provider\{{extension.name.cap}}Provider;
use Windwalker\Component\Component;
use Windwalker\Debugger\Debugger;
use Windwalker\Helper\LanguageHelper;
use Windwalker\Helper\ProfilerHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Class {{extension.name.cap}}Component
 *
 * @since 1.0
 */
abstract class {{extension.name.cap}}Component extends Component
{
	/**
	 * Property name.
	 *
	 * @var string
	 */
	protected $name = '{{extension.name.cap}}';

	/**
	 * prepare
	 *
	 * @return  void
	 */
	protected function prepare()
	{
		if (JDEBUG)
		{
			Debugger::registerWhoops();
		}

		// Register provider
		$this->container->registerServiceProvider(new {{extension.name.cap}}Provider);

		// Load language
		$lang = $this->container->get('language');

		LanguageHelper::loadAll($lang->getTag(), $this->option);

		// Load asset
		$asset = $this->container->get('helper.asset');

		$asset->windwalker();

		// Register Tasks
		with(new TaskMapper($this))->register();

		parent::prepare();
	}

	/**
	 * postExecute
	 *
	 * @param mixed $result
	 *
	 * @return  mixed
	 */
	protected function postExecute($result)
	{
		// Debug profiler
		if (JDEBUG && \JFactory::getDocument()->getType() == 'html')
		{
			$result .= "<hr />" . ProfilerHelper::render('Windwalker', true);
		}

		return parent::postExecute($result);
	}
}
