/* $Id$
 * Glue for Tiki and jquery.mobile.js
 */


$(document).bind("mobileinit", function() {

	// sadly (Tiki 9 jqm 1.1 RC) most of the time we get stuch it's due to ajax loading, so disable globally again for noe :(
	$.mobile.ajaxEnabled = false;

	$('[data-role=page]').live('pageinit', function(e) {
		$("#fixedwidth").show();
		ajaxLoadingHide();
		if ($("#js_ajax_include").length) {
			$.getScript( $("#js_ajax_include").text(), function () {
				// callback after js loaded
			} );
		}
	});
});
