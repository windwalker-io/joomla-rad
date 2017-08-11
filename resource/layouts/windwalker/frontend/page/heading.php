<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

/**
 * @var $this      \Windwalker\View\Engine\PhpEngine
 * @var $params    \Joomla\Registry\Registry
 * @var $category  \Windwalker\Data\Data
 */

extract($displayData, EXTR_OVERWRITE);
?>
<?php if ($params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($params->get('page_heading')); ?>
	</h1>
<?php endif; ?>

<?php if (($params->get('show_category_title') || $params->get('page_subheading')) && isset($category)) : ?>
	<h2>
		<?php echo $this->escape($params->get('page_subheading')); ?>
		<?php if ($params->get('show_category_title')) : ?>
			<span class="subheading-category"><?php echo $this->escape($category->title); ?></span>
		<?php endif; ?>
	</h2>
<?php endif; ?>
