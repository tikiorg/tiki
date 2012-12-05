<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Feed_ForwardLink_Search
{
	var $type = "forwardlink";
	var $version = 0.1;
	var $page = '';

	function __construct($page)
	{
		$this->page = $page;
		parent::__construct($page);
	}

	static function goToNewestWikiRevision($version, &$phrase)
	{
		if (!isset($_SESSION)) {
			session_start();
		}

		if (!empty($_SESSION['phrase'])) { //recover from redirect if it happened
			$phrase = $_SESSION['phrase'];
			unset($_SESSION['phrase']);
			return;
		}

		if (empty($phrase)) return;

		$newestRevision = self::findWikiRevision($phrase);

		if ($newestRevision == false) {
			TikiLib::lib("header")->add_jq_onready(
<<<JQ
				$('<div />')
					.html(
						tr('This can happen if the page you are linking to has changed since you obtained the forwardlink or if the rights to see it are different that what you have set at the moment.') +
						'&nbsp;&nbsp;' +
						tr('If you are logged in, try loggin out and then recreate the forwardlink.')
					)
					.dialog({
						title: tr('Phrase not found'),
						modal: true
					});
JQ
			);
			return;
		}

		if ($version != $newestRevision['version']) {
			$_SESSION['phrase'] = $phrase; //prep for redirect if it happens;

			header('Location: ' . TikiLib::tikiUrl() . 'tiki-pagehistory.php?page=' . $newestRevision['page'] . '&preview=' . $newestRevision['version'] . '&nohistory');
			exit();
		}
	}

	static function findWikiRevision($phrase)
	{
		global $tikilib;

		$phrase = JisonParser_Phraser_Handler::superSanitize($phrase);

		$query = Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->filterFieldByValueLike('Value', $phrase)
			->render(false)
			->getLast();

		if (empty($query)) return false; //couldn't find it

		$query = end($query); //query has a key of itemId, we just need it's details
		$version = $query['Attribute'];
		$page = $query['Page'];
		$data = $query['Value'];

		$date = $tikilib->getOne('SELECT lastModif FROM tiki_pages WHERE pageName = ? AND version = ?', array($page, $version));

		if (empty($date) == true) $date = $tikilib->getOne('SELECT lastModif FROM tiki_history WHERE pageName = ? AND version = ?', array($page, $version));

		return array(
			'page' => $page,
			'version' => $version,
			'data' => $data,
			'date' => $date,
			'phrase' => $phrase
		);
	}


	static function restoreForwardLinkPhrasesInWikiPage($items, $phrase = "")
	{

		global $tikilib, $headerlib, $smarty;
		$phrase = JisonParser_Phraser_Handler::superSanitize($phrase);
		$phrases = array();
		$phraseMatchIndex = -1;

		$parsed = $smarty->getTemplateVars('parsed');
		if (empty($parsed)) {
			$parsed = $smarty->getTemplateVars('previewd');
		}

		foreach ($items as $i => $item) {
			if (!empty($item->textlink->href)) {
				if (JisonParser_Phraser_Handler::hasPhrase($parsed, $item->forwardlink->text) != true) {
					continue;
				}

				$phrases[] = $item->forwardlink->text;

				$i = count($phrases) - 1;

				if (JisonParser_Phraser_Handler::superSanitize($phrase) == JisonParser_Phraser_Handler::superSanitize($item->forwardlink->text)) {
					$phraseMatchIndex = $i;
				}

				$item->forwardlink->dateLastUpdated = $tikilib->get_short_datetime($item->forwardlink->dateLastUpdated);
				$item->forwardlink->dateOriginated = $tikilib->get_short_datetime($item->forwardlink->dateOriginated);
				$item->textlink->dateLastUpdated = $tikilib->get_short_datetime($item->textlink->dateLastUpdated);
				$item->textlink->dateOriginated = $tikilib->get_short_datetime($item->textlink->dateOriginated);

				$headerlib->add_jq_onready(
					"var phrase = $('span.forwardlinkMiddle".$i."')
						.addClass('ui-state-highlight');

					var phraseLink = $('<a><sup>&</sup></a>')
						.data('metadataHere', " . json_encode($item->forwardlink) . ")
						.data('metadataThere', " . json_encode($item->textlink) . ")
						.addClass('forwardlinkA')
						.insertBefore(phrase.first());"
				);
			}
		}

		$phraser = new JisonParser_Phraser_Handler();
		$phraser->setCssWordClasses(
			array(
				'start'=>'forwardlinkStart',
				'middle'=>'forwardlinkMiddle',
				'end'=>'forwardlinkEnd'
			)
		);

		if ($phraseMatchIndex > -1) {
			$headerlib->add_jq_onready(
				"var selection = $('span.forwardlinkStart". $phraseMatchIndex.",span.forwardlinkEnd".$phraseMatchIndex."').realHighlight();

				$('body,html').animate({
					scrollTop: selection.first().offset().top - 10
				});"
			);
		}

		self::restorePhrasesInWikiPage($phraser, $phrases);
	}

	static function restoreTextLinkPhrasesInWikiPage($items, $phrase = "")
	{
		global $tikilib, $headerlib, $smarty;
		$phrase = JisonParser_Phraser_Handler::superSanitize($phrase);
		$phrases = array();
		$phraseMatchIndex = -1;

		$parsed = $smarty->getTemplateVars('parsed');
		if (empty($parsed)) {
			$parsed = $smarty->getTemplateVars('previewd');
		}

		foreach ($items as &$item) {
			if (!empty($item->forwardlink->href)) {
				if (JisonParser_Phraser_Handler::hasPhrase($parsed, $item->textlink->text) != true) {
					continue;
				}

				$phrases[] = $item->textlink->text;

				$i = count($phrases) - 1;

				if (JisonParser_Phraser_Handler::superSanitize($phrase) == JisonParser_Phraser_Handler::superSanitize($item->textlink->text)) {
					$phraseMatchIndex = $i;
				}

				$item->forwardlink->dateLastUpdated = $tikilib->get_short_datetime($item->forwardlink->dateLastUpdated);
				$item->forwardlink->dateOriginated = $tikilib->get_short_datetime($item->forwardlink->dateOriginated);
				$item->textlink->dateLastUpdated = $tikilib->get_short_datetime($item->textlink->dateLastUpdated);
				$item->textlink->dateOriginated = $tikilib->get_short_datetime($item->textlink->dateOriginated);

				$headerlib->add_jq_onready(
					"var phrase = $('span.textlinkMiddle".$i."')
						.addClass('ui-state-highlight');

					var phraseLink = $('<a><sup>&</sup></a>')
						.data('metadataHere', " . json_encode($item->textlink) . ")
						.data('metadataThere', " . json_encode($item->forwardlink) . ")
						.addClass('textlinkA')
						.insertAfter(phrase.last());"
				);
			}
		}

		$phraser = new JisonParser_Phraser_Handler();

		$phraser->setCssWordClasses(
			array(
				'start'=>'textlinkStart',
				'middle'=>'textlinkMiddle',
				'end'=>'textlinkEnd'
			)
		);

		if ($phraseMatchIndex > -1) {
			$headerlib->add_jq_onready(
				"var selection = $('span.textlinkStart".$phraseMatchIndex.",span.textlinkEnd".$phraseMatchIndex."').realHighlight();

				$('body,html').animate({
					scrollTop: selection.first().offset().top - 10
				});"
			);
		}

		self::restorePhrasesInWikiPage($phraser, $phrases);
	}

	static function restorePhrasesInWikiPage(JisonParser_Phraser_Handler $phraser, $phrases)
	{
		global $headerlib, $smarty;

		$headerlib
			->add_jsfile('lib/jquery/tablesorter/jquery.tablesorter.js')
			->add_cssfile('lib/jquery_tiki/tablesorter/themes/tiki/style.css')
			->add_jq_onready(
<<<JQ
				$('a.forwardlinkA,a.textlinkA')
					.css('cursor', 'pointer')
					.click(function() {
						var me = $(this),
						metadataHere = me.data('metadataHere'),
						metadataThere = me.data('metadataThere');

						var table = $('<table class="tablesorter" style="width: 100%;"/>');
						var thead = $('<thead><tr /></thead>').appendTo(table);
						var tbody = $('<tbody><tr /></tbody>').appendTo(table);

						function a(head, body) {
							$('<th />').text(head).appendTo(thead.find('tr'));

							$('<td />').html(body).appendTo(tbody.find('tr'));
						}

						a(tr('Sentence text'), metadataThere.text);
						a(tr('Date Created'), metadataThere.dateOriginated);
						a(tr('Date Updated Here'), metadataHere.dateLastUpdated);
						a(tr('Date Updated There'), metadataThere.dateLastUpdated);
						a(tr('Click below to read Citing blocks'), '<input type="submit" value="' + tr('Read') + '" />');

						var form = $('<form method="POST" />')
							.attr('action', metadataThere.href)
							.append($('<input type="hidden" name="phrase" />').val(metadataThere.text))
							.append(table)
							.dialog({
								title: tr('Linked to: ') + metadataHere.text,
								modal: true,
								width: $(window).width() * 0.8
							});

						return false;
					});
JQ
				,
				100
			);

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
}
