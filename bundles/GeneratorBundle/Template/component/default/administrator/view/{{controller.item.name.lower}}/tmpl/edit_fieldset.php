<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

$fieldset = $data->fieldset;
?>
<fieldset id="{{controller.item.name.lower}}-edit-fieldset-<?php echo $fieldset->name ?>" class="<?php echo $data->class ?>">
	<legend>
		<?php echo $fieldset->label ? JText::_($fieldset->label) : JText::_('{{extension.element.upper}}_EDIT_FIELDSET_' . $fieldset->name); ?>
	</legend>

	<?php foreach ($data->form->getFieldset($fieldset->name) as $field): ?>
		<div id="control_<?php echo $field->id; ?>">
			<?php echo $field->renderField() . "\n\n"; ?>
		</div>
	<?php endforeach;?>
</fieldset>
