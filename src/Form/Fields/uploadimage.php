<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
use Windwalker\Helper\DateHelper;
use Windwalker\Image\ThumbHelper;

defined('_JEXEC') or die;

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';

/**
 * Supports an upload image field, and if file exists, will show this image..
 *
 * @since 2.0
 */
class JFormFieldUploadimage extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'Uploadimage';

	/**
	 * Method to get the field input markup for the file field.
	 * Field attributes allow specification of a maximum file size and a string
	 * of accepted file extensions.
	 *
	 * @return  string  The field input markup.
	 *
	 * @note    The field does not include an upload mechanism.
	 * @see     JFormFieldFile
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$accept   = $this->element['accept'] ? ' accept="' . (string) $this->element['accept'] . '"' : '';
		$size     = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$class    = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$readonly = (string) $this->element['readonly'];
		$value    = $this->value;

		$width  = $this->element['width'] ? $this->element['width'] : 150;
		$height = $this->element['height'] ? $this->element['height'] : 150;
		$crop   = $this->element['crop'] ? $this->element['crop'] : 1;

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		if ($readonly != 'false' && $readonly)
		{
			return JHtml::image($this->value, $this->name, array('width' => 150));
		}
		else
		{
			$html = '';

			if ($this->value)
			{
				$html .= '<div class="image-' . $this->id . '">' . JHtml::image(ThumbHelper::resize($this->value, $width, $height, \JImage::CROP_RESIZE), $this->name, array()) . '</div>';
			}

			$html .= '<input type="file" name="' . $this->getName($this->element['name'] . '_upload') . '" id="' . $this->id . '"' . ' value=""' . $accept . $disabled . $class . $size
				. $onchange . ' />';

			$html .= '<label><input type="checkbox" name="' . $this->getName($this->element['name'] . '_delete') . '" id="' . $this->id . '"' . ' value="1" />' . JText::_('JACTION_DELETE') . '</label>';
			$html .= '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '" />';

			return $html;
		}

	}

	/**
	 * Method to attach a JForm object to the field.
	 *  Catch upload files when form setup.
	 *
	 * @param   SimpleXMLElement $element  The JXmlElement object representing the <field /> tag for the form field object.
	 * @param   mixed            $value    The form field value to validate.
	 * @param   string           $group    The field name group control value. This acts as as an array container for the field.
	 *                                     For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                     full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		parent::setup($element, $value, $group);

		$container = \Windwalker\DI\Container::getInstance();
		$input = $container->get('input');

		if ($input->get($this->element['name'] . '_delete') == 1)
		{
			$this->value = '';
		}
		else
		{
			// Upload Image
			// ===============================================
			if (isset($_FILES['jform']['name']['profile']))
			{
				foreach ($_FILES['jform']['name']['profile'] as $key => $var)
				{
					if (!$var)
					{
						continue;
					}

					// Get Field Attr
					$width  = $this->element['save_width'] ? $this->element['save_width'] : 800;
					$height = $this->element['save_height'] ? $this->element['save_height'] : 800;

					// Build File name
					$src  = $_FILES['jform']['tmp_name']['profile'][$key];
					$var  = explode('.', $var);
					$date = DateHelper::getDate();
					$name = md5((string) $date . $width . $height . $src) . '.' . array_pop($var);
					$url  = "images/cck/{$date->year}/{$date->month}/{$date->day}/" . $name;

					// A Event for extend.
					$container->get('event.dispatcher')
						->trigger('onCCKEngineUploadImage', array(&$url, &$this, &$this->element));

					$dest = JPATH_ROOT . '/' . $url;

					// Upload First
					JFile::upload($src, $dest);

					// Resize image
					$img = new JImage;
					$img->loadFile(JPATH_ROOT . '/' . $url);
					$img = $img->resize($width, $height);

					switch (array_pop($var))
					{
						case 'gif':
							$type = IMAGETYPE_GIF;
							break;
						case 'png':
							$type = IMAGETYPE_PNG;
							break;
						default :
							$type = IMAGETYPE_JPEG;
							break;
					}

					// Save
					$img->toFile($dest, $type, array('quality' => 85));

					// Set in Value
					$this->value = $url;
				}
			}
		}

		return true;
	}

	/**
	 * Show image for com_users.
	 *
	 * @param string $value The formfield value.
	 *
	 * @return string Image html string.
	 */
	public static function showImage($value)
	{
		if ($value)
		{
			return JHtml::image($value, 'image');
		}
	}
}
