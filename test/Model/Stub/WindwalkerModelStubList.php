<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Model\Stub;

use JDatabaseQuery;
use Windwalker\Model\ListModel;

/**
 * The StubModel class.
 * 
 * @since  {DEPLOY_VERSION}
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
}
