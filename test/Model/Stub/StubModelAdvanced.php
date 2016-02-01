<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Model\Stub;

use Windwalker\Model\AbstractAdvancedModel;

/**
 * The StubModelAdvanced class.
 */
class StubModelAdvanced extends AbstractAdvancedModel
{
	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'advencedmodeltest';

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'advanced';

	/**
	 * Property option.
	 *
	 * @var  string
	 */
	protected $option = 'com_advencedmodeltest';

	/**
	 * Property context.
	 *
	 * @var  string
	 */
	protected $context = 'com_advencedmodeltest.advanced';
}
