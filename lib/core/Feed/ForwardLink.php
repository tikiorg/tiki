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

	public function name()
	{
		return $this->type . '_' . $this->name;
	}

	static function forwardLink($name)
	{
		$me = new self();
		$me->name = $name;
		return $me;
	}


	private static function getQuestionInputs($page, $itemId)
	{
		print_r(json_encode(
			Tracker_Query::tracker('Wiki Attributes')
				->byName()
				->itemId((int)$itemId)
				->inputDefaults(array(
					'Page' => $page,
					'Type' => 'Question'
				))
				->queryInput()
		));
		exit(0);
	}

	private static function getTimeStamp()
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

	private static function goToPhraseExistance($phrase, $page, $version)
	{
		session_start();
		if (!empty($phrase)) $_SESSION['phrase'] = $phrase; //prep for redirect if it happens;

		if (!empty($phrase)) Feed_ForwardLink_Search::goToNewestWikiRevision($version, $phrase, $page);

		if (!empty($_SESSION['phrase'])) { //recover from redirect if it happened
			$phrase = $_SESSION['phrase'];
			unset($_SESSION['phrase']);
		}
	}

	private static function restorePhrasesInWikiPage($page, $phrase)
	{
		global $smarty, $headerlib;
		$items = self::forwardLink($page)->getItems();
		$phrases = array();

		$phraseI = 0;
		foreach ($items as $item) {

			$thisText = addslashes(htmlspecialchars($item->forwardlink->text));
			$thisDate = addslashes(htmlspecialchars($item->forwardlink->date));
			$thisHref = addslashes(htmlspecialchars($item->textlink->href));
			$linkedText = addslashes(htmlspecialchars($item->textlink->text));

			$phrases[] = $thisText;

			if ($thisText == $phrase) {
				$headerlib->add_js(<<<JQ
				$(function() {
					$('#page-data')
						.rangyRestoreSelection('$thisText', function(r) {
							var phraseLink = $('<a>*</a>')
								.attr('href', '$thisHref')
								.attr('text', '$thisText')
								.attr('linkedText', '$linkedText')
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
						.attr('href', '$thisHref')
						.attr('text', '$thisText')
						.attr('linkedText', '$linkedText')
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
										'<td>$thisDate</td>' +
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

	static function editQuestionsInterface($page, $questions)
	{
		global $headerlib;
		$perms = Perms::get();

		//check if profile is created
		$trackerId = TikiLib::lib('trk')->get_tracker_by_name('Wiki Attributes');
		if ($trackerId < 1 && $perms->admin == 'y') {
			$headerlib->add_jq_onready(<<<JQ
				var addQuestionsButton = $('<span class="button"><a href="tiki-admin.php?profile=Simple+Wiki+Attributes&repository=&page=profiles&list=List">' + tr('Apply Profile "Simple Wiki Attributes" To Add ForwardLink Questions') + '</a></span>')
					.appendTo('#page-bar');
JQ
			);
		} else {
			$wikiPerms = Perms::get(array( 'type' => 'wiki page', 'object' => $page ));
			$trackerPerms = Perms::get(array( 'type' => 'tracker', 'object' => $page ));

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
						var itemAction = 'insert_item';

						if (itemId) {
							if (remove) {
								itemAction = 'remove_item';
							} else {
								itemAction = 'update_item';
							}
						}

						$.getJSON('?itemId=' + itemId, function(item) {
							$.modal();
							var frm = $('<form />')
								.submit(function() {
									var serialized = frm.serialize();
									$.modal(tr('Saving...'));
									var fields = '';

									$.each(frm.serializeArray(), function() {
										fields += 'fields[' + this.name + ']=' + this.value + '&';
									});

									$.post('tiki-ajax_services.php?controller=tracker&action=' + itemAction + '&' + frm.serialize() + '&itemId=' + itemId + '&trackerId=' + trackerId + '&' + fields, function() {
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
								}, true);

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

	static function createForwardLinksInterface($page, $questions, $dateLastUpdated, $language)
	{
		global $tikilib, $headerlib, $prefs;

		//setup clipboard data
		$page = urlencode($page);
		$href = TikiLib::tikiUrl() . 'tiki-index.php?page=' . $page;
		$websiteTitle = urlencode($prefs['browsertitle']);
		$dateOriginated = self::findDatePageOriginated($page);

		$answers = array();
		foreach ($questions as $question) {
			$answers[] = array(
				'question'=> strip_tags($question['Value']),
				'answer'=> '',
			);
		}

		$userData = self::findAuthorData($page);
		$moderatorData = self::findModeratorData();

		$clipboarddata = json_encode(array(
			'websiteTitle'=>            $websiteTitle,
			'websiteSubtitle'=>         $page,
			'moderator'=>               $moderatorData['Name'],
			'moderatorInstitution'=>    $moderatorData['Business Name'],
			'moderatorProfession'=>     $moderatorData['Profession'],
			'hash'=>                    '', //hash isn't yet known
			'author'=>                  $userData['Name'],
			'authorInstitution' =>      $userData['Business Name'],
			'authorProfession'=>        $userData['Profession'],
			'href'=>                    $href,
			'answers'=>                 $answers,
			'dateLastUpdated'=>         $dateLastUpdated,
			'dateLastUpdated'=>         $dateOriginated,
			'language'=>                $language
		));

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

		$page = urlencode($page);
		$href = TikiLib::tikiUrl() . 'tiki-index.php?page=' . $page;

		$websiteTitle = addslashes(htmlspecialchars($prefs['browsertitle']));

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
		$dateLastUpdated = $args['lastModif'];
		$lang = $args['lang'];

		if (isset($_REQUEST['itemId'])) {
			self::getQuestionInputs($page, $_REQUEST['itemId']);
		}

		$phrase = (!empty($_REQUEST['phrase']) ? addslashes(htmlspecialchars($_REQUEST['phrase'])) : '');

		self::goToPhraseExistance($phrase, $page, $version);

		self::restorePhrasesInWikiPage($page, $phrase);

		$questions = Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filter(array('field'=> 'Type','value'=> 'Question'))
			->filter(array('field'=> 'Page','value'=> $page))
			->query();

		self::editQuestionsInterface($page, $questions);

		//Get language, which is the description of $lang
		foreach(TikiLib::lib("tiki")->list_languages() as $listLanguage) {
			if ($listLanguage['value'] == $lang) {
				$language = $listLanguage['name'];
			}
		}

		self::createForwardLinksInterface($page, $questions, $dateLastUpdated, $language);
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
		$checks = array();

		foreach ($item->feed->entry as $i => $newEntry) {
			$checks[$i] = array();

			$checks[$i]["hashableHere"] = JisonParser_Phraser_Handler::superSanitize($prefs['browsertitle']);
			$checks[$i]["phraseThere"] =JisonParser_Phraser_Handler::superSanitize($newEntry->forwardlink->text);
			$checks[$i]["hashHere"] = hash_hmac("md5", $checks[$i]["hashableHere"], $checks[$i]["phraseThere"]);
			$checks[$i]["hashThere"] = $newEntry->forwardlink->hash;
			$checks[$i]["exists"] = JisonParser_Phraser_Handler::hasPhrase(TikiLib::lib("wiki")->get_parse($_REQUEST['page']), utf8_encode($newEntry->forwardlink->text));
			$checks[$i]["reason"] = "";
			$checks[$i]['exists'] = JisonParser_Phraser_Handler::hasPhrase(
							TikiLib::lib('wiki')->get_parse($_REQUEST['page']),
							utf8_encode($newEntry->forwardlink->text)
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
					$checks[$i]['reason'] .= '_no_existance_hash_pass_';
				} else {
					$checks[$i]['reason'] .= '_no_existance_';
				}

				unset($item->feed->entry[$i]);
			}
		}

		if ($this->debug) {
			print_r($checks);
		}

		if (count($item->feed->entry) > 0) {
			$replace = true;
			$contents->entry += $item->feed->entry;
		}

		return $replace;
	}

	static public function findAuthorData($page, $version = -1)
	{
		global $tikilib;

		if ($version < 0) {
			$user = TikiLib::getOne("SELECT user FROM tiki_pages WHERE pageName = ?", array($page));
		} else {
			$user = TikiLib::getOne("SELECT user FROM tiki_history WHERE pageName = ? AND version = ?", array($page, $version));
		}

		if (empty($user))  return array();

		$authorData = end(Tracker_Query::tracker("Users")
			->byName()
			->filter(array('field'=> 'login','value'=> $user))
			->getOne());

		if (empty($authorData['Name'])) {
			$authorData['Name'] = $tikilib->get_user_preference($user, "realName");
		}


		return $authorData;
	}

	static public function findModeratorData()
	{
		$moderatorData = end(Tracker_Query::tracker("Users")
			->byName()
			->filter(array('field'=> 'login','value'=> 'admin')) //admin is un-deletable
			->getOne());

		if (empty($authorData['Name'])) {
			$moderatorData['Name'] = TikiLib::get_user_preference('admin', "realName");
		}

		return $moderatorData;
	}

	static public function findDatePageOriginated($page)
	{
		$date = TikiLib::getOne('SELECT lastModif FROM tiki_history WHERE pageName = ? ORDER BY lastModif DESC', array($page));

		if (empty($date)) {
			//page doesn't yet have history
			$date = TikiLib::getOne('SELECT lastModif FROM tiki_pages WHERE pageName = ?', array($page));
		}

		return $date;
	}
}
