<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// File name: FutureUI.php
// Required path: /lib/core/Feed
//
// Programmer: Robert Plummer
//
// Purpose: Inject FutureLink UI components into Wiki editing screens.  Managed page's saved attributes per
//          FutureLink UI interaction.  Generates and presents FutureLink text string to user.

Class FutureLink_FutureUI extends Feed_Abstract
{
	var $type = 'futurelink';
	var $version = 0.1;
	var $isFileGal = true;
	var $debug = false;
	var $page = '';
	static $pagesParsed = array();
	static $parsedDatas = array();
	var $metadata = array();
	var $verifications = array();
	var $itemsAdded = array();

	function __construct($page)
	{
		$this->page = $page;
		$this->metadata = FutureLink_MetadataAssembler::pageFutureLink($page);
		return parent::__construct($page);
	}

	private function getTimeStamp()
	{
		//May be used soon for encrypting futurelinks
		if (isset($_REQUEST['action'], $_REQUEST['hash']) && $_REQUEST['action'] == 'timestamp') {
			$client = new Zend\Http\Client(TikiLib::tikiUrl() . 'tiki-timestamp.php', array('timeout' => 60));
			$client->getRequest()->getQuery()->set('hash', $_REQUEST['hash']);
			$client->getRequest()->getQuery()->set('clienttime', time());
			$response = $client->send();
			echo $response->getBody();
			exit();
		}
	}

	function editInterfaces()
	{
		$perms = Perms::get();

		//check if profile is created
        // TODO: implement this or similar checks in search.php to explain why phrase query fails -- 2013-04-09 LDG
		$trackerId = TikiLib::lib('trk')->get_tracker_by_name('Wiki Attributes');
		if ($trackerId < 1 && $perms->admin == 'y') {
			TikiLib::lib('header')->add_jq_onready(
<<<JQ
				var addQuestionsButton = $('<span class="button btn btn-default"><a href="tiki-admin.php?profile=Simple+Wiki+Attributes&repository=&page=profiles&list=List">' + tr('Apply Profile "Simple Wiki Attributes" To Add FutureLink Questions') + '</a></span>')
					.appendTo('#page-bar');
JQ
			);
		} else {

			$trackerPerms = Perms::get(array( 'type' => 'tracker', 'object' => $trackerId ));
			$page = htmlspecialchars($this->page);

			if ($trackerPerms->edit == true) {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_function_icon');
				$pencilicon = smarty_function_icon(['name' => 'pencil'], $smarty);
				$deleteicon = smarty_function_icon(['name' => 'delete'], $smarty);
				TikiLib::lib('header')
					->add_jsfile('lib/jquery_tiki/tiki-trackers.js')
					->add_jq_onready(
<<<JQ
						function trackerForm(trackerId, itemId, tracker_fn_name, type, fn) {
							$.tikiModal(tr("Loading..."));

							$.tracker_get_item_inputs({
								trackerId: trackerId,
								itemId: itemId,
								byName: true,
								defaults: {
									Page: '$page',
									Type: type
								}
							}, function(item) {
								$.tikiModal();

								var frm = $('<form />')
									.submit(function() {
										$.tikiModal(tr('Saving...'));

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

								$.tikiModal();
							});
						}

						function genericSingleTrackerItemInterface(type, item) {
							var addButton = $('<span class="button btn btn-default"><a href="tiki-view_tracker.php?trackerId=' + $trackerId + '">' + tr("Edit FutureLink " + type) + '</a></span>')
								.click(function() {
									var box = $('<table style="width: 100%;" />');

									$.each(item, function() {
										$('<tr>')
											.append('<td>' + this.Value + '</td>')
											.append('<td title="' + tr("Edit") + '"><a class="edit" data-itemid="' + this.itemId + '">$pencilicon</a></td>')
											.append('<td title="' + tr("Delete") + '"><a class="delete" data-itemid="' + this.itemId + '">$deleteicon</a></td>')
											.appendTo(box);
									});

									box.find('a.edit').click(function() {
										var me = $(this);
										var itemId = me.data('itemid');
										trackerForm($trackerId, itemId, 'tracker_update_item', type, function(frm) {

											frm.find('span.trackerInput:not(.Value').hide();

											var dialogSettings = {
												title: tr('Editing FutureLink ' + type),
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
										title: tr("Edit FutureLink " + type),
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

				$this->editQuestionsInterface($this->metadata->questions(), $trackerId);

				$keywords = json_encode($this->metadata->keywords(false));
				TikiLib::lib('header')->add_jq_onready("genericSingleTrackerItemInterface('Keywords', $keywords);");

				$scientificField = json_encode($this->metadata->scientificField(false));
				TikiLib::lib('header')->add_jq_onready("genericSingleTrackerItemInterface('Scientific Field', $scientificField);");

				$minimumMathNeeded = json_encode($this->metadata->minimumMathNeeded(false));
				TikiLib::lib('header')->add_jq_onready("genericSingleTrackerItemInterface('Minimum Math Needed', $minimumMathNeeded);");

				$minimumStatisticsNeeded = json_encode($this->metadata->minimumStatisticsNeeded(false));
				TikiLib::lib('header')->add_jq_onready("genericSingleTrackerItemInterface('Minimum Statistics Needed', $minimumStatisticsNeeded);");
			}
		}
	}

	function editQuestionsInterface($questions, $trackerId)
	{
		$questions = json_encode($questions);
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_icon');
		$pencilicon = smarty_function_icon(['name' => 'pencil'], $smarty);
		$deleteicon = smarty_function_icon(['name' => 'delete'], $smarty);

		TikiLib::lib('header')
			->add_jq_onready(
<<<JQ
				var addQuestionsButton = $('<span class="button btn btn-default"><a href="tiki-view_tracker.php?trackerId=' + $trackerId + '">' + tr("Edit FutureLink Questions") + '</a></span>')
					.click(function() {
						var questionBox = $('<table style="width: 100%;" />');
						var questions = $questions;
						$.each(questions, function() {
							$('<tr>')
								.append('<td>' + this.Value + '</td>')
								.append('<td title="' + tr("Edit") + '"><a class="edit" data-itemid="' + this.itemId + '">$pencilicon</a></td>')
								.append('<td title="' + tr("Delete") + '"><a class="delete" data-itemid="' + this.itemId + '">$deleteicon</a></td>')
								.appendTo(questionBox);
						});

						questionBox.find('a.edit').click(function() {
							var me = $(this);
							var itemId = me.data('itemid');
							trackerForm($trackerId, itemId, 'tracker_update_item', 'Question', function(frm) {

								frm.find('span.trackerInput:not(.Value)').hide();

								var dialogSettings = {
									title: tr('Editing FutureLink Question: ') + me.parent().parent().text(),
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
							title: tr("Edit FutureLink Questions"),
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

	function createPastLinksInterface()
	{
		global $prefs, $user;
		$headerlib = TikiLib::lib('header');
		$tikilib = TikiLib::lib('tiki');

		$answers = json_encode($this->metadata->raw->answers);

		$clipboarddata = json_encode($this->metadata->raw);

		$page = urlencode($this->page);

		$headerlib->add_jq_onready(
<<<JQ
			var answers = $answers,

			createPastLinkButton = $('.pastlinkCreationButton');

			if (!createPastLinkButton.length) {
				createPastLinkButton = $('<div />')
					.appendTo('body')
					.text(tr('Create PastLink'))
					.addClass('pastlinkCreationButton')
					.css('position', 'fixed')
					.css('top', '0px')
					.css('font-size', '10px')
					.css('z-index', 99999)
					.fadeTo(0, 0.85)
					.button();
			}

			createPastLinkButton
				.click(function() {
					$(this).remove();
					$.notify(tr('Highlight text to be linked'));

					$(document).bind('mousedown', function() {
						if (me.data('rangyBusy')) return;
						$('div.pastlinkCreate').remove();
						$('embed[id*="ZeroClipboard"]').parent().remove();
					});

					var me = $('#page-data').rangy(function(o) {
						if (me.data('rangyBusy')) return;
						o.text = $.trim(o.text);

						var pastlinkCreate = $('<div>' + tr('Accept PastLink') + '</div>')
							.button()
							.addClass('pastlinkCreate')
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
										title: tr("Please answer the questions below"),
										buttons: answersDialogButtons,
										modal: true,
										width: $(window).width() / 2
									});
								}

								//var timestamp = '';

								function acceptPhrase() {
									/* Will integrate when timestamping works
									$.tikiModal(tr("Please wait while we process your request..."));
									$.getJSON("tiki-index.php", {
										action: "timestamp",
										hash: hash,
										page: '$page'
									}, function(json) {
										timestamp = json;
										$.tikiModal();
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

									clipboarddata.hash = md5(
										rangy.superSanitize(
											clipboarddata.author +
											clipboarddata.authorInstitution +
											clipboarddata.authorProfession
										)
									,
										rangy.superSanitize(clipboarddata.text)
									);

									me.data('rangyBusy', true);

									var pastlinkCopy = $('<div></div>');
									var pastlinkCopyButton = $('<div>' + tr('Click here to copy to clipboard') + '</div>')
										.button()
										.appendTo(pastlinkCopy);
									var pastlinkCopyValue = $('<textarea style="width: 100%; height: 80%;"></textarea>')
										.val(encodeURI(JSON.stringify(clipboarddata)))
										.appendTo(pastlinkCopy);

									pastlinkCopy.dialog({
										title: tr("Copy text and metadata"),
										modal: true,
										close: function() {
											me.data('rangyBusy', false);
											$(document).mousedown();
										},
										draggable: false
									});

									pastlinkCopyValue.select().focus();

									var clip = new ZeroClipboard.Client();
									clip.setHandCursor( true );

									clip.addEventListener('complete', function(client, text) {
										pastlinkCreate.remove();
										pastlinkCopy.dialog( "close" );
										clip.hide();
										me.data('rangyBusy', false);


										$.notify(tr('Text and metadata copied to clipboard'));
										return false;
									});

									clip.glue( pastlinkCopyButton[0] );

									clip.setText(pastlinkCopyValue.val());


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
		$headerlib = TikiLib::lib('header');

		$page = $args['object'];
		$version = $args['version'];

		$headerlib
			->add_jsfile('vendor/rangy/rangy/uncompressed/rangy-core.js')
			->add_jsfile('vendor/rangy/rangy/uncompressed/rangy-cssclassapplier.js')
			->add_jsfile('vendor/rangy/rangy/uncompressed/rangy-selectionsaverestore.js')
			->add_jsfile('lib/rangy_tiki/rangy-phraser.js')
			->add_jsfile('lib/ZeroClipboard.js')
			->add_jsfile('lib/core/JisonParser/Phraser.js')
			->add_jsfile('vendor/jquery/md5/js/md5.js');

		$me = new FutureLink_FutureUI($page);

		$phrase = (!empty($_POST['phrase']) ? $_POST['phrase'] : '');
		FutureLink_Search::goToNewestWikiRevision($version, $phrase);
        FutureLink_Search::restoreFutureLinkPhrasesInWikiPage($me->getItems(), $phrase);

		$me->editInterfaces();
		$me->createPastLinksInterface();
	}

	static function wikiSave($args)
	{
		global $groupPluginReturnAll;
		$groupPluginReturnAll = true;
		$body = TikiLib::lib('tiki')->parse_data($args['data']);
		$groupPluginReturnAll = false;

		$page = $args['object'];
		$version = $args['version'];

		$body = JisonParser_Phraser_Handler::superSanitize($body);

        (new Tracker_Query('Wiki Attributes'))
			->byName()
			->replaceItem(
				array(
					'Page' => $page,
					'Attribute' => $version,
					'Value' => $body,
					'Type' => 'FutureLink Revision'
				)
			);
	}

	function addItem($item)
	{
		parent::addItem($item);

		$exists = array();
		$verificationsCount = count($this->verifications);
		foreach ($this->verifications as &$verification) {
			foreach ($verification['reason'] as $reason) {
				if ($reason == 'exists') {
					$exists[] = true;
				}
			}
		}

        //If they were not added, but the reason is that they already exist, we show that they were sent successfully
		if (count($exists) == $verificationsCount) {
            return true;
        }

		return $this->itemsAdded;
	}

	function appendToContents(&$contents, $item)
	{
		global $prefs, $_REQUEST;

		if ($this->debug == true) {
			ini_set('error_reporting', E_ALL);
			ini_set('display_errors', 1);
		}
		$this->itemsAdded = false;

		foreach ($item->feed->entry as $i => $newEntry) {
			$this->verifications[$i] = array();
			$this->verifications[$i]["reason"] = array();

			//lets remove the new entry if it has already been accepted in the past
			foreach ($contents->entry as &$existingEntry) {
				if (
					$existingEntry->pastlink->text == $newEntry->pastlink->text &&
					$existingEntry->pastlink->href == $newEntry->pastlink->href
				) {
					$this->verifications[$i]['reason'][] = 'exists';
					unset($item->feed->entry[$i]);
				}
			}

			$revision = FutureLink_Search::findWikiRevision($newEntry->futurelink->text);

			$this->verifications[$i]["hashBy"] = JisonParser_Phraser_Handler::superSanitize(
				$newEntry->futurelink->author .
				$newEntry->futurelink->authorInstitution .
				$newEntry->futurelink->authorProfession
			);

			$this->verifications[$i]['foundRevision'] = $revision;

			$this->verifications[$i]["metadataHere"] = $this->metadata->raw;

			$this->verifications[$i]["phraseThere"] =JisonParser_Phraser_Handler::superSanitize($newEntry->futurelink->text);
			$this->verifications[$i]["hashHere"] = hash_hmac("md5", $this->verifications[$i]["hashBy"], $this->verifications[$i]["phraseThere"]);
			$this->verifications[$i]["hashThere"] = $newEntry->futurelink->hash;
			$this->verifications[$i]['exists'] = JisonParser_Phraser_Handler::hasPhrase(
				$revision['data'],
				$this->verifications[$i]["phraseThere"]
			);

			if ($this->verifications[$i]['hashHere'] != $this->verifications[$i]['hashThere']) {
				$this->verifications[$i]['reason'][] = 'hash_tampering';
				unset($item->feed->entry[$i]);
			}

			if ($newEntry->futurelink->websiteTitle != $prefs['browsertitle']) {
				$this->verifications[$i]['reason'][] = 'title';
				unset($item->feed->entry[$i]);
			}

			if ($this->verifications[$i]['exists'] == false) {
				if (empty($this->verifications[$i]['reason'])) {
					$this->verifications[$i]['reason'][] = 'no_existence_hash_pass';
				} else {
					$this->verifications[$i]['reason'][] = 'no_existence';
				}

				unset($item->feed->entry[$i]);
			}

			foreach ($newEntry->futurelink as $key => $value) {
				if (isset(FutureLink_MetadataAssembler::$acceptableKeys[$key]) && FutureLink_MetadataAssembler::$acceptableKeys[$key] == true) {
					//all clear
				} else {
					$this->verifications[$i]['reason'][] = 'metadata_tampering' . ($this->debug == true ? $key : '');
					unset($item->feed->entry[$i]);
				}
			}

			foreach ($newEntry->pastlink as $key => $value) {
				if (isset(FutureLink_MetadataAssembler::$acceptableKeys[$key]) && FutureLink_MetadataAssembler::$acceptableKeys[$key] == true) {
					//all clear
				} else {
					$this->verifications[$i]['reason'][] = 'metadata_tampering' . ($this->debug == true ? $key : '');
					unset($item->feed->entry[$i]);
				}
			}
		}

		if (empty($item->feed->entry) == false) {
			$this->itemsAdded = true;

			foreach ($item->feed->entry as &$entry) {
                (new Tracker_Query('Wiki Attributes'))
					->byName()
					->replaceItem(
						array(
							'Page' => $this->page,
							'Attribute' => '',
							'Value' => $entry->futurelink->text,
							'Type' => 'FutureLink Accepted'
						)
					);
			}

			if (empty($contents->entry) == true) {
				$contents->entry = array();
			}

			$contents->entry = array_merge($contents->entry, $item->feed->entry);
		}
	}
}
