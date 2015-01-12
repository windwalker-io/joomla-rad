<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use {{extension.name.cap}}\Router\Route;
use Windwalker\View\Helper\ViewHtmlHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Prepare data for this template.
 *
 * @var $container Windwalker\DI\Container
 * @var $data      Windwalker\Data\Data
 * @var $state     Joomla\Registry\Registry
 * @var $user      \JUser
 */
$container = $this->getContainer();
$data      = $this->data;
$state     = $data->state;
$user      = $container->get('user');
$params    = $data->params;
$item      = $data->item;

$anchor_id = '{{controller.item.name.lower}}-item-' . $item->id;
?>
<div id="<?php echo $anchor_id; ?>" class="{{controller.list.name.lower}}-item item<?php echo $item->state == 0 ? ' well well-small' : null; ?>">
	<div class="{{controller.list.name.lower}}-item-inner">

		<!-- Heading -->
		<!-- ============================================================================= -->
		<div class="heading">
			<h2><?php echo $params->get('link_titles_in_list', 1) ? JHtml::_('link', $item->link, $item->title) : $item->title ?></h2>
		</div>
		<!-- ============================================================================= -->
		<!-- Heading -->

		<!-- afterDisplayTitle -->
		<!-- ============================================================================= -->
		<?php echo $this->item->event->afterDisplayTitle; ?>
		<!-- ============================================================================= -->
		<!-- afterDisplayTitle -->

		<!-- beforeDisplayContent -->
		<!-- ============================================================================= -->
		<?php echo $this->item->event->beforeDisplayContent; ?>
		<!-- ============================================================================= -->
		<!-- beforeDisplayContent -->

		<!-- Info -->
		<!-- ============================================================================= -->
		<div class="info">
			<div class="info-inner">
				<?php echo ViewHtmlHelper::showInfo($item, 'category_title', 'jcategory', 'folder', Route::_('{{extension.element.lower}}.{{controller.list.name.lower}}', array('id' => $item->catid))); ?>
				<?php echo ViewHtmlHelper::showInfo($item, 'created', '{{extension.element.lower}}_created', 'calendar'); ?>
				<?php echo ViewHtmlHelper::showInfo($item, 'modified', '{{extension.element.lower}}_modified', 'calendar'); ?>
				<?php echo ViewHtmlHelper::showInfo($item, 'name', '{{extension.element.lower}}_created_by', 'user'); ?>
			</div>
		</div>
		<!-- ============================================================================= -->
		<!-- Info -->

		<hr class="info-separator" />

		<!-- Content -->
		<!-- ============================================================================= -->
		<div class="content">
			<div class="content-inner row-fluid">

				<?php $text_span = 12; ?>

				<?php if (!empty($item->images)): ?>
					<?php $text_span = $text_span - 3; ?>
					<div class="content-img thumbnail span3">
						<?php echo JHtml::_('image', $item->images, $item->title); ?>
					</div>
				<?php endif; ?>

				<div class="text span<?php echo $text_span; ?>">
					<?php echo $item->text; ?>
				</div>

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
					<?php echo JHtml::_('link', $item->link, JText::_('{{extension.element.upper}}_READMORE'), array('class' => 'btn btn-small btn-primary')); ?>
				</p>
			</div>
		</div>
		<!-- ============================================================================= -->
		<!-- Link -->

		<!-- afterDisplayContent -->
		<!-- ============================================================================= -->
		<?php echo $this->item->event->afterDisplayContent; ?>
		<!-- ============================================================================= -->
		<!-- afterDisplayContent -->

	</div>
</div>
