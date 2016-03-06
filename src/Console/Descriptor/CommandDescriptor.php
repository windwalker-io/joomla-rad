<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Console\Descriptor;

use Windwalker\Console\Command\AbstractCommand;
use Windwalker\Console\Descriptor\Text\TextCommandDescriptor;

/**
 * Class Option Descriptor
 *
 * @since  2.0
 */
class CommandDescriptor extends TextCommandDescriptor
{
	/**
	 * Render all items description.
	 *
	 * @return  string
	 */
	public function render()
	{
		// Count the max command length as column width.
		foreach ($this->items as $item)
		{
			/** @var $item AbstractCommand */
			$length = strlen($item->getName());

			if ($length > $this->maxLength)
			{
				$this->maxLength = $length;
			}
		}

		$description = array();

		foreach ($this->items as $item)
		{
			$currentLevel = $this->renderItem($item);

			$children = array();

			foreach ($item->getChildren() as $child)
			{
				$children[] = $this->renderItem($child);
			}

			if ($children)
			{
				$children = implode("\n", $children);
				$children = str_replace("\n", "\n  ", $children);

				$description[] = $currentLevel . "\n  " . $children . "\n";
			}
			else
			{
				$description[] = $currentLevel;
			}
		}

		return implode("\n", $description);
	}
}
