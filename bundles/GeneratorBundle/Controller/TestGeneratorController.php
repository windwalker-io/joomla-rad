<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace GeneratorBundle\Controller;

use GeneratorBundle\Controller\Test\GenController;
use Windwalker\Registry\Registry;
use Windwalker\String\StringNormalise;
use Windwalker\Utilities\Reflection\ReflectionHelper;

define('WINDWALKER_ROOT', realpath(JPATH_LIBRARIES . '/windwalker'));

/**
 * The TestGeneratorController class.
 *
 * @since  2.1
 */
class TestGeneratorController extends GeneratorController
{
	/**
	 * Property lastOutput.
	 *
	 * @var  mixed
	 */
	protected $lastOutput = null;

	/**
	 * Property lastReturn.
	 *
	 * @var  mixed
	 */
	protected $lastReturn = null;

	/**
	 * Method to run the application routines.  Most likely you will want to instantiate a controller
	 * and execute it, or perform some sort of task directly.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		$config = array();

		$controller = new GenController($this->container, $this->io, new Registry($config));

		return $controller->execute();
	}
}
