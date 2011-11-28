<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * For HtmlFeed_Remote Protocol
 */
class Feed_Remote_Abstract
{
	var $feedUrl = "";
	var $feedName = "";
	var $name = "";
	var $items = array();
	var $item = array();
	var $contents = array();
	var $lastModif = 0;
	var $date = 0;
	var $type = "";
	
	static function url($feedUrl)
	{
		$me = new self($feedUrl);
		
		return $me;
	}
	
	public function siteName()
	{
		$siteName = $this->feedUrl;
		
		$siteName = str_replace("http://", "", $siteName);
		$siteName = str_replace("https://", "", $siteName);
		$siteName = array_shift(explode('/', $siteName));
		
		return $siteName;
	}
	
	public function __construct($feedUrl = "")
	{
		$this->feedUrl = $feedUrl;
		$this->feedName = $this->siteName()."_feed_remote_" . $this->type;
	}
	
	private function replace()
	{
		$file = FileGallery_File::filename($this->feedName);
		
		$new = $this->contents;
		$old = json_decode($file->data());
		
		if (!$file->exists()) {
			$file
				->setParam("filename", $this->feedName)
				->setParam("description", tr("A remote " . $this->type . " feed from ") . $this->feedUrl)
				->create(json_encode($this->contents));
				
			return;
		}
		
		if ($old->feed->date < $new->feed->date) {
			$file->replace(json_encode($this->contents));	
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
	
	public function getItems()
	{
		global $tikilib;
		if (!empty($this->items)) return $this->items;
		
		$contents = file_get_contents($this->feedUrl);
		$contents = json_decode($contents);
		
		if (!empty($contents->feed->entry) && $contents->feed->type == $this->type)
		{
			$this->contents = $contents;
			$this->items = $contents->feed->entry;
			$this->replace();
		}
		
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