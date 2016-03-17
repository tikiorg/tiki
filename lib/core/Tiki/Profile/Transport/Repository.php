<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_Transport_Repository implements Tiki_Profile_Transport_Interface
{
	private $url;
	
	function __construct($url)
	{
		$this->url = $url;
	}

	function getPageContent($pageName)
	{
		$exportUrl = dirname($this->url) . '/tiki-export_wiki_pages.php?'
			. http_build_query(array( 'page' => $pageName ));

		$content = TikiLib::lib('tiki')->httprequest($exportUrl);
		$content = str_replace("\r", '', $content);
		$begin = strpos($content, "\n\n");

		if ( $begin !== false ) {
			$content = substr($content, $begin + 2);

			// This allows compatibility with Tiki 8 and below, which export page content HTML-escaped. This should not be done for Tiki 9 and above and should be removed once only these are supported (after Tiki 6 reaches EOL).
			$content = htmlspecialchars_decode($content);

			return $content;
		} else {
			return null;
		}
	}

	function getPageParsed($pageName)
	{
		$pageUrl = dirname($this->url) . '/tiki-index_raw.php?'
			. http_build_query(array( 'page' => $pageName ));

		$content = TikiLib::lib('tiki')->httprequest($pageUrl);
		// index_raw replaces index.php with itself, so undo that here
		$content = str_replace('tiki-index_raw.php', 'tiki-index.php', $content);

		return $content;
	}
}

