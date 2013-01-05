/* (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * $Id$

 * This file should be renamed validator_tiki.js as it's now a general collection of add-ons for jquery.validate
 * TODO after 9.0
 */

// see http://stackoverflow.com/questions/1300994/

jQuery.validator.addMethod("required_in_group", function (value, element, options) {
	var numberRequired = options[0];
	var selector = options[1];
	//Look for our selector within the parent form
	var validOrNot = $(selector, element.form).filter(
			function () {
				// Each field is kept if it has a value
				return $(this).val();
				// Set to true if there are enough, else to false
			}).length >= numberRequired;

	// The elegent part - this element needs to check the others that match the
	// selector, but we don't want to set off a feedback loop where each element
	// has to check each other element. It would be like:
	// Element 1: "I might be valid if you're valid. Are you?"
	// Element 2: "Let's see. I might be valid if YOU'RE valid. Are you?"
	// Element 1: "Let's see. I might be valid if YOU'RE valid. Are you?"
	// ...etc, until we get a "too much recursion" error.
	//
	// So instead we
	//  1) Flag all matching elements as 'currently being validated'
	//  using jQuery's .data()
	//  2) Re-run validation on each of them. Since the others are now
	//     flagged as being in the process, they will skip this section,
	//     and therefore won't turn around and validate everything else
	//  3) Once that's done, we remove the 'currently being validated' flag
	//     from all the elements
	if (!$(element).data('being_validated')) {
		var fields = $(selector, element.form);
		fields.data('being_validated', true);
		// .valid() means "validate using all applicable rules" (which
		// includes this one)
		fields.valid();
		fields.data('being_validated', false);
	}
	return validOrNot;
	// {0} below is the 0th item in the options field
}, jQuery.format("Please fill out at least {0} of these fields."));

// for validating tracker file attachments based on required_in_group
// similar but needs a different message

jQuery.validator.addMethod("required_tracker_file", function (value, element, options) {
	var numberRequired = options[0];
	var selector = options[1];
	var validOrNot = $(selector, element.form).filter(
			function () {
				return $(this).val();
			}).length >= numberRequired;

	if (!$(element).data('being_validated')) {
		var fields = $(selector, element.form);
		fields.data('being_validated', true);
		fields.valid();
		fields.data('being_validated', false);
	}
	return validOrNot;
}, jQuery.format("File required"));

/**
 * Wait for AJAX form validation to finish before proceeding with submit
 *
 * @param	me form element
 * @return	{Boolean}
 */
function process_submit(me) {

	if (!$(me).attr("is_validating")) {
		$(me).attr("is_validating", true);
		$(me).validate();
	}
	if ($(me).validate().pendingRequest > 0) {
		setTimeout(function() {process_submit(me);}, 500);
		return false;
	}
	$(me).attr("is_validating", false);

	if (!$(me).valid()) {
		return false;
	}
	// disable submit button(s)
	// FIXME after 9.0? - this seems to prevent the "save" field being sent in the request on webkit
	//$(me).find("input[type=submit]").attr("disabled", true);

	me.submit();
	return true;
}
