<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Request.php 36391 2011-08-22 20:26:16Z changi67 $

/**
 * For htmlFeed Protocol
 */
class HtmlFeed
{		
	public function getLinks()
	{
		global $tikilib,$page,$cachebuild;
		
		$cachebuild = true;
		
		$links = array();
		$this->clearCache();
		
		$parserlib = TikiLib::lib("parser");
		foreach(TikiLib::lib("wiki")->get_pages_contains("{htmlfeed") as $pagesInfo) {
			foreach($pagesInfo as $pageInfo) {
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
	
	public function addSimpleLink($parentName, $name, $description, $lastModif, $author)
	{
		$this->appendToCache(
			$parentName,
			HtmlFeed_Item::simplePage(array(
				"origin" 		=> "",
				"name" 			=> $name,
				"title" 		=> "",
				"description" 	=> $description,
				"lastModif" 	=> $lastModif,
				"author" 		=> $author,
				"hits"			=> "",
				"unusual"		=> "",
				"importance" 	=> "",
				"keywords"		=> "",
				"type" 			=> "simple"
			)
		));
	}
	
	public function feed()
	{
		return array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => array (
				'type' => 'htmlfeed',
				'entry' => $this->getLinks(),
			)
		);
	}
}