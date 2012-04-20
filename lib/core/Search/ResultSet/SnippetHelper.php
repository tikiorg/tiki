<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ResultSet_SnippetHelper implements Zend_Filter_Interface
{
	private $length;
	function __construct($length = 240)
	{
		$this->length = (int) 240;
	}

	function filter($content)
	{
		global $prefs;
		if ($prefs['unified_parse_results'] == 'y') {
			$parserlib = TikiLib::lib('parser');
			$content = $parserlib->parse_data($content, array('parsetoc' => false));
		}
		return substr(strip_tags(str_replace(array('~np~', '~/np~'), '', $content)), 0, $this->length);
	}
}

