<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Tiki\FileGallery;

// Required path: /lib/core/Feed
//
// Programmer: Robert Plummer
//
// Purpose: The base class reused in FutureLink, PastLink and others

abstract class Feed_Abstract
{
	public $name = "";
	public $items = array();
	public $item = array();
	public $contents = array();
	public $type = "";
	public $isFileGal = false;
	public $version = "0.0";
	public $encoding = "";

	function __construct($name = "")
	{
		if (empty($name)) {
			$name = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			$name = explode('/', $name);
			array_pop($name);
			$name = implode($name, '/');
		} else {
			$name = str_replace("http://", "", $name);
			$name = str_replace("https://", "", $name);
			$name = explode('/', $name);
			$name = array_shift($name);
		}

		$this->name = $this->type . "_" . $name;
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
				$result[] = addslashes(htmlspecialchars($item->name));
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

	public function setEncoding($contents)
	{
		if (is_array($contents)) throw new Exception('die');
		$this->encoding = mb_detect_encoding($contents, "ASCII, UTF-8, ISO-8859-1");
	}

	private function open()
	{
		if ($this->isFileGal == true) {
			$contents = FileGallery\File::filename($this->name)->data();
		} else {
			$contents = TikiLib::lib("cache")->getCached($this->name, get_class($this));
		}

		$this->setEncoding($contents);

		$contents = json_decode($contents);
		if (empty($contents)) return array();
		return $contents;
	}

	private function save($contents)
	{
		$contents = json_encode($contents);

		if ($this->isFileGal == true) {
            //TODO: abstract
			FileGallery\File::filename($this->name)
				->setParam('description', '')
				->replace($contents);

		} else {
            //TODO: abstract
			TikiLib::lib("cache")->cacheItem($this->name, $contents, get_class($this));
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
			FileGallery\File::filename($this->name)->delete();
		} else {
			TikiLib::lib("cache")->empty_type_cache(get_class($this));
		}
	}

	function appendToContents(&$contents, $items)
	{
		if (isset($items->feed->entry)) {
			$contents->entry[] = $items->feed->entry;
		} elseif (isset($items)) {
			$contents->entry[] = $items;
		}
	}

	public function addItem($item)
	{
		global $tikilib;
		$contents = $this->open();

		if (empty($contents)) {
			$contents = new Feed_Contents($this->type);
		}

		//this allows us to intercept the contents and do things like check the validity of the content being appended to the contents
		$this->appendToContents($contents, $item);

		$this->save($contents);

		return $this;
	}

	public function feed($origin = '')
	{
		global $tikilib;
		$contents = $this->getContents();

        //TODO: convert to actual object
		$feed = new Feed_Container(
			$this->version,
			$this->encoding, //we get this from the above call to open
			$contents,
            (!empty($origin) ? $origin : $tikilib->tikiUrl() . 'tiki-feed.php'),
			$this->type
		);

		return $feed;
	}

	public function listArchives()
	{
		$archives = array();

		if ($this->isFileGal == true) {
			$file = FileGallery\File::filename($this->name);
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
