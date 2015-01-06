/** $Id$
 *
 * To facilitate use of ajax services, allowing for update of page information without refreshing and
 * providing modal popup feedback
 */

/**
 * For use as an onclick function. First service specified should return a modal form.
 * After the form is submitted, a jquery function may be specified to update the page.
 * At the end, a success modal pops up and then automatically closes after 5 seconds.
 *
 * @param element           object  Element with the onclick (this)
 * @param firstSvc          object  Service parameters (controller and action), optional additional parameters (params),
 *                                      and optional function + selector to grab form data or a data attribute
 * @param secondSelector    string  Selector for element that jQuery function will be performed on
 * @param secondFn          string  Valid function name to apply to update the page information through ajax
 * @returns {boolean}
 */
function modalActionModal(element, firstSvc, secondSelector, secondFn) {
	var settings, handler;
	settings = getHrefAndParams(element, firstSvc);
	if (settings) {
		handler = $.clickModal({
			success: function (data) {
				$.closeModal({
					done: function () {
						$(secondSelector)[secondFn](settings.params);
						if (!data.FORWARD) {
							return false;
						}
						setTimeout(function () {
							$.openModal({
								remote: $.service(data.FORWARD.controller, data.FORWARD.action, data.FORWARD),
								open: function () {
									setTimeout(function () {
										$.closeModal({});
									}, 5000);
								}
							});
						}, 0);
					}
				});
			}
		}, settings.href);
		handler.apply(element, arguments);
	} else {
		return false;
	}
}

/**
 * Similar to above function but used when only a server action is needed followed by a feedback modal popup
 *
 * @param element
 * @param firstSvc
 * @param secondSelector
 * @param secondFn
 */

function actionModal(element, firstSvc, secondSelector, secondFn) {
	var settings, params;
	settings = getHrefAndParams(element, firstSvc);
	$.openModal({
		remote: settings.href,
		open: function () {
			if ($('div.modal-body div').hasClass('alert-success')) {
				$(secondSelector)[secondFn](settings.params);
				setTimeout(function () {
					$.closeModal({});
				}, 5000);
			}
		}
	});
}

/**
 * Function to refresh table rows using ajax. Used as a secondFn in the above onclick functions
 * @param params
 */
$.fn.refreshTableRows = function (params) {
	var id = this.selector;
	if ($(this).hasClass('tablesorter')) {
		$(this).trigger('update');
	} else {
		$.ajax({
			url: location,
			dataType: 'html',
			data: $(params).serialize() + 'tsAjax=y',
			success: function (data) {
				var parsedpage = $.parseHTML(data),
					tbody = $(parsedpage).find(id + ' tbody');
				$(id + ' tbody').html(tbody.html());
				$(id + ' input[type="checkbox"]').prop('checked', false);
			}
		});
	}
};

/**
 * Utility function used to generate the href and parameters for functions above
 *
 * @param element		element oject		Onclick element
 * @param settings		plain onject		Onclick object parameter
 * @returns {*}
 */

function getHrefAndParams(element, settings) {
	var href, params;
	if ($.isPlainObject(settings)) {
		//if ajax service is defined within onclick function
		if (typeof settings.controller !== 'undefined' && typeof settings.action !== 'undefined') {
			//in case params are defined in within settings object
			if (typeof settings.params !== 'undefined') {
				params = settings.params;
			} else {
				$.each(settings, function (key, value) {
					//params may be elsewhere, e.g., in the closest form, or a custom data attribute in the element
					if ($.inArray(key, ['closest', 'data']) > -1) {
						params = $(element)[key](value);
						if (value === 'form') {
							params = $(params).serialize();
						}
					}
				});
			}
			href = $.service(settings.controller, settings.action, {params: params, modal: 1});
		//in case the service is fully defined in a custom data element (e.g., using the smarty service function
		} else if (typeof settings.data !== 'undefined') {
			href = $(element).data(settings.data);
			params = href.slice(href.indexOf('?') + 1);
		}
		return {'href': href, 'params': params};
	} else {
		return false;
	}
}