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
<?php if ($data->params->get('show_pagination', 1) == 1 || ($data->params->get('show_pagination', 1) == 2 && $data->pagination->get('pages.total') > 1)) : ?>
	<div class="pagination">
		<?php if ($data->params->def('show_pagination_results', 1)) : ?>
			<p class="counter">
				<?php echo $data->pagination->getPagesCounter(); ?>
			</p>
		<?php endif; ?>

		<?php echo $data->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>
