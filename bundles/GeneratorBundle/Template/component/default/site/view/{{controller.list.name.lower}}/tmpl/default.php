<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

use Windwalker\Data\Data;
use Windwalker\View\Layout\FileLayout;

// No direct access
defined('_JEXEC') or die;

JHtmlBootstrap::tooltip();

/**
 * Prepare data for this template.
 *
 * @var $container \Windwalker\DI\Container
 * @var $data      \Windwalker\Data\Data
 * @var $state     \Joomla\Registry\Registry
 * @var $user      \JUser
 * @var $this      \Windwalker\View\Engine\PhpEngine
 */
$container = $this->getContainer();
$data      = $this->data;
$state     = $data->state;
$user      = $container->get('user');
?>
<form action="<?php echo JRoute::_('index.php?option={{extension.element.lower}}&view={{controller.list.name.lower}}'); ?>" method="post" name="adminForm" id="adminForm">

	<div id="{{extension.name.lower}}-wrap" class="windwalker list container-fluid {{controller.list.name.lower}}<?php echo $this->escape($data->params->get('pageclass_sfx')); ?>">
		<div id="{{extension.name.lower}}-wrap-inner">

            <!-- Heading -->
            <?php echo with(new FileLayout('windwalker.frontend.page.heading'))->render($data->dump()); ?>
            <!-- Heading End -->

            <!-- Category Description -->
            <?php echo with(new FileLayout('windwalker.frontend.page.category_desc'))->render($data->dump()); ?>
            <!-- Category Description End -->

            <!-- {{controller.list.name.cap}} List -->
            <div id="{{controller.list.name.lower}}-wrap">

                <!--Columns-->
                <?php if (!empty($data->items)): ?>

                    <?php foreach ((array) $data->items as $key => &$item): ?>
                        <div class="item">
                            <?php echo $this->loadTemplate('item', array('item' => $item)); ?>
                        </div>

                        <span class="row-separator"></span>
                        <!-- LINE END -->
                    <?php endforeach; ?>

                <?php endif; ?>
                <!--Columns End-->

                <!--Pagination-->
                <?php echo with(new FileLayout('windwalker.frontend.page.pagination'))->render($data->dump()); ?>
                <!--Pagination End-->
            </div>
        </div>


        <div>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="return" id="return_url" value="<?php echo base64_encode(JUri::getInstance()->toString()); ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>

</form>