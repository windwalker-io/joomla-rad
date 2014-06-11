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
	 * Property gitignore.
	 *
	 * @var  string
	 */
	protected $gitignore = <<<GI
# Development system files #
.*
!/.gitignore

# Windwalker #
/config.json
GI;


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
		$this->exec("php -r \"readfile('https://getcomposer.org/installer');\" | php");

		rename('composer.phar', BUILD_ROOT . '/composer.phar');

		$this->exec(sprintf('php %s/composer.phar update', BUILD_ROOT));

		$this->out('>> Remove composer.phar');
		unlink(sprintf('%s/composer.phar', BUILD_ROOT));

		$this->out('>> Writing .gitignore');
		file_put_contents(BUILD_ROOT . '/.gitignore', $this->gitignore);

		include BUILD_ROOT . '/vendor/autoload.php';

		$dir = new \Windwalker\Filesystem\Path\PathLocator(BUILD_ROOT);

		$zip = new ZipArchive;

		@unlink(BUILD_ROOT . '/../rad.zip');

		$zip->open(BUILD_ROOT . '/../rad.zip', ZIPARCHIVE::CREATE);

		foreach ($dir->getFiles(true) as $file)
		{
			$file = str_replace(BUILD_ROOT . DIRECTORY_SEPARATOR , '', $file->getPathname());

			if (strpos($file, '.') === 0 && $file != '.gitignore')
			{
				continue;
			}

			$this->out('Zip file: ' . $file);
			$zip->addFile($file);
		}

		$zip->close();

		$this->out('Zip success to: ' . realpath(BUILD_ROOT . '/../rad.zip'));
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
	public function out($text, $nl = true)
	{
		fwrite(STDOUT, $text . ($nl ? "\n" : ''));

		return $this;
	}
}

$build = new Build;

$build->execute();
