<?php
/**
 * Created by PhpStorm.
 * User: Ezio
 * Date: 2013/11/14
 * Time: 下午 9:02
 */

namespace SystemBundle\Command\System\Off;

use SystemBundle\Command\System\On\OnCommand;

/**
 * The OffCommand class.
 *
 * @since  2.1.5
 */
class OffCommand extends OnCommand
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
	protected $name = 'off';

	/**
	 * Property description.
	 *
	 * @var  string
	 */
	protected $description = 'Set this site offline.';

	/**
	 * Property usage.
	 *
	 * @var  string
	 */
	protected $usage = 'off [option]';

	/**
	 * Property offline.
	 *
	 * @var  int
	 */
	protected $offline = 1;
}
