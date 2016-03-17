/* (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * $Id$
 */


jQuery.validator.setDefaults({
	errorClass: 'label label-warning',
	errorPlacement: function(error, element) {
		if ($(element).parents('.input-group').length > 0) {
			error.insertAfter($(element).parents('.input-group').first());
		} else if ($(element).parents('.has-error').length > 0) {
			error.appendTo($(element).parents('.has-error').first());
		} else {
			error.insertAfter(element);
		}
	},
	highlight: function(element) {
		$(element).parents('div, p').first().addClass('has-error');
	},
	unhighlight: function(element) {
		$(element).parents('div, p').first().removeClass('has-error');
	},
	ignore: '.ignore'
});


// see http://stackoverflow.com/questions/1300994/

jQuery.validator.addMethod("required_in_group", function (value, element, options) {
	var numberRequired = options[0], excluded;
	var selector = options[1];
	if (typeof options[2] != 'undefined') {
		excluded = options[2];
	} else {
		excluded = '';
	}
	//Look for our selector within the parent form
	var validOrNot = $(selector, element.form).filter(
			function () {
				// Each field is kept if it has a value
				return ($(this).val() && $(this).val().toLowerCase() != excluded);
				// Set to true if there are enough, else to false
			}).length >= numberRequired;

	var validator = this;
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
		validator.valid();
		fields.data('being_validated', false);
	}

	return validOrNot;

	// {0} below is the 0th item in the options field
}, jQuery.validator.format(tr("Please fill out {0} of these fields.")));

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
}, jQuery.validator.format("File required"));

// for validating email fields where multiple addresses can be entered
// separator is options[0] and defaults to comma

jQuery.validator.addMethod("email_multi", function (value, element, options) {
	var separator = options[0] || ",";
	var emails = $(element).val().split(separator);

	for (var i = 0; i < emails.length; i++) {
		if (!$.validator.methods["email"].call( this, $.trim(emails[i]), element )) {
			return false;
		}
	}

	return true;

}, jQuery.validator.format("Please enter valid email addresses separated by commas"));

jQuery.validator.addClassRules("email_multi", {
	email_multi: true
});

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

	// prevent double submission
	if(!$(me).data("sub")) {
		$(me).tikiModal('Save in Progress...');
		$(me).data("sub", true);
		me.submit();
	}
	return true;
}
