<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Api\Listener;

use Windwalker\Component\Component;

/**
 * Class ApiListener
 *
 * @since 2.0
 */
class ApiListener extends \JEvent
{
	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = null;

	/**
	 * Constructor
	 *
	 * @param   string $name     The component name.
	 * @param   object &$subject The object to observe.
	 */
	public function __construct($name, &$subject)
	{
		$this->name = $name;

		parent::__construct($subject);
	}

	/**
	 * onAfterComponentPrepare
	 *
	 * @param string    $name
	 * @param Component $component
	 *
	 * @return  void
	 */
	public function onAfterComponentPrepare($name, Component $component)
	{
		if ($name != $this->name)
		{
			return;
		}

		$component->registerTask('user.login', '\\Windwalker\\Api\\Controller\\User\\LoginController');
		$component->registerTask('user.logout', '\\Windwalker\\Api\\Controller\\User\\LogoutController');
	}
}
