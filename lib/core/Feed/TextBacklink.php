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
				->add_jsfile("lib/rangy_tiki/rangy-serializer.js")
				->add_jsfile("lib/ZeroClipboard.js");
				
		if (!empty($_REQUEST['tbp_serial'])) {
			$headerlib
				->add_jq_onready("
					$('#top').rangyRestore('" . $_REQUEST['tbp_serial'] . "', function(o) {
						$('html,body').animate({
							scrollTop: o.selection
								.addClass('ui-state-highlight')
								.offset()
									.top
						});
					});
				");
		} else {
			$headerlib
				->add_jq_onready("
					$('<div />')
						.appendTo('body')
						.text(tr('Create TextLink & ForwardLink'))
						.css('position', 'fixed')
						.css('top', '0px')
						.css('right', '0px')
						.css('font-size', '10px')
						.fadeTo(0, 0.85)
						.button()
						.click(function() {
							$(this).remove();
							$('<div />')
								.text(tr('Highlight some text and click the accept button once finished'))
								.mousedown(function() {return false;})
								.dialog({
									title: tr('Create TextLink & ForwardLink'),
									modal: true
								});
					
							$(document).bind('mousedown', function() {
								if (me.data('rangyBusy')) return;
								$('div.tbp_create').remove();
								$('embed[id*=\'ZeroClipboard\']').parent().remove();
							});
							
							var me = $('#top').rangy(function(o) {
								if (me.data('rangyBusy')) return;
								var tbp_create = $('<div>' + tr('Accept TextLink & ForwardLink') + '</div>')
									.button()
									.addClass('tbp_create')
									.css('position', 'absolute')
									.css('top', o.y + 'px')
									.css('left', o.x + 'px')
									.css('font-size', '10px')
									.fadeTo(0,0.80)
									.click(function() {
										return false;
									})
									.appendTo('body');
									
									var clip = new ZeroClipboard.Client();
									clip.setHandCursor( true );
									
									clip.addEventListener('mousedown', function() {
										me.data('rangyBusy', true);
									});
									
									clip.addEventListener('complete', function(client, text) {
						                tbp_create.remove();
										clip.hide();
										me.data('rangyBusy', false);
										
										
										$('<div />')
											.text(tr('TextLink & ForwardLink data copied to your clipboard'))
											.mousedown(function() {return false;})
											.dialog({
												title: tr('TextLink & ForwardLink Copied'),
												modal: true
											});
											
										return false;
						            });
									
									clip.glue( tbp_create[0] );
									
									clip.setText(o.serial);
									
									$('embed[id*=\'ZeroClipboard\']')
										.parent()
										.one('click', function() {
											alert('If multi lines are detected at this point we would ask the user if they would like to add more lines if two or more lines are selected.');
										});
							});
					});
				");
		}
	}
}