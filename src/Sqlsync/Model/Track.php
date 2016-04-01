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
use Symfony\Component\Yaml\Parser as SymfontYamlParser;

/**
 * Class Track
 */
class Track extends \JModelDatabase
{
	/**
	 * @var string
	 */
	protected $file;

	/**
	 * @var string
	 */
	protected $global;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->global = SQLSYNC_LIB . '/Resource/track.yml';

		$this->file = ProfileHelper::getPath() . '/track.yml';

		parent::__construct();
	}

	/**
	 * getTrackList
	 *
	 * @return Registry
	 */
	public function getTrackList()
	{
		$track = new Registry;

		if (!file_exists($this->file))
		{
			$buffer = file_get_contents($this->global);

			\JFile::write($this->file, $buffer);
		}

		$track->loadFile($this->file, 'yaml');

		return $track;
	}

	/**
	 * setTrack
	 *
	 * @param        $tables
	 * @param string $status
	 *
	 * @return void
	 */
	public function setTrack($tables, $status = 'all')
	{
		$db = \JFactory::getDbo();

		$prefix = $db->getPrefix();

		$tables = (array) $tables;

		$track = $this->getTrackList();

		foreach ($tables as $table)
		{
			$table = TableHelper::stripPrefix($table, $prefix);

			$track->set('table.' . $table, $status);
		}

		$this->saveTrackList($track);
	}

	/**
	 * saveTrackList
	 *
	 * @param $track
	 *
	 * @return void
	 */
	public function saveTrackList($track)
	{
		jimport('joomla.filesystem.file');

		$content = $track->toString('yaml');

		\JFile::write($this->file, $content);

		$this->state->set('track.save.path', $this->file);
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
		$db = \JFactory::getDbo();

		$prefix = $db->getPrefix();

		$num = strlen($prefix);

		$table = '#__' . substr($table, $num);

		return $table;
	}
}
