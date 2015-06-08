<?php
/**
 * Part of rad project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
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
		'test',
		'.gitignore',
		'.travis.yml',
		'phpunit.dist.xml',
		'README.md'
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
		$this->removeFiles();

		$this->exec("php -r \"readfile('https://getcomposer.org/installer');\" | php");

		rename('composer.phar', BUILD_ROOT . '/composer.phar');

		$this->exec(sprintf('php %s/composer.phar install', BUILD_ROOT));

		$this->out('>> Remove composer.phar');
		unlink(sprintf('%s/composer.phar', BUILD_ROOT));

		include BUILD_ROOT . '/vendor/autoload.php';

		$dir = new \Windwalker\Filesystem\Path\PathLocator(BUILD_ROOT);

		$zip = new ZipArchive;

		@unlink(BUILD_ROOT . '/../rad.zip');

		$zip->open(BUILD_ROOT . '/../rad.zip', ZIPARCHIVE::CREATE);

		foreach ($dir->getFiles(true) as $file)
		{
			$file = str_replace(BUILD_ROOT . DIRECTORY_SEPARATOR , '', $file->getPathname());

			$this->out('Zip file: ' . $file);
			$zip->addFile($file);
		}

		$zip->close();

		$this->out('Zip success to: ' . realpath(BUILD_ROOT . '/../rad.zip'));
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
