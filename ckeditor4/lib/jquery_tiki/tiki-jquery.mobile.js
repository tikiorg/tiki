/* $Id$
 * Glue for Tiki and jquery.mobile.js
 */

var jsAjaxIncludes = [];

$(document).bind("mobileinit", function() {

	// sadly (Tiki 9 jqm 1.1 RC) most of the time we get stuch it's due to ajax loading, so disable globally again for noe :(
	$.mobile.ajaxEnabled = false;

	$('[data-role=page]').on('pageinit', function(e) {
		$("#fixedwidth").show();
		ajaxLoadingHide();

		var jsFile = $("#js_ajax_include").text();

		if ($.inArray(jsFile, jsAjaxIncludes) === -1) {
			jsAjaxIncludes.push(jsFile);
			$.getScript( jsAjaxIncludes, function () {
				// callback after js loaded
			} );
		}

	}).on("pageshow", function(e) {

		var currentPage = $(e.target), options = {};
		var $images = $("a[rel*=\'box\'][rel*=\'type=img\'], a[rel*=\'box\'][rel!=\'type=\']", e.target);
		if ($images.length) {
			$images.photoSwipe(options,  currentPage.attr("id"));
		}
		return true;

	}).on("pagehide", function(e) {

		if (typeof PhotoSwipe !== "undefined") {
			var currentPage = $(e.target), photoSwipeInstance = PhotoSwipe.getInstance(currentPage.attr("id"));

			if (typeof photoSwipeInstance != "undefined" && photoSwipeInstance != null) {
				PhotoSwipe.detatch(photoSwipeInstance);
			}
		}
		return true;
	});
});
