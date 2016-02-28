// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$(document).ready(function() {

	var listDirty = false;

	var setupList = function() {
		$(".surveyquestions tbody").sortable({

			opacity:.6,

			stop:function (event, ui) {
				if ($(".save_list:visible").length === 0) {
					$(".save_list").show("fast").parent().show("fast");
					listDirty = true;
				}
			}

		}).disableSelection();
	};

	$(window).bind("beforeunload", function( e ) {
		if (listDirty) {
			var m = tr("You have unsaved changes to your survey, are you sure you want to leave the page without saving?");
			e = e || window.event;
			if (e && !($.browser.safari || $.browser.webkit)) {
				e.returnValue = m;
			}
			return m;
		}
	});

	setupList();

	$(".save_list").click(function(){

		var $ids = $(this).parent().find(".surveyquestions td.id");
		$(".surveyquestions").tikiModal(tr("Saving..."));

		var data = $ids.map(function () {
			return $(this).text();
		}).get().join();

		listDirty = false;
		$("input[name=questionIds]", "#reorderForm").val(data);
		$("#reorderForm").submit();

		return false;
	});

});

