<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class Build
 *
 * @since 1.0
 */
class Build
{
	/**
	 * Property removes.
	 *
	 * @var  array
	 */
	protected $removes = array(
		'docs',
		'test',
		'.gitignore',
		'.travis.yml',
		'phpunit.xml.dist',
		'README.md',
		'update.xml'
	);

	/**
	 * Class init.
	 */
	public function __construct()
	{
		define('BUILD_ROOT', realpath(__DIR__ . '/..'));
	}

	/**
	 * execute
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Prepare zip name.
		$zipFile = BUILD_ROOT . '/../windwalker-rad-%s.zip';

		$version = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;

		if (!$version)
		{
			$this->out('Please enter a version.');
			$this->out('[Usage] php build.php <version>');

			exit();
		}

		// Remove unnecessary files and folders.
		$this->removeFiles();

		// Prepare load composer
		$this->exec("php -r \"readfile('https://getcomposer.org/installer');\" | php");

		rename('composer.phar', BUILD_ROOT . '/composer.phar');

		$this->exec(sprintf('php %s/composer.phar install', BUILD_ROOT));

		$this->out('>> Remove composer.phar');

		unlink(sprintf('%s/composer.phar', BUILD_ROOT));

		// Include dependency to do more things.
		include BUILD_ROOT . '/vendor/autoload.php';

		$zipFile = new \SplFileInfo(\Windwalker\Filesystem\Path::clean(sprintf($zipFile, $version)));

		$dir = new \Windwalker\Filesystem\Path\PathLocator(BUILD_ROOT);

		// Start ZIP archive
		$zip = new ZipArchive;

		@unlink($zipFile->getPathname());

		$zip->open($zipFile->getPathname(), ZIPARCHIVE::CREATE);

		foreach ($dir->getFiles(true) as $file)
		{
			$file = str_replace(BUILD_ROOT . DIRECTORY_SEPARATOR , '', $file->getPathname());

			if (strpos($file, '.') === 0)
			{
				continue;
			}

			$this->out('[Zip file] ' . $file);
			$zip->addFile(str_replace('\\', '/', $file));
		}

		$zip->close();

		$this->out('Zip success to: ' . realpath($zipFile->getPathname()));
	}

	/**
	 * removeFiles
	 *
	 * @return  void
	 */
	public function removeFiles()
	{
		foreach ($this->removes as $remove)
		{
			$path = BUILD_ROOT . '/' . $remove;

			if (is_file($path))
			{
				unlink($path);
			}
			elseif (is_dir($path))
			{
				$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);

				foreach($files as $file) 
				{
					$file->isDir() && !$file->isLink() ? rmdir($file->getPathname()) : unlink($file->getPathname());
				}

				rmdir($path);
			}

			$this->out('[Remove] ' . $remove);
		}

		$this->out();
	}

	/**
	 * exec
	 *
	 * @param   string $command
	 *
	 * @return  Build
	 */
	protected function exec($command)
	{
		$this->out('>> ' . $command);

		$return = exec($command);

		$this->out($return . "\n");

		return $this;
	}

	/**
	 * out
	 *
	 * @param   string  $text
	 * @param   boolean $nl
	 *
	 * @return  Build
	 */
	public function out($text = null, $nl = true)
	{
		fwrite(STDOUT, $text . ($nl ? "\n" : ''));

		return $this;
	}
}

$build = new Build;

$build->execute();
