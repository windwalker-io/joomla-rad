<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\Asset\Makesum;

use Windwalker\Console\Command\Command;

/**
 * Class Makesum
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class MakesumCommand extends Command
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	/**
	 * Console(Argument) name.
	 *
	 * @var  string
	 */
	protected $name = 'makesum';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Make asset md5sum';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'makesum <cmd><component></cmd> <cmd><template></cmd> <option>[option]</option>';

	/**
	 * Property paths.
	 *
	 * @var  \SplPriorityQueue
	 */
	protected $paths = null;

	/**
	 * Property useWindwalker.
	 *
	 * @var  bool
	 */
	protected $useWindwalker = false;

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function initialise()
	{
		$this->addOption('a')
			->alias('admin')
			->description('Use admin client.');

		$this->addOption('w')
			->alias('windwalker')
			->description('Use Windwalker sum style.');

		parent::initialise();
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$name     = $this->getArgument(0) ? : $this->app->close('Please enter a asset name.' . "\n");
		$template = $this->getArgument(1) ? : $this->app->close('Please enter template name.' . "\n");

		$name     = strtolower($name);
		$template = strtolower($template);

		if (!is_dir(JPATH_THEMES . '/' . $template))
		{
			$this->out('No template: ' . $template);

			exit(0);
		}

		$this->useWindwalker = $this->getOption('w', 0);

		$this->registerPaths($this->getOption('a'));

		jimport('joomla.filesystem.file');

		// Make CSS
		$this->scanFiles($name, $template, 'css');

		$this->scanFiles($name, $template, 'js');
	}

	/**
	 * scanFiles
	 *
	 * @param   string  $name
	 * @param   string  $template
	 * @param   string  $type
	 *
	 * @return  void
	 */
	protected function scanFiles($name, $template, $type = 'css')
	{
		foreach (clone $this->paths as $path)
		{
			$replace = array(
				'{name}'     => $name,
				'{type}'     => $type,
				'{template}' => $template
			);

			$path = strtr($path, $replace);

			$path = JPATH_BASE . '/' . $path;

			if (!is_dir($path))
			{
				continue;
			}

			if ($this->useWindwalker)
			{
				$this->makeSum($path, $type);
			}
			else
			{
				$sum = $this->makeSum($path, $type);

				if( $sum )
				{
					if (\JFile::write($path . '/MD5SUM', $sum))
					{
						$this->out('Wrote file: ' . $path . '/MD5SUM');
					}
				}
			}
		}
	}

	/**
	 * makeSum
	 *
	 * @param   string  $dir
	 * @param   string  $type
	 *
	 * @return  string|void
	 */
	protected function makeSum($dir, $type = 'css')
	{
		$dirs = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::KEY_AS_PATHNAME);

		$files = new \RecursiveIteratorIterator($dirs);

		$content = '';

		foreach ($files as $file)
		{
			/** @var $file \SplFileInfo */
			if ($file->isDir() || $file->getExtension() != $type)
			{
				continue;
			}

			$content = file_get_contents((string) $file);

			if ($this->useWindwalker)
			{
				$content = md5($content);

				$path = \JPath::clean($file . '.sum');

				if (\JFile::write($path, $content))
				{
					$this->out('Wrote file: ' . $path);
				}
			}
			else
			{
				$content .= file_get_contents((string) $file);
			}
		}

		if (! $this->useWindwalker)
		{
			return md5($content);
		}

		return true;
	}

	/**
	 * registerPaths
	 *
	 * @param bool $admin
	 *
	 * @return  void
	 */
	protected function registerPaths($admin)
	{
		$this->paths = new \SplPriorityQueue;

		$prefix = $admin ? 'administrator/' : '';

		// (1) Find: templates/[tmpl]/[type]/[name]/[file_name].[type]
		$this->paths->insert($prefix . 'templates/{template}/{type}/{name}', 800);

		// (2) Find: templates/[tmpl]/[type]/[file_name].[type]
		$this->paths->insert($prefix . 'templates/{template}/{type}', 700);

		// (3) Find: components/[name]/asset/[type]/[file_name].[type]
		$this->paths->insert($prefix . 'components/{name}/asset/{type}', 600);

		// (4) Find: components/[name]/asset/[file_name].[type]
		$this->paths->insert($prefix . 'components/{name}/asset', 500);

		// (5) Find: media/[name]/[type]/[file_name].[type]
		$this->paths->insert('media/{name}/{type}', 400);

		// (6) Find: media/[name]/[file_name].[type]
		$this->paths->insert('media/{name}', 300);

		// (7) Find: media/windwalker/[type]/[file_name].[type]
		$this->paths->insert('media/windwalker/{type}', 200);

		// (8) Find: media/windwalker/[file_name].[type]
		$this->paths->insert('media/windwalker', 100);
	}
}
