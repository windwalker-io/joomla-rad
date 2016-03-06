<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

use Windwalker\DI\Container;
use Windwalker\Helper\XmlHelper;
use Windwalker\Script\JQueryScript;
use Windwalker\Script\WindwalkerScript;

defined('JPATH_PLATFORM') or die;

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';

/**
 * Field to select a user id from a modal list.
 *
 * @since 2.0
 */
class JFormFieldWinduser extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Winduser';

	/**
	 * Method to get the user field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$html     = array();
		$groups   = $this->getGroups();
		$excluded = $this->getExcluded();
		$link     = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=' . $this->id
			. (isset($groups) ? ('&amp;groups=' . base64_encode(json_encode($groups))) : '')
			. (isset($excluded) ? ('&amp;excluded=' . base64_encode(json_encode($excluded))) : '');

		// Initialize some field attributes.
		$attr = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize JavaScript field attributes.
		$onchange = (string) $this->element['onchange'];

		// Load the modal behavior script.
		WindwalkerScript::modal('.hasUserModal');
		JQueryScript::ui(array('effect'));

		// Build the script.
		$js = <<<JS
function jSelectUser_{$this->id}(id, title) {
	var input = jQuery('#{$this->id}_id');
	var oldId = input.val();

	if (oldId != id) {
		input.val(id);
		jQuery('#{$this->id}_name').val(title).removeClass('invalid').delay(300).effect('highlight');
	}

	$onchange;

	Windwalker.Modal.hide();
};
JS;

		// Add the script to the document head.
		$asset = Container::getInstance()->get('helper.asset');
		$asset->internalJS($js);

		// Load the current username if available.
		$table = JTable::getInstance('user');

		if ($this->value)
		{
			$table->load($this->value);
		}
		// Handle the special case for "current".
		elseif (strtoupper($this->value) == 'CURRENT')
		{
			// 'CURRENT' is not a reasonable value to be placed in the html
			$this->value = JFactory::getUser()->id;
			$table->load($this->value);
		}
		else
		{
			$table->name = JText::_('JLIB_FORM_SELECT_USER');
		}

		// Create a dummy text field with the user name.
		$html[] = '<div class="input-append">';
		$html[] = '	<input type="text" id="' . $this->id . '_name" value="' . htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') . '"'
			. ' readonly' . $attr . ' />';

		// Create the user select button.
		if (!XmlHelper::getBool($this->element, 'readonly', false))
		{
			$html[] = '		<a class="btn btn-primary hasUserModal modal_' . $this->id . '" title="' . JText::_('JLIB_FORM_CHANGE_USER') . '" href="' . $link . '"'
				. ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = '<i class="icon-user"></i></a>';
		}

		$html[] = '</div>';

		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . $this->value . '" />';

		return implode("\n", $html);
	}

	/**
	 * Method to get the filtering groups (null means no filtering)
	 *
	 * @return  mixed  array of filtering groups or null.
	 */
	protected function getGroups()
	{
		return null;
	}

	/**
	 * Method to get the users to exclude from the list of users
	 *
	 * @return  mixed  Array of users to exclude or null to to not exclude them
	 */
	protected function getExcluded()
	{
		return null;
	}
}
