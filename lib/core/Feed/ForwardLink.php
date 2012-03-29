<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_ForwardLink extends Feed_Abstract
{
	var $type = 'forwardlink';
	var $version = '0.1';
	var $isFileGal = true;

	public function name()
	{
		return $this->type . "_" . $this->name;
	}

	static function forwardLink($name)
	{
		$me = new self();
		$me->name = $name;
		return $me;
	}

	static function wikiView($args)
	{
		global $prefs, $headerlib, $smarty, $_REQUEST, $user;

		if (isset($_REQUEST['itemId']))
		{
			print_r(json_encode(
				Tracker_Query::tracker('Wiki Attributes')
					->byName()
					->itemId((int)$_REQUEST['itemId'])
					->inputDefaults(array(
						"Page" => $args['object'],
						"Type" => "Question"
					))
					->queryInput()
			));
			exit(0);
		}

		/* May be used soon for encrypting forwardlinks
		if (isset($_REQUEST['action'], $_REQUEST['hash']) && $_REQUEST['action'] == 'timestamp') {
			$client = new Zend_Http_Client('http://localhost/tiki/trunk/tiki-timestamp.php', array('timeout' => 60));
			$client->setParameterGet('hash', $_REQUEST['hash']);
			$client->setParameterGet('clienttime', time());
			$response = $client->request();
			echo $response->getBody();
			exit();
		}
		*/

		$phrase = (!empty($_REQUEST['phrase']) ? htmlspecialchars($_REQUEST['phrase']) : '');

		session_start();
		if (!empty($phrase)) $_SESSION['phrase'] = $phrase; //prep for redirect if it happens;

		if (!empty($phrase)) Feed_ForwardLink_Search::goToNewestWikiRevision($args['version'], $phrase, $args['object']);

		if (!empty($_SESSION['phrase'])) { //recover from redirect if it happened
			$phrase = $_SESSION['phrase'];
			unset($_SESSION['phrase']);
		}

		$_REQUEST['preview'] = (!empty($_REQUEST['preview']) ? $_REQUEST['preview'] : $args['version']);
		$phraseI = 0;

		$feedItems = self::forwardLink($args['object'])->getItems();
		$phrases = array();
		foreach ($feedItems as $item) {
			$phrases[] = $thisText = htmlspecialchars($item->forwardlink->text);
		}

		$phraser = new JisonParser_Phraser_Handler();

		$parsed = $smarty->getTemplateVars('parsed');
		if (!empty($parsed)) {
			$smarty->assign('parsed', $phraser->findPhrases($parsed, $phrases));
		} else {
			$previewd = $smarty->getTemplateVars('previewd');
			if (!empty($previewd)) {
				$previewd = $phraser->findPhrases($previewd, $phrases);
				$smarty->assign('previewd', $previewd);
			}
		}

		foreach ($feedItems as $item) {
			$thisText = htmlspecialchars($item->forwardlink->text);
			$thisHref = htmlspecialchars($item->textlink->href);
			$linkedText = htmlspecialchars($item->textlink->text);

			if ($thisText == $phrase) {
				$headerlib->add_js(<<<JQ
				window.phraseLinks = (window.phraseLinks ? window.phraseLinks : []);
				$(function() {
					$('#page-data')
						.rangyRestoreSelection('$thisText', function(r) {
							var phraseLink = $('<a>*</a>')
								.attr('href', '$thisHref')
								.attr('text', '$thisText')
								.attr('linkedText', '$linkedText')
								.addClass('forwardlink')
								.insertBefore(r.selection[0]);
							
							window.phraseLinks.push(phraseLink);

							r.selection.addClass('ui-state-highlight');

							$('body,html').animate({
								scrollTop: r.start.offset().top
							});
						});
				});
JQ
				);
			} else {

				$headerlib->add_jq_onready(<<<JQ
					window.phraseLinks = (window.phraseLinks ? window.phraseLinks : []);
					var phraseLink = $('<a>*</a>')
						.attr('href', '$thisHref')
						.attr('text', '$thisText')
						.attr('linkedText', '$linkedText')
						.addClass('forwardlink')
						.insertBefore('.phraseStart$phraseI');
					
					window.phraseLinks.push(phraseLink);
					
					$('.phrase$phraseI').addClass('ui-state-highlight');
JQ
				);
			}
			$phraseI++;
		}

		$headerlib
			->add_jsfile("lib/jquery/tablesorter/jquery.tablesorter.js")
			->add_cssfile("lib/jquery_tiki/tablesorter/themes/tiki/style.css")
			->add_jq_onready(<<<JQ
				$('#page-data').trigger('rangyDone');

				$('.forwardlink')
					.click(function() {
						me = $(this);
						var href = me.attr('href');
						var text = me.attr('text');
						var linkedText = me.attr('linkedText');
						
						var table = $('<div>' +
							'<table class="tablesorter">' +
								'<thead>' + 
									'<tr>' +
										'<th>' + tr('Date') + '</th>' +
										'<th>' + tr('Click below to read Citing blocks') + '</th>' +
									'</tr>' +
								'</thead>' +
								'<tbody>' +
									'<tr>' +
										'<td>$date</td>' +
										'<td><a href="' + href + '" class="read">Read</a></td>' + 
									'</tr>' +
								'</tbody>' +
							'</table>' +
						'</div>')
							.dialog({
								title: text,
								modal: true
							})
							.tablesorter();
					
						return false;
					});
JQ
			);

		$questions = Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filter(array('field'=> 'Type','value'=> 'Question'))
			->filter(array('field'=> 'Page','value'=> $args['object']))
			->query();

		$answers = array();
		foreach ($questions as $question) {
			$answers[] = array(
				'question'=> strip_tags($question['Value']),
				'answer'=> '',
			);
		}

		$answers = json_encode($answers);

		$headerlib
			->add_jsfile('lib/rangy/uncompressed/rangy-core.js')
			->add_jsfile('lib/rangy/uncompressed/rangy-cssclassapplier.js')
			->add_jsfile('lib/rangy/uncompressed/rangy-selectionsaverestore.js')
			->add_jsfile('lib/rangy_tiki/rangy-phraser.js')
			->add_jsfile('lib/ZeroClipboard.js')
			->add_jsfile('lib/core/JisonParser/Phraser.js')
			->add_jsfile('lib/jquery/md5.js');

		$authorDetails = json_encode(
						end(
										Tracker_Query::tracker('ForwardLink Author Details')
										->byName()
										->excludeDetails()
										->filter(array('field'=> 'User','value'=> $user))
										->render(false)
										->query()
						)
		);

		$page = urlencode($args['object']);
		$href = TikiLib::tikiUrl() . 'tiki-index.php?page=' . $page;
		$version = $args['version'];
		$date = $args['lastModif'];
		//print_r( $prefs );

		$websiteTitle = htmlspecialchars($prefs['browsertitle']);

		$headerlib->add_jq_onready(<<<JQ
			var answers = $answers;

			$('<div />')
				.appendTo('body')
				.text(tr('Create ForwardLink'))
				.css('position', 'fixed')
				.css('top', '0px')
				.css('right', '0px')
				.css('font-size', '10px')
				.css('z-index', 99999)
				.fadeTo(0, 0.85)
				.button()
				.click(function() {
					$(this).remove();
					$.notify(tr('Highlight text to be linked'));

					$(document).bind('mousedown', function() {
						if (me.data('rangyBusy')) return;
						$('div.forwardLinkCreate').remove();
						$('embed[id*="ZeroClipboard"]').parent().remove();
					});

					var me = $('#page-data').rangy(function(o) {
						if (me.data('rangyBusy')) return;
						o.text = $.trim(o.text);
						
						var forwardLinkCreate = $('<div>' + tr('Accept TextLink & ForwardLink') + '</div>')
							.button()
							.addClass('forwardLinkCreate')
							.css('position', 'absolute')
							.css('top', o.y + 'px')
							.css('left', o.x + 'px')
							.css('font-size', '10px')
							.fadeTo(0,0.80)
							.mousedown(function() {
								var suggestion = $.trim(rangy.expandPhrase(o.text, '\\n', me[0]));
								var buttons = {};

								if (suggestion == o.text) {
									getAnswers();
								} else {
									buttons[tr('Ok')] = function() {
										o.text = suggestion;
										me.box.dialog('close');
										getAnswers();
									};

									buttons[tr('Cancel')] = function() {
										me.box.dialog('close');
										getAnswers();
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

								function getAnswers() {
									if (!answers.length) {
										return acceptPhrase();
									}

									var answersDialog = $('<table width="100%;" />');

									$.each(answers, function() {
										var tr = $('<tr />').appendTo(answersDialog);
										$('<td style="font-weight: bold; text-align: left;" />')
											.text(this.question)
											.appendTo(tr);
										
										$('<td style="text-align: right;"><input class="answerValues" style="width: inherit;"/></td>')
											.appendTo(tr);
									});

									var answersDialogButtons = {};
									answersDialogButtons[tr("Ok")] = function() {
										$.each(answers, function(i) {
											answers[i].answer = escape(answersDialog.find('.answerValues').eq(i).val());
										});

										answersDialog.dialog('close');

										acceptPhrase();
									};

									answersDialog.dialog({
										title: tr("Please fill in the questions below"),
										buttons: answersDialogButtons,
										modal: true,
										width: $(window).width() / 2
									});
								}

								//var timestamp = '';

								function acceptPhrase() {
									/* Will integrate when timestamping works
									$.modal(tr("Please wait while we process your request..."));
									$.getJSON("tiki-index.php", {
										action: "timestamp",
										hash: hash,
										page: '$page'
									}, function(json) {
										timestamp = json;
										$.modal();
										makeClipboardData();
									});
									*/
									makeClipboardData();
								}

								function makeClipboardData() {
									var data = {
										websiteTitle: '$websiteTitle',
										websiteSubtitle: '',
										moderator: '',
										moderatorInfo: '',
										subtitle: '',
										text: (o.text + '').replace(/[\\n'"]/g,' '),
										hash: '',
										author: '',
										href: '$href',
										answers: answers,
										version: $version,
										date: $date
									};

									data.hash = md5(data.websiteTitle, data.text);

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
								}
							})
							.appendTo('body');
					});
			});
JQ
);
		//check if profile is created
		$trackerId = TikiLib::lib('trk')->get_tracker_by_name('Wiki Attributes');
		if ($trackerId < 1) {
			$headerlib->add_jq_onready(<<<JQ
				var addQuestionsButton = $('<span class="button"><a href="tiki-admin.php?profile=Simple+Wiki+Attributes&repository=&page=profiles&list=List">' + tr('Apply Profile "Simple Wiki Attributes" To Add ForwardLink Questions') + '</a></span>')
					.appendTo('#page-bar');
JQ
			);
		} else {
			$wikiPerms = Perms::get(array( 'type' => 'wiki page', 'object' => $args['object'] ));
			$trackerPerms = Perms::get(array( 'type' => 'tracker', 'object' => $args['object'] ));

			$questionsCount = count($questions);
			$questions = json_encode($questions);

			if ( $wikiPerms->edit ) {
				$headerlib->add_jq_onready(<<<JQ
					function showField(field, obj) {
						obj = $('<span/>').append(obj);
						switch (field) {
							case 'Value':break;
							default: obj.hide();
						}
						return obj;
					}

					function trackerForm(trackerId, itemId, fn, remove) {
						$.modal(tr("Loading..."));
						$.getJSON('?itemId=' + itemId, function(item) {
							$.modal();
							var frm = $.trackerForm(trackerId, itemId, remove)
								.submit(function() {
									var serialized = frm.serialize();
									$.modal(tr('Saving...'));
									$.post(frm.attr('action') + '?' + serialized, function() {
										document.location = document.location + '';
									});

									return false;
								});

							for( field in item ) {
								showField(field, item[field]).appendTo(frm);
							}

							fn(frm);

							$.modal();
						});
					}

					var addQuestionsButton = $('<span class="button"><a href="tiki-view_tracker.php?trackerId=' + $trackerId + '">' + tr("Edit ForwardLink Questions") + '</a></span>')
						.click(function() {
							var questionBox = $('<table style="width: 100%;" />');
							var questions = $questions;
							$.each(questions, function() {
								$('<tr>')
									.append('<td>' + this.Value + '</td>')
									.append('<td title="' + tr("Edit") + '"><a class="edit" href="tiki-view_tracker_item.php?itemId=' + this.itemId + '" data-itemid="' + this.itemId + '"><img src="img/icons/pencil.png" /></a></td>')
									.append('<td title="' + tr("Delete") + '"><a class="delete" href="tiki-view_tracker_item.php?itemId=' + this.itemId + '" data-itemid="' + this.itemId + '"><img src="img/icons/cross.png" /></a></td>')
									.appendTo(questionBox);
							});

							questionBox.find('a.edit').click(function() {
								var me = $(this);
								var itemId = me.data('itemid');
								trackerForm($trackerId, itemId, function(frm) {
									var dialogSettings = {
										title: tr('Editing: ') + me.parent().parent().text(),
										modal: true,
										buttons: {}
									};

									dialogSettings.buttons[tr('OK')] = function() {
										frm.submit();
									};

									dialogSettings.buttons[tr('Cancel')] = function() {
										questionDialog.dialog('close');
									};

									var questionDialog = $('<div />')
										.append(frm)
										.dialog(dialogSettings);
								});

								return false;
							});

							questionBox.find('a.delete').click(function() {
								if (!confirm(tr("Are you sure?"))) return false;

								var me = $(this);
								var itemId = me.data('itemid');
								trackerForm($trackerId, itemId, function(frm) {
									frm.submit();
								});

								return false;
							});

							var questionBoxOptions = {
								title: "Edit ForwardLink Questions",
									modal: true,
									buttons: {}
							};
							questionBoxOptions.buttons[tr("New")] = function () {
								trackerForm($trackerId, 0, function(frm) {
									var newFrmDialogSettings = {
										buttons: {},
										modal: true,
										title: tr('New')
									};

									newFrmDialogSettings.buttons[tr('Save')] = function() {
										frm.submit();
									};

									newFrmDialogSettings.buttons[tr('Cancel')] = function() {
										questionDialog.dialog('close');
									};

									var questionDialog = $('<div />')
										.append(frm)
										.dialog(newFrmDialogSettings);
								});
							};
							questionBox.dialog(questionBoxOptions);
							return false;
						})
						.appendTo('#page-bar');
JQ
				);
			}
		}
	}
	
	static function wikiSave($args)
	{
		//print_r($args);
	}

	function appendToContents(&$contents, $item)
	{
		global $prefs, $_REQUEST;
		$replace = false;

		//lets remove the newentry if it has already been accepted in the past
		foreach ($contents->entry as $i => $existingEntry) {
			foreach ($item->feed->entry as $j => $newEntry) {
				if (
					$existingEntry->textlink->text == $newEntry->textlink->text &&
					$existingEntry->textlink->href == $newEntry->textlink->href
				) {
					unset($item->feed->entry[$j]);
				}
			}
		}

		//lets check if the hash is correct and that the phrase actually exists within the wiki page
		foreach ($item->feed->entry as $i => $newEntry) {

			if ($this->showFailures) {
				print_r(
					array(
						"hashIncluded"=>	$newEntry->forwardlink->hash,
						"hashCalculated"=> 	hash_hmac("md5", htmlspecialchars($prefs['browsertitle']), $newEntry->forwardlink->text),
						"metadata"=> 		($newEntry->forwardlink->websiteTitle != $prefs['browsertitle']),
						"hasPhrase"=> 		(JisonParser_Phraser_Handler::hasPhrase(TikiLib::lib("wiki")->get_parse($_REQUEST['page']), $newEntry->forwardlink->text)),
						"page"=>			$_REQUEST['page']
					)
				);
			}

			if (
				$newEntry->forwardlink->hash != hash_hmac("md5", htmlspecialchars($prefs['browsertitle']), $newEntry->forwardlink->text) ||
				$newEntry->forwardlink->websiteTitle != $prefs['browsertitle'] ||
				JisonParser_Phraser_Handler::hasPhrase(TikiLib::lib("wiki")->get_parse($_REQUEST['page']), $newEntry->forwardlink->text) != true
			) {
				unset($item->feed->entry[$i]);
			}
		}

		if (count($item->feed->entry) > 0) {
			$replace = true;
			$contents->entry += $item->feed->entry;
		}

		return $replace;
	}
}
