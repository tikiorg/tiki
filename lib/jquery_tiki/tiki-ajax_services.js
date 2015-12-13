/** $Id$
 *
 * To facilitate use of ajax services, including providing providing modal popup forms and feedback
 */

/**
 * Triggers confirmClick function below when an action item within a popover is clicked
 */
$('[data-toggle=popover]').on('shown.bs.popover', function() {
	$('.confirm-click').click(function() {
		confirmClick(this, 'href');
		return false;
	});
});

/**
 * Uses url from an element attribute (usually href from an anchor element) as content for an ajax services popup modal
 *
 * - The {service} smarty function should be used to generate the attribute url and query string
 * - This function assumes all parameters are specified in the {service} function
 *
 * @param element
 * @param attr
 * @returns {boolean}
 */
function confirmClick(element, attr) {
	var url = $(element).attr(attr), params = 'modal=1';
	postModal(url, params);
	return false;
}

/**
 * Triggers confirmForm function below when a form with class confirm-form is submitted
 */

$('form.confirm-form').submit(function() {
	confirmForm(this);
	return false;
});

/**
 * Uses post from a submitted form as content for an ajax services popup modal
 *
 * - Form action attribute should use the {service} smarty function
 * - The only parameters that should be set in the {service} function is controller and action (optional)
 *      All other parameters should be set using input elements
 * - An ajax services action can be submitted with the form, in which case this function assumes
 *      the form action attribute is not set, e.g. {service controller=user}
 * - Use something like the following to bind this function to the submission of the form
 *      $('form.confirm-form').submit(function() {
 *          confirmForm(this);
 *          return false;
 *      });
 * @param form
 * @returns {boolean}
 */
function confirmForm(form) {
	var submitAction = $(form.action).val() || false, params = $(form).serialize() + '&modal=1',
		formAction = $(form).attr('action'), url;
	//see if the action was submitted - it will override the form action attribute if so
	if (submitAction) {
		//get controller from form action attribute and action from submitted action
		//different methods for extracting controller depending on whether sefurl is being used
		var sefurl = formAction.indexOf('tiki-ajax_services.php') <= -1;
		if (sefurl) {
			var split = formAction.split('-');
			url = $.serviceUrl({controller: split[1], action: submitAction});
		} else {
			url = formAction + '&action=' + submitAction;
		}
	} else {
		url = formAction;
	}
	postModal(url, params);
	return false;
}

/**
 * Utility used by the above two functions to generate the popup modal
 * - &_POST is used instead of $_GET - avoids limitations on parameters
 * - The buttons in the popup modal will need to be specified in the ajax services action template, usually in a
 *      div with the class 'modal-footer'
 *
 * @param url
 * @param params
 */
function postModal(url, params) {
	var target = $('.modal.fade:not(.in)').first();
	$.post(url, params, function (data) {
		$('.modal-content', target).html(data);
		target.modal();
	});
}

/**
 * Use data posted from a popup modal as input for the ajax service action
 *
 * @param form
 */
function confirmAction(form) {
	//this is the ajax action once the confirm submit button is clicked
	$.ajax({
		dataType: 'json',
		url: $(form).attr('action'),
		type: 'POST',
		data: $(form).serialize(),
		success: function (data) {
			var extra = data.extra || false, forward = data.FORWARD || false, dataurl = data.url || false,
				feedback = data.feedback || false, modal = data.modal || false;
			/* if extra is set to post and forward and modal are not set
			feedback is put in a predefined feedback box on the page itself
			after the page is refreshed
			 */
			if (extra && !forward && !modal) {
				//page is refreshed and feedback information is posted back to url
				//requires php and smarty tpl logic to receive and display
				if (extra === 'post') {
					var newform = $('<form/>', {id: 'ajaxfeedback', action : location.href, method : 'POST'})
						.append($('<input />', {type: 'submit'}));
					if (feedback) {
						$.each(feedback, function (key, value) {
							if ($.isArray(value) || $.isPlainObject(value)) {
								$.each(value, function (key2, value2) {
									newform.append($('<input />',
										{type: 'hidden', name: key + '[' + key2 + ']', value: value2}));
								});
							} else {
								newform.append($('<input />', {type: 'hidden', name: key, value: value}));
							}
						});
					}
					newform.appendTo(document.body).submit();
					return false;
				}
			}
			/*
			If forward and modal are set, the FORWARD service is passed to the modal and
			replaces the contents. Redirect and timer functionality are incorporated as well
			 */
			if (forward && modal) {
				//clear modal content
				$(form).children().remove();
				//load service in forward
				$(form).loadService(forward, {origin: form});
				//clear all buttons except for the close (modal dismiss) button
				$(form).closest('.modal').find('.modal-footer .btn').not('.btn-dismiss').remove();
				//put an event listener for the modal closing
				$(form).closest('.modal').on('hidden.bs.modal', function () {
					if (dataurl) {
						//redirect to specified url in data.url
						document.location.href = dataurl;
					} else{
						//otherwise, refresh page
						document.location.href = document.location.href.replace(/#.*$/, "");
					}
				});
				//set timer and close modal once expired (triggering redirect/refresh)
				counter = forward.ajaxtimer;
				timer = setInterval(function() {
					$('span#timer-seconds').html(--counter);
					if (counter == 0 || counter < 0) {
						$.closeModal();
					}
				}, 1000);
				return false;
			} else {
				$.closeModal();
			}
			//if there's a data.FORWARD after ajax success, then this function assumes it needs to inject an alert
			//into the current page and then redirect to a second page. Used in user controller for banning
			if (forward){
				var alerthref, parsedhtml, alert, msgdiv, timer, counter;
				alerthref = $.service(forward.controller, forward.action, forward);
				$.ajax({
					dataType: 'html',
					url: alerthref,
					type: 'POST',
					success: function (data2) {
						$('div#posted-ajax-feedback').fadeOut(1000);
						//insert the success alert onto the page
						parsedhtml = $.parseHTML(data2);
						//extracts content from templates/utilities/alert.tpl
						alert = $(parsedhtml).find('div#alert-wrapper');
						//receiving page needs to have a div with id ajax-feedback
						msgdiv = $('div#ajax-feedback');
						if (msgdiv.length === 0){
							return false;
						}
						msgdiv.html(alert.html())
							.fadeIn(1000);
						//scroll up to feedback alert if not visible
						if ($(document).scrollTop() > msgdiv.offset().top) {
							$('html, body').animate({
								scrollTop: msgdiv.offset().top
							}, 1000);
						}
						counter = forward.ajaxtimer;
						timer = setInterval(function() {
							$('span#timer-seconds').html(--counter);
							if (counter == 0 || counter < 0) { clearInterval(timer)}
						}, 1000);
					}
				});
				if (dataurl) {
					setTimeout(function () {
						window.location.href = dataurl;
						return false;
					}, (data.FORWARD.ajaxtimer + 1) * 1000);
				}
			} else {
				return false;
			}
			return false;
		},
		error: function (jqxhr, status, errorObj) {
			var msgdiv = $('div#error_report');
			$.closeModal();
			if ($(document).scrollTop() > msgdiv.offset().top) {
				$('html, body').animate({
					scrollTop: msgdiv.offset().top
				}, 1000);
			}
		}
	});
}