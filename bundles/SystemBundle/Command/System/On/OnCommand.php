<?php
/**
 * Created by PhpStorm.
 * User: Ezio
 * Date: 2013/11/14
 * Time: 下午 9:02
 */

namespace SystemBundle\Command\System\On;

use Windwalker\Console\Command\Command;

/**
 * The OnCommand class.
 *
 * @since  2.1.5
 */
class OnCommand extends Command
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
	protected $name = 'on';

	/**
	 * Property description.
	 *
	 * @var  string
	 */
	protected $description = 'Set this site online.';

	/**
	 * Property usage.
	 *
	 * @var  string
	 */
	protected $usage = 'on [option]';

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
	 *
	 * @throws \Exception
	 */
	protected function doExecute()
	{
		jimport('joomla.filesystem.file');

		$file = JPATH_CONFIGURATION . '/configuration.php';

		\JPath::setPermissions($file, '0644');

		$config = \JFactory::getConfig();

		$config->set('offline', $this->offline);

		$class = $config->toString('php', array('class' => 'JConfig'));

		if (!\JFile::write($file, $class))
		{
			throw new \Exception('Writing config fail.');
		}

		\JPath::setPermissions($file, '0444');

		$this->out("\nSystem <info>" . strtoupper($this->name) . "</info>");

		return;
	}
}
