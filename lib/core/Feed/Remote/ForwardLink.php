<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Feed_Remote_ForwardLink extends Feed_Remote_Abstract
{
	var $type = "Feed_Remote_ForwardLink";
	var $forwardlink = array();
	
	static function forwardlink($forwardlink = array())
	{
		$me = new self($forwardlink->href);
		$me->forwardlink = $forwardlink;
		return $me;
	}
	
	static function wikiView($args)
	{
		global $tikilib, $headerlib;
		
		 static $Feed_Remote_ForwardLink_I = 0;
		++$Feed_Remote_ForwardLink_I;
		$i = $Feed_Remote_ForwardLink_I;
		
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
			->render(false)
			->query();
		
		$forwardLinks = array();
		foreach($wikiAttributes as $wikiAttribute) {
			$forwardLinks[] = $forwardLink = json_decode($wikiAttribute['Value']);
			
			if (isset($forwardLink->href)) {
				$result = Feed_Remote_ForwardLink_Contribution::send(array(
					"page"=> $args['object'],
					"href"=> urldecode($forwardLink->href),
					"textlinkBody"=> $args['data'],
					"textlinkHref"=> $tikilib->tikiUrl() . 'tiki-index.php?page=' . $args['object']
				));
			
			
				if (isset($result) && $result == "success") {
					$headerlib->add_jq_onready(<<<JQ
						$('<div></div>')
							.text(tr("Contribution Accepted"))
							.dialog();
JQ
);
				}
			}
		}
		
		$forwardLinks = json_encode($forwardLinks);
		
		if (!empty($forwardLinks))
		$headerlib->add_jq_onready(<<<JQ
			var forwardLinks = $forwardLinks;
			$.each(forwardLinks, function() {
				if (this.href) {
					$('<a>*</a>')
						.attr('href', unescape(this.href))
						.appendTo('#top');
				}
			});
JQ
);
	}
}