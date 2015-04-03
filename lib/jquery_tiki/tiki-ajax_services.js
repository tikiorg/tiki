/** $Id$
 *
 * To facilitate use of ajax services, allowing for update of page information without refreshing and
 * providing modal popup feedback (e.g., a warning notice that no items were selected)
 */

function confirmModal(element, firstSvc) {
	var settings;
	settings = getHrefAndParams(element, firstSvc);
	if (settings) {
		$.openModal({
			remote: settings.href + '?' + settings.params
		});
	}
}

function confirmAction(element, firstSvc) {
	var settings, extra;
	settings = getHrefAndParams(element, firstSvc);
	if (settings) {
		$.ajax({
			dataType: 'json',
			url: settings.href,
			type: 'POST',
			data: settings.params,
			success: function (data) {
				if (typeof data.extra !== 'undefined' && data.extra !== 'null' && typeof data.FORWARD === 'undefined') {
					if (data.extra === 'reload') {
							location.reload();
					} else if (data.extra === 'post') {
						var form = $('<form/>', {id: 'ajaxfeedback', action : location.href, method : 'POST'})
							.append($('<input />', {type: 'submit'}));
						if (typeof data.feedback !== 'undefined') {
							$.each(data.feedback, function (key, value) {
								if ($.isArray(value) || $.isPlainObject(value)) {
									$.each(value, function (key2, value2) {
										form.append($('<input />', {type: 'hidden', name: key + '[' + key2 + ']', value: value2}));
									});
								} else {
									form.append($('<input />', {type: 'hidden', name: key, value: value}));
								}
							});
						}
						form.appendTo(document.body).submit();
					} else {
						extra = $.parseJSON(data.extra);
						if (typeof extra.secondSelector !== 'undefined' && typeof extra.secondFn !== 'undefined') {
							if (typeof extra.secondParams !== 'undefined') {
								if (extra.secondParams === 'noparams') {
									$(extra.secondSelector)[extra.secondFn]();
								} else {
									$(extra.secondSelector)[extra.secondFn](extra.secondParams);
								}
							} else {
								$(extra.secondSelector)[extra.secondFn](settings.params);
							}
						}
					}
				}
				$.closeModal();
				if (typeof data.FORWARD !== 'undefined' && data.extra !== 'reload'){
					var alerthref, parsedhtml, alert, msgdiv, timer, counter;
					alerthref = $.service(data.FORWARD.controller, data.FORWARD.action, data.FORWARD);
					$.ajax({
						dataType: 'html',
						url: alerthref,
						type: 'POST',
						success: function (data2) {
							$('div#posted-ajax-feedback').fadeOut(1000);
							//insert the success alert onto the page
							parsedhtml = $.parseHTML(data2);
							alert = $(parsedhtml).find('div#alert-wrapper');
							msgdiv = $('div#ajax-feedback');
							msgdiv.html(alert.html())
								.fadeIn(1000);
							//scroll up to feedback alert if not visible
							if ($(document).scrollTop() > msgdiv.offset().top) {
								$('html, body').animate({
									scrollTop: msgdiv.offset().top
								}, 1000);
							}
							counter = data.FORWARD.ajaxtimer;
							timer = setInterval(function() {
								$('span#timer-seconds').html(--counter);
								if (counter == 0 || counter < 0) { clearInterval(timer)}
							}, 1000);
						}
					});
					if (typeof data.url !== 'undefined') {
						setTimeout(function () {
							window.location.href = data.url;
							return false;
						}, (data.FORWARD.ajaxtimer + 1) * 1000);
					}
				} else {
					return false;
				}
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
	} else {
		return false;
	}
}

/**
 * Utility function used to generate the href and parameters for functions above for the services smarty function
 * Handles the following cases:
 * - ajax service (controller and action) is defined within onclick function and parameters are defined:
 * 		- also in the onclick function with a params object
 * 		- or in another element, like closest form, or a data attribute of the element
 * - ajax service is fully defined in a custom data element, e.g. using the smarty service function
 * 		- in this case the parameters should be in a parameter named params
 * @param element		element object		Onclick element
 * @param settings		plain object		Onclick object parameter
 * @returns an object with the href and url parameters
 */
function getHrefAndParams(element, settings) {
	var query = null, query2 = null, href, base, params, formAction, plus;
	if ($.isPlainObject(settings)) {
		$.each(settings, function (key, value) {
			//in case the action is an object specifying another element, e.g. a selected option
			if (key === 'action' && $.isPlainObject(value)) {
				if (typeof value.selector !== 'undefined' && typeof value.fn !== 'undefined') {
					if (typeof value.inputval !== 'undefined') {
						settings.action = $(value.selector)[value.fn](value.inputval);
					} else {
						settings.action = $(value.selector)[value.fn]();
					}
				}
			}
			//params may be elsewhere, e.g., in the closest form, or a custom data attribute in the element
			if ($.inArray(key, ['closest', 'data']) > -1) {
				if (query === null) {
					query = $(element)[key](value);
					if (value === 'form') {
						formAction = $(query).attr('action');
						query = $(query).serialize();
					} else {
						query = JSON.stringify(query);
					}
				} else {
					query2 = $(element)[key](value);
					if (value === 'form') {
						formAction = $(query2).attr('action');
						query2 = $(query2).serialize();
					} else {
						query2 = JSON.stringify(query2);
					}
				}
				if (query2 !== null) {
					query = query + '&' + query2;
				}
			}
			//add any params set in the onclick function
			if (key === 'params' && $.isPlainObject(value)) {
				plus = decodeURIComponent($.param(value));
				query = query === null ? plus : query + '&' + plus;
			}
		});
		//three ways to set the ajax service:
		//in the onclick function
		if (typeof settings.controller !== 'undefined' && typeof settings.action !== 'undefined') {
			href = $.service(settings.controller, settings.action, {'params': query});
			query = null;
		//in the form action attribute
		} else if (typeof formAction !== 'undefined') {
			href = formAction;
		//service is fully defined in a custom data element (e.g., using the smarty service function)
		} else if (typeof settings.data !== 'undefined') {
			href = $(element).data(settings.data);
		}
		if (typeof href !== 'undefined') {
			href = href.split('?');
			base = href[0];
			params = href[1];
		}
		if (query !== null) {
			if (query.indexOf('?') > -1 && typeof base === 'undefined') {
				href = query.split('?');
				base = href[0];
				params = typeof params === 'undefined' ? href[1] : params + '&' + href[1];
			} else if (query.indexOf('?') === -1) {
				params = typeof params === 'undefined' ? query : params + '&' + query;
			}
		}
		return {'href': base, 'params': params};
	} else {
		return false;
	}
}