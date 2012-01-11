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
