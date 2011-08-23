/**
 * JS helpers for Tiki Connect - used on admin/connect so far
 * 
 * $Id$
 */


$("#connect_list_btn a").click(function(){
	if (jqueryTiki.ui) {
		var $d = $("<div id='connect_list__dialog' style='display:none'></div>")
			.appendTo(document.body);

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
				modal: true,
				buttons: {
					Ok: function() {
						$( this ).dialog( "close" );
					}
				},
				create: function(event, ui) {
					$.getJSON('tiki-ajax_services.php', {
							controller: 'connect',
							action: 'list'
						}, function (data, status) {

							if (data) {
								$d.append($("<h3>" + tr("Tiki Version") + "</h3>")).append($("<p>" + data.version + "</p>"));

								var formatList = function( inArray ) {
									var $dl = $("<dl />");
									for (var key in inArray) {
										$dl.append($("<dt>" + key + "</dt><dd>" + inArray[key] + "</dd>"));
									}
									return $dl;
								}

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

								$din.appendTo($d);
								$din.tabs();
							}	// error TODO
							ajaxLoadingHide();
					});
				},
				open: function (){
					ajaxLoadingShow($d);
				}
			});
	}
	return false;
});

$("#connect_send_btn a").click(function(){

	$.getJSON('tiki-ajax_services.php', {
			controller: 'connect',
			action: 'send'
		}, function (data, status) {
			if (data) {
				if (data.status === 'pending' && confirm(data.message)) {
					$.getJSON('tiki-ajax_services.php', {
							controller: 'connect',
							action: 'send',
							guid: data.guid
						}, function (data, status) {
							alert(data.message);
					});
				} else {
					alert(data.message);
				}
			} else {
				alert(tr("The server did not reply"));
			}
	});
	return false;
});