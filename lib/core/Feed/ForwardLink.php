<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_ForwardLink extends Feed_Abstract
{
	var $type = "Feed_ForwardLink";
	
	function wikiView($args)
	{
		global $tikilib, $headerlib, $_REQUEST;
		
		if (isset($_GET['protocol'], $_GET['contribution']) && $_GET['protocol'] == 'forwardlink') {
			
			//here we do the confirmation that another wiki is trying to talk with this one
			$response = array(
				"protocol"=>	"forwardlink",
				"response"=>	"success",
				"date"=>		$args['lastModif'],
			);
			
			ini_set('error_reporting', E_ALL);
			ini_set('display_errors', 1);

			$_GET['contribution'] = json_decode($_GET['contribution']);
			$_GET['contribution']->origin = $_SERVER['REMOTE_ADDR'];
			print_r($_GET['contribution']);
			
			Feed_ForwardLink_Contribution::forwardLink($args['object'])
				->addItem($_GET['contribution']);
			
			echo json_encode($response);
			die;
		}
		
		foreach(Feed_ForwardLink_Contribution::forwardLink($args['object'])->getItems() as $item) {
			foreach($item->feed->entry as $entry) {
				$thisText = htmlspecialchars($entry->forwardlink->text);
				$thisSerial = htmlspecialchars($entry->forwardlink->serial);
				$thisHref = htmlspecialchars($entry->textlink->href);
				$headerlib->add_jq_onready(<<<JQ
					$('#page-data')
						.rangyRestore('$thisText', function(o) {
							$('<a>&nbsp;*&nbsp;</a>')
								.attr('href', '$thisHref')
								.insertBefore(o.selection.first());
						});
JQ
);
			}
		}
		
		$serial = urldecode(isset($_REQUEST['serial']) ? htmlspecialchars($_REQUEST['serial']) : "");
		
		$wikiAttributes = TikiLib::lib("trkqry")
			->tracker("Wiki Attributes")
			->byName()
			->excludeDetails()
			->filter(array(
				'field'=> 'Type',
				'value'=> 'Question'
			))
			->filter(array(
				'field'=> 'Page',
				'value'=> $args['object']
			))
			->render(false)
			->query();
		
		//print_r($wikiAttributes);
		$answers = array();
		foreach($wikiAttributes as $wikiAttribute) {
			$answers[] = array(
				"question"=> strip_tags($wikiAttribute['Value']),
				"answer"=> '',
			);
		}
		
		$answers = json_encode($answers);
		
		$headerlib
				->add_jsfile("lib/rangy/uncompressed/rangy-core.js")
				->add_jsfile("lib/rangy/uncompressed/rangy-cssclassapplier.js")
				->add_jsfile("lib/rangy/uncompressed/rangy-selectionsaverestore.js")
				->add_jsfile("lib/rangy_tiki/rangy-serializer.js")
				->add_jsfile("lib/rangy_tiki/rangy-phraser.js")
				->add_jsfile("lib/ZeroClipboard.js");
				
		if (!empty($serial)) {
			$headerlib->add_jq_onready(<<<JQ
				$('.rangy-selection').each(function() {
					if ($(this).data('serial') == '$serial') {
						$('html,body').animate({
							scrollTop: $(this)
								.addClass('ui-state-highlight')
								.offset()
									.top
						});
					}
				});
JQ
);
		} else {
			$href = $tikilib->tikiUrl() . 'tiki-pagehistory.php?page=' . $args['object'] . '&nohistory&preview=' . $args['version'] . '&serial=';
			$version = $args['version'];
			$date = $args['lastModif'];
			
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
							$('div.forwardLinkCreate').remove();
							$('embed[id*="ZeroClipboard"]').parent().remove();
						});
						
						var me = $('#page-data').rangy(function(o) {
							if (me.data('rangyBusy')) return;
							
							var forwardLinkCreate = $('<div>' + tr('Accept TextLink & ForwardLink') + '</div>')
								.button()
								.addClass('forwardLinkCreate')
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
										href: escape('$href' + escape(o.serial)),
										serial: escape(o.serial),
										answers: answers,
										version: $version,
										date: $date
									};
									
									me.data('rangyBusy', true);
									
									var forwardLinkCopy = $('<div></div>');
									var forwardLinkCopyButton = $('<div>' + tr('Copy To Clipboard') + '</div>')
										.button()
										.appendTo(forwardLinkCopy);
									var forwardLinkCopyValue = $('<textarea style="width: 100%; height: 80%;"></textarea>')
										.val(encodeURI(JSON.stringify(data)))
										.appendTo(forwardLinkCopy);
									forwardLinkCopy.dialog({
										title: tr("Copy This"),
										modal: true,
										close: function() {
											me.data('rangyBusy', false);
											$(document).mousedown();
										},
										draggable: false
									});
									
									forwardLinkCopyValue.select().focus();
									
									var clip = new ZeroClipboard.Client();
									clip.setHandCursor( true );
									
									clip.addEventListener('complete', function(client, text) {
						                forwardLinkCreate.remove();
										forwardLinkCopy.dialog( "close" );
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
									
									clip.glue( forwardLinkCopyButton[0] );
									
									clip.setText(forwardLinkCopyValue.val());
									
									
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