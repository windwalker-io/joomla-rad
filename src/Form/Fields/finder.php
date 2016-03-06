<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

use Windwalker\DI\Container;
use Windwalker\Helper\DateHelper;
use Windwalker\Helper\XmlHelper;
use Windwalker\Script\WindwalkerScript;
use Windwalker\String\StringHelper;

JFormHelper::loadFieldClass('text');

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';

/**
 * Supports a File finder to pick files.
 *
 * @since 2.0
 */
class JFormFieldFinder extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Finder';

	/**
	 * The initialised state of the document object.
	 *
	 * @var boolean
	 */
	protected static $initialised = false;

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	public function getInput()
	{
		// Load the modal behavior script.
		WindwalkerScript::modal('.hasFinderModal');

		if (!static::$initialised)
		{
			$this->setScript();
		}

		// Setup variables for display.
		// ================================================================
		$html     = array();
		$disabled = XmlHelper::getBool($this->element, 'disabled');
		$readonly = XmlHelper::getBool($this->element, 'readonly');
		$link     = $this->getLink();
		$title    = $this->getTitle();

		// Set Title
		// ================================================================
		if (empty($title))
		{
			$title = \JText::_(XmlHelper::get($this->element, 'select_label', 'LIB_WINDWALKER_FORMFIELD_FINDER_SELECT_FILE'));
		}

		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The text field.
		// ================================================================
		$preview = $this->getPreview();

		// The current user display field.
		$html[] = '<span class="' . (!$disabled && !$readonly ? 'input-append' : '') . '">';
		$html[] = '<input type="text" class="finder-item-name ' . (!$disabled && !$readonly ? 'input-medium ' . $this->element['class'] : $this->element['class']) . '" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';

		if (!$disabled && !$readonly) :
			$html[] = '<a class="hasFinderModal btn btn-primary" title="' . JText::_('LIB_WINDWALKER_FORMFIELD_FINDER_BROWSE_FILES') . '"  href="' . $link . '&amp;' . JSession::getFormToken() . '=1">
							<i class="icon-picture"></i> ' . JText::_('LIB_WINDWALKER_FORMFIELD_FINDER_BROWSE_FILES')
				. '</a>';
		endif;
		$html[] = '</span>';

		// The  class='required' for client side validation
		// ================================================================
		$class = '';

		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		// Velue store input
		$disabled_attr = $disabled ? ' disabled="true" ' : '';
		$html[]        = '<input type="hidden" id="' . $this->id . '"' . $class . ' name="' . $this->name . '" value="' . $this->value . '" ' . $disabled_attr . ' />';

		$html = implode("\n", $html);

		$options = array(
			'text' => array(
				'clear_title' => JText::_('LIB_WINDWALKER_FORMFIELD_FINDER_SELECT_FILE')
			)
		);

		$this->initScript('#' . $this->id, $options);

		if (!$disabled && !$readonly)
		{
			$html .= '<a class="btn btn-danger hasTooltip clear-button" title="' . JText::_('JLIB_FORM_BUTTON_CLEAR') . '"' . ' href="javascript: void(0)">';
			$html .= '<i class="icon-remove"></i></a>';
		}

		// Image Preview
		// ================================================================
		$html = $html . $preview;

		return $html;
	}

	/**
	 * Get Preview Image.
	 *
	 * @return  string Preview image html.
	 */
	public function getPreview()
	{
		// The Preview.
		$preview      = (string) $this->element['preview'];
		$showPreview  = true;
		$html         = array();

		switch ($preview)
		{
			case 'no': // Deprecated parameter value
			case 'false':
			case 'none':
				$showPreview = false;
				break;

			case 'yes': // Deprecated parameter value
			case 'true':
			case 'show':
				break;
		}

		if ($showPreview)
		{
			if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
			{
				$src = JURI::root() . $this->value;
			}
			else
			{
				$src = '';
			}

			$width  = (int) XmlHelper::get($this->element, 'preview_width', 300);
			$height = (int) XmlHelper::get($this->element, 'preview_height', 200);
			$style  = '';
			$style .= ($width > 0)  ? 'max-width:' . $width . 'px;'   : '';
			$style .= ($height > 0) ? 'max-height:' . $height . 'px;' : '';
			$style .= 'margin: 10px 0;';

			$imgattr = array(
				'id'    => $this->id . '_preview',
				'class' => 'media-preview',
				'style' => $style,
			);

			$imgattr['class'] = $imgattr['class'] . ' img-polaroid';

			$img             = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $imgattr);
			$previewImg      = '<div class="preview-img" id="' . $this->id . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
			$previewImgEmpty = '<div class="preview-empty" id="' . $this->id . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
				. JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';

			$html[] = '<div class="media-preview add-on fltlft">';

			$html[] = ' ' . $previewImgEmpty;
			$html[] = ' ' . $previewImg;

			$html[] = '</div>';
		}

		return implode("\n", $html);
	}

	/**
	 * initScript
	 *
	 * @param   string  $selector
	 * @param   array   $options
	 *
	 * @return  void
	 */
	protected function initScript($selector, $options)
	{
		$options = \Windwalker\Asset\AssetManager::getJSObject($options);

		$asset = Container::getInstance()->get('helper.asset');
		$asset->internalJS(<<<JS
// Finder Field for $selector
jQuery(document).ready(function($) {
    var finder = $('$selector').finderField($options);

    window.windwalkerFinderSelect_{$this->id} = function (selected, elFinder, root) {
		finder.selectFile(selected, elFinder, root);
	}
});
JS
);
	}

	/**
	 * Set Selecting JS.
	 *
	 * @return void
	 */
	public function setScript()
	{
		// Build Select script.
		$url_root = JUri::root();

		$script = <<<JS

;(function($) {

    var plugin = 'finderField';

    var urlRoot = "$url_root";

    /**
     * Windwalker Finder object.
     *
     * @param {jQuery} element
     * @param {Object} options
     *
     * @constructor
     */
    var WindwalkerFinder = function(element, options) {
		this.element = element;
		this.options = $.extend(true, {}, options);

		this.wrapper = this.element.parents('.controls');

		this.nameInput = this.wrapper.find('.finder-item-name');
		this.previewWrapper = this.wrapper.find('.preview-img');
		this.previewImage = this.wrapper.find('.media-preview');
		this.previewEmpty = this.wrapper.find('.preview-empty');
		this.clearButton = this.wrapper.find('.clear-button');

		this.registerEvents();
    };

    WindwalkerFinder.prototype = {

        /**
         * Register events.
         */
        registerEvents: function() {
    	    var self = this;

			this.clearButton.click(function(event) {
			    event.stopPropagation();
			    event.preventDefault();

				self.clear();
			});
    	},

        /**
         * Select file action.
         *
         * @param {Array}    selected  The selected files list.
         * @param {elFinder} elFinder  The elFinder object.
         * @param {string}   root      Root URL string.
         */
    	selectFile: function(selected, elFinder, root) {
    	    if(selected.length < 1) {
    	    	return;
    	    }

            var link = elFinder.url(selected[0].hash) ;
            var name = selected[0].name;

            // Clean DS
            link = link.replace(/\\\\/g, '/');
            link = link.replace( root, '' );

            // Detect is image
            var onlyImage = false;

            if(selected[0].mime.substring(0, 5) == 'image' ) {
                this.element.attr('image', 1);
            	this.element.attr('mime', selected[0].mime.split('/')[1]);

                this.element.val(link);
                this.nameInput.val(name);
            } else {
            	this.element.attr('image', 0);
            	this.element.attr('mime', selected[0].mime.split('/')[1]);

                if (!onlyImage) {
                    this.element.val(link);
                	this.nameInput.val(name);
                } else {
                    return;
                }
            }

            this.refreshPreview();

            setTimeout( function() {
                Windwalker.Modal.hide();
            } ,200);
    	},

        /**
         * Clear image selected.
         */
    	clear: function() {
    	    this.element.val(null);
    	    this.nameInput.val(this.options.text.clear_title);

    	    this.refreshPreview();
    	},

        /**
         * Refresh preview.
         */
    	refreshPreview: function() {
            var value   = this.element.val();
            var imgExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
            var ext     = value.split('.').pop();

            if (this.previewWrapper.length > 0) {
                if ($.inArray(imgExts, ext.toLowerCase())) {
                    this.previewImage.attr('src', urlRoot + value);
                    this.previewWrapper.css('display', '');
                    this.previewEmpty.css('display', 'none');
                } else {
                    this.previewImage.attr('src', '');
                    this.previewWrapper.css('display', 'none');
                    this.previewEmpty.css('display', 'none');
                }
            }

            if (!value) {
                this.previewImage.attr('src', '');
                this.previewWrapper.css('display', 'none');
				this.previewEmpty.css('display', '');
            }
        }
    };

    /**
     * Push to plugin.
     *
     * @param {Object} options
     *
     * @returns {*}
     */
    $.fn[plugin] = function(options)
    {
        if (!$.data(this, "windwalker." + plugin))
        {
            $.data(this, "windwalker." + plugin, new WindwalkerFinder(this, options));
        }

        return $.data(this, "windwalker." + plugin);
    };

})(jQuery);
JS;

		// Add the script to the document head.
		$asset = Container::getInstance()->get('helper.asset');
		$asset->internalJS($script);
	}

	/**
	 * Get item title.
	 *
	 * @return string The title text.
	 */
	public function getTitle()
	{
		$path = $this->value;

		if (!$path)
		{
			return null;
		}

		$path = JPath::clean($path, '/');
		$path = explode('/', $path);

		$file_name = array_pop($path);

		return $file_name;
	}

	/**
	 * Get Finder link.
	 *
	 * @return string The link string.
	 */
	public function getLink()
	{
		$input   = Container::getInstance()->get('input');
		$handler = $this->element['handler'] ? (string) $this->element['handler'] : $input->get('option');

		$root       = XmlHelper::get($this->element, 'root', '/');
		$start_path = XmlHelper::get($this->element, 'start_path', '/');
		$onlymimes  = XmlHelper::get($this->element, 'onlymimes', '');

		$root = $this->convertPath($root);
		$start_path = $this->convertPath($start_path);

		$link = "index.php?option={$handler}&task=finder.elfinder.display&tmpl=component&finder_id={$this->id}&root={$root}&start_path={$start_path}&onlymimes={$onlymimes}&callback=windwalkerFinderSelect_{$this->id}";

		return $link;
	}

	/**
	 * convertPath
	 *
	 * @param string $path
	 *
	 * @return  string
	 */
	protected function convertPath($path)
	{
		$user = Container::getInstance()->get('user');
		$date = DateHelper::getDate();

		$replace = array(
			'username' => $user->username,
			'name' => $user->name,
			'session' => \JFactory::getSession()->getId(),
			'year' => $date->year,
			'month' => $date->month,
			'day' => $date->day
		);

		return StringHelper::parseVariable($path, $replace);
	}
}
