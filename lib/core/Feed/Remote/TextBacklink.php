<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Feed_Remote_TextBacklink extends Feed_Remote_Abstract
{
	var $type = "feed_remote_textbacklink";
	
	static function url($feedUrl = "http://localhost/")
	{
		$me = new self($feedUrl);
		return $me;
	}
}