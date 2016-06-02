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
<?php if ($data->params->get('show_description', 1) || $data->params->def('show_description_image', 1)) : ?>
	<div class="category-desc">
		<?php
		if ($data->params->get('show_description_image') && $data->category->params->get('image'))
		{
			echo JHtml::image($data->category->params->get('image'), 'Desc Image');
		}

		if ($data->params->get('show_description') && $data->category->description)
		{
			echo JHtml::_('content.prepare', $data->category->description, '', $data->view->option . '.category');
		}
		?>
		<div class="clr"></div>
	</div>
<?php endif; ?>
