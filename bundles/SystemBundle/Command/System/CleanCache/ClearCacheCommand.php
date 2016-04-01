<?php

namespace SystemBundle\Command\System\CleanCache;

use Windwalker\Console\Command\Command;

class ClearCacheCommand extends Command
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'clear-cache';

	/**
	 * Property description.
	 *
	 * @var  string
	 */
	protected $description = 'Clear system cache.';

	/**
	 * Property usage.
	 *
	 * @var  string
	 */
	protected $usage = 'clear-cache <cmd><folder></cmd> <option>[option]</option>';

	/**
	 * Property offline.
	 *
	 * @var  int
	 */
	protected $offline = 0;

	/**
	 * doExecute
	 *
	 * @return  void
	 */
	protected function doExecute()
	{
		jimport('joomla.filesystem.folder');

		$folder = $this->getArgument(0, '/');

		$path = JPATH_BASE . '/cache/' . trim($folder, '/\\');

		$path = realpath($path);

		if (!$path)
		{
			$this->out('Path: "' . $folder . '" not found.');

			return;
		}

		$this->out('Clearing cache files...');

		if ($path != realpath(JPATH_BASE . '/cache'))
		{
			\JFolder::delete($path);
		}
		else
		{
			$files = new \FilesystemIterator($path);

			foreach ($files as $file)
			{
				if ($file->getBasename() == 'index.html'){
					continue;
				}

				if ($file->isFile())
				{
					unlink((string) $file);
				}
				else
				{
					\JFolder::delete((string) $file);
				}
			}
		}

		$this->out(sprintf('Path: %s cleaned.', $path));

		return;
	}
}
