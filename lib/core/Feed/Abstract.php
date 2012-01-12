<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
		$contents = $this->getContents();
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
	
	private function open()
	{
		if ($this->isFileGal == true) {
			$contents = FileGallery_File::filename($this->name())->data();
		} else {
			$contents = TikiLib::lib("cache")->getCached($this->name(), $this->type);
		}
		
		return json_decode($contents);
	}
	
	private function save($contents)
	{
		$contents = json_encode($contents);
		
		if ($this->isFileGal == true) {
			FileGallery_File::filename($this->name())
				->setParam('description', '')
				->replace($contents);
			
		} else {
			TikiLib::lib("cache")->cacheItem($this->name(), $contents, $this->type);
		}
		
		return $this;
	}
	
	public function getContents()
	{
		global $tikilib;
		$contents = $this->open();

		if (!empty($contents)) return $contents;
		
		//at this point contents is empty, so lets fill it
		$this->replace();
		
		$contents = $this->open();
		
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
	
	function appendToContents(&$contents, $items)
	{
		$replace = false;
		
		if (isset($item->date)) {
			if ($contents->date < $item->date) {
				$contents->date = $item->date;
				$replace = true;
			}
		}
		
		$contents->entry[] = $item->feed->entry;
		
		return $replace;
	}
	
	public function addItem($item)
	{
		global $tikilib;
		$replace = false;
		
		$contents = $this->open();		
		
		if (empty($contents)) {
			$contents = (object)array(
				'date' => 0,
				'type' => $this->type,
				'entry' => array()
			);
		}
		
		$item = (object)$item;
		
		if ($this->appendToContents($contents, $item) == false) return $this;
		
		$this->save($contents);
		
		return $this;
	}
	
	public function feed()
	{
		global $tikilib;
		$feed = $this->getContents();
		
		return (object)array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => $feed,
			'origin' => $tikilib->tikiUrl() . 'tiki-feed.php'
		);
	}
}