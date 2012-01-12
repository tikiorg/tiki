<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Feed_Remote_Abstract
{
	var $feedHref = "";
	var $feedName = "";
	var $name = "";
	var $items = array();
	var $item = array();
	var $contents = array();
	var $lastModif = 0;
	var $date = 0;
	var $type = "";
	
	public function siteName()
	{
		$siteName = $this->feedHref;
		
		$siteName = str_replace("http://", "", $siteName);
		$siteName = str_replace("https://", "", $siteName);
		$siteName = array_shift(explode('/', $siteName));
		
		return $siteName;
	}
	
	public function __construct($feedHref = "")
	{
		$this->feedHref = $feedHref;
		$this->feedName = $this->siteName() . "_" . $this->type;
	}
	
	public function replace()
	{
		$file = FileGallery_File::filename($this->feedName);
		$new = json_decode($this->getContents());
		$old = json_decode( $file->data() );
		
		if ($old->feed->date < $new->feed->date || $file->exists() == false) {
			return $file
				->setParam("description", "A " . $this->type . " feed from " . $this->feedHref)
				->replace($this->getContents());
		}
	}
	
	public function listArchives()
	{
		$archives = array();
		$file = FileGallery_File::filename($this->feedName);
		
		foreach($file->listArchives() as $archive) {
			$archive = json_decode( FileGallery_File::id($archive['id'])->data() );
			$archives[$archive->feed->date] = $archive->feed->entry;
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
		foreach($archive as $item) {
			if ($name == $item->name) {
				return $item;
			}
		}
	}
	
	public function getItemFromDates($name)
	{
		$archives = array();

		foreach($this->listArchives() as $archive) {
			foreach($archive as $item) {
				if ($name == $item->name) {
					$archives[] = $item;
				}
			}
		}
		
		return $archives;
	}
	
	public function setContents($contents)
	{
		$this->contents = $contents;
		return $this;
	}
	
	public function getContents($fromFile = false)
	{
		if ($fromFile == true) {
			$this->contents = FileGallery_File::filename($this->feedName)->data();
		}
		
		if (!empty($this->contents)) {
			return $this->contents;
		} else {
			$this->contents = file_get_contents($this->feedHref);
			return $this->contents;
		}
	}
	
	public function getItems()
	{
		global $tikilib;
		
		if (!empty($this->items)) return $this->items;
		
		$contents = json_decode($this->getContents());
		
		//if (!empty($contents->feed->entry) && $contents->feed->type == $this->type)
		//{
			$this->items = $contents->feed->entry;
			$this->replace();
		//}
		
		return $this->items;
	}
	
	public function listItemNames()
	{
		$result = array();
		foreach($this->getItems() as $item) {
			if (!empty($item->name)) {
				$result[] = htmlspecialchars($item->name);
			}
		}
		return $result;
	}
	
	public function getItem($name)
	{	
		foreach($this->getItems() as $item) {
			if ($name == $item->name) {
				$this->item = $item;
			}
		}
		return $this->item;
	}
}