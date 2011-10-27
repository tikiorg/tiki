<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Request.php 36391 2011-08-22 20:26:16Z changi67 $

/**
 * For TextBacklink Protocol
 */
class HtmlFeed_Remote
{
	var $feedUrl = "";
	var $name = "";
	
	public function __construct($feedUrl = "", $name = "")
	{
		$this->feedUrl = $feedUrl;
		$this->name = $name;
	}
	
	public function getLinks()
	{
		global $tikilib;
		
		$contents = file_get_contents($this->feedUrl);
		$contents = json_decode($contents);
		
		if (!empty($contents->feed->entry) && $contents->feed->type == "htmlfeed")
		{
			return $contents->feed->entry;
		}
		
		//empty or wrong, return nothing
		return array();
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
	
	public function getLink()
	
	{
		foreach($this->getLinks() as $item) {
			if ($this->name == $item->name) {
				return $item;
			}
		}
		return array();
	}
}