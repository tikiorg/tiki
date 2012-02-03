<?php

class Feed_ForwardLink_Search
{
	static function goToNewestWikiRevision($version, $phrase, $pageName)
	{
		$newestRevision = self::newestWikiRevision($phrase, $pageName);

		if ($version != $newestRevision) {
			header( 'Location: ' . TikiLib::tikiUrl() . 'tiki-pagehistory.php?page=' . $pageName . '&preview=' . $newestRevision . '&nohistory') ;
			exit();
		}
	}
	
	static function newestWikiRevision($phrase, $pageName)
	{
		$match = -1;
		$i = 0;
		
		$page = end(TikiLib::fetchAll("SELECT data, version FROM tiki_pages WHERE pageName = ?", array($pageName)));
		$match = (JisonParser_Phraser_Handler::hasPhrase($phrase, $page['data']) == true ? $page['version'] : -1);
		
		if ($match == false) {
			foreach (TikiLib::fetchAll("SELECT data, version FROM tiki_history WHERE pageName = ? ORDER BY version DESC", array($pageName)) as $page) {
				$match = (JisonParser_Phraser_Handler::hasPhrase($phrase, $page['data']) == true ? $page['version'] : -1);
				
				if ($match == true) break;
			}
		}
		
		return (int)$match;
	}
}