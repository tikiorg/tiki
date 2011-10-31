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
	
	public function getLinks()
	{
		global $tikilib,$page,$cachebuild, $htmlFeedUrl, $lastModif;
		
		$cachebuild = true;
		
		$links = array();
		$this->clearCache();
		$site = $this->siteName();
		$htmlFeedUrl = $site . "/tiki-index.php?page=" . urlencode($page);
		
		$parserlib = TikiLib::lib("parser");
		foreach(TikiLib::lib("wiki")->get_pages_contains("{htmlfeed") as $pagesInfo) {
			foreach($pagesInfo as $pageInfo) {
				$lastModif = $pageInfo['lastModif'];
				$this->updateLastModif($lastModif);
				
				$page = $pageInfo['pageName'];
				$parserlib->parse_data($pageInfo['data']);
				
				$cache = $this->getCache($page);
				if (!empty($cache)) {
					$cache = json_decode($cache);
					foreach($cache as $item) {
						$links[] = $item;
					}
				}
			}
		}
		
		$cachebuild = false;
		return $links;
	}
	
	private function updateLastModif($lastModif = 0)
	{
		if ($lastModif > $this->lastModif) $this->lastModif = $lastModif;
	}
	
	private function siteName()
	{
		$site = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		$site = explode('/', $site);
		array_pop($site);
		$site = implode($site, '/');
		return $site;
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
	
	private function getCache($parentName)
	{
		global $tikilib;
		return TikiLib::lib("cache")->getCached($parentName, "htmlfeed");
	}
	
	private function clearCache()
	{
		global $tikilib;
		TikiLib::lib("cache")->empty_type_cache("htmlfeed");
	}
	
	private function appendToCache($parentName, $link)
	{
		global $tikilib;
		$cache = TikiLib::lib("cache")->getCached($parentName, "htmlfeed");
		
		if (empty($cache)) {
			$cache = array();
		} else {
			$cache = json_decode($cache);
		}
		
		$cache[] = $link;
		
		TikiLib::lib("cache")->cacheItem($parentName, json_encode($cache), "htmlfeed");
	}
	
	public function addSimpleLink($parentName, $name, $description, $lastModif, $author, $url)
	{
		$this->appendToCache(
			$parentName,
			HtmlFeed_Item::simplePage(array(
				"origin" 		=> "",
				"name" 			=> $name,
				"title" 		=> "",
				"description" 	=> $description,
				"date" 			=> $lastModif,
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
		$links = $this->getLinks();
		
		return array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => array (
				'date' => $this->lastModif,
				'type' => 'htmlfeed',
				'entry' => $links,
			)
		);
	}
}