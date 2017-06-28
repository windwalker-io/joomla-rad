<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

/**
 * @var $data \Windwalker\Data\Data
 */
$data->viewObject->tab_name = '{{controller.item.name.lower}}EditTab';

// The fieldsets below should be removed.
$data->viewObject->ignore_fieldsets = array(
	'information',
	'publish',
	'text',
	'created',
	'rules',
	'quickadd'
);

$data->viewObject->ignore_fields = array();

$data->viewObject->extra_fields = array();
?>
<script>
	jQuery(function ($) {
		$('div[id^=attrib-]').addClass('form-horizontal');
    });
</script>
<?php echo JLayoutHelper::render('joomla.edit.params', $data->viewObject);
