<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync\Importer;

use Joomla\Utilities\ArrayHelper;
use Windwalker\Sqlsync\Model\Table;
use Symfony\Component\Yaml\Parser;

/**
 * Class YamlImporter
 */
class YamlImporter extends AbstractImporter
{
	/**
	 * @var array
	 */
	public $sql = array();

	/**
	 * @var array
	 */
	protected $columns = array();

	/**
	 * @var array
	 */
	protected $indexes = array();

	/**
	 * @var array
	 */
	protected $dataPks = array();

	/**
	 * @var array
	 */
	protected $tables;

	/**
	 * @var bool
	 */
	protected $debug = false;

	/**
	 * @var array
	 */
	protected $analyze = array();

	/**
	 * import
	 *
	 * @param string $content
	 *
	 * @return bool|mixed
	 */
	public function import($content)
	{
		$parser = new Parser;

		$content = $parser->parse($content);

		// First level: Tables
		foreach ($content as $tableName => $table)
		{
			$newTableName = $this->renameTable($table);

			if (!$newTableName)
			{
				$newTableName = $this->addTable($table);
			}

			$tableName = $newTableName ?: $tableName;

			$this->changeColumns($tableName, ArrayHelper::getValue($table, 'columns', array()));

			$this->changeIndexes($tableName, ArrayHelper::getValue($table, 'index', array()));

			$this->changeDatas($tableName, ArrayHelper::getValue($table, 'data', array()), ArrayHelper::getValue($table, 'columns', array()));
		}

		$this->state->set('import.analyze', $this->analyze);

		return true;
	}

	/**
	 * Destructor. Write last import schema for debug.
	 */
	public function __destruct()
	{
		$sql = implode(";\n\n", $this->sql) . ';';

		\JFile::write(JPATH_ROOT . '/tmp/sqlsync/last-import-schema.sql', $sql);
	}

	/**
	 * renameTable
	 *
	 * @param array $table
	 *
	 * @return bool|mixed
	 */
	public function renameTable($table)
	{
		$query = $this->db->getQuery(true);

		$from    = (array) ArrayHelper::getValue($table, 'from', array());
		$newName = ArrayHelper::getValue($table, 'name', array());

		$tableName = null;

		$tableList = $this->getTableList();

		foreach ($from as $fromName)
		{
			if (in_array($fromName, $tableList) && $newName != $fromName)
			{
				$tableName = $fromName;

				break;
			}
		}

		if ($tableName)
		{
			$this->sql[] = $sql = 'RENAME TABLE ' . $query->qn($tableName) . ' TO ' . $query->qn($newName);

			$this->execute($sql);

			$this->analyze('Table', 'Rename');

			return $this->debug ? false : $newName;
		}

		return false;
	}

	/**
	 * addTable
	 *
	 * @param array $table
	 *
	 * @return mixed
	 */
	public function addTable($table)
	{
		$tableList = $this->getTableList();
		$name = ArrayHelper::getValue($table, 'name', array());

		$indexes = ArrayHelper::getValue($table, 'index', array());

		if (in_array($name, $tableList))
		{
			return $name;
		}

		$columns = ArrayHelper::getValue($table, 'columns', array());

		$addColumns = array();

		foreach ($columns as $column)
		{
			$null = ($column['Null'] == 'NO') ? ' NOT NULL' : '';

			@$ai = $column['Extra'] == 'auto_increment' ? ' AUTO_INCREMENT' : '';

			$comment = $column['Comment'] ? ' COMMENT ' . $this->db->quote($column['Comment']) : '';

			if ($column['Default'] == 'CURRENT_TIMESTAMP')
			{
				$default = ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
			}
			else
			{
				$default = is_null($column['Default']) ? '' : ' DEFAULT ' . $this->db->quote($column['Default']);
			}

			$addColumns[] = "{$this->db->quoteName($column['Field'])} {$column['Type']}{$null}{$default}{$ai}{$comment}";
		}

		// Add Primary Key
		$primaryColumns = array();

		foreach ($indexes as $index)
		{
			if (ArrayHelper::getValue($index, 'Key_name') == 'PRIMARY')
			{
				$primaryColumns[] = $this->db->qn(ArrayHelper::getValue($index, 'Column_name'));
			}
		}

		if ($primaryColumns)
		{
			$addColumns[] = "PRIMARY KEY (" . implode(', ', $primaryColumns) . ")";
		}

		$this->sql[] = $sql = "CREATE TABLE IF NOT EXISTS `{$name}` (\n  " . implode(",\n  ", $addColumns) . "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8";

		$this->execute($sql);

		$this->analyze('Table', 'Created');

		return $name;
	}

	/**
	 * changeColumns
	 *
	 * @param string $tableName
	 * @param array  $columns
	 *
	 * @return bool
	 */
	public function changeColumns($tableName, $columns)
	{
		$before = '';

		foreach ($columns as $columnName => $column)
		{
			$result = $this->renameColumn($tableName, $columnName, $column);

			if (!$result)
			{
				$result = $this->addColumn($tableName, $columnName, $before, $column);

				if ($result)
				{
					continue;
				}
			}

			$columnName = $result ?: $columnName;

			$this->changeColumn($tableName, $columnName, $column);

			$before = $columnName;
		}

		$this->dropColumns($tableName, $columns);

		return true;
	}

	/**
	 * renameColumn
	 *
	 * @param string $tableName
	 * @param string $columnName
	 * @param array  $column
	 *
	 * @return bool|mixed
	 */
	public function renameColumn($tableName, $columnName, $column)
	{
		$from    = (array) ArrayHelper::getValue($column, 'From', array());
		$newName = ArrayHelper::getValue($column, 'Field', array());

		$oldColumns = $this->getColumnList($tableName);

		$oldName = null;

		foreach ($oldColumns as $key => $val)
		{
			if (in_array($key, $from) && $newName != $key)
			{
				$oldName = $key;

				break;
			}
		}

		if ($oldName)
		{
			$this->sql[] = $sql = "ALTER TABLE {$tableName} CHANGE `{$oldName}` `{$newName}` {$column['Type']}";

			$this->execute($sql);

			$this->analyze('Column', 'Rename');

			return $this->debug ? false : $newName;
		}

		return false;
	}

	/**
	 * addColumn
	 *
	 * @param string $tableName
	 * @param string $columnName
	 * @param string $before
	 * @param array  $column
	 *
	 * @return bool
	 */
	protected function addColumn($tableName, $columnName, $before, $column)
	{
		$oldColumns = array_keys($this->getColumnList($tableName));

		if (!in_array($columnName, $oldColumns))
		{
			$null = ($column['Null'] == 'NO') ? 'NOT NULL' : '';

			@$ai = $column['Extra'] == 'auto_increment' ? 'AUTO_INCREMENT' : '';

			$comment = $column['Comment'] ? 'COMMENT ' . $this->db->quote($column['Comment']) : '';

			$position = $before ? 'AFTER ' . $before : 'FIRST';
			
			if ($column['Default'] == 'CURRENT_TIMESTAMP')
			{
				$default = ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
			}
			else
			{
				$default = is_null($column['Default']) ? '' : ' DEFAULT ' . $this->db->quote($column['Default']);
			}

			// Build sql
			$this->sql[] = $sql = "ALTER TABLE `{$tableName}` ADD `{$columnName}` {$column['Type']} {$null} {$default} {$ai} {$comment} {$position}";

			$this->execute($sql);

			$this->analyze('Column', 'Added');

			return true;
		}
	}

	/**
	 * changeColumn
	 *
	 * @param string $tableName
	 * @param string $columnName
	 * @param array  $column
	 *
	 * @return bool
	 */
	protected function changeColumn($tableName, $columnName, $column)
	{
		$oldColumn = $this->getOldColumn($tableName, $columnName);

		unset($oldColumn['Collation']);
		unset($oldColumn['Key']);
		unset($oldColumn['Privileges']);
		unset($column['From']);

		if ($oldColumn == $column)
		{
			return false;
		}

		$null = ($column['Null'] == 'NO') ? ' NOT NULL' : '';

		@$ai = $column['Extra'] == 'auto_increment' ? ' AUTO_INCREMENT' : '';

		$comment = $column['Comment'] ? ' COMMENT ' . $this->db->quote($column['Comment']) : '';

		if ($column['Default'] == 'CURRENT_TIMESTAMP')
		{
			$default = ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
		}
		else
		{
			$default = $column['Default'] ? ' DEFAULT ' . $this->db->quote($column['Default']) : '';
		}

		// Build sql
		$this->sql[] = $sql = "ALTER TABLE `{$tableName}` CHANGE `{$columnName}` `{$columnName}` {$column['Type']}{$null}{$default}{$ai}{$comment}";

		$this->execute($sql);

		$this->analyze('Column', 'Changed');

		return true;

		// print_r($oldColumn);print_r($column);die;
	}

	/**
	 * dropColumns
	 *
	 * @param string $tableName
	 * @param array  $columns
	 *
	 * @return void
	 */
	protected function dropColumns($tableName, $columns)
	{
		$oldColumns = array_keys($this->getColumnList($tableName));

		$newColumns = array_keys($columns);

		foreach ($oldColumns as $column)
		{
			if (!in_array($column, $newColumns))
			{
				$this->sql[] = $sql = "ALTER TABLE `{$tableName}` DROP `{$column}`";

				$this->execute($sql);

				$this->analyze('Column', 'Droped');
			}
		}
	}

	/**
	 * changeIndexes
	 *
	 * @param string $tableName
	 * @param array  $indexes
	 *
	 * @return bool
	 */
	protected function changeIndexes($tableName, $indexes)
	{
		$oldIndexes = $this->getOldIndexes($tableName);

		$oldIdxIdx = $this->getIndexesIndex($oldIndexes);

		$newIdxIdx = $this->getIndexesIndex($indexes);

		foreach ($newIdxIdx as $indexName => $columns)
		{
			$oldColumns = ArrayHelper::getValue($oldIdxIdx, $indexName);

			if ($oldColumns != $columns)
			{
				$this->changeIndex($tableName, $indexName, $columns, $indexes, (boolean) $oldColumns);
			}
		}

		$this->dropIndexes($tableName, $oldIdxIdx, $newIdxIdx);

		return true;
	}

	/**
	 * changeIndex
	 *
	 * @param string $tableName
	 * @param string $indexName
	 * @param array  $columns
	 * @param array  $indexes
	 * @param bool   $noDrop
	 *
	 * @return bool
	 */
	protected function changeIndex($tableName, $indexName, $columns,  $indexes, $noDrop = true)
	{
		$index = null;

		foreach ($indexes as $idx)
		{
			if ($idx['Key_name'] == $indexName)
			{
				$index = $idx;
			}
		}

		if ($noDrop)
		{
			$this->dropIndex($tableName, $indexName);
		}

		if ($index['Key_name'] == 'PRIMARY')
		{
			$this->sql[] = $sql = "ALTER TABLE `{$tableName}` ADD PRIMARY KEY (" . implode(', ', $columns) . ")";
		}
		else
		{
			$indexType = $index['Non_unique'] ? 'INDEX' : 'UNIQUE';

			$db = $this->db;

			$columns = array_map(
				function($v) use($db)
				{
					$v = explode('(', $v);

					$v[0] = $db->qn($v[0]);

					$v = implode('(', $v);

					return $v;
				},
				$columns
			);

			$this->sql[] = $sql = "ALTER TABLE `{$tableName}` ADD {$indexType} `{$indexName}` (" . implode(', ', $columns) . ")";
		}

		$this->execute($sql);

		$this->analyze('Index', 'Changed');

		return true;
	}

	/**
	 * dropIndexes
	 *
	 * @param $tableName
	 * @param $oldIdxIdx
	 * @param $newIdxIdx
	 *
	 * @return bool
	 */
	protected function dropIndexes($tableName, $oldIdxIdx, $newIdxIdx)
	{
		foreach ($oldIdxIdx as $oldIdx => $columns)
		{
			if (!isset($newIdxIdx[$oldIdx]))
			{
				$this->dropIndex($tableName, $oldIdx);
			}
		}

		return true;
	}

	/**
	 * dropIndex
	 *
	 * @param $tableName
	 * @param $indexName
	 *
	 * @return bool
	 */
	protected function dropIndex($tableName, $indexName)
	{
		if ($indexName == 'PRIMARY')
		{
			$this->sql[] = $sql = "ALTER TABLE {$tableName} DROP PRIMARY KEY";
		}
		else
		{
			$this->sql[] = $sql = "ALTER TABLE {$tableName} DROP INDEX `{$indexName}`";
		}

		$this->execute($sql);

		$this->analyze('Index', 'Droped');

		return true;
	}

	/**
	 * changeDatas
	 *
	 * @param string $tableName
	 * @param array  $datas
	 * @param array  $columns
	 *
	 * @return bool
	 */
	protected function changeDatas($tableName, $datas, $columns)
	{
		if (!$datas)
		{
			return false;
		}

		$query = $this->db->getQuery(true);

		$values = array();

		// Clean
		$this->sql[] = $sql = "TRUNCATE TABLE `{$tableName}`";

		$this->execute($sql);

		// Add
		foreach ($datas as $data)
		{
			$data = (array) $data;

			$data = array_map(
				function($d) use ($query)
				{
					return $query->q($d);
				},
				$data
			);

			$value = implode(', ', $data);

			$this->rowCount++;

			$this->sql[] = $sql = (string) sprintf("INSERT `%s` VALUES (%s)", $tableName, $value);

			$this->execute($sql);

			$this->analyze('Data', 'Inserted');
		}
	}

	/**
	 * getTableList
	 *
	 * @return array|mixed
	 */
	protected function getTableList()
	{
		if (!empty($this->tables))
		{
			return $this->tables;
		}

		$tableModel = new Table;

		$tables = $tableModel->listAll();

		return $this->tables = $tables;
	}

	/**
	 * getColumnList
	 *
	 * @param string $table
	 *
	 * @return mixed
	 */
	protected function getColumnList($table)
	{
		if (!empty($this->columns[$table]))
		{
			return $this->columns[$table];
		}

		$columns = $this->db->setQuery('SHOW FULL COLUMNS FROM ' . $this->db->quoteName($this->db->escape($table)))->loadAssocList('Field');

		return $this->columns[$table] = $columns;
	}

	/**
	 * getOldColumn
	 *
	 * @param string $tableName
	 * @param string $columnName
	 *
	 * @return mixed
	 */
	protected function getOldColumn($tableName, $columnName)
	{
		$list = $this->getColumnList($tableName);

		return ArrayHelper::getValue($list, $columnName);
	}

	/**
	 * getOldIndexes
	 *
	 * @param string $table
	 *
	 * @return mixed
	 */
	protected function getOldIndexes($table)
	{
		if (!empty($this->indexes[$table]))
		{
			return $this->indexes[$table];
		}

		$indexes = $this->db->setQuery("SHOW INDEX FROM `{$table}`")->loadAssocList();

		return $this->indexes[$table] = $indexes;
	}

	/**
	 * getIndexesIndex
	 *
	 * @param array $indexes
	 *
	 * @return array
	 */
	protected function getIndexesIndex($indexes)
	{
		$indexesIndex = array();

		foreach ($indexes as $index)
		{
			$keyname = $index['Key_name'];

			if (empty($indexesIndex[$keyname]))
			{
				$indexesIndex[$keyname] = array();
			}

			$columnName = $index['Column_name'];

			if (!empty($index['Sub_part']))
			{
				$columnName .= '(' . $index['Sub_part'] . ')';
			}

			$indexesIndex[$keyname][] = $columnName;
		}

		return $indexesIndex;
	}

	/**
	 * analyze
	 *
	 * @param string $schema
	 * @param string $action
	 *
	 * @return bool
	 */
	protected function analyze($schema, $action)
	{
		if (empty($this->analyze[$schema][$action]))
		{
			$this->analyze[$schema][$action] = 1;

			return true;
		}

		$this->analyze[$schema][$action]++;

		return true;
	}

	/**
	 * execute
	 *
	 * @param string $sql
	 *
	 * @return bool|mixed
	 */
	protected function execute($sql)
	{
		return $this->debug ? false : $this->db->setQuery($sql)->execute();
	}
}
