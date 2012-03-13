<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
 
abstract class Feed_Abstract
{
	var $name = "";
	var $items = array();
	var $item = array();
	var $contents = array();
	var $type = "";
	var $isFileGal = false;
	
	function __construct($name = "")
	{
		$this->name($name);
	}
	
	public function name($name = "")
	{
		if (empty($name)) {
			$name = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			$name = explode('/', $name);
			array_pop($name);
			$name = implode($name, '/');
		} else {
			$name = str_replace("http://", "", $name);
			$name = str_replace("https://", "", $name);
			$name = array_shift(explode('/', $name));
		}
		
		$this->name = $name;
		
		return $this;
	}
	
	public function getItems()
	{
		$contents = $this->getContents();
		
		if (empty($contents->entry)) return array();
		
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
			$contents = FileGallery_File::filename($this->name)->data();
		} else {
			$contents = TikiLib::lib("cache")->getCached($this->name, $this->type);
		}
		
		$contents = json_decode($contents);
		if (empty($contents)) return array();
		return $contents;
	}
	
	private function save($contents)
	{
		$contents = json_encode($contents);
		
		if ($this->isFileGal == true) {
			FileGallery_File::filename($this->name)
				->setParam('description', '')
				->replace($contents);
			
		} else {
			TikiLib::lib("cache")->cacheItem($this->name, $contents, $this->type);
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
			FileGallery_File::filename($this->name)->delete();
		} else {
			TikiLib::lib("cache")->empty_type_cache($this->type);
		}
	}
	
	function appendToContents(&$contents, $items)
	{	
		$contents->entry[] = $item->feed->entry;
	}
	
	public function addItem($item)
	{
		global $tikilib;
		$contents = $this->open();		
		
		if (empty($contents)) {
			$contents = (object)array(
				'date' => 0,
				'type' => $this->type,
				'entry' => array()
			);
		}
		
		$item = (object)$item;
		$contents->entry[] = $item;
		$this->save($contents);
		
		return $this;
	}
	
	public function feed()
	{
		global $tikilib;
		return (object)array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => $this->getContents(),
			'origin' => $tikilib->tikiUrl() . 'tiki-feed.php'
		);
	}
	
	public function listArchives()
	{
		$archives = array();
		
		if ($this->isFileGal == true) {
			foreach ($file->listArchives() as $archive) {
				$archive = $this->open();
				$archives[$archive->feed->date] = $archive->feed->entry;
			}
		}
		
		return $archives;
	}
	
	public function getItemsFromDate($date)
	{
		$archives = $this->listArchives();
		$archive = $archives[$date];
		return $archive;
	}
	
	public function getItemFromDate($name, $date)
	{
		$archive = $this->getItemsFromDate($date);
		foreach ($archive as $item) {
			if ($name == $item->name) {
				return $item;
			}
		}
	}
	
	public function getItemFromDates($name)
	{
		$archives = array();

		foreach ($this->listArchives() as $archive) {
			foreach ($archive as $item) {
				if ($name == $item->name) {
					$archives[] = $item;
				}
			}
		}
		
		return $archives;
	}
}
