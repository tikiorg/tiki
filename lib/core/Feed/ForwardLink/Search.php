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
		$newestRevision = self::newestWikiRevision($phrase, $page);

		if ($newestRevision < 1) {
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

		if ($version != $newestRevision) {
			header('Location: ' . TikiLib::tikiUrl() . 'tiki-pagehistory.php?page=' . $page . '&preview=' . $newestRevision . '&nohistory');
			exit();
		}
	}
	
	static function newestWikiRevision($phrase, $page)
	{
		$match = -1;
		$i = 0;
		
		$page = end(TikiLib::fetchAll("SELECT data, version FROM tiki_pages WHERE pageName = ?", array($page)));
		
		$page['data'] = TikiLib::lib("parser")->parse_data($page['data']);
		
		$match = (JisonParser_Phraser_Handler::hasPhrase($page['data'], $phrase) == true ? $page['version'] : -1);
		
		if ($match < 0) {
			foreach (TikiLib::fetchAll("SELECT data, version FROM tiki_history WHERE pageName = ? ORDER BY version DESC", array($page)) as $page) {
				$match = (JisonParser_Phraser_Handler::hasPhrase($page['data'], $phrase) == true ? $page['version'] : -1);
				
				if ($match > -1) break;
			}
		}
		
		return (int)$match;
	}
}
