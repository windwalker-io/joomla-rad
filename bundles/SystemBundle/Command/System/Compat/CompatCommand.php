<?php

namespace SystemBundle\Command\System\Compat;

use Windwalker\Console\Command\Command;
use Windwalker\Filesystem\Folder;

class CompatCommand extends Command
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
	protected $name = 'compat';

	/**
	 * Property description.
	 *
	 * @var  string
	 */
	protected $description = 'Add compat helper file for IDE after Joomla 3.8.';

	/**
	 * Property usage.
	 *
	 * @var  string
	 */
	protected $usage = 'compat <cmd><folder></cmd> <option>[option]</option>';

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
		$folder = $this->getArgument(0, 'tmp');

		$path = JPATH_BASE . '/' . trim($folder, '/\\');

		$path = realpath($path);

		if (!$path)
		{
			Folder::create($path);
		}

		$this->out('Creating helper file...');

		$aliases = \JLoader::getDeprecatedAliases();

		$placeholders = [];

		foreach ($aliases as $alias)
		{
			$version = $alias['version'];

			if (version_compare(JVERSION, $version, '>='))
			{
				continue;
			}

			$placeholders[] = $this->getTemplate($alias['old'], $alias['new']);
		}

		$placeholders = "<?php\n\n" . implode("\n\n", $placeholders);

		file_put_contents($path . '/compat.php', $placeholders);

		$this->out(sprintf('File: %s created.', $path . '/compat.php'));

		return;
	}

	/**
	 * getTemplate
	 *
	 * @param string $class
	 * @param string $targetClass
	 *
	 * @return  string
	 */
	protected function getTemplate($class, $targetClass)
	{
		$class = ucfirst($class);

		return <<<PHP
if (!class_exists('$class'))
{
	/**
	 * @deprecated  This class is only a placeholder.
	 */
	class $class extends $targetClass {}
}
PHP;
	}
}
