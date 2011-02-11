/* $Id$
 * Glue for Tiki and jquery.mobile.js
 */


$(document).bind("mobileinit", function() {
	// override jquery.mobile settings here - e.g.
	if (typeof mobile_ajaxEnabled !== "undefined") {
		$.mobile.ajaxEnabled = mobile_ajaxEnabled;
	}
});
