<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

const _JEXEC = 1;
defined('_JEXEC') or die;

include_once __DIR__ . '/library/console.php';

define('BUILD_ROOT', realpath(__DIR__ . '/..'));

/**
 * Class Build
 *
 * @since 1.0
 */
class Release extends \Asika\SimpleConsole\Console
{
	/**
	 * Property manifests.
	 *
	 * @var  array
	 */
	protected $manifests = array(
		'windwalker.xml'
	);

	/**
	 * Property stagingBranch.
	 *
	 * @var  string
	 */
	protected $stagingBranch = 'staging';

	/**
	 * Property masterBranch.
	 *
	 * @var  string
	 */
	protected $masterBranch = 'master';

	/**
	 * Property dry.
	 *
	 * @var  bool
	 */
	protected $dry = false;

	/**
	 * Property force.
	 *
	 * @var  bool
	 */
	protected $force = false;

	/**
	 * Property help.
	 *
	 * @var  string
	 */
	protected $help = <<<HELP
[Usage] php release.php <version> <next_version>

[Options]
    h | help        Show help information
    d | dry-run     Dry run.
    f | force       Force operation.
HELP;

	/**
	 * execute
	 *
	 * @return  void
	 * @throws \Asika\SimpleConsole\CommandArgsException
	 */
	protected function doExecute()
	{
		$this->dry = $this->getOption(array('d', 'dry-run'));
		$this->force = $this->getOption(array('f', 'force'));
		$version = $this->getArgument(0);
		$nextVersion = $this->getArgument(1);

		$f = $this->force ? ' -f' : '';

		if (!$version)
		{
			throw new \Asika\SimpleConsole\CommandArgsException('Please enter a version.');
		}

		if (!$this->dry)
		{
			$this->exec('git checkout ' . $this->masterBranch);

			$this->exec('git merge ' . $this->stagingBranch);

			$this->exec('git tag ' . $version . $f);

			$this->exec('git checkout ' . $this->stagingBranch);
		}

		if (!$nextVersion)
		{
			$v = array_pad(explode('.', $version), 3, 0);

			$v[2]++;

			$nextVersion = implode('.', $v);
		}

		$this->upNewVersions($nextVersion);

		if (!$nextVersion)
		{
			$v = array_pad(explode('.', $version), 3, 0);

			$v[0] += 2;

			$nextVersion = implode('.', $v);
		}

		$this->upVersions($nextVersion);

		if (!$this->dry)
		{
			$this->exec(sprintf('git commit -am "Prepare for %s development."', $nextVersion));

			$this->exec('git push origin ' . $this->stagingBranch . ' ' . $this->masterBranch . $f);

			$this->exec('git push origin --tags' . $f);
		}

		$this->out()->out('Release to version: ' . $version . ' and prepared for ' . $nextVersion);
	}

	/**
	 * upVersions
	 *
	 * @param string $version
	 *
	 * @return  void
	 */
	protected function upVersions($version)
	{
		$manifests = $this->manifests;

		foreach ($manifests as $manifest)
		{
			$file = BUILD_ROOT . '/' . $manifest;

			if (is_file($file))
			{
				$xml = file_get_contents($file);

				$xml = preg_replace('/<version>([\w.]+)<\/version>/', '<version>' . $version . '</version>', $xml);

				$this->out(sprintf('[Replace version] %s', $manifest));

				if (!$this->dry)
				{
					file_put_contents($file, $xml);
				}
			}
		}
	}

	/**
	 * upVersions
	 *
	 * @param string $version
	 *
	 * @return  void
	 */
	protected function upNewVersions($version)
	{
		$manifests = $this->manifests;

		foreach ($manifests as $manifest)
		{
			$file = BUILD_ROOT . '/' . $manifest;

			if (is_file($file))
			{
				$xml = file_get_contents($file);

				$xml = preg_replace('/<newversion>([\w.]+)<\/newversion>/', '<newversion>' . $version . '</newversion>', $xml);

				$this->out(sprintf('[Replace newversion] %s', $manifest));

				if (!$this->dry)
				{
					file_put_contents($file, $xml);
				}
			}
		}
	}

	/**
	 * cleanPath
	 *
	 * @param string $path
	 * @param string $ds
	 *
	 * @return  string
	 */
	public static function cleanPath($path, $ds = DIRECTORY_SEPARATOR)
	{
		return str_replace(array('/', '\\'), $ds, $path);
	}
}

$build = new Release;

$build->execute();
