// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-admin_modules.js 39475 2012-01-12 22:47:05Z sylvieg $

$(document).ready(function() {

	var tocDirty = false;

	$(window).bind("beforeunload", function( e ) {
		if (tocDirty) {
			var m = tr("You have unsaved changes to your structure, are you sure you want to leave the page without saving?");
			var e = e || window.event;
			if (e && !($.browser.safari || $.browser.webkit)) {
				e.returnValue = m;
			}
			return m;
		}
	});

	$(".admintoc:first").nestedSortable( {

		disableNesting: 'no-nest',
		forcePlaceholderSize: true,
		handle: 'div',
		helper: 'clone',
		items: 'li',
		//maxLevels: 3,
		opacity: .6,
		tabSize: 20,
		tolerance: 'pointer',
		toleranceElement: '> div',
		placeholder: "ui-state-highlight",

		stop: function (event, ui) {
			if ($(".save_structure a:visible").length === 0) {
				$(".save_structure a").show("fast").parent().show("fast");
				tocDirty = true;
			}
//			$(this).removeClass("ui-state-active");
		},
		start: function (event, ui) {
//			$(this).addClass("ui-state-active");
		},
		receive: function(event, ui) {

		}
	});

	$(".save_structure").click(function(){
		var $sortable = $(this).parent().find(".admintoc:first");
		var ary = $sortable.nestedSortable('toArray', {startDepthCount: 0, listType:"ol"});

		$sortable.modal(tr("Saving..."));

		$.getJSON($.service("wiki", "save_structure", {data: $.toJSON(ary)}), function (data) {
			$sortable.modal();
			if (data) {
				$(".save_structure").hide();
				tocDirty = false;
			}
		});
		return false;
	});

});
