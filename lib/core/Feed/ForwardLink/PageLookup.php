<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Feed_ForwardLink_PageLookup extends Feed_Abstract
{
	var $type = 'forwardlink';
	var $forwardLink = array();
	var $version = "0.1";
	
	static function forwardLink($forwardLink = array())
	{
		$me = new self($forwardLink->href);
		$me->forwardLink = $forwardLink;
		return $me;
	}

	static function wikiView($args)
	{
		global $tikilib, $headerlib;

		 static $Feed_ForwardLink_PageLookup = 0;
		++$Feed_ForwardLink_PageLookup;

		$wikiAttributes = Tracker_Query::tracker('Wiki Attributes')
			->byName()
			->excludeDetails()
			->filter(array('field'=> 'Type', 'value'=> 'ForwardLink'))
			->filter(array('field'=> 'Page', 'value'=> $args['object']))
			->render(false)
			->query();

		$forwardLinks = array();

		foreach ($wikiAttributes as $wikiAttribute) {
			$forwardLinks[] = $forwardLink = json_decode($wikiAttribute['Value']);

			$forwardLink->href = urldecode($forwardLink->href);

			if (isset($forwardLink->href)) {
				$result = Feed_ForwardLink_Send::send(
								array(
									'page'=> $args['object'],
									'forwardLink'=> $forwardLink,
									'textlink'=> array(
										'body'=> $args['data'],
										'href'=> $tikilib->tikiUrl() . 'tiki-index.php?page=' . $args['object']
									)
								)
				);
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
						.appendTo('#page-data');
				}
			});
JQ
);
	}
}
