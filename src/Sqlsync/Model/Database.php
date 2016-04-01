<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync\Model;

use Windwalker\Sqlsync\Exporter\AbstractExporter;
use Windwalker\Sqlsync\Helper\ProfileHelper;

/**
 * Class Database
 */
class Database extends \JModelDatabase
{
	/**
	 * save
	 *
	 * @param string $type
	 * @param null   $folder
	 *
	 * @return bool
	 */
	public function save($type = 'sql', $folder = null)
	{
		$config   = \JFactory::getConfig();
		$profile  = ProfileHelper::getProfile();

		$export = $this->export($type);

		$file = 'site-' . $config->get('db') . '-' . $profile . '-' . date('Y-m-d-H-i-s');

		if ($type == 'yaml')
		{
			$file .= '.yml';
		}
		else
		{
			$file .= '.' . $type;
		}

		$path = $folder ? $folder . '/' . $file : ProfileHelper::getPath() . '/export/' . $type . '/' . $file;

		\JFile::write($path, $export);

		$this->state->set('dump.path', $path);

		return true;
	}

	/**
	 * export
	 *
	 * @param string $type
	 *
	 * @return mixed
	 */
	public function export($type = 'sql')
	{
		/** @var $exporter AbstractExporter */
		$exporter = AbstractExporter::getInstance($type);

		// Export it.
		return $exporter->export(true, false);
	}

	/**
	 * getExported
	 *
	 * @return array
	 */
	public function getExported()
	{
		$path = ProfileHelper::getPath();

		$list = \JFolder::files($path . '/export/sql', '.', false, true);

		rsort($list);

		return $list;
	}

	/**
	 * importFromFile
	 *
	 * @param $file
	 *
	 * @return bool
	 */
	public function importFromFile($file)
	{
		$sql = file_get_contents($file);

		$sql = trim($sql);

		return $this->import($sql);
	}

	/**
	 * import
	 *
	 * @param $queries
	 *
	 * @return bool
	 */
	public function import($queries)
	{
		if (!is_array($queries))
		{
			$queries = $this->db->splitSql($queries);
		}

		foreach ($queries as $query)
		{
			$query = trim($query);

			$this->db->setQuery($query)->execute();
		}

		$this->state->set('import.queries', count($queries));

		return true;
	}

	/**
	 * dropAllTables
	 *
	 * @return bool
	 */
	public function dropAllTables()
	{
		$tables = $this->db->setQuery('SHOW TABLES')->loadColumn();

		if (!$tables)
		{
			return;
		}

		$query = $this->db->getQuery(true);

		array_map(
			function($table) use($query)
			{
				return $query->qn($table);
			}, $tables
		);

		$this->db->setQuery('DROP TABLE IF EXISTS ' . implode(', ', $tables))->execute();

		return true;
	}
}
