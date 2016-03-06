<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace GeneratorBundle\FileOperator;

use Muse\FileOperator\CopyOperator as CodeGeneratorCopyOperator;
use Windwalker\Filesystem\File;
use Windwalker\String\StringHelper;

/**
 * Class CopyOperator
 *
 * @since 1.0
 */
class CopyOperator extends CodeGeneratorCopyOperator
{
	/**
	 * copyFile
	 *
	 * @param string $src
	 * @param string $dest
	 * @param array  $replace
	 *
	 * @return  void
	 */
	protected function copyFile($src, $dest, $replace = array())
	{
		// Replace dest file name.
		$dest = StringHelper::parseVariable($dest, $replace);

		if (is_file($dest))
		{
			$this->io->out('File exists: ' . $dest);
		}
		else
		{
			$content = StringHelper::parseVariable(file_get_contents($src), $replace);

			if (File::write($dest, $content))
			{
				$this->io->out('File created: ' . $dest);
			}
		}
	}
}
