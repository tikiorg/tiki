<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Search\ResultSet;

class UrlHighlightTermsTransform
{
	private $termsParameter;

	function __construct($terms)
	{
		if ($terms) {
			$this->termsParameter = 'highlight=' . urlencode(implode(' ', $terms));
		} else {
			$this->termsParameter = '';
		}
	}

	function __invoke($entry)
	{
		if (isset($entry['url']) && $this->termsParameter) {
			$entry['url'] = $entry['url'] . (strpos($entry['url'], '?') === false ? '?' : '&') . $this->termsParameter;

		}

		return $entry;
	}
}

