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

		$classmap = file_get_contents(JPATH_LIBRARIES . '/classmap.php');
		
		preg_match_all('/JLoader::registerAlias\(\'([\w|\\\\]+)\',\s+\'([\w|\\\\]+)\',\s+\'([\d.]+)\'/', $classmap, $matches, PREG_SET_ORDER);

		$placeholders = [];

		foreach ($matches as $match)
		{
			$version = isset($match[3]) ? $match[3] : '4.0';

			if (version_compare(JVERSION, $version, '>='))
			{
				continue;
			}

			$placeholders[] = $this->getTemplate($match[1], $match[2]);
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
		$targetClass = str_replace('\\\\', '\\', $targetClass);

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
