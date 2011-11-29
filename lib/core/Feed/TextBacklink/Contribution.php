<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_TextBacklink_Contribution extends Feed_Remote_Abstract
{
	var $type = "textbacklink_contribution";
	
	static function url($feedUrl)
	{
		$me = new self($feedUrl);
		$me->contents = $contents;
		return $me;
	}
	
	function setContents($contents)
	{
		$this->contents = $contents;
		return $this;
	}
}
