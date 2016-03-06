<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Script;

use Windwalker\Asset\AssetManager;

/**
 * The WindwalkerScript class.
 *
 * @since  2.1
 */
class WindwalkerScript extends AbstractScriptManager
{
	/**
	 * Quickadd script.
	 *
	 * @param  string  $selector  The selector to enable this script.
	 * @param  array   $options   The options of this script.
	 *
	 * @return  void
	 */
	public static function quickadd($selector, $options = array())
	{
		$asset = static::getAsset();

		if (!static::inited(__METHOD__))
		{
			JQueryScript::ui(array('effect'));
			$asset->addJS('quickadd.min.js');
		}

		if (!static::inited(__METHOD__, func_get_args()))
		{
			$options = AssetManager::getJSObject($options);

			$js = <<<JS
jQuery(document).ready(function($) {
	$('$selector').quickadd($options);
});
JS;

			$asset->internalJS($js);
		}
	}

	/**
	 * The modal to open link.
	 *
	 * @param  string  $selector  The selector to enable this script.
	 *
	 * @return  void
	 */
	public static function modal($selector = '.hasModal')
	{
		$asset = static::getAsset();

		if (!static::inited(__METHOD__))
		{
			\JHtmlBootstrap::framework();

			$js = <<<JS
var Windwalker;

// Init modal
jQuery(document).ready(function($)
{
	var modalBox = $('<div class="modal fade hide" id="windwalker-iframe-modal"> \
    <div class="modal-dialog"> \
        <div class="modal-content"> \
            <div class="modal-body" style="max-height: 500px;"> \
                <iframe width="100%" style="min-height: 450px;" src="" frameborder="0"></iframe> \
            </div> \
        </div> \
    </div> \
</div>');

	$('body').append(modalBox);

	Windwalker = Windwalker || (Windawlekr = {});

	Windwalker.Modal = {
		hide: function() { modalBox.modal('hide') },
		toggle: function() { modalBox.modal('toggle') },
		show: function() { modalBox.modal('show') },
		handleUpdate: function() { modalBox.modal('handleUpdate') },
	};
});
JS;

			$asset->internalJS($js);
		}

		if (!static::inited(__METHOD__, func_get_args()))
		{
			$js = <<<JS
// Modal task
jQuery(document).ready(function($)
{
	$('{$selector}').click(function(event)
	{
		var link   = $(this);
		var modal  = $('#windwalker-iframe-modal');
		var href   = link.attr('href');
		var iframe = modal.find('iframe');

		iframe.attr('src', href);
		modal.modal('show');
		modal.on('hide.bs.modal', function() {
		    iframe.attr('src', '');
		})

		event.stopPropagation();
		event.preventDefault();
	});
});
JS;

			$asset->internalJS($js);
		}
	}
}
