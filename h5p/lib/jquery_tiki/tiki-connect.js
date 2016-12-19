/**
 * JS helpers for Tiki Connect - used on admin/connect so far
 *
 * $Id$
 */


$("#connect_list_btn").click(function() {
	if (jqueryTiki.ui) {
		var $d = $("<div id='connect_list__dialog' style='display:none'></div>")
				.appendTo(document.body);

		var spinner = $(this).tikiModal(tr(" "));

		var w = 600;
		var h = 400;
		if ($(document.body).width() < w) {
			w = $(document.body).width() * 0.8;
		}
		if ($(document.body).height() < h) {
			h = $(document.body).height() * 0.8;
		}

		$d.dialog({
			width: w,
			height: h,
			title: tr("Tiki Connect Data Preview"),
			modal: false,
			buttons: {
				Ok: function() {
					$(this).dialog("close");
				}
			},
			create: function(event, ui) {
				$.getJSON($.service('connect', 'list'), function (data, status) {

					if (data) {
						$d.append($("<h3>" + tr("Tiki Version") + "</h3>")).append($("<p>" + data.version + "</p>"));

						var formatList = function(inArray) {
							var $dl = $("<dl />");
							for (var key in inArray) {
								if (inArray.hasOwnProperty(key)) {
									$dl.append($("<dt>" + key + "</dt><dd>" + inArray[key] + "</dd>"));
								}
							}
							return $dl;
						};

						var $din = $("<div />");
						var $tabs = $("<ul />").appendTo($din);		// list for tabs

						if (data.prefs) {
							$tabs.append("<li><a href='#ctab-m'>" + tr("Prefs") + "</a></li>");
							$("<div id='ctab-m' />").append(formatList(data.prefs)).appendTo($din);
						}
						if (data.site) {
							$tabs.append("<li><a href='#ctab-p'>" + tr("Site Info") + "</a></li>");
							$("<div id='ctab-p' />").append(formatList(data.site)).appendTo($din);
						}
						if (data.server) {
							$tabs.append("<li><a href='#ctab-s'>" + tr("Server") + "</a></li>");
							$("<div id='ctab-s' />").append(formatList(data.server)).appendTo($din);
						}
						if (data.tables) {
							$tabs.append("<li><a href='#ctab-d'>" + tr("Database") + "</a></li>");
							$("<div id='ctab-d' />").append(formatList(data.tables)).appendTo($din);
						}
						if (data.votes) {
							$tabs.append("<li><a href='#ctab-v'>" + tr("Votes") + "</a></li>");
							$("<div id='ctab-v' />").append(formatList(data.votes)).appendTo($din);
						}

						$din.appendTo($d);
						$din.tabs();
					}	// error TODO
					ajaxLoadingHide();
				}).fail(function (xhr) {
					$('html, body').animate({scrollTop: $("#tikifeedback").offset().top}, 500);
					$d.dialog("close");
					ajaxLoadingHide();
				});
			},
			open: function () {
				ajaxLoadingShow($d);
			},
			close: function () {
				spinner.tikiModal();
			}
		});
	}
	return false;
});

$("#connect_send_btn").click(function() {

	var spinner = $(this).tikiModal(" ");

	$.getJSON($.service('connect', 'send'), function (data, status) {
		if (data && data.message) {
			if (data.status === 'pending') {
				var cap = prompt(data.message, "");
				if (cap) {
					$.getJSON($.service('connect', 'send'), {
						guid: data.guid,
						captcha: cap
					}, function (data, status) {
						alert(data.message);
						if (data.status === "confirmed") {
							$("input[name=connect_guid]").val(data.guid);	// already set server-side but update form to match
						}
					}).fail(function (xhr) {
						$('html, body').animate({scrollTop: $("#tikifeedback").offset().top}, 500);
					});
				} else {
					$.getJSON($.service('connect', 'cancel'), {
						guid: data.guid
					});
				}
			} else {
				alert(data.message);
			}
		} else {
			alert(tr("The server did not reply"));
		}
	}).fail(function (xhr) {
		$('html, body').animate({ scrollTop: $("#tikifeedback").offset().top }, 500);
	}).always(function () {
		spinner.tikiModal();
		return false;
	});
	return false;
});

$("#connect_feedback_cbx").click(function(){
	var spinner = $(this).parent().tikiModal(" ");
	if ($("#connect_feedback_cbx:checked").length > 0) {
		$(".adminoptionbox .tikihelp, .adminoptionbox .icon:not(.connectVoter)").eachAsync({
			bulk: 0,	// needs bulk:0 to smooth out the animation it seems
			loop: function() { $(this).hide(); }
		});
		$(".connectVoter").eachAsync({
			bulk: 0,
			loop: function() { $(this).show(); },
			end: function() { spinner.tikiModal(); }
		});
		setCookie("show_tiki_connect", 1, "", "session");
	} else {
		$(".adminoptionbox .tikihelp, .adminoptionbox .icon:not(.connectVoter)").eachAsync({
			bulk: 0,
			loop: function() { $(this).show(); }
		});
		$(".connectVoter").eachAsync({
			bulk: 0,
			loop: function() {  $(this).hide(); },
			end: function() { spinner.tikiModal(); }
		});
		deleteCookie("show_tiki_connect");
	}
});

if (getCookie("show_tiki_connect")) {
	$("#connect_feedback_cbx").click();
}

var connectVote = function(pref, vote, el) {
	var spinner = $(el).parent().tikiModal(" ");
	if ($(el).data("newVote")) {
		vote = $(el).data("newVote");
	}
	$.getJSON($.service("connect", "vote", {"pref": pref, "vote": vote }), function (json) {
		if (json && json.newVote ) {
			$(el).data("newVote", json.newVote).find("span").setIcon(json.newVote);
		}
		spinner.tikiModal();
	}).error(function (){
		alert(tr("Tiki Connect is not set up properly. Please visit admin/connect/settings to configure the feature."))
	});
};
