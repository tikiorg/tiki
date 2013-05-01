<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// File name: PageLookup.php
// Required path: /lib/core/Feed/ForwardLink
//
// Programmer: Robert Plummer
//
// Purpose: Generates URI to reach destination for passed ForwardLink and redirects browser to that URI.

class Feed_ForwardLink_PageLookup extends Feed_Abstract
{
	var $type = 'forwardlink';
	var $forwardLink = array();
	var $version = 0.1;

	static function forwardLink($forwardLink = array())
	{
		$me = new self($forwardLink->href);
		$me->forwardLink = $forwardLink;
		return $me;
	}

	static function wikiView($args)
	{
		return;
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

			if (isset($forwardLink->href)) {
				$forwardLink->href = urldecode($forwardLink->href);

				$result = Feed_ForwardLink_Send::send(
					array(
						'forwardLink'=> $forwardLink,
						'textlink'=> array(
							'body'=> $args['data'],
							'href'=> $tikilib->tikiUrl() . 'tiki-index.php?page=' . $args['object']
						)
					)
				);
			}
		}
	}
}
