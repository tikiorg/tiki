<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_TextBacklink_Contribution extends Feed_Remote_Abstract
{
	var $type = "feed_textbacklink_contribution";
	
	static function url($feedUrl = "http://localhost/")
	{
		$me = new self($feedUrl);
		return $me;
	}
	
	static function local()
	{
		global $tikilib;
		$me = self::url($tikilib->tikiUrl());
		return $me;
	}
	
	static function getContributedItems()
	{
		global $tikilib;
		$me = self::url($tikilib->tikiUrl());
		$me->getContents(true);
		return json_decode($me->getContents());
	}
}
