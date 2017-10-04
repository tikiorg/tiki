/** $Id$
 *
 * To facilitate use of ajax services, including providing providing modal popup forms and feedback
 */


/**
 * To trigger ajax services modal with a form submission
 *
 *  - clicked submit element must have a class of confirm-submit
 *  - the formaction attribute of the submit element must be set using the bootstrap_modal smarty function
 *  - the form attribute of the submit element must be set with the form id
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

		// IE seems to need some help submitting the form here?
		$('.confirm-action-btn', target).click(function () {
			$(this).parents(".modal-content").find("form").submit();
		});

		target.modal();
	});
	return false;
});

/**
 * To trigger ajax services when there are no modals involved
 *
 *  - clicked submit element must have a class of service-submit
 *  - the formaction attribute of the submit element must be set using the service smarty function
 *  - the form attribute of the submit element must be set with the form id
 */
$('[type=submit].service-submit').click(function() {
	$.post($(this).attr('formaction'), $(this.form).serialize(), function (data) {});
	return false;
});

// Triggers the form submission in ajax confirmation modals.
// This is needed for IE 10/11 since it does not support the form attribute on buttons.
$(document).on('click', 'button[form="confirm-action"]' ,function(ev) {
	ev.preventDefault();
	$('#confirm-action').submit();
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
			if (!data) {
				$.closeModal();
				return;
			}
			var extra = data.extra || false, dataurl = data.url || false, dataError = data.error || false;
			if (extra) {
				/* Simply close modal. Feedback is added to the page without refreshing in the ajax service using the
				the sandard Feedback class function send_headers(). Used when there is an error in submitting modal
				form*/
				if (extra === 'close') {
					$.closeModal();
				//Close modal and refresh page. Feedback can be added to the refreshed page in the ajax service using
				//the Feedback class
				} else if (extra === 'refresh') {
					$.closeModal();
					document.location.href = document.location.href.replace(/#.*$/, "");
				}
			}
			//send to another page
			if (dataurl) {
				$.closeModal();
				//reload if the same base url, otherwise page won't refresh if only adding a hash anchor to the same page
				var $href = document.location.href.replace(/#.*$/, "");
				if (dataurl.indexOf($href) > -1 || dataurl === $href) {
					document.location.href = dataurl;
					location.reload();
				//different url prior to any anchor tags
				} else {
					document.location.assign(dataurl);
				}
			}
			//send error
			if (dataError) {
				if (dataError === 'CSRF') {
					dataError = tr('Potential cross-site request forgery (CSRF) detected. Operation blocked. The security ticket may have expired - reloading the page may help.');
				}
				$.closeModal();
				feedback (
					dataError,
					'error'
				);
			}
			return false;
		}
	});
}