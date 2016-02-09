<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

use Windwalker\Data\Data;

$data = new Data($displayData);

/**
 * @var $this  \Windwalker\View\Engine\PhpEngine
 */
?>
<?php if ($data->params->get('show_page_heading', 1)) : ?>
	<h1>
		<?php echo $this->escape($data->params->get('page_heading')); ?>
	</h1>
<?php endif; ?>

<?php if ($data->params->get('show_category_title') or $data->params->get('page_subheading')) : ?>
	<h2>
		<?php echo $this->escape($data->params->get('page_subheading')); ?>
		<?php if ($data->params->get('show_category_title')) : ?>
			<span class="subheading-category"><?php echo $this->escape($data->category->title); ?></span>
		<?php endif; ?>
	</h2>
<?php endif; ?>
