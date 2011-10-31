<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * For htmlFeed Protocol
 */
class HtmlFeed
{
	var $lastModif = 0;
	
	public function updateCache()
	{
		global $tikilib,$page,$cachebuild, $htmlFeedUrl, $lastModif;
		$cachebuild = true;
		
		$links = array();
		$this->clearCache();
		$site = $this->siteName();
		$parserlib = TikiLib::lib("parser");
		foreach(TikiLib::lib("wiki")->get_pages_contains("{htmlfeed") as $pagesInfo) {
			foreach($pagesInfo as $pageInfo) {
				
				$lastModif = $pageInfo['lastModif'];
				
				$page = $pageInfo['pageName'];
				
				$htmlFeedUrl = $site . "/tiki-index.php?page=" . urlencode($page);
				
				$parserlib->parse_data($pageInfo['data']);
			}
		}
		
		$cachebuild = false;
	}
	
	private function siteName()
	{
		$site = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		$site = explode('/', $site);
		array_pop($site);
		$site = implode($site, '/');
		return $site;
	}
	
	public function getLinks()
	{
		$cache = $this->getCache();
		return $cache->entry;
	}
	
	public function listLinkNames()
	{
		$result = array();
		foreach($this->getLinks() as $link) {
			if (!empty($link->name)) {
				$result[] = htmlspecialchars($link->name);
			}
		}
		return $result;
	}
	
	public function getLink($name)
	{
		foreach($this->getLinks() as $item) {
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
		
		if ($cache) return $cache;
		
		return $this->updateCache();
	}
	
	private function clearCache()
	{
		global $tikilib;
		TikiLib::lib("cache")->empty_type_cache("htmlfeed");
	}
	
	private function appendToCache($link)
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
		
		$cache->date = ($cache->date > $link['date'] ? $cache->date : $link['date']);
		
		$cache->entry[] = $link;
		
		TikiLib::lib("cache")->cacheItem($this->siteName(), json_encode($cache), "htmlfeed");
	}
	
	public function addSimpleLink($name, $description, $lastModif, $author, $url)
	{
		$this->appendToCache(
			HtmlFeed_Item::simplePage(array(
				"origin" 		=> "",
				"name" 			=> $name,
				"title" 		=> "",
				"description" 	=> $description,
				"date" 			=> (int)$lastModif,
				"author" 		=> $author,
				"hits"			=> "",
				"unusual"		=> "",
				"importance" 	=> "",
				"keywords"		=> "",
				"type" 			=> "simple",
				"url"			=> $url
			)
		));
	}
	
	public function feed()
	{
		$feed = json_decode( $this->getCache() );
		
		return array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => $feed,
		);
	}
}