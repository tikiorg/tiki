<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Feed_Html extends Feed_Abstract
{
	public $lastModif = 0;
	public $type = "html_feed";
	public $version = 0.1;

	public function replace()
	{
		global $feedItem, $caching, $page;

		$this->delete();

		$caching = true; //this variable is used to block recursive parse_data below
		foreach (TikiLib::lib("wiki")->get_pages_contains("{htmlfeed") as $pagesInfo) {
			foreach ($pagesInfo as $pageInfo) {
				$feedItem = (object)array(
					"origin" 		=> $this->name,
					"name" 			=> $pageInfo['pageName'],
					"title" 		=> $pageInfo['pageName'],
					"data" 			=> "",
					"date" 			=> (int)$pageInfo['lastModif'],
					"author" 		=> $pageInfo['user'],
					"hits"			=> $pageInfo['hits'],
					"importance" 	=> $pageInfo['pageRank'],
					"keywords"		=> $pageInfo['keywords'],
					"href"			=> $this->name . "/tiki-index.php?page=" . urlencode($pageInfo['pageName'])
				);

				TikiLib::lib("parser")->parse_data($pageInfo['data']);

				unset($feedItem);
			}
		}

		$caching = false;
	}

	function appendToContents(&$contents, $item)
	{
		$contents->entry[] = $item;
	}
}
