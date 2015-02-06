<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_function_defaultmapcenter($params, $smarty)
{
	$smarty->loadPlugin('smarty_modifier_escape');
	global $prefs;
	$geolib = TikiLib::lib('geo');
	$coords = $geolib->parse_coordinates($prefs['gmap_defaultx'] . ',' . $prefs['gmap_defaulty'] . ',' . $prefs['gmap_defaultz']);
	$center = $geolib->build_location_string($coords); 
	return smarty_modifier_escape($center);
}
