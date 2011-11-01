<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class HtmlFeed_Item
{
	var $defaults = array(
		"origin" 		=> "",
		"name" 			=> "",
		"title" 		=> "",
		"description" 	=> "",
		"lastModif" 	=> "",
		"author" 		=> "",
		"hits"			=> "",
		"unusual"		=> "",
		"importance" 	=> "",
		"keywords"		=> "",
		"type" 			=> "simple",
		"url"			=> ""
	);
	
	var $params = array();
	
	static function simple($params)
	{
		$me = new self();
		
		$me->defaults['type'] = "simple";
		
		$me->params = array_merge($params, $me->defaults);
		return $params;
	}
	
	static function article($article)
	{
		$me = new self();
		
		$me->defaults['type'] = "article";
		
		$me->params = array_merge($params, $me->defaults);
		return $params;
	}
}
