<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_TextBacklink extends Feed_Abstract
{
	var $type = "feed_textbacklink";
	
	public function replace()
	{
		global $tikilib, $feedItem, $caching;
		
		$this->delete();
		$site = $tikilib->tikiUrl();
		
		$caching = true; //this variable is used to block recursive parse_data below
		
		foreach (TikiLib::lib("wiki")->get_pages_contains("{textbacklink") as $pagesInfo) {
			foreach ($pagesInfo as $pageInfo) {
				$feedItem = Feed_Html_Item::simple(
								array(
									"origin" 		=> $site,
									"name" 			=> $pageInfo['pageName'],
									"title" 		=> $pageInfo['pageName'],
									"description" 	=> $description,
									"date" 			=> (int)$pageInfo['lastModif'],
									"author" 		=> $pageInfo['user'],
									"hits"			=> $pageInfo['hits'],
									"unusual"		=> "",
									"importance" 	=> $pageInfo['pageRank'],
									"keywords"		=> $pageInfo['keywords'],
									"href"			=> $tikilib->tikiUrl() . "tiki-pagehistory.php?" .
											"page=" . $pageInfo['pageName'] .'&'.
											"preview_date=" . (int)$pageInfo['lastModif'] . "&" .
											"nohistory"
								)
				);
				
				TikiLib::lib("parser")->parse_data($pageInfo['data']);
				
				unset($feedItem);
			}
		}
		
		$caching = false;
	}
}