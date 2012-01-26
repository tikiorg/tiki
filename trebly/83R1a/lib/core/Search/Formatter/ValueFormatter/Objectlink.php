<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Objectlink.php 36090 2011-08-11 20:31:15Z sept_7 $

class Search_Formatter_ValueFormatter_Objectlink implements Search_Formatter_ValueFormatter_Interface
{
	function render($name, $value, array $entry)
	{
		global $smarty;
		$smarty->loadPlugin('smarty_function_object_link');

		$params = array(
			'type' => $entry['object_type'],
			'id' => $entry['object_id'],
			'title' => $value,
		);

		if (isset($entry['url'])) {
			$params['url'] = $entry['url'];
		}

		return '~np~' . smarty_function_object_link($params, $smarty) . '~/np~';
	}
}

