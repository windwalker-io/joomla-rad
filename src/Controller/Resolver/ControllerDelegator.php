<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Controller\Resolver;

use Joomla\CMS\Application\BaseApplication;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Windwalker\Controller\Controller;
use Windwalker\String\StringNormalise;
use Windwalker\Utilities\ArrayHelper;

/**
 * The Controller Delegator.
 *
 * @since 2.0
 */
class ControllerDelegator
{
	/**
	 * Controller class.
	 *
	 * @var  string
	 */
	public $class = null;

	/**
	 * Input object.
	 *
	 * @var  \JInput
	 */
	public $input = null;

	/**
	 * Application object.
	 *
	 * @var  CMSApplication
	 */
	public $app = null;

	/**
	 * The controller config.
	 *
	 * @var  array
	 */
	public $config = null;

	/**
	 * Alias to get controller.
	 *
	 * @var  array
	 */
	protected $aliases = array();

	/**
	 * Method to get controller.
	 *
	 * @param string          $class  Controller class.
	 * @param \JInput         $input  The input object.
	 * @param BaseApplication $app    The application object.
	 * @param array           $config The controller config.
	 *
	 * @return \Windwalker\Controller\Controller Controller instance.
	 */
	public function getController($class, \JInput $input, BaseApplication $app, $config = array())
	{
		$this->class  = $class;
		$this->input  = $input;
		$this->app    = $app;
		$this->config = $config;

		$this->registerAliases();

		return $this->createController($class);
	}

	/**
	 * Check session token or die.
	 *
	 * @param string $method
	 * @param bool   $redirect
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function checkToken($method = 'post', $redirect = true)
	{
		$valid = Session::checkToken($method);

		if (!$valid && $redirect)
		{
			$referrer = $this->input->server->getString('HTTP_REFERER');

			if (!Uri::isInternal($referrer))
			{
				$referrer = 'index.php';
			}

			$app = Factory::getApplication();
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN_NOTICE'), 'warning');
			$app->redirect($referrer);
		}

		return $valid;
	}

	/**
	 * Register aliases.
	 *
	 * @return  void
	 */
	protected function registerAliases()
	{
		// Override if necessary
	}

	/**
	 * Add alias.
	 *
	 * @param string $class  Class name.
	 * @param string $alias  Alias for this controller.
	 *
	 * @return  $this
	 */
	public function addAlias($class, $alias)
	{
		$class = StringNormalise::toClassNamespace($class);
		$alias = StringNormalise::toClassNamespace($alias);

		$this->aliases[$class] = $alias;

		return $this;
	}

	/**
	 * Remove alias.
	 *
	 * @param   string $class The class to remove.
	 *
	 * @return  ControllerDelegator Return self to support chaining.
	 */
	public function removeAlias($class)
	{
		$class = StringNormalise::toClassNamespace($class);

		if (!empty($this->aliases[$class]))
		{
			unset($this->aliases[$class]);
		}

		return $this;
	}

	/**
	 * Resolve alias.
	 *
	 * @param   string $class Controller class.
	 *
	 * @return  string Alias name.
	 */
	public function resolveAlias($class)
	{
		return ArrayHelper::getValue($this->aliases, $class, $class);
	}

	/**
	 * Create Controller.
	 *
	 * @param   string $class Controller class name.
	 *
	 * @return  \Windwalker\Controller\Controller Controller instance.
	 */
	protected function createController($class)
	{
		$class = $this->resolveAlias($class);

		/** @var Controller $controller */
		$controller = new $class($this->input, $this->app, $this->config, $this);

		$controller->setDelegator($this);

		return $controller;
	}

	/**
	 * getUser
	 *
	 * @param int $id
	 *
	 * @return  User
	 */
	public function getUser($id = null)
	{
		return Factory::getUser($id);
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 */
	public function allowAdd($data = array())
	{
		$user = $this->getUser();
		
		return (
			$user->authorise('core.create', $this->config['option'])
			|| count($user->getAuthorisedCategories($this->config['option'], 'core.create'))
		);
	}

	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	public function allowSave($data, $key = 'id')
	{
		$recordId = isset($data[$key]) ? $data[$key] : '0';

		if ($recordId)
		{
			return $this->allowEdit($data, $key);
		}
		
		return $this->allowAdd($data);
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	public function allowEdit($data = array(), $key = 'id')
	{
		return $this->getUser()->authorise('core.edit', $this->config['option']);
	}

	/**
	 * Check update access.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	public function allowUpdateState($data = array(), $key = 'id')
	{
		return $this->getUser()->authorise('core.edit.state', $this->config['option']);
	}

	/**
	 * Method to check delete access.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	public function allowDelete($data = array(), $key = 'id')
	{
		return $this->getUser()->authorise('core.edit', $this->config['option']);
	}

	/**
	 * If category need authorize, we can write in this method.
	 *
	 * @param   array   $data  Category record.
	 * @param   string  $key   Preimary key name.
	 *
	 * @return  boolean Can edit or not.
	 */
	public function allowCategoryAdd($data, $key = 'catid')
	{
		return $this->getUser()->authorise('core.create', $this->config['option'] . '.category.' . $data[$key]);
	}
}
