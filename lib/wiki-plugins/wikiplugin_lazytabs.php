<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  die;
}

function wikiplugin_lazytabs_help() 
{
        return "Cutom Tabs Engine";
}

function wikiplugin_lazytabs_info() 
{
	return array(
		'name' => tra('Lazy Tabs'),
		'documentation' => tra('PluginLazyTabs'),			
		'description' => tra('Display page content in a set of tabs'),
		'prefs' => array( 'wikiplugin_tabs' ),
		'body' => NULL,
		'params' => array(
		),
	);
}

function wikiplugin_lazytabs($data, $params) 
{
	global $tikilib, $smarty, $headerlib, $user;
	$lazyTabs = true;
	
	foreach ($tikilib->get_user_groups($user) as $group) {
		if ($group == "NoLazyTabs") {
			$lazyTabs = false;
		}
	}
	
	if ($lazyTabs == true) {
		$headerlib->add_jq_onready(
						'var lazyTabsTable = $("table.lazytabs")
				.hide();

			var tabParent = $("<div id=\'lazyTabContainer\' />")
				.insertAfter(lazyTabsTable);
				
			var tabMenu = $("<ul id=\'tabMenu\' class=\'tabs\' />")
				.appendTo(tabParent);
			
			lazyTabsTable
				.find("a").each(function() {
					var a = $(this).clone();
					
					a.attr("href", a.attr("href").replace(/tiki-index.php/g, "tiki-index_raw.php"));
					
					$("<li />")
						.append(a)
						.appendTo(tabMenu);
						
					
				});
			
			$("<img id=\'lazyTabSpinner\' src=\'img/spinner.gif\' style=\'position: absolute;z-index: 999999999\' />")
				.insertBefore(tabParent)
				.hide();
			
			tabParent
				.tabs({
					load: function(e, ui) {
						$("#lazyTabSpinner").fadeOut();
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
						$("#lazyTabSpinner").fadeIn();
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
			}'
		);
		
		return "<style>
			#tabMenu {
				width: 100% ! important;
			}
			#top {
				display: none;
			}
			.lazytabs {
				display: none;
			}
			.ui-tabs-panel {
				padding: 0px ! important;
			}
		</style>
		";
	}
}
