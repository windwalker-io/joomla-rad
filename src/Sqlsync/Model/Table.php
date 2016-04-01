<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync\Model;

use Joomla\Registry\Registry;
use Windwalker\Sqlsync\Helper\ProfileHelper;
use Windwalker\Sqlsync\Helper\TableHelper;

/**
 * Class Table
 */
class Table extends \JModelDatabase
{
	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->prefix = $this->db->getPrefix();
	}

	/**
	 * listAll
	 *
	 * @return mixed
	 */
	public function listAll()
	{
		return $this->listTables();
	}

	/**
	 * listSite
	 *
	 * @return mixed
	 */
	public function listSite()
	{
		return $this->listTables($this->prefix);
	}

	/**
	 * listTables
	 *
	 * @param string $like
	 *
	 * @return mixed
	 */
	public function listTables($like = '')
	{
		$db = \JFactory::getDbo();

		// Show list tables
		$sql = 'SHOW TABLES';

		if ($like)
		{
			$sql .= " LIKE '{$like}%'";
		}

		$tables = $db->setQuery($sql)->loadColumn();

		foreach ($tables as &$table)
		{
			$table = $this->stripPrefix($table);
		}

		return $tables;
	}

	/**
	 * status
	 *
	 * @return array
	 */
	public function status()
	{
		$trackObject = new Track;

		$tables = $this->listAll();

		$track  = $trackObject->getTrackList();

		$statusList = array();

		foreach ($tables as $table)
		{
			$status = array();

			$trackStatus = $track->get('table.' . $table);

			$status['table']  = $table;

			$status['status'] = $trackStatus ?: 'none';

			$statusList[] = $status;
		}

		return $statusList;
	}

	/**
	 * sync
	 *
	 * @return bool
	 */
	public function sync()
	{
		$statusList = $this->status();

		$path = ProfileHelper::getPath() . '/track.yml';

		$trackList = array();

		foreach ($statusList as $status)
		{
			$trackList['table'][$status['table']] = $status['status'];
		}

		$track = new Registry($trackList);

		$trackModel = new Track;

		$trackModel->saveTrackList($track);

		$this->state->set('track.save.path', $trackModel->getState()->get('track.save.path'));

		return true;
	}

	/**
	 * stripPrefix
	 *
	 * @param $table
	 *
	 * @return string
	 */
	protected function stripPrefix($table)
	{
		return TableHelper::stripPrefix($table, $this->prefix);
	}
}
