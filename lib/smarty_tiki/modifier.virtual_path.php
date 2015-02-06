<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Translate only if feature_multilingual is on

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_virtual_path($fileId, $type = 'file')
{
	global $prefs;
	global $base_url;
	$filegallib = TikiLib::lib('filegal');

	return  $base_url . 'tiki-webdav.php' . ($filegallib->get_full_virtual_path($fileId, $type));
}
