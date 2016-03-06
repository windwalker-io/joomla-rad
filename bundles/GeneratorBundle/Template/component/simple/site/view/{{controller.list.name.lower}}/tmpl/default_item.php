<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

use {{extension.name.cap}}\Router\Route;
use Windwalker\View\Helper\FrontViewHelper;

// No direct access
defined('_JEXEC') or die;

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
$item      = $data->item;

?>
<div class="{{controller.list.name.lower}}-item item">
	<div class="{{controller.list.name.lower}}-item-inner">

		<!-- Heading -->
		<!-- ============================================================================= -->
		<div class="heading">
			<h2>
                <?php echo JHtml::_('link', $item->link, $item->title) ?>
            </h2>
		</div>
		<!-- ============================================================================= -->
		<!-- Heading -->

		<!-- Content -->
		<!-- ============================================================================= -->
		<div class="content">
			<div class="content-inner row-fluid">

				<?php if (!empty($item->images)): ?>
					<div class="content-img thumbnail span3">
						<?php echo JHtml::_('image', $this->escape($item->images), $this->escape($item->title)); ?>
					</div>
				<?php endif; ?>

                <!-- Text -->
                <!-- ============================================================================= -->
				<div class="text span8">
					<?php echo $item->text; ?>
				</div>
                <!-- ============================================================================= -->
                <!-- Text -->
			</div>
		</div>
		<!-- ============================================================================= -->
		<!-- Content -->

		<!-- Link -->
		<!-- ============================================================================= -->
		<div class="row-fluid">
			<div class="span12">
				<p></p>
				<p class="readmore">
                    <a href="<?php echo $this->escape($item->link); ?>" class="btn btn-small btn-primary">
                        <?php echo JText::_('{{extension.element.upper}}_READMORE'); ?>
                    </a>
				</p>
			</div>
		</div>
		<!-- ============================================================================= -->
		<!-- Link -->

	</div>
</div>
