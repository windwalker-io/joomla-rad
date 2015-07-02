/*!
 * AKQuickAdd JS
 *
 * Copyright 2013 Asikart.com
 * License GNU General Public License version 2 or later; see LICENSE.txt, see LICENSE.php
 *
 * Generator: AKHelper
 * Author: Asika
 */

var AKQuickAdd = ({

	/**
	 * Object init.
	 *
	 * @param {string} id
	 * @param {Object} option
	 */
	init: function(id, option)
	{
		var inputs = $$('#' + id)[0].getElements('input, select, textarea');

		// Set Option
		option.task = option.task || 'quickAddAjax';
		option.option = option.quickadd_handler;
		option.ajax = 1;
		option.formctrl = id;

		if (!this.option)
		{
			this.option = Array();
		}

		this.option[id] = option;

		this.send_setted = Array();

		// Remove Required
		inputs.each(function(e)
		{
			e.removeClass('required');
			e.set('default', e.get('value'));

			e.erase('required').erase('aria-required');
		});
	},

	/**
	 * Submit data.
	 *
	 * @param {int}   id
	 * @param {Event} event
	 */
	submit: function(id, event)
	{
		var button = event.target;
		var area = $$('.' + id).getLast();
		var option = this.option[id];
		var uri = new URI();

		// Prevent Event
		event.preventDefault();

		// Set button disabled
		button.addClass('disabled');
		button.set('disabled', true);

		// Set Request Option
		var requestSetting = {

			/**
			 * Url string.
			 *
			 * @type string
			 */
			url: 'index.php',

			/**
			 * On success event.
			 *
			 * @param {string} responseText
			 */
			onSuccess: function(responseText)
			{
				// Response
				var response = JSON.decode(responseText);

				// Set Result Action
				if (response.Result)
				{
					// Reset inputs
					var inputs = area.getElements('input, select, textarea');

					inputs.each(function(e)
					{
						e.set('value', e.get('default'));
					});

					var data = response.data;
					var select_id = '#' + id.replace('_quickadd', '');

					// Detect Joomla! version
					if (option.joomla3)
					{
						// Hide Modal
						jQuery('.' + id).modal('hide');

						// Add new Option in Select
						var select = jQuery(select_id);

						if (select)
						{
							select.append(new Option(data[option.value_field], data[option.key_field], true, true));
						}
					}
					else
					{
						// Hide Modal
						SqueezeBox.close();

						// Add new Option in Select
						var select = $$(select_id)[0];
						if (select)
						{
							new Element('option', { text: data[option.value_field], value: data[option.key_field], selected: true}).inject(select, 'bottom');
						}
					}

					// Add Title for Modal input
					var modal_name = $$(select_id + '_name');
					var modal_id = $$(select_id + '_id');

					// Wait and highlight
					setTimeout(function()
					{
						$$(select_id).highlight();
					}, 500);

					// Wait and highlight for chosen
					var chzn = $$(select_id + '_chzn .chzn-single span');

					if (chzn.length > 0)
					{
						setTimeout(function()
						{
							select.trigger("liszt:updated");
							chzn.highlight();
						}, 500);
					}

					// Wait and highlight for modal
					if (modal_name)
					{
						setTimeout(function()
						{
							modal_name.set('value', data[option.value_field]);
							modal_id.set('value', data[option.key_field]);
							modal_name.highlight();
						}, 500);
					}
				} else
				{
					// Warning
					alert(response.errorMsg);
				}

			},

			/**
			 * On complete event.
			 */
			onComplete: function()
			{
				// Reset button
				button.removeClass('disabled');
				button.set('disabled', null);
			}
		};

		// Set Request Option once
		if (!this.send_setted[id] || !option.joomla3)
		{
			area.set('send', requestSetting);
			this.send_setted[id] = 1;
		}

		// Send Request
		uri.setData(option);

		area.send(uri.toString());
	},

	/**
	 * Close Modal.
	 *
	 * @param {string} id
	 */
	closeModal: function(id)
	{
		if (this.option[id].joomla3)
		{
		}
		else
		{
			SqueezeBox.close();
		}
	}
});
