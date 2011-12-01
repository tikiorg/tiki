<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
 
abstract class Feed_Abstract
{	
	public function siteName()
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
	
	public function getCache()
	{
		global $tikilib;
		$cache = TikiLib::lib("cache")->getCached($this->siteName(), $this->type);

		if ($cache) return $cache;
		
		$this->updateCache();
		
		return TikiLib::lib("cache")->getCached($this->siteName(), $this->type);
	}
	
	public function clearCache()
	{
		global $tikilib;
		TikiLib::lib("cache")->empty_type_cache($this->type);
	}
	
	public function appendToCache($item)
	{
		global $tikilib;
		$cache = TikiLib::lib("cache")->getCached($this->siteName(), $this->type);
		
		if (empty($cache)) {
			$cache = (object)array(
				'date' => 0,
				'type' => $this->type,
				'entry' => array()
			);
		} else {
			$cache = json_decode($cache);
		}
		
		$cache->date = ($cache->date > $item['date'] ? $cache->date : $item['date']);
		
		$cache->entry[] = $item;
		
		TikiLib::lib("cache")->cacheItem($this->siteName(), json_encode($cache), $this->type);
	}
	
	public function addItem($feedItem)
	{
		$this->appendToCache($feedItem);
	}
	
	public function feed()
	{
		global $tikilib;
		$feed = json_decode($this->getCache());
		
		return array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => $feed,
			'origin' => $tikilib->tikiUrl() . 'tiki-feed.php'
		);
	}
}