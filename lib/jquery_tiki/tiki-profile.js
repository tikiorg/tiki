// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
(function ($) {

	$ = $.extend($, {
		profilesRefreshCache: function ( baseURI, entry ) {
			var datespan = $('#profile-date-' + entry);

			if ($('profile-status-' + entry + ' > span.icon-status-pending').is(':visible')) {
				return;
			}

			$('#profile-status-' + entry + ' > span.icon-status-pending').show();
			$('#profile-status-' + entry + ' > span.icon-status-open').hide();
			$('#profile-status-' + entry + ' > span.icon-status-closed').hide();

			$.post(baseURI + '&refresh=' + escape(entry), function (result) {
				if (result.slice(0,1) !== '<') {
					var data = eval('(' + result + ')');
					$.each(['open', 'pending', 'closed'], function (key, value) {
						if (value == data.status) {
							$('#profile-status-' + entry + ' > span.icon-status-' + value).show();
						} else {
							$('#profile-status-' + entry + ' > span.icon-status-' + value).hide();
						}
					});
					datespan.html(data.lastupdate);
				} else {
					feedback(tr('Error loading page'), 'error');
				}
			})
			.fail(function () {
				feedback(tr('Error loading page'), 'error');
			});
		},
		previewProfile: function (baseURI, domain, profile) {
			$('#preview-changes-' + profile).css('display', 'block');

			var data = {
				pd: escape(domain),
				pp: escape(profile),
				install: true,
				dryrun: true,
				ajax: true
			};

			$.post(baseURI, data, function (result) {
				var header = $('<div class="modal-header"></div>');
				var footer = $('<div class="modal-footer"></div>');
				var content = $('<div class="modal-body"></div>');
				header.html('<button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">' + tr('Profile Changes') + '</h4>');
				footer.html('<button type="button" class="btn btn-default" data-dismiss="modal">' + tr('Close') + '</button>');

				if (result.trim() != '') {
					content.html(result);
				} else {
					var info = $('<div class="alert alert-info alert-dismissable "></div>');
					var infoTitle = $('<h4><span class="icon icon-information fa fa-info-circle fa-fw "></span>&nbsp;<span class="rboxtitle">' + tr('Information') + '</span></h4>');
					var infoContent = $('<div class="rboxcontent" style="display: inline"><p>' + tr('Profile without changes') + '</p></div>');
					info.append(infoTitle);
					info.append(infoContent);
					content.html(info);
				}

				$('#bootstrap-modal-2 .modal-content').empty();
				$('#bootstrap-modal-2 .modal-content').append(header);
				$('#bootstrap-modal-2 .modal-content').append(content);
				$('#bootstrap-modal-2 .modal-content').append(footer);
				$('#bootstrap-modal-2').modal();
			})
			.fail(function () {
				feedback(tr('Error loading profile'), 'error');
			})
			.done(function () {
				$('#preview-changes-' + profile).css('display', 'none');
			});
		},
		profilesShowDetails: function ( baseURI, id, domain, profile ) {
			var nid = id + "-sub";
			var infoId = id + "-info";
			var prev = $('#' + id);
			var obj = $('#' + infoId);

			if ( obj.length > 0 ) {
				obj.remove();
				return;
			}

			var infoOb = $('<span></span>')
				.css('font-style', 'italic')
				.css('margin-left', '15px');
			prev.children('td').first().append(infoOb);
			infoOb.html("Loading profile...");

			$.post(baseURI + '&getinfo&pd=' + escape(domain) + '&pp=' + escape(profile), function (result) {
				var data = eval("(" + result + ")");
				var row = $('<tr id=' + infoId + '></tr>');
				row.css('background-color', '#FFFFFF');
				var cell = $('<td colspan="3"></td>');

				if ( data.installable || data.already ) {
					var pStep = $('<p></p>');
					pStep.css('font-weight', 'bold');

					if ( data.installable ) {
						pStep.html('Click on Apply Now to apply Profile');
					} else if ( data.already ) {
						pStep.html('A version of this profile is already applied.');
					}

					var form = $('<form method="post" class="form-horizontal" action="' + document.location.href + '"></form>');
					var span = $('<span id="preview-changes-' + profile + '" '
						+ 'style="font-style: italic; padding: 10px; display: none;">'
						+ tr('Loading profile changes...') + '</span>');
					var submit = $('<input type="submit">');
					var submitPreview = $('<input type="button" name="profile_preview" value="' + tr('Preview Changes') + '" '
						+ 'onclick="' + "$.previewProfile('" + baseURI + "', '" + domain + "', '" + profile + "');" + '"'
						+ 'class="btn btn-primary" style="margin-left: 20px;">');
					cell.html(form);
					$(row).append(cell);

					var rowNum = 0;
					for (i in data.userInput) {
						if ( typeof(data.userInput[i]) != 'string' ) {
							continue;
						}

						var iRow = $('<div class="form-group"></div>');
						var iLabel = $('<label class="col-lg-5 col-sm-5 control-label">' + i + '</label>');
						var iDivField = $('<div class="col-lg-5"></div>');
						var iField = $('<input type="text" class="form-control" name="' + i + '" value="' + data.userInput[i] + '">');
						iDivField.html(iField);
						iRow.append(iLabel);
						iRow.append(iDivField);
						form.append(iRow);
						rowNum++;
					}

					if ( data.installable ) {
						submit.attr('name', 'install');
						submit.attr('value', 'Apply Now');
						submit.attr('class', 'btn btn-primary');
						form.attr('onsubmit', 'return confirm("Are you sure you want to apply the profile ' + profile + '?");');
					} else if ( data.already ) {
						submit.attr('name', 'forget');
						submit.attr('value', 'Forget and Re-apply');
						submit.attr('class', 'btn btn-primary');
						form.attr("onsubmit", 'return confirm("Are you sure you want to re-apply the profile ' + profile + '?");');
					}

					var divGroupButton = $('<div class="form-group"></div>');
					var divButton = $('<div class="col-lg-5"></div>');

					if (rowNum > 0) {
						divButton.addClass('col-lg-offset-5');
					}

					divButton.append(submit)
					divButton.append(submitPreview)
					divButton.append(span);
					divButton.append(pStep);
					divGroupButton.append(divButton);
					form.append(divGroupButton);

					var pd = $('<input type="hidden">');
					pd.attr('name', 'pd');
					pd.attr('value', domain);
					form.append(pd);

					var pp = $('<input type="hidden">');
					pp.attr('name', 'pp');
					pp.attr('value', profile);
					form.append(pp);
				} else if ( data.error ) {
					var p = $('<p class="text-danger"></p>');
					p.css('font-weight', 'bold');
					p.html(tr("An error occurred during the profile validation. This profile cannot be applied.<br>Message: ") + data.error);
					cell.html(p);
					$(row).append(cell);
				} else {
					var p = $('<p class="text-danger"></p>');
					p.css('font-weight', 'bold');
					p.html("An error occurred during the profile validation. This profile cannot be applied.");
					cell.html(p);
					$(row).append(cell);
				}

				if ( data.dependencies.length > 1 ) {
					var ul = $('<ul></ul>');

					for (k in data.dependencies) {
						if ( typeof(data.dependencies[k]) != 'string') {
							continue;
						}

						var li = $('<li></li>');
						var a = $('<a href="' + data.dependencies[k] + '">' + data.dependencies[k] + '</a>');

						li.append(a);
						ul.append(li);
					}

					var p = $('<p>These profiles will be applied:</p>');
					cell.append(p);
					cell.append(ul);
				}

				var body = $('<div></div>');
				body.css('height', '200px');
				body.css('overflow', 'auto');
				body.css('border', '2px solid black');
				body.css('padding', '8px');
				body.css('resize', 'both');
				body.css('margin-top', '20px');
				body.html(data.content);

				cell.append(body);
				$(row).insertAfter(prev);

				if (data.feedback.length) {
					alert("Profile issues: \n" + data.feedback);
				}
			})
			.fail(function () {
				feedback(tr('Error loading profile'), 'error');
			})
			.done(function () {
				infoOb.html('');
			});
		}
	});

	$(document).on('click', '#select_all_prefs_to_export', function () {
		$("input[name^=prefs_to_export]:visible,input[name^=modules_to_export]:visible").click();
	});

	$(document).on('change', '#export_type', function () {
		$(".profile_export_list").hide();
		$("#" + $(this).val() + "_to_export_list").show();
	});

	$(document).on('click', '#export_show_added', function () {
		$(this)[0].form.submit();
	});

	$(document).on('change', '#repository, #categories', function () {
		if ($(this).val()) {
			$(".quickmode_notes").hide(400);
		} else {
			$(".quickmode_notes").show(400);
		}
	});

	if ($("#profile-0").length > 0) {
		$(".quickmode_notes").hide();
		$(window).scrollTop($("#step2").offset().top);
	} else {
		$(".quickmode_notes").show();
	}
}(jQuery));