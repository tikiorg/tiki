/* $Id$
 * Glue for Tiki and jquery.mobile.js
 */


$(document).bind("mobileinit", function() {
	// override jquery.mobile settings here - e.g.
	if (typeof mobile_ajaxEnabled !== "undefined") {
		$.mobile.ajaxEnabled = mobile_ajaxEnabled;
	}

	$('[data-role=page]').live('pagecreate', function(event) {
		$("#fixedwidth").show();
		ajaxLoadingHide();
		if ($("#js_ajax_include").length) {
			$.getScript( $("#js_ajax_include").text(), function () {
				// callback after js loaded
			} );
		}
	});
});
