<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_ForwardLink_Contribution extends Feed_Abstract
{
	var $type = "local_page";
	var $name = "";
	var $isFileGal = true;
	 
	static function forwardLink($name)
	{
		$me = new self();
		$me->name = $name;
		return $me;
	}
	
	public function name()
	{
		return $this->type . "_" . $this->name;
	}
	
	function appendToContents($contents, $item)
	{
		foreach($contents->entry as $existingEntry) {
			foreach($item->feed->entry as $newEntry) {
				if (
					$existingEntry->textlink->date == $newEntry->textlink->date &&
					$existingEntry->textlink->text == $newEntry->textlink->text &&
					$existingEntry->textlink->href == $newEntry->textlink->href
				) {
					return $contents;
				}
			}
		}
		
		foreach($item->feed->entry as $newEntry) {
			$contents->entry[] = $newEntry;
		}
		
		return $contents;
	}
}
