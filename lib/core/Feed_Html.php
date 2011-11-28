<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * For htmlFeed Protocol
 */
class Feed_Html
{
	var $lastModif = 0;
	
	public function updateCache()
	{
		global $feedItem, $caching, $page;
		
		$this->clearCache();
		$site = $this->siteName();
		
		$caching = true; //this variable is used to block recursive parse_data below
		
		foreach (TikiLib::lib("wiki")->get_pages_contains("{htmlfeed") as $pagesInfo) {
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
	
	private function siteName()
	{
		$site = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		$site = explode('/', $site);
		array_pop($site);
		$site = implode($site, '/');
		return $site;
	}
	
	public function getItems()
	{
		$cache = $this->getCache();
		return $cache->entry;
	}
	
	public function listItemNames()
	{
		$result = array();
		foreach ($this->getItems() as $item) {
			if (!empty($item->name)) {
				$result[] = htmlspecialchars($item->name);
			}
		}
		return $result;
	}
	
	public function getItem($name)
	{
		foreach ($this->getItems() as $item) {
			if ($name == $item->name) {
				return $item;
			}
		}
		return array();
	}
	
	private function getCache()
	{
		global $tikilib;
		$cache = TikiLib::lib("cache")->getCached($this->siteName(), "htmlfeed");

		//if ($cache) return $cache;
		
		$this->updateCache();
		
		return TikiLib::lib("cache")->getCached($this->siteName(), "htmlfeed");
	}
	
	private function clearCache()
	{
		global $tikilib;
		TikiLib::lib("cache")->empty_type_cache("htmlfeed");
	}
	
	private function appendToCache($item)
	{
		global $tikilib;
		$cache = TikiLib::lib("cache")->getCached($this->siteName(), "htmlfeed");
		
		if (empty($cache)) {
			$cache = (object)array(
				'date' => 0,
				'type' => 'htmlfeed',
				'entry' => array()
			);
		} else {
			$cache = json_decode($cache);
		}
		
		$cache->date = ($cache->date > $item['date'] ? $cache->date : $item['date']);
		
		$cache->entry[] = $item;
		
		TikiLib::lib("cache")->cacheItem($this->siteName(), json_encode($cache), "htmlfeed");
	}
	
	public function addSimpleItem($feedItem)
	{
		$this->appendToCache($feedItem);
	}
	
	public function feed()
	{
		$feed = json_decode($this->getCache());
		
		return array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => $feed,
		);
	}
}
