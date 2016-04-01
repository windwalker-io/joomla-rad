<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync\Importer;

use Windwalker\Sqlsync\Helper\AbstractQueryHelper;

/**
 * Class AbstractImporter
 */
abstract class AbstractImporter extends \JModelDatabase
{
	/**
	 * @var array
	 */
	static protected $instance = array();

	/**
	 * @var AbstractQueryHelper
	 */
	protected $queryHelper;

	/**
	 * @var int
	 */
	protected $tableCount = 0;

	/**
	 * @var int
	 */
	protected $rowCount = 0;

	/**
	 * getInstance
	 *
	 * @param string $type
	 *
	 * @return mixed
	 */
	static public function getInstance($type = 'yaml')
	{
		if (!empty(self::$instance[$type]))
		{
			return self::$instance[$type];
		}

		$class = 'Windwalker\Sqlsync\\Importer\\' . ucfirst($type) . 'Importer';

		return self::$instance[$type] = new $class;
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->queryHelper = AbstractQueryHelper::getInstance($this->db->name);
	}

	/**
	 * import
	 *
	 * @param $content
	 *
	 * @return mixed
	 */
	abstract public function import($content);
}
