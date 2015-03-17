// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// hook in timeline libs which need to be in the head - may have to alter headerlib to allow this to be done serverside.

(function() {
	Timeline_ajax_url = "vendor/simile_timeline/simile_timeline/timeline_ajax/simile-ajax-api.js";
	Timeline_urlPrefix = "vendor/simile_timeline/simile_timeline/timeline_js/";
	Timeline_parameters = "bundle=true";
	var head = document.getElementsByTagName("head")[0];
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.language = "JavaScript";
	script.src = "vendor/simile_timeline/simile_timeline/timeline_js/timeline-api.js?bundle=true;";
	head.appendChild(script);
})();

// globals to track initialisation mainly

var ttlTimelineReady = false, ttlInitCount = 0, ttlTimeline;

/***
 * Set up Simile Timeline widget
 *
 * @param elementId        id of div to contain timeline
 * @param dataSource
 * @param scale1        timescale of top band (hour, day, week, month, year, decade, century)
 * @param scale2        optional lower band scale
 * @param band2_height
 */

function ttlInit( elementId, dataSource, scale1, scale2, band2_height ) {
	if (!$("#" + elementId).length) {
		return;
	}

	ajaxLoadingShow(elementId);

	if (typeof scale2 === 'undefined') {
		scale2 = '';
	}
	if (typeof band2_height === 'undefined') {
		band2_height = 30;
	}
	// wait for Timeline to be loaded
	if (ttlInitCount < 30 && (
			typeof window.SimileAjax === "undefined" ||
			typeof window.SimileAjax.loaded === "undefined" ||
			typeof window.Timeline === "undefined" ||
			typeof window.Timeline.createBandInfo === "undefined" ||
			typeof window.Timeline.DateTime === "undefined" ||
			typeof window.Timeline.GregorianDateLabeller === "undefined" ||
			typeof window.Timeline.GregorianDateLabeller.monthNames === "undefined" ||
			typeof window.Timeline.GregorianDateLabeller.getMonthName === "undefined" )) {

		if (typeof window.Timeline !== "undefined" && typeof window.Timeline.DateTime === "undefined" && typeof window.SimileAjax.DateTime !== "undefined") {
			window.Timeline.DateTime = window.SimileAjax.DateTime;
		}
		window.setTimeout( function() { ttlInit( elementId, dataSource, scale1, scale2 ); }, 1000);
		ttlInitCount++;
		return;
	} else {
		ttlTimelineReady = true;
	}

	if (!ttlTimelineReady) {	// just seems to need a little bit longer...
		location.replace(location.href);	// at least 10 secs - reload
		return;
	}

	// timeline finally loaded(?)
	window.SimileAjax.History.enabled = false;

	var ttl_eventSource = new Timeline.DefaultEventSource();
	ttl_eventSource.loadJSON(dataSource, ".");	// The data

	var bandInfos = [
		window.Timeline.createBandInfo({
			width:		  scale2 === "" ? "100%" : (100 - band2_height) + "%",
			intervalUnit:   window.Timeline.DateTime[scale1.toUpperCase()],
			eventSource:	ttl_eventSource,
			intervalPixels: 100
		})
	];
	if (scale2) {
		bandInfos.push(
			window.Timeline.createBandInfo({
				width:          band2_height + "%",
				intervalUnit:   window.Timeline.DateTime[scale2.toUpperCase()],
				eventSource:	ttl_eventSource,
				intervalPixels: 200,
				layout:			"overview"
			})
		);
		bandInfos[1].syncWith = 0;
		bandInfos[1].highlight = true;
		//bandInfos[1].eventPainter.setLayout(bandInfos[0].eventPainter.getLayout());
	}
	try {
		ttlTimeline = window.Timeline.create(document.getElementById(elementId), bandInfos);
	} catch (e) {
		location.replace(location.href);
	}
	ajaxLoadingHide();
	ttlTimeline.layout(); // display the Timeline

}	// end ttlInit()

var ttlResizeTimerID = null;
$(window).resize( function () {
	if (ttlTimeline && ttlResizeTimerID == null) {
		ttlResizeTimerID = window.setTimeout(function() {
			ttlResizeTimerID = null;
			ttlTimeline.layout();
		}, 500);
	}
});

