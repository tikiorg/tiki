<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_TextBacklink extends Feed_Abstract
{
	var $type = "feed_textbacklink";
	
	public function replace()
	{
		global $tikilib, $feedItem, $caching;
		
		$this->delete();
		$site = $tikilib->tikiUrl();
		
		$caching = true; //this variable is used to block recursive parse_data below
		
		foreach (TikiLib::lib("wiki")->get_pages_contains("{textlink") as $pagesInfo) {
			foreach ($pagesInfo as $pageInfo) {
				$feedItem = Feed_Html_Item::simple(
								array(
									"origin" 		=> $site,
									"name" 			=> $pageInfo['pageName'],
									"title" 		=> $pageInfo['pageName'],
									"description" 	=> $description,
									"date" 			=> (int)$pageInfo['lastModif'],
									"author" 		=> $pageInfo['user'],
									"hits"			=> $pageInfo['hits'],
									"unusual"		=> "",
									"importance" 	=> $pageInfo['pageRank'],
									"keywords"		=> $pageInfo['keywords'],
									"href"			=> $tikilib->tikiUrl() . "tiki-pagehistory.php?" .
											"page=" . $pageInfo['pageName'] .'&'.
											"preview_date=" . (int)$pageInfo['lastModif'] . "&" .
											"nohistory"
								)
				);
				
				TikiLib::lib("parser")->parse_data($pageInfo['data']);
				
				unset($feedItem);
			}
		}
		
		$caching = false;
	}

	function wikiView($args)
	{
		global $headerlib, $_REQUEST;
		$_REQUEST['tbp_serial'] = (isset($_REQUEST['tbp_serial']) ? htmlspecialchars($_REQUEST['tbp_serial']) : "");
	
		$headerlib
				->add_jsfile("lib/rangy/rangy-core.js")
				->add_jsfile("lib/rangy/rangy-cssclassapplier.js")
				->add_jsfile("lib/rangy/rangy-selectionsaverestore.js")
				->add_jsfile("lib/rangy/rangy-serializer.js")
				->add_jsfile("lib/ZeroClipboard.js");
				
		if (!empty($_REQUEST['tbp_serial'])) {
			$headerlib
				->add_jq_onready("
					$('#top').rangyRestore('" . $_REQUEST['tbp_serial'] . "');
				");
		} else {
			$headerlib
				->add_jq_onready("
					$('<div />')
						.appendTo('body')
						.text(tr('Create TextBacklink'))
						.css('position', 'fixed')
						.css('top', '0px')
						.css('right', '0px')
						.fadeTo(0, 0.85)
						.button()
						.click(function() {
							$(this).remove();
							$('<div />')
								.text(tr('Highlight some text and click the accept button once finished'))
								.mousedown(function() {return false;})
								.dialog({
									title: tr('Create TextBacklink'),
									modal: true
								});
					
							$(document).bind('mousedown', function() {
								if ($.rangyBusy) return;
								$('div.tbp_create').remove();
								$('embed[id*=\'ZeroClipboard\']').parent().remove();
							});
							
							$('#top').rangy(function(o) {
								if ($(this).data('rangyBusy')) return;
								var tbp_create = $('<div>' + tr('Accept TextBacklink') + '</div>')
									.button()
									.addClass('tbp_create')
									.css('position', 'absolute')
									.css('top', o.y + 'px')
									.css('left', o.x + 'px')
									.fadeTo(0,0.80)
									.click(function() {
										return false;
									})
									.appendTo('body');
									
									var clip = new ZeroClipboard.Client();
									clip.setHandCursor( true );
									
									clip.addEventListener('mousedown', function() {
										$.rangyBusy = true;
									});
									
									clip.addEventListener('complete', function(client, text) {
						                tbp_create.remove();
										clip.hide();
										$.rangyBusy = false;
										
										
										$('<div />')
											.text(tr('TextBacklink data copied to your clipboard'))
											.mousedown(function() {return false;})
											.dialog({
												title: tr('TextBacklink Copied'),
												modal: true
											});
											
										return false;
						            });
									
									clip.glue( tbp_create[0] );
									
									clip.setText(o.serial);
							});
					});
				");
		}
	}
}