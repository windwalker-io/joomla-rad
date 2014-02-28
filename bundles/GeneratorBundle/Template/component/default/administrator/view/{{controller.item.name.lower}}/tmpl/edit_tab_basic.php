<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$tab       = $data->tab;
$fieldsets = $data->form->getFieldsets();
?>

<?php echo JHtmlBootstrap::addTab('{{controller.item.name.lower}}EditTab', $tab, \JText::_($data->view->option . '_EDIT_' . strtoupper($tab))) ?>

<div class="row-fluid">
	<div class="span8">
		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['information'], 'class' => 'form-horizontal')); ?>

		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['text'], 'class' => 'form-horizontal')); ?>
	</div>

	<div class="span4">
		<?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsets['publish'], 'class' => 'form-horizontal')); ?>
	</div>
</div>

<?php echo JHtmlBootstrap::endTab(); ?>
