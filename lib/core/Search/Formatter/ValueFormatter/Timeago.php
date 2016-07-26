<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_ValueFormatter_Timeago extends Search_Formatter_ValueFormatter_Datetime
{

	function render($name, $value, array $entry)
	{
		global $prefs;

		if (preg_match('/^\d{14}$/', $value)) {
			// Facing a date formated as YYYYMMDDHHIISS as indexed in lucene
			// Always stored as UTC
			$value = date_create_from_format('YmdHise', $value . 'UTC')->getTimestamp();
		}

		if ($prefs['jquery_timeago'] === 'y') {
			TikiLib::lib('header')->add_jq_onready('$("time.timeago").timeago();');
			return '<time class="timeago" datetime="' . TikiLib::date_format('c', $value, false, 5, false) .  '">' . $value . '</time>';
		} else  {
			return parent::render($name, $value, $entry);
		}
	}
}

