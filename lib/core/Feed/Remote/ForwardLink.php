<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Feed_Remote_ForwardLink extends Feed_Remote_Abstract
{
	var $type = "Feed_Remote_ForwardLink";
	
	static function href($feedHref = "")
	{
		$me = new self($feedHref);
		return $me;
	}
	
	static function wikiView($args)
	{
		$wikiAttributes = TikiLib::lib("trkqry")
			->tracker("Wiki Attributes")
			->byName()
			->excludeDetails()
			->filter(array(
				'field'=> 'Type',
				'value'=> 'ForwardLink'
			))
			->filter(array(
				'field'=> 'Page',
				'value'=> $args['object']
			))
			->query();
		
		//print_r($wikiAttributes);
	}
}