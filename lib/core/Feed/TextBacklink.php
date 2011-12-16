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
		$serial = urldecode(isset($_REQUEST['tbp_serial']) ? htmlspecialchars($_REQUEST['tbp_serial']) : "");
		
		$wikiAttributes = TikiLib::lib("trkqry")
			->tracker("Wiki Attributes")
			->byName()
			->excludeDetails()
			->equals(array("Question", $args['object']))->fields(array("Type", "Page"))
			->query();
		
		$answers = array();
		foreach($wikiAttributes as $wikiAttribute) {
			$answers[] = array(
				"question"=> strip_tags($wikiAttribute['Value']),
				"answer"=> '',
			);
		}
		
		$answers = json_encode($answers);
		
		$headerlib
				->add_jsfile("lib/rangy/rangy-core.js")
				->add_jsfile("lib/rangy/rangy-cssclassapplier.js")
				->add_jsfile("lib/rangy/rangy-selectionsaverestore.js")
				->add_jsfile("lib/rangy_tiki/rangy-serializer.js")
				->add_jsfile("lib/ZeroClipboard.js");
				
		if (!empty($serial)) {
			$headerlib->add_jq_onready(<<<JQ
				$('#top')
					.rangyRestore('$serial', function(o) {
						$('html,body').animate({
							scrollTop: o.selection
								.addClass('ui-state-highlight')
								.offset()
									.top
						});
					});
JQ
);
		} else {
			$headerlib->add_jq_onready(<<<JQ
				var answers = $answers;
				
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
							$('embed[id*="ZeroClipboard"]').parent().remove();
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
								.mousedown(function() {
									alert(tr('Temporary Message: If multi lines are detected at this point we would ask the user if they would like to add more lines if two or more lines are selected.'));

									$.each(answers, function() {
										this.answer = prompt(this.question);
									});
									
									var data = {
										text: o.text,
										href: escape(document.location),
										serial: escape(o.serial),
										answers: answers
									};
									
									me.data('rangyBusy', true);
									
									var tbp_copy = $('<div></div>');
									var tbp_copy_value = $('<textarea style="width: 100%;"></textarea>')
										.val(JSON.stringify(data))
										.appendTo(tbp_copy);
									var tbp_copy_button = $('<div>' + tr('Copy To Clipboard') + '</div>')
										.button()
										.appendTo(tbp_copy);
									tbp_copy.dialog({
										title: tr("Copy This"),
										modal: true,
										close: function() {
											me.data('rangyBusy', false);
											$(document).mousedown();
										},
										draggable: false,
										resizable: false
									});
									
									tbp_copy_value.select().focus();
									
									var clip = new ZeroClipboard.Client();
									clip.setHandCursor( true );
									
									clip.addEventListener('complete', function(client, text) {
						                tbp_create.remove();
										tbp_copy.dialog( "close" );
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
									
									clip.glue( tbp_copy_button[0] );
									
									clip.setText(tbp_copy_value.val());
									
									
									$('embed[id*="ZeroClipboard"]').parent().css('z-index', '9999999999');
								})
								.appendTo('body');
						});
				});
JQ
);
		}
	}
}