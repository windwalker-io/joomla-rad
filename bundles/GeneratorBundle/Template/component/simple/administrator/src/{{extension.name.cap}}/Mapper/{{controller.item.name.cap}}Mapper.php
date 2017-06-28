<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

namespace {{extension.name.cap}}\Mapper;

defined('_JEXEC') or die;

use Windwalker\DataMapper\Proxy\AbstractDataMapperProxy;

/**
 * The {{controller.item.name.cap}}Mapper class.
 *
 * @since  1.0
 */
class {{controller.item.name.cap}}Mapper extends AbstractDataMapperProxy
{
	/**
	 * Property table.
	 *
	 * @var  string
	 */
	protected static $table = '#__{{extension.name.lower}}_{{controller.list.name.lower}}';
}
