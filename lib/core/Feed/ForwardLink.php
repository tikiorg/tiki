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
	var $debug = false;
	var $name = '';

	public function name($name = "") //$name is not used, but is there for compatibility with abstract
	{
		$this->name = $name;
		return $this->type . '_' . $this->name;
	}

	static function forwardLink($name)
	{
		$me = new self();
		$me->name = $name;
		return $me;
	}

	private function getTimeStamp()
	{
		//May be used soon for encrypting forwardlinks
		if (isset($_REQUEST['action'], $_REQUEST['hash']) && $_REQUEST['action'] == 'timestamp') {
			$client = new Zend_Http_Client(TikiLib::tikiUrl() . 'tiki-timestamp.php', array('timeout' => 60));
			$client->setParameterGet('hash', $_REQUEST['hash']);
			$client->setParameterGet('clienttime', time());
			$response = $client->request();
			echo $response->getBody();
			exit();
		}
	}

	private function goToPhraseExistence($phrase, $version)
	{
		if (!isset($_SESSION)) {
			session_start();
		}

		if (!empty($phrase)) $_SESSION['phrase'] = $phrase; //prep for redirect if it happens;

		if (!empty($phrase)) Feed_ForwardLink_Search::goToNewestWikiRevision($version, $phrase, $this->name);

		if (!empty($_SESSION['phrase'])) { //recover from redirect if it happened
			$phrase = $_SESSION['phrase'];
			unset($_SESSION['phrase']);
		}
	}

	private function restorePhrasesInWikiPage($phrase)
	{
		global $smarty, $headerlib;
		$items = self::forwardLink($this->name)->getItems();
		$phrases = array();

		$phraseI = 0;
		foreach ($items as $item) {
			$thisText = "";
			$thisDate = "";
			$thisHref = "";
			$linkedText = "";

			if (isset($item->forwardlink->text))
				$thisText = addslashes(htmlspecialchars($item->forwardlink->text));

			if(isset($item->forwardlink->date))
				$thisDate = addslashes(htmlspecialchars($item->forwardlink->date));

			if(isset($item->textlink->href))
				$thisHref = addslashes(htmlspecialchars($item->textlink->href));

			if(isset($item->textlink->text))
				$linkedText = addslashes(htmlspecialchars($item->textlink->text));

			$phrases[] = $thisText;

			if ($thisText == $phrase) {
				$headerlib->add_js(<<<JQ
				$(function() {
					$('#page-data')
						.rangyRestoreSelection('$thisText', function(r) {
							var phraseLink = $('<a>*</a>')
								.data('href', '$thisHref')
								.data('text', '$thisText')
								.data('linkedText', '$linkedText')
								.data('date', '$thisDate')
								.addClass('forwardlink')
								.insertBefore(r.selection[0]);

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
					var phraseLink = $('<a>*</a>')
						.data('href', '$thisHref')
						.data('text', '$thisText')
						.data('linkedText', '$linkedText')
						.data('date', '$thisDate')
						.addClass('forwardlink')
						.insertBefore('.phraseStart$phraseI');

					$('.phrase$phraseI').addClass('ui-state-highlight');
JQ
				);
			}
			$phraseI++;
		}

		$headerlib
			->add_jsfile('lib/jquery/tablesorter/jquery.tablesorter.js')
			->add_cssfile('lib/jquery_tiki/tablesorter/themes/tiki/style.css')
			->add_jq_onready(<<<JQ
				$('#page-data').trigger('rangyDone');

				$('.forwardlink')
					.click(function() {
						me = $(this);
						var href = me.data('href');
						var text = me.data('linkedText');
						var linkedText = me.data('linkedText');
						var date = me.data('date');

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
										'<td>' + date + '</td>' +
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
	}

	function editInterfaces($metadata)
	{
		$perms = Perms::get();

		//check if profile is created
		$trackerId = TikiLib::lib('trk')->get_tracker_by_name('Wiki Attributes');
		if ($trackerId < 1 && $perms->admin == 'y') {
			TikiLib::lib('header')->add_jq_onready(<<<JQ
				var addQuestionsButton = $('<span class="button"><a href="tiki-admin.php?profile=Simple+Wiki+Attributes&repository=&page=profiles&list=List">' + tr('Apply Profile "Simple Wiki Attributes" To Add ForwardLink Questions') + '</a></span>')
					.appendTo('#page-bar');
JQ
			);
		} else {

			$trackerPerms = Perms::get(array( 'type' => 'tracker', 'object' => $trackerId ));

			if ($trackerPerms->edit == true) {
				TikiLib::lib('header')
					->add_jsfile('lib/jquery_tiki/tiki-trackers.js')
					->add_jq_onready(<<<JQ
						function trackerForm(trackerId, itemId, tracker_fn_name, type, fn) {
							$.modal(tr("Loading..."));

							$.tracker_get_item_inputs({
								trackerId: trackerId,
								itemId: itemId,
								byName: true,
								defaults: {
									Page: '$this->name',
									Type: type
								}
							}, function(item) {
								$.modal();

								var frm = $('<form />')
									.submit(function() {
										$.modal(tr('Saving...'));

										frm[tracker_fn_name]({
											trackerId: trackerId,
											itemId: itemId,
											byName: true
										}, function() {
											document.location = document.location + '';
										});

										return false;
									});

								for( field in item ) {
									var input = $('<span />')
										.append(item[field])
										.addClass(field)
										.addClass('trackerInput')
										.appendTo(frm);
								}

								fn(frm);

								$.modal();
							});
						}

						function genericSingleTrackerItemInterface(type, item) {
							var addButton = $('<span class="button"><a href="tiki-view_tracker.php?trackerId=' + $trackerId + '">' + tr("Edit ForwardLink " + type) + '</a></span>')
								.click(function() {
									var box = $('<table style="width: 100%;" />');

									$.each(item, function() {
										$('<tr>')
											.append('<td>' + this.Value + '</td>')
											.append('<td title="' + tr("Edit") + '"><a class="edit" data-itemid="' + this.itemId + '"><img src="img/icons/pencil.png" /></a></td>')
											.append('<td title="' + tr("Delete") + '"><a class="delete" data-itemid="' + this.itemId + '"><img src="img/icons/cross.png" /></a></td>')
											.appendTo(box);
									});

									box.find('a.edit').click(function() {
										var me = $(this);
										var itemId = me.data('itemid');
										trackerForm($trackerId, itemId, 'tracker_update_item', type, function(frm) {

											frm.find('span.trackerInput:not(.Value').hide();

											var dialogSettings = {
												title: tr('Editing ForwardLink ' + type),
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

									box.find('a.delete').click(function() {
										if (!confirm(tr("Are you sure?"))) return false;

										var me = $(this);
										var itemId = me.data('itemid');
										trackerForm($trackerId, itemId, 'tracker_remove_item', type, function(frm) {

											frm.find('span.trackerInput:not(.Value)').hide();

											frm.submit();
										}, true);

										return false;
									});

									box.options = {
										title: tr("Edit ForwardLink " + type),
											modal: true,
											buttons: {}
									};

									if (item.length < 1) {
										box.options.buttons[tr("New")] = function () {
											trackerForm($trackerId, 0, 'tracker_insert_item', type, function(frm) {

												frm.find('span.trackerInput:not(.Value)').hide();

												var newFrmDialogSettings = {
													buttons: {},
													modal: true,
													title: tr('New ' + type)
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
									}
									box.dialog(box.options);
									return false;
								})
								.appendTo('#page-bar');
						}
JQ
					);

				$this->editQuestionsInterface($metadata->questions(), $trackerId);

				$keywords = json_encode($metadata->keywords(false));
				TikiLib::lib('header')->add_jq_onready("genericSingleTrackerItemInterface('Keywords', $keywords);");

				$scientificField = json_encode($metadata->scientificField(false));
				TikiLib::lib('header')->add_jq_onready("genericSingleTrackerItemInterface('Scientific Field', $scientificField);");

				$minimumMathNeeded = json_encode($metadata->minimumMathNeeded(false));
				TikiLib::lib('header')->add_jq_onready("genericSingleTrackerItemInterface('Minimum Math Needed', $minimumMathNeeded);");

				$minimumStatisticsNeeded = json_encode($metadata->minimumStatisticsNeeded(false));
				TikiLib::lib('header')->add_jq_onready("genericSingleTrackerItemInterface('Minimum Statistics Needed', $minimumStatisticsNeeded);");
			}
		}
	}

	function editQuestionsInterface($questions, $trackerId)
	{
		$questions = json_encode($questions);

		TikiLib::lib('header')
			->add_jq_onready(<<<JQ
				var addQuestionsButton = $('<span class="button"><a href="tiki-view_tracker.php?trackerId=' + $trackerId + '">' + tr("Edit ForwardLink Questions") + '</a></span>')
					.click(function() {
						var questionBox = $('<table style="width: 100%;" />');
						var questions = $questions;
						$.each(questions, function() {
							$('<tr>')
								.append('<td>' + this.Value + '</td>')
								.append('<td title="' + tr("Edit") + '"><a class="edit" data-itemid="' + this.itemId + '"><img src="img/icons/pencil.png" /></a></td>')
								.append('<td title="' + tr("Delete") + '"><a class="delete" data-itemid="' + this.itemId + '"><img src="img/icons/cross.png" /></a></td>')
								.appendTo(questionBox);
						});

						questionBox.find('a.edit').click(function() {
							var me = $(this);
							var itemId = me.data('itemid');
							trackerForm($trackerId, itemId, 'tracker_update_item', 'Question', function(frm) {

								frm.find('span.trackerInput:not(.Value').hide();

								var dialogSettings = {
									title: tr('Editing ForwardLink Question: ') + me.parent().parent().text(),
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
							trackerForm($trackerId, itemId, 'tracker_remove_item', 'Question', function(frm) {

								frm.find('span.trackerInput:not(.Value)').hide();

								frm.submit();
							}, true);

							return false;
						});

						var questionBoxOptions = {
							title: "Edit ForwardLink Questions",
								modal: true,
								buttons: {}
						};
						questionBoxOptions.buttons[tr("New")] = function () {
							trackerForm($trackerId, 0, 'tracker_insert_item', 'Question', function(frm) {

								frm.find('span.trackerInput:not(.Value)').hide();

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

	function createForwardLinksInterface($metadata)
	{
		global $tikilib, $headerlib, $prefs, $user;

		$answers = json_encode($metadata->raw['answers']);

		$clipboarddata = json_encode($metadata->raw);

		$headerlib
			->add_jsfile('lib/rangy/uncompressed/rangy-core.js')
			->add_jsfile('lib/rangy/uncompressed/rangy-cssclassapplier.js')
			->add_jsfile('lib/rangy/uncompressed/rangy-selectionsaverestore.js')
			->add_jsfile('lib/rangy_tiki/rangy-phraser.js')
			->add_jsfile('lib/ZeroClipboard.js')
			->add_jsfile('lib/core/JisonParser/Phraser.js')
			->add_jsfile('lib/jquery/md5.js');

		$page = urlencode($this->name);

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

								function encode(s){
									for(var c, i = -1, l = (s = s.split("")).length, o = String.fromCharCode; ++i < l;
										s[i] = (c = s[i].charCodeAt(0)) >= 127 ? o(0xc0 | (c >>> 6)) + o(0x80 | (c & 0x3f)) : s[i]
									);
									return s.join("");
								}

								function makeClipboardData() {

									var clipboarddata = $clipboarddata;

									clipboarddata.text = encode((o.text + '').replace(/\\n/g, ''));

									clipboarddata.hash = md5(rangy.superSanitize(clipboarddata.websiteTitle), rangy.superSanitize(clipboarddata.text));

									me.data('rangyBusy', true);

									var forwardLinkCopy = $('<div></div>');
									var forwardLinkCopyButton = $('<div>' + tr('Copy To Clipboard') + '</div>')
										.button()
										.appendTo(forwardLinkCopy);
									var forwardLinkCopyValue = $('<textarea style="width: 100%; height: 80%;"></textarea>')
										.val(encodeURI(JSON.stringify(clipboarddata)))
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
	}

	static function wikiView($args)
	{
		global $prefs, $headerlib, $smarty, $_REQUEST, $user, $tikilib;

		$page = $args['object'];
		$version = $args['version'];

		$metadata = Feed_ForwardLink_Metadata::pageForwardLink($page);

		$phrase = (!empty($_REQUEST['phrase']) ? addslashes(htmlspecialchars($_REQUEST['phrase'])) : '');

		$me = new self($page);

		$me->goToPhraseExistence($phrase, $version);

		$me->restorePhrasesInWikiPage($phrase);

		$me->editInterfaces($metadata);

		$me->createForwardLinksInterface($metadata);
	}
	
	static function wikiSave($args)
	{
		//print_r($args);
	}

	function appendToContents(&$contents, $item)
	{
		global $prefs, $_REQUEST, $groupPluginReturnAll;
		$groupPluginReturnAll = true;
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
		$checks = array();

		foreach ($item->feed->entry as $i => $newEntry) {
			$checks[$i] = array();

			$checks[$i]["hashableHere"] = JisonParser_Phraser_Handler::superSanitize($prefs['browsertitle']);
			$checks[$i]["phraseThere"] =JisonParser_Phraser_Handler::superSanitize($newEntry->forwardlink->text);
			$checks[$i]["parentHere"] = JisonParser_Phraser_Handler::superSanitize(TikiLib::lib("wiki")->get_parse($_REQUEST['page']));
			$checks[$i]["hashHere"] = hash_hmac("md5", $checks[$i]["hashableHere"], $checks[$i]["phraseThere"]);
			$checks[$i]["hashThere"] = $newEntry->forwardlink->hash;
			$checks[$i]["reason"] = "";
			$checks[$i]['exists'] = JisonParser_Phraser_Handler::hasPhrase(
				TikiLib::lib('wiki')->get_parse($_REQUEST['page']),
				$newEntry->forwardlink->text
			);

			$checks[$i]['reason'] = '';

			if ($checks[$i]['hashHere'] != $checks[$i]['hashThere']) {
				$checks[$i]['reason'] .= '_hash_';
				unset($item->feed->entry[$i]);
			}

			if ($newEntry->forwardlink->websiteTitle != $prefs['browsertitle']) {
				$checks[$i]['reason'] .= '_title_';
				unset($item->feed->entry[$i]);
			}

			if (!$checks[$i]['exists']) {
				if (empty($checks[$i]['reason'])) {
					$checks[$i]['reason'] .= '_no_existence_hash_pass_';
				} else {
					$checks[$i]['reason'] .= '_no_existence_';
				}

				unset($item->feed->entry[$i]);
			}
		}

		if ($this->debug) {
			print_r($checks);
		}

		if (count($item->feed->entry) > 0) {
			$replace = true;

			//these are new items, so we want to add them to the list
			foreach($item->feed->entry as $entry) {
				$entryText = JisonParser_Phraser_Handler::superSanitize($entry->forwardlink->text);
				$entryHash = md5($entryText);

				Tracker_Query::tracker('Wiki Attributes')
					->byName()
					->replaceItem(array(
						'Page' => $this->name(),
						'Attribute' => $entryHash,
						'Value' => $entryText,
						'Type' => 'ForwardLink'
					));
			}

			$contents->entry += $item->feed->entry;
		}

		$groupPluginReturnAll = false;

		return $replace;
	}
}
