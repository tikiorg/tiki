<?php

class Feed_ForwardLink_Search
{
	static function goToNewestWikiRevision($version, $phrase, $page)
	{
		$newestRevision = self::newestWikiRevision($phrase, $page);
		
		if ($version != $newestRevision) {
			header( 'Location: ' . TikiLib::tikiUrl() . 'tiki-pagehistory.php?page=' . $page . '&preview=' . $newestRevision . '&nohistory') ;
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