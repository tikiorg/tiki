/** $Id$
 *
 * To facilitate use of ajax services, including providing providing modal popup forms and feedback
 */


/**
 * To trigger ajax services with a form submission
 *
 *  - clicked submit element must have a class of confirm-submit
 *  - the formaction attribute of the submit element must be set using the bootstrap_modal smarty function
 *  - the form attribute of the submit element must be set with the form name
 *  - for a submit button related to a select element:
 *      - the name attribute of the select element must be set to action (name=action)
 *      - the select option value being submitted should be the action value only (e.g., remove_users)
 *      - the submit element's formaction attribute value will be used for the first part of the services url,
 *          ie without the action specified - eg {bootstrap_modal controller=user}
 *      - the above requirements for a submitted select value (ie, name=action, value contains only the action, rest of
 *          url in formaction) is necessary for ajax services to work when javascript is not enabled
 */

$('[type=submit].confirm-submit').click(function() {
	var target = $('.modal.fade:not(.in)').first();
	$.post($(this).attr('formaction'), $(this.form).serialize(), function (data) {
		$('.modal-content', target).html(data);
		target.modal();
	});
	return false;
});

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