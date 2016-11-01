/*
 * jQuery AutoTab plugin
 * http://dev.lousyllama.com/auto-tab
 * 
 * Copyright (c) 2007 Matthew Miller
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 * 
 * Updated: 2007/06/26 16:50:52
 * Version: 1.00
 */

(function($) {

/**
 * autotab provides an easy way to apply auto tabbing to input
 * elements on a form. On top of auto tabbing forward, if the
 * user presses the backspace key on a box that is empty, the
 * supplied previous element will gain focus.
 * 
 * The maxlength attribute is required on each auto tab element.
 * 
 * @example  $('#area_code').autotab('#number1');
 * @desc Auto tabs to the #number1 element
 * 
 * @example  $('#number1').autotab('#number2', '#area_code');
 * @desc Auto tabs to the #number2 element, backspaces to the #area_code element
 * 
 * @name autotab
 * @param String (required) Element to focus on when maxlength is reached
 * @param String (optional) Element to focus on when backspacing
 * @cat Plugins/AutoTab
 */
$.fn.autotab = function(options) {
	var defaults = {
		format: 'all',		// text, numeric, alphanumeric, all
		limit: 5,			// Defaults to maxlength value
		uppercase: false,	// Converts a string to UPPERCASE
		lowercase: false,	// Converts a string to lowecase
		no_space: false,	// Remove spaces in the user input
		target: null,		// Where to auto tab to
		previous: null		// Backwards auto tab when all data is backspaced
	};

	$.extend(defaults, options);

	var limit = $(this).attr('maxlength');

	if(limit != 2147483647)
		defaults.limit = limit;

	var key = function(e) {
		if(!e)
			e = window.event;

		return e.keyCode;
	};

	// IE does not recognize the backspace key
	// with keypress in a blank input box
	if($.browser.msie)
	{
		this.keydown(function(e) {
			if(key(e) == 8)
			{
				var val = $(this).val();

				if(val.length == 0 && defaults.previous)
					defaults.previous.focus();
			}
		});
	}

	this.keypress(function(e) {
		if(key(e) == 8)
		{
			var val = $(this).val();

			if(val.length == 0 && defaults.previous)
				defaults.previous.focus();
		}
	}).keyup(function(e) {
		var val = $(this).val();

		switch(defaults.format)
		{
			case 'text':
				var pattern = new RegExp('[0-9]+', 'g');
				var val = val.replace(pattern, '');
				break;

			case 'alpha':
				var pattern = new RegExp('[^a-zA-Z]+', 'g');
				var val = val.replace(pattern, '');
				break;

			case 'numeric':
				var pattern = new RegExp('[^0-9]+', 'g');
				var val = val.replace(pattern, '');
				break;

			case 'alphanumeric':
				var pattern = new RegExp('[^0-9a-zA-Z]+', 'g');
				var val = val.replace(pattern, '');
				break;

			case 'all':
			default:
				break;
		}

		if(defaults.no_space)
		{
			pattern = new RegExp('[ ]+', 'g');
			val = val.replace(pattern, '');
		}

		if(defaults.uppercase)
			val = val.toUpperCase();

		if(defaults.lowercase)
			val = val.toLowerCase();

		$(this).val(val);

		switch(key(e))
		{
			// Do not auto tab when the following keys are pressed
			case 8:		// Backspace
			case 9:		// Tab
			case 16:	// Shift
			case 17:	// Ctrl
			case 18:	// Alt
			case 32:	// [SPACE]
			case 33:	// Page Up
			case 34:	// Page Down
			case 35:	// End
			case 36:	// Home
			case 37:	// Left Arrow
			case 38:	// Up Arrow
			case 39:	// Right Arrow
			case 40:	// Down Arrow
				break;

			default:
				if(val.length == defaults.limit && defaults.target)
					defaults.target.focus();
				break;
		}
	});

	return this;
};

})(jQuery);