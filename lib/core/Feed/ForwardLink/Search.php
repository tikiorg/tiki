<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Feed_ForwardLink_Search
{
	var $type = "forwardlink";
	var $version = "0.1";
	
	static function goToNewestWikiRevision($version, $phrase, $page)
	{
		$newestRevision = self::findWikiRevision($phrase, $page);

		if ($newestRevision['version'] < 1) {
			TikiLib::lib("header")->add_jq_onready(<<<JQ
				$('<div />')
					.html(
						tr('This can happen if the page you are linking to has changed since you obtained the forwardlink or if the page is not viewable by the public.') +
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
			header('Location: ' . TikiLib::tikiUrl() . 'tiki-pagehistory.php?page=' . $page . '&preview=' . $newestRevision['version'] . '&nohistory');
			exit();
		}
	}
	
	static function findWikiRevision($phrase, $page, $findLast = false)
	{
		$page = end(TikiLib::fetchAll("SELECT * FROM tiki_pages WHERE pageName = ?", array($page)));
		
		$page['data'] = TikiLib::lib("parser")->parse_data($page['data']);

		$pageMatch = (JisonParser_Phraser_Handler::hasPhrase($page['data'], $phrase) == true ? $page : array("version" => -1));

		$foundExistence = false;

		if ($pageMatch['version'] < 0 || $findLast == true) {
			foreach (TikiLib::fetchAll("SELECT * FROM tiki_history WHERE pageName = ? ORDER BY version DESC", array($page)) as $page) {
				$hasPhrase = JisonParser_Phraser_Handler::hasPhrase($page['data'], $phrase);

				//In this case we are trying to find the first occurance of the phrase, useful for finding the phrase when it last existed (rev for redirect)
				if ($hasPhrase == true && $foundExistence == false) {
					$foundExistence = true;
					$pageMatch = $page;
					break;
				}

				//In this case we are trying to find the last occurance of the phrase, useful for finding the phrase when it first existed (author etc)
				if ($hasPhrase == false && $foundExistence == true && $findLast == true) {
					$pageMatch = $page;
					break;
				}

			}
		}

		return $pageMatch;
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
}
