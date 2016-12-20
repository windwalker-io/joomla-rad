<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

use Windwalker\View\Layout\FileLayout;

// No direct access
defined('_JEXEC') or die;

// Prepare script
JHtmlBootstrap::tooltip();
JHtmlFormbehavior::chosen('select');
JHtmlDropdown::init();

/**
 * Prepare data for this template.
 *
 * @var $this      \Windwalker\View\Engine\PhpEngine
 * @var $container \Windwalker\DI\Container
 */
$container = $this->getContainer();
?>
<div id="{{extension.name.lower}}" class="windwalker {{controller.list.name.lower}} tablelist row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

		<?php if (!empty($this->data->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<h4 class="page-header"><?php echo JText::_('JOPTION_MENUS'); ?></h4>
			<?php echo $this->data->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php else: ?>
		<div id="j-main-container">
		<?php endif;?>

			<?php echo with(new FileLayout('joomla.searchtools.default'))->render(array('view' => $this->data)); ?>

            <?php if (count($data->items)): ?>
			    <?php echo $this->loadTemplate('table'); ?>
            <?php else: ?>
                <div class="well no-data-block" style="padding: 100px; text-align: center;">
                    <p>
                        <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </p>
                </div>
            <?php endif; ?>

			<?php echo with(new FileLayout('joomla.batchtools.modal'))->render(array('view' => $this->data, 'task_prefix' => '{{controller.list.name.lower}}.')); ?>

			<!-- Hidden Inputs -->
			<div id="hidden-inputs">
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHtml::_('form.token'); ?>
			</div>

		</div>
	</form>
</div>
