/** $Id$
 *
 * To facilitate use of ajax services, allowing for update of page information without refreshing and
 * providing modal popup feedback (e.g., a warning notice that no items were selected)
 */

/**
 * For use as an onclick function. First service specified should return a modal form.
 * After the form is submitted, a jquery function may be specified to update the page.
 * 	or a modal pops up (e.g.,
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
						if (!data.FORWARD && secondSelector && secondFn) {
							$(secondSelector)[secondFn](settings.params);
							return false;
						} else if (data.FORWARD){
							$.openModal({
								remote: $.service(data.FORWARD.controller, data.FORWARD.action, data.FORWARD)
							});
						} else {
							location.reload();
							return false;
						}
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
 * For use as an onclick function when these steps are desired:
 * 	- First, a server action is performed (no form beforehand)
 * 	- Then, optionally EITHER a server update action is performed or a modal pops up
 * 		(e.g., a warning notice that no items were selected)
 *
 * @param element
 * @param firstSvc
 * @param secondSelector
 * @param secondFn
 */

function actionModal(element, firstSvc, secondSelector, secondFn) {
	var settings, params;
	settings = getHrefAndParams(element, firstSvc);
	if (settings) {
		$.ajax({
			dataType: 'json',
			url: settings.href,
			success: function (data) {
				if (!data.FORWARD && secondSelector && secondFn) {
					$(secondSelector)[secondFn](settings.params);
					return false;
				} else if (data.FORWARD){
					$.openModal({
						remote: $.service(data.FORWARD.controller, data.FORWARD.action, data.FORWARD)
					});
				} else {
					return false;
				}
			}
		});
	} else {
		return false;
	}
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
			data: params + 'tsAjax=y',
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
			href = $.service(settings.controller, settings.action, {params: params});
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