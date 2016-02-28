<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * For HtmlFeed_Remote Protocol
 */
class Feed_Html_Receive extends Feed_Abstract
{
	public $type = "html_feed";
	public $href = "";
	public $version = "0.1";

	function __construct($href)
	{
		$this->href = $href;
		parent::__construct($href);
	}

	public function getContents()
	{
		if (!empty($this->contents)) {
			return $this->contents;
		} else {
			$feed = json_decode(file_get_contents($this->href));
			$this->contents = $feed->feed;
		}
		return $this->contents;
	}
}
