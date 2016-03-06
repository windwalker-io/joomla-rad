<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Model\Stub;

use JDatabaseQuery;
use Windwalker\DI\Container;
use Windwalker\Helper\ArrayHelper;
use Windwalker\Model\ListModel;

/**
 * The StubModel class.
 * 
 * @since  2.1
 */
class WindwalkerModelStubList extends ListModel
{
	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'windwalker';

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'stublist';

	/**
	 * Property option.
	 *
	 * @var  string
	 */
	protected $option = 'com_windwalker';

	/**
	 * Property context.
	 *
	 * @var  string
	 */
	protected $context = 'com_windwalker.stublist';

	/**
	 * Property listQuery.
	 *
	 * @var
	 */
	protected $listQuery;

	/**
	 * Property loadedForm.
	 *
	 * @var \JForm
	 */
	protected $loadedForm;

	/**
	 * Property userState.
	 *
	 * @var  array
	 */
	public $userState = array();

	/**
	 * Configure tables through QueryHelper.
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$this->addTable('test', '#__test_table');
	}

	/**
	 * quickCleanCache
	 *
	 * @return  void
	 */
	public function quickCleanCache()
	{
		$this->resetCache();
	}

	/**
	 * deleteCache
	 *
	 * @param string $id
	 *
	 * @return  void
	 */
	public function deleteCache($id)
	{
		$this->setCache($id, null);
	}

	/**
	 * getListQuery
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		return $this->listQuery;
	}

	/**
	 * setListQuery
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return  void
	 */
	public function setListQuery(JDatabaseQuery $query)
	{
		$this->listQuery = $query;
	}

	/**
	 * setLoadedForm
	 *
	 * @param $form
	 *
	 * @return  void
	 */
	public function setLoadedForm($form)
	{
		$this->loadedForm = $form;
	}

	/**
	 * loadForm
	 *
	 * @param string $name
	 * @param string $source
	 * @param array  $options
	 * @param bool   $clear
	 * @param string $xpath
	 *
	 * @return  \JForm
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = null)
	{
		if ($this->loadedForm instanceof \RuntimeException)
		{
			throw new \RuntimeException();
		}

		return $this->loadedForm;
	}

	/**
	 * Gets the value of a user state variable and sets it in the session
	 *
	 * This is the same as the method in JApplication except that this also can optionally
	 * force you back to the first page when a filter has changed
	 *
	 * @param   string  $key       The key of the user state variable.
	 * @param   string  $request   The name of the variable passed in a request.
	 * @param   string  $default   The default value for the variable if not found. Optional.
	 * @param   string  $type      Filter for the variable, for valid values see {@link \JFilterInput::clean()}. Optional.
	 * @param   boolean $resetPage If true, the limitstart in request is set to zero
	 *
	 * @return  array The request user state.
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
	{
		/** @var \JApplicationCms $app */
		$app       = Container::getInstance()->get('app');
		$input     = $app->input;
		$oldState  = ArrayHelper::getValue($this->userState, $key);
		$currentState = (!is_null($oldState)) ? $oldState : $default;
		$newState     = $input->get($request, null, $type);

//		if (($currentState != $newState) && ($resetPage))
//		{
//			$input->set('limitstart', 0);
//		}

		// Save the new value only if it is set in this request.
		if ($newState !== null)
		{
			ArrayHelper::setValue($this->userState, $key, $newState);
		}
		else
		{
			$newState = $currentState;
		}

		return $newState;
	}
}
