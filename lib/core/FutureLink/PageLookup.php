<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// File name: PageLookup.php
// Required path: /lib/core/Feed/FutureLink
//
// Programmer: Robert Plummer
//
// Purpose: Generates URI to reach destination for passed FutureLink and redirects browser to that URI.

class FutureLink_PageLookup extends Feed_Abstract
{
	var $type = 'futurelink';
	var $futureLink = [];
	var $version = 0.1;

	static function futureLink($futureLink = [])
	{
		$me = new self($futureLink->href);
		$me->futureLink = $futureLink;
		return $me;
	}

	static function wikiView($args)
	{
		$tikilib = TikiLib::lib('tiki');

		static $FutureLink_PageLookup = 0;
		++$FutureLink_PageLookup;

		$wikiAttributes = (new Tracker_Query('Wiki Attributes'))
			->byName()
			->excludeDetails()
			->filter(['field' => 'Type', 'value' => 'FutureLink'])
			->filter(['field' => 'Page', 'value' => $args['object']])
			->render(false)
			->query();

		$futureLinks = [];

		foreach ($wikiAttributes as $wikiAttribute) {
			$futureLinks[] = $futureLink = json_decode($wikiAttribute['Value']);

			if (isset($futureLink->href)) {
				$futureLink->href = urldecode($futureLink->href);

				//TODO: this shouldn't work, need to upgrade
				$result = FutureLink_SendToFuture::send(
					[
						'futureLink' => $futureLink,
						'pastlink' => [
							'body' => $args['data'],
							'href' => $tikilib->tikiUrl() . 'tiki-index.php?page=' . $args['object']
						]
					]
				);
			}
		}
	}
}
