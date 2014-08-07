<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_Transform_UrlPrefix
{
	private $prefix;

	function __construct($prefix)
	{
		$this->prefix = $prefix;
	}

	function __invoke($entry)
	{
		if (isset($entry['url'])) {
			$entry['url'] = $this->prefix . $entry['url'];
			$entry['_external'] = true;
		}

		return $entry;
	}
}

