<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
 
abstract class Feed_Abstract
{
	var $isFileGal = false;
		
	public function siteName()
	{
		$site = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		$site = explode('/', $site);
		array_pop($site);
		$site = implode($site, '/');
		return $site;
	}
	
	public function name()
	{
		return $this->siteName();
	}
	
	public function getItems()
	{
		$contents = json_decode($this->getContents());
		return $contents->entry;
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
	
	public function replace()
	{
		
	}
	
	public function getContents()
	{
		global $tikilib;
		if ($this->isFileGal == true) {
			$contents = FileGallery_File::filename($this->name())->data();
		} else {
			$contents = TikiLib::lib("cache")->getCached($this->name(), $this->type);
		}

		if (!empty($contents)) return $contents;
		
		//at this point contents is empty, so lets fill it
		$this->replace();
		
		if ($this->isFileGal == true) {
			$contents = FileGallery_File::filename($this->name())->data();
		} else {
			$contents = TikiLib::lib("cache")->getCached($this->name(), $this->type);
		}
		
		return $contents;
	}
	
	public function delete()
	{
		global $tikilib;
		
		if ($this->isFileGal == true) {
			FileGallery_File::filename($this->name())->delete();
		} else {
			TikiLib::lib("cache")->empty_type_cache($this->type);
		}
	}
	
	public function addItem($item)
	{
		global $tikilib;
		$replace = false;
		
		if ($this->isFileGal == true) {
			$contents = FileGallery_File::filename($this->name())->data();
		} else {
			$contents = TikiLib::lib("cache")->getCached($this->name(), $this->type);
		}		
		
		if (empty($contents)) {
			$contents = (object)array(
				'date' => 0,
				'type' => $this->type,
				'entry' => array()
			);
		} else {
			$contents = json_decode($contents);
		}
		
		$item = (object)$item;
		
		if (isset($item->date)) {
			if ($contents->date < $item->date) {
				$contents->date = $item->date;
				$replace = true;
			}
		}
		
		$contents->entry[] = $item;
		
		if ($replace == false) return;
		
		if ($this->isFileGal == true) {
			FileGallery_File::filename($this->name())
				->setParam('description', '')
				->replace(json_encode($contents));
			
		} else {
			TikiLib::lib("cache")->cacheItem($this->name(), json_encode($contents), $this->type);
		}
	}
	
	public function feed()
	{
		global $tikilib;
		$feed = json_decode($this->getContents());
		
		return array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => $feed,
			'origin' => $tikilib->tikiUrl() . 'tiki-feed.php'
		);
	}
}