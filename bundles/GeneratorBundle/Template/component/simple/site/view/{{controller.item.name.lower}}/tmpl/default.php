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
 * @var $item      \Windwalker\Data\Data
 * @var $params    \Joomla\Registry\Registry
 * @var $this      \Windwalker\View\Engine\PhpEngine
 */
$container = $this->getContainer();
$params = $data->params;
$item = $data->item;
?>

<form action="<?php echo JUri::getInstance(); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<div id="{{extension.name.cap}}" class="windwalker item container-fluid {{controller.item.name.lower}}">
		<div id="{{extension.name.lower}}-wrap-inner">

			<div class="{{controller.item.name.lower}}-item item<?php echo $item->state == 0 ? ' well well-small' : ''; ?>">
				<div class="{{controller.item.name.lower}}-item-inner">

					<!-- Heading -->
					<!-- ============================================================================= -->
					<div class="heading">
						<h2><?php echo JHtml::_('link', $item->link, $this->escape($item->title)); ?></h2>
					</div>
					<!-- ============================================================================= -->
					<!-- Heading -->

					<!-- Content -->
					<!-- ============================================================================= -->
					<div class="content">
						<div class="content-inner row-fluid">

							<div class="span12">
								<?php if (!empty($item->images)): ?>
									<div class="content-img">
										<?php echo JHtml::_('image', $this->escape($item->images), $this->escape($item->title)); ?>
									</div>
								<?php endif; ?>

                                <!-- Text -->
                                <!-- ============================================================================= -->
								<div class="text">
									<?php echo $item->text; ?>
								</div>
                                <!-- ============================================================================= -->
                                <!-- Text End -->
							</div>

						</div>
					</div>
					<!-- ============================================================================= -->
					<!-- Content End -->
				</div>
			</div>

		</div>
	</div>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="return" value="<?php echo base64_encode(JUri::getInstance()->toString()); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>        
