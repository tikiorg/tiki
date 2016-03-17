<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  die;
}

function wikiplugin_pagetabs_help()
{
        return "Cutom Tabs Engine";
}

function wikiplugin_pagetabs_info()
{
	return array(
		'name' => tra('Page Tabs'),
		'documentation' => tra('PluginPageTabs'),
		'description' => tra('Display content of wiki pages in a set of tabs'),
		'prefs' => array( 'wikiplugin_pagetabs' ),
		'iconname' => 'copy',
		'introduced' => 9,
		'body' => NULL,
		'params' => array(
			'pages' => array(
				'required' => false,
				'name' => tra('Wiki page names'),
				'description' => tr('The wiki pages you would like to use in this plugin, optional, separate with
					pipe %0|%1. Or a table with the class of "pagetabs" on the main page. On child pages use as a way
					to redirect to the parent.', '<code>', '</code>'),
				'since' => '9.0',
				'default' => '',
				'separator' => '|',
				'filter' => 'pagename',
				'profile_reference' => 'wiki_page',
			),
		),
	);
}

function wikiplugin_pagetabs($data, $params)
{
	global $user;
	$headerlib = TikiLib::lib('header');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');

	static $pagetabsindex = 0;
	++$pagetabsindex;
	extract($params, EXTR_SKIP);

	$pages = json_encode($pages);

	$pageTabs = true;

	foreach ($tikilib->get_user_groups($user) as $group) {
		if ($group == "NoPageTabs") {
			$pageTabs = false;
		}
	}



	if ($pageTabs == true) {
		$headerlib
			->add_jq_onready(
				'
				var tabPages = '.$pages.';

				var tabsTable = $("table.pagetabs")
					.hide();

				var tabParent = $("<div id=\'TabContainer\' />")
					.insertAfter("#pagetabs'.$pagetabsindex.'");

				var tabMenu = $("<ul id=\'tabMenu\' class=\'tabs\' />")
					.appendTo(tabParent);

				if (tabPages) {
					$.each(tabPages, function(i) {
						var a = $("<a href=\'tiki-index_raw.php?full&page=" + tabPages[i] + "\' />")
							.text(tabPages[i]);

						$("<li />")
								.append(a)
								.appendTo(tabMenu);
					});
				} else {
					tabsTable
						.find("a").each(function() {
							var a = $(this).clone();

							a.attr("href", a.attr("href").replace(/tiki-index.php/g, "tiki-index_raw.php"));

							$("<li />")
								.append(a)
								.appendTo(tabMenu);


						});
				}
				$("<img id=\'tabSpinner\' src=\'img/spinner.gif\' style=\'position: absolute;z-index: 999999999\' />")
					.insertBefore(tabParent)
					.hide();

				tabParent
					.tabs({
						load: function(e, ui) {
							$("#tabSpinner").fadeOut();
							//(url|#anchor1,anchor2|text)
							$(ui.panel)
								.find(".wikitext a")
								.each(function() {
									$(this).attr("href", ($(this).attr("href") + "").replace("_raw", ""));
								})
								.unbind("click")
								.click(function() {
									var pageAttr = $(this).attr("href").split("=");
									if (pageAttr.length < 2) return true;

									var page = pageAttr[1].split("#");

									if (page.length < 2) return true;

									var otherA = tabMenu.find("[href$=\'" + page[0] + "\']");

									if (otherA.length < 1) return true;

									otherA.click();

									tabParent.one( "tabsload", function() {
										$("#" + page[1]).ready(function() {
											var aTop = $("#" + page[1]).offset().top;

											$("html, body").scrollTop(aTop);
										});
									});

									return false;
								});
							$("#top").show();
						},
						select: function() {
							$("#tabSpinner").fadeIn();
						}
					});

				tabParent.find("ul:first li").addClass("tabmark");

				var pageAttr = (document.location + "").split("#");

				if (pageAttr.length > 1) {
					var initTab = pageAttr[1].split("_");
					var initA = initTab[1];
					initTab = initTab[0];

					tabMenu.find("[title=\'" + initTab + "\']").click();

					if (!initA) return;

					tabParent.one("tabsload", function() {
						$("#" + initA).ready(function() {
							var aTop = $("#" + initA).offset().top;
							$("html, body").scrollTop(aTop);
						});
					});
				}
		'
			)
		->add_css(
			'
			#tabMenu {
				width: 100% ! important;
			}
			#top {

			}
			.pagetabs {
				display: none;
			}
			.ui-tabs-panel {
				padding: 0px ! important;
			}
		'
		);
	}

	return "<span id='pagetabs$pagetabsindex' />";
}
