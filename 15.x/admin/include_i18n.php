<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

// Get list of available languages
$languages = array();
$langLib = TikiLib::lib('language');
$languages = $langLib->list_languages(false, null, true);
$smarty->assign_by_ref('languages', $languages);

global $tikifeedback;
if (!empty($tikifeedback)) {
	foreach ($tikifeedback as $item) {
		if ($item['name'] === 'available_languages' || $item['name'] === 'restrict_language') {
			TikiLib::lib('cache')->empty_cache('temp_cache');
			break;
		}
	}
}
