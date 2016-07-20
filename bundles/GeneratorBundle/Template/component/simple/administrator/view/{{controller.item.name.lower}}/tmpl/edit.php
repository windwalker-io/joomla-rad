<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

JHtmlBootstrap::tooltip();
JHtmlFormbehavior::chosen('select');
JHtmlBehavior::formvalidator();

/**
 * Prepare data for this template.
 *
 * @var $this      \Windwalker\View\Engine\PhpEngine
 * @var $container \Windwalker\DI\Container
 * @var $data      \Windwalker\Data\Data
 * @var $item      \stdClass
 */
$container = $this->getContainer();
$form      = $data->form;
$item      = $data->item;

// Setting tabset
$tabs = array(
	'tab_basic'
);
?>
<!-- Validate Script -->
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == '{{controller.item.name.lower}}.edit.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<div id="{{extension.name.lower}}" class="windwalker {{controller.item.name.lower}} edit-form row-fluid">
	<form action="<?php echo JUri::getInstance(); ?>"  method="post" name="adminForm" id="adminForm"
		class="form-validate" enctype="multipart/form-data">

		<?php echo JHtmlBootstrap::startTabSet('{{controller.item.name.lower}}EditTab', array('active' => 'tab_basic')); ?>

			<?php
			foreach ($tabs as $tab)
			{
				echo $this->loadTemplate($tab, array('tab' => $tab));
			}
			?>

		<?php echo JHtmlBootstrap::endTabSet(); ?>

		<!-- Hidden Inputs -->
		<div id="hidden-inputs">
			<input type="hidden" name="option" value="{{extension.element.lower}}" />
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
