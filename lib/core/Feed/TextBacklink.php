<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Item.php 39018 2011-11-28 15:48:42Z robertplummer $

//TextBack-Link Protocol Feed

Class Feed_Tbp extends Feed_Abstract
{
	var $type = "tbpfeed";
	
	public function updateCache()
	{
		global $feedItem, $caching, $page;
		
		$this->clearCache();
		$site = $this->siteName();
		
		$caching = true; //this variable is used to block recursive parse_data below
		
		foreach (TikiLib::lib("wiki")->get_pages_contains("{tbpfeed") as $pagesInfo) {
			foreach ($pagesInfo as $pageInfo) {
				$feedItem = Feed_Tbp_Item::simple(
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
									"url"			=> $site . "/tiki-pagehistory.php?" .
											"page=" . urlencode($page) .'&'. 
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