<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_Transform_UrlPrefix
{
	private $prefixMap = null;

	function __construct(array $prefixMap)
	{
		$this->prefixMap = $prefixMap;
	}

	function __invoke($entry)
	{
		if (isset($entry['url'], $entry['_index'])) {
			$index = $entry['_index'];
			if (isset($this->prefixMap[$index])) {
				$entry['url'] = $this->prefixMap[$index] . $entry['url'];
			}
		}

		return $entry;
	}
}

