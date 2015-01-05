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
	var href, params;
	if ($.isPlainObject(firstSvc)) {
		if (typeof firstSvc.params !== 'undefined') {
			params = firstSvc.params;
		} else {
			$.each(firstSvc, function(key, value) {
				//more functions can be added to the array filter
				if ($.inArray(key, ['closest', 'data']) > -1) {
					params = $(element)[key](value);
				}
				if (value === 'form') {
					params = $(params).serialize();
				}
			});
			href = $.service(firstSvc.controller, firstSvc.action, {params: params, modal: 1});
		}
		var handler = $.clickModal({
			success: function (data) {
				$.closeModal({
					done: function () {
						$(secondSelector)[secondFn](params);
						if (! data.FORWARD) {
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
		}, href);
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
	if ($.isPlainObject(firstSvc)) {
		if (typeof firstSvc.params !== 'undefined') {
			params = firstSvc.params;
		} else {
			$.each(firstSvc, function(key, value) {
				//more functions can be added to the array filter
				if ($.inArray(key, ['closest', 'data']) > -1) {
					params = $(element)[key](value);
				}
				if (value === 'form') {
					params = $(params).serialize();
				}
			});
			href = $.service(firstSvc.controller, firstSvc.action, {params: params, modal: 1});
		}
		$.openModal({
			remote: $.service(firstSvc.controller, firstSvc.action, {
				params: params,
				modal: 1
			}),
			open: function (params) {
				if ($('div.modal-body div').hasClass('alert-success')) {
					$(secondSelector)[secondFn](params);
					setTimeout(function () {
						$.closeModal({});
					}, 5000);
				}
			}
		});
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
