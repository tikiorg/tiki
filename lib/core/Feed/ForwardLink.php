<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
		
		if (isset($_REQUEST['protocol'], $_REQUEST['contribution']) && $_REQUEST['protocol'] == 'forwardlink') {
			
			//here we do the confirmation that another wiki is trying to talk with this one
			$response = array(
				"protocol"=>	"forwardlink",
				"response"=>	"success",
				"date"=>		$args['lastModif'],
			);

			$_REQUEST['contribution'] = json_decode($_REQUEST['contribution']);
			$_REQUEST['contribution']->origin = $_SERVER['REMOTE_ADDR'];
			
			Feed_ForwardLink_Contribution::forwardLink($args['object'])
				->addItem($_REQUEST['contribution']);
				//->getItems();
			
			echo json_encode($response);
			die;
		}
		
		$_REQUEST['preview'] = (!empty($_REQUEST['preview']) ? $_REQUEST['preview'] : $args['version']);
		$phraseI;
		foreach(Feed_ForwardLink_Contribution::forwardLink($args['object'])->getItems() as $item) {
				if ($_REQUEST['preview'] == $item->forwardlink->version) {
					$thisText = htmlspecialchars($item->forwardlink->text);
					$thisHref = htmlspecialchars($item->textlink->href);
					$linkedText = htmlspecialchars($item->textlink->text);
					
					$headerlib->add_jq_onready(<<<JQ
						$('#page-data')
							.rangyRestore('$thisText', function(o) {
								$('<a>*</a>')
									.attr('href', '$thisHref')
									.attr('text', '$linkedText')
									.addClass('forwardlink')
									.insertBefore(o.start);
								
								o.selection.addClass('ui-state-highlight');
							});
JQ
					);
					$phraseI++;
				}
		}
		
		if (!empty($_REQUEST['phrase'])) {
			$phrase = htmlspecialchars($_REQUEST['phrase']);
			$headerlib->add_jq_onready(<<<JQ
				$('#page-data').rangyRestoreSelection('$phrase', function(o) {
					$('body,html').animate({
						scrollTop: o.start.offset().top
					});
					$('#page-data').trigger('rangyDone');
				});
JQ
			);
		}
		
		$headerlib->add_jq_onready(<<<JQ
			$('.forwardlink')
				.click(function() {
					me = $(this);
					var href = me.attr('href');
					var text = me.attr('text');
					
					$('<form action="' + href + '" method="post">' + 
						'<input type="hidden" name="phrase" value="' + text + '" />' +
					'</form>')
						.appendTo('body')
						.submit();
					
					return false;
				});
JQ
		);
		
		$headerlib->add_jq_onready("
			$('#page-data').trigger('rangyDone');
		");
		
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
				->add_jsfile("lib/rangy_tiki/rangy-phraser.js")
				->add_jsfile("lib/ZeroClipboard.js")
				->add_jsfile("lib/rangy_tiki/phraser.js");
			
		$href = $tikilib->tikiUrl() . 'tiki-pagehistory.php?page=' . urlencode($args['object']) . '&nohistory&preview=' . $args['version'];
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
					$.notify(tr('Highlight some text and click the accept button once finished'));
			
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
								var suggestion = rangy.expandPhrase(o.text, '\\n', me[0]);
								var buttons = {};
								
								if (suggestion == o.text) {
									accept();
								} else {
									buttons[tr('Ok')] = function() {
										o.text = suggestion;
										accept();
									};
									
									buttons[tr('Cancel')] = function() {
										accept();
									};
									
									me.box = $('<div>' +
										'<table>' +
											'<tr>' +
												'<td>' + tr('You selected:') + '</td>' +
												'<td><b>"</b>' + o.text + '<b>"</b></td>' +
											'</tr>' +
											'<tr>' +
												'<td>' + tr('Suggested selection:') + '</td>' +
												'<td class="ui-state-highlight"><b>"</b>' + suggestion + '<b>"</b></td>' +
											'</tr>' +  
										'</tabl>' + 
									'</div>')
										.dialog({
											title: tr("Suggestion"),
											buttons: buttons,
											width: $(window).width() / 2,
											modal: true
										})
								}
								
								function accept() {
									$.each(answers, function() {
										this.answer = prompt(this.question);
									});
									
									var data = {
										text: (o.text + '').replace(/[\\n'"]/g,' '),
										href: '$href',
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
										
										
										$.notify(tr('TextLink & ForwardLink data copied to your clipboard'));
										return false;
						            });
									
									clip.glue( forwardLinkCopyButton[0] );
									
									clip.setText(forwardLinkCopyValue.val());
									
									
									$('embed[id*="ZeroClipboard"]').parent().css('z-index', '9999999999');
									
									if (me.box)
										me.box.dialog('close');
								}
							})
							.appendTo('body');
					});
			});
JQ
);
	}
}