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

    var defaultOptions = {
        task: 'quickAddAjax',
        ajax: 1
    };

    /**
     * Quick Add object.
     *
     * @param {jQuery} element
     * @param {Object} options
     *
     * @constructor
     */
    var QuickAdd = function(element, options)
    {
        this.element = element;
        this.control = element.parents('.controls');
        this.select  = this.control.find('> select');
        this.inputs  = this.element.find('input, select, textarea');
        this.submitButton = this.element.find('button.quickadd_submit');

        this.options = $.extend(true, {}, defaultOptions, options);

        this.options.option   = this.options.quickadd_handler;
        this.options.formctrl = element.selector.substr(1);

        // Remove all required and set default
        this.inputs.each(function(e)
        {
            var $input = $(this);

            $input.removeClass('required')
                .removeAttr('required')
                .removeAttr('aria-required');

            $input.attr('default', $input.val());
        });

        this.registerEvents();
    };

    QuickAdd.prototype = {
        /**
         * Register Events.
         */
        registerEvents: function()
        {
            var self = this;

            this.submitButton.click(function(event)
            {
                event.preventDefault();
                event.stopPropagation();

                self.createItem();
            });

            this.inputs.on('keydown', function(event)
            {
                if (event.keyCode == 13)
                {
                    event.preventDefault();
                    event.stopPropagation();

                    if (event.ctrlKey || event.metaKey)
                    {
                        self.createItem();
                    }
                }
            });
        },

        /**
         * Create item ajax.
         */
        createItem: function()
        {
            var data = {};
            var self = this;

            this.submitButton.attr('disabled', true);

            $.each(this.options, function(i)
            {
                data[i] = this;
            });

            $.each(this.inputs, function(i)
            {
                var $input = $(this);

                data[$input.attr('name')] = $input.val();
            });

            $.ajax({
                url: 'index.php',
                data: data,
                dataType: 'json',
                mwthod: 'POST'
            }).done(function(data, status, jqXHR)
            {
                if (data.Result)
                {
                    self.inputs.each(function(i)
                    {
                        var $input = $(this);

                        $input.val($input.attr('default'));
                    });

                    // Hide Modal
                    self.element.modal('hide');

                    var optionText  = data.data[self.options.value_field];
                    var optionValue = data.data[self.options.key_field];

                    // Add new Option in Select
                    if (self.select.length)
                    {
                        self.select.append(
                            $('<option>', {
                                text: optionText,
                                value: optionValue
                            })
                        );

                        self.select.val(optionValue);
                    }

                    // Add Title for Modal input
                    var selectId  = '#' + self.options.formctrl.replace('_quickadd', '');
                    var modalName = $(selectId + '_name');
                    var modalId   = $(selectId + '_id');

                    // Wait and highlight for chosen
                    var chzn = self.control.find('> .chzn-container .chzn-single span');

                    if (chzn.length > 0)
                    {
                        setTimeout(function()
                        {
                            self.select.trigger("liszt:updated");
                            $(chzn).effect('highlight');
                        }, 500);
                    }
                    else
                    {
                        // Wait and highlight
                        setTimeout(function()
                        {
                            $(self.select).effect('highlight');
                        }, 500);
                    }

                    // Wait and highlight for modal
                    if (modalName.length)
                    {
                        setTimeout(function()
                        {
                            modalName.attr('value', optionText);
                            modalId.attr('value', optionValue);
                            $(modalName).effect('highlight');
                        }, 500);
                    }
                }
                else
                {
                    alert(data.errorMsg);
                }

            }).fail(function(jqXHR, status, error)
            {
                alert(status);
            }).always(function()
            {
                self.submitButton.attr('disabled', false);
            });
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
            $.data(this, "windwalker." + plugin, new QuickAdd(this, options));
        }

        return $.data(this, "windwalker." + plugin);
    };
    
})(jQuery);
