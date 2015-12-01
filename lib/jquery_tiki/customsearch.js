/* (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * $Id$
 *
 * Custom search js helper function, mainly to maintain the search state in the url hash
 *
 * N.B. Onlty works for a single customsearch instance per page with the id #customsearch_0
 */


(function ($) {

	var $csForm, $formInputs;

	$(document).on("formSearchReady", function () {

		$csForm = $('#customsearch_0');
		$formInputs = $("input[type!=Submit]:not(.ignore), select:not(.ignore)", $csForm);

		customsearch_0 = customsearch_0 ? customsearch_0 : {};

		$csForm.unbind("submit").submit(function () {

			var maxRecords = $(".max-records", $csForm).val();
			if (maxRecords) {
				customsearch_0["max-records"] = maxRecords;
			}

			customsearch_0.load();
			return false;
		});

		$(".sort-by", $csForm).change(function () {
			customsearch_0.sort_mode = $(this).val();
			$csForm.submit();
			return false;
		});

		$(".max-records", $csForm).change(function () {
			$csForm.submit();
		});

		getHash();
	});


	$(document).bind("pageSearchReady", function () {

		var $csResults = $("#customsearch_0_results");

		$('.facets ul').registerFacet();

		// sticky facets
		var $facets = $(".facets"),
			pos = $facets.offset();

		if (pos != undefined) {
			var width = $facets.css("width"),
				topOffset = 70,
				footer = $("footer"),
				footPos = footer.offset().top,
				facetHeight = $facets.height();

			$window.scroll(function () {
				var windowpos = $window.scrollTop();
				var top = 60;
				if (windowpos > pos.top - topOffset) {
					if (footPos - windowpos - topOffset < facetHeight) {
						top = footPos - windowpos - facetHeight - 10;
					} else {
						top = 60;
					}
					$facets.css({
						position: "fixed",
						top: top + "px",
						width: width
					});
					//console.log(footPos - windowpos);
				} else {
					$facets.css({
						position: "inherit",
						top: "auto",
						width: width
					});
				}

			});
		}

		// update the url hash with the current returned search results
		setHash();

		return true;
	});

	function setHash() {
		var ser = "";

		$formInputs.each(function () {
			if ($(this).is("input[type=checkbox]")) {
				if ($(this).prop("checked") != $(this.outerHTML).val()) {	// only add to hash if set differently to initial value
					var state = $(this).prop("checked") ? 1 : 0;
					if ($(this).attr("id")) {
						ser += $(this).attr("id") + "=" + state + "&";
					} else {
						ser += "." + $(this).prop("className") + "=" + state + "&";
					}
				}
			} else if ($(this).val() && $(this).val() != $(this.outerHTML).val()) {	// if different
				if ($(this).attr("id")) {
					ser += $(this).attr("id") + "=" + encodeURIComponent($(this).val()) + "&";
				} else {
					ser += "." + $(this).prop("className") + "=" + encodeURIComponent($(this).val()) + "&";
				}
			}
		});

		var $pagenums = $(".active", ".pagination:first");
		var val = $pagenums.text().match(/\d+/);
		val = val && val.length ? val[0] : 0;
		if (!val) {
			val = $(".pagenumstep").val();
		}

		if (val > 1) {        // offset
			var max = $(".max-records", $csForm).val();
			if (!max) {
				max = customsearch_0.max-records;
			}
			ser += "offset=" + (val - 1) * max;
		}

		window.location.hash = ser.replace(/&$/, "");
	}

	function getHash() {

		var params = {};
		var hashKey, e, a = /\+/g, // Regex for replacing addition symbol with a space
			r = /([^&;=]+)=?([^&;]*)/g,
			d = function (s) {
				return decodeURIComponent(s.replace(a, " "));
			},
			q = window.location.hash.substring(1);

		if (location.hash) {
			// from http://stackoverflow.com/questions/4197591/parsing-url-hash-fragment-identifier-with-javascript - thanks :)
			while (e = r.exec(q)) {
				params[d(e[1])] = d(e[2]);
			}

			var triggerIt = false, $el, selector;
			customsearch_0.quiet = true;

			for (hashKey in params) {
				if (params.hasOwnProperty(hashKey) && hashKey.length > 1) {
					if (hashKey.indexOf(".") === 0) {
						selector = hashKey + ":first";
					} else {
						selector = "#" + hashKey;
					}
					try {
						$el = $(selector, $csForm);
						if ($(selector + "[type=checkbox]").length) {
							triggerIt = true;
							$el.prop("checked", hashParams[hashKey] == "1").trigger('change');
						} else if ($el.length) {
							triggerIt = true;
							$el.val(params[hashKey]).trigger('change').trigger("chosen:updated");
						} else if (hashKey === "offset") {
							triggerIt = true;
							customsearch_0.offset = params[hashKey];
						}
						if (hashKey == "supplier") {
							setSupplier(params[hashKey]);

						}
					} catch (e) {
						// just ignore malformed selectors
					}
				}
			}
			customsearch_0.quiet = false;
		}
		if (window.location.search) {

			q = window.location.search.substring(1);

			while (e = r.exec(q)) {
				params[d(e[1])] = d(e[2]);
			}

			customsearch_0.offset = 0;

			if (params.q && params.q != "") {
				$("#search", $csForm).val(params.q).trigger("change");
				triggerIt = true;
			} else {
				$("#search", $csForm).val("").trigger("change");
				triggerIt = true;
			}
		}
		if (triggerIt) {
			$csForm.submit();
		}
	}

}(jQuery));
