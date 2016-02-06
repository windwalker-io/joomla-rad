/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

;(function($)
{
    "use strict";

    var plugin = 'quickadd';

    var defaultOptions = {};

    var QuickAdd = function(element, options)
    {
        this.element = element;
        this.control = element.parents('.controls');
        this.inputs = this.control.find('.modal').find('input, select, textarea');
        this.submitButton = this.control.find('.modal button[type=submit]');

        this.options = $.extend(defaultOptions, options);

        // Remove all required
        this.inputs.each(function(e)
        {
            $(this).removeClass('required').removeAttr('required').removeAttr('aria-required');
        });

        this.registerEvents();
    };

    QuickAdd.prototype.registerEvents = function()
    {
        this.submitButton.click(function(event)
        {
            event.preventDefault();
            event.stopPropagation();

            console.log('123'); 
        });
    };
    
    $.fn[plugin] = function(options)
    {
        if (!$.data(this, "windwalker." + plugin))
        {
            $.data(this, "windwalker." + plugin, new QuickAdd(this, options));
        }

        return $.data(this, "windwalker." + plugin);
    };
    
})(jQuery);
