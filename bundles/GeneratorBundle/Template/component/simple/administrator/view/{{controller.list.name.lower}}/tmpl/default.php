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
$grid = $data->grid;
?>

<div id="{{extension.name.lower}}" class="windwalker {{controller.list.name.lower}} tablelist row-fluid">
	<form action="<?php echo JUri::getInstance(); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

		<?php if (!empty($this->data->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<h4 class="page-header"><?php echo JText::_('JOPTION_MENUS'); ?></h4>
			<?php echo $this->data->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php else: ?>
		<div id="j-main-container">
		<?php endif;?>

            <ul class="nav nav-list">
                <?php foreach ($data->items as $i => $item): ?>
                    <?php $grid->setItem($item, $i) ?>
                    <li>
                        <?php echo $grid->editTitle($item->title); ?>
                    </li>
                <?php endforeach; ?>
            </ul>


			<!-- Hidden Inputs -->
			<div id="hidden-inputs">
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHtml::_('form.token'); ?>
			</div>

		</div>
	</form>
</div>
