<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

require_once 'lib/videogals/kalturalib.php';
if (is_object($kalturaadminlib) && !empty($kalturaadminlib->session)) {
	// contribution wizard
	$kcwDefault = $kalturaadminlib->updateStandardTikiKcw();
	if ($kcwDefault) {
		$kcwText = "<div class='adminoptionbox'>KCW Configuration ID: $kcwDefault (automatically configured)</div>";
	} else {
		$kcwText = "<div class='adminoptionbox'>Unable to retrieve configuration from Kaltura. Please reload page after setting up the Kaltura Partner Settings section</div>";	
	}
	// TODO make way to override this for certain sites...
	$tikilib->set_preference('kaltura_kcwUIConf', $kcwDefault);
	// players
	$players = $kalturaadminlib->getPlayersUiConfs();
	$kplayerlist = '<table>';
	foreach ($players as $p) {
		$kplayerlist .= '<tr><td>';
		$kplayerlist .= $p['id'];
		$kplayerlist .= '</td><td>';
		$kplayerlist .= $p['name'];
		$kplayerlist .= '</td></tr>';
	}
	$kplayerlist .= '</table>';
} else {
	$kcwText = "<div class='adminoptionbox'>Unable to retrieve configuration from Kaltura. Please reload page after setting up the Kaltura Partner Settings section</div>";
	$kplayerlist = "<div class='adminoptionbox'>Unable to retrieve list of valid player IDs. Please reload page after setting up the Kaltura Partner Settings section</div>";
}
$smarty->assign('kcwText', $kcwText);
$smarty->assign('kplayerlist', $kplayerlist); 
