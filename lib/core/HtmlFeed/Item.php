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
		"type" 			=> "simple"
	);
	
	var $params = array();
	
	static function simplePage($params)
	{
		$me = new self();
		$me->defaults['type'] = "simplePage";
		$me->params = array_merge($params, $me->defaults);
		return $params;
	}
}
