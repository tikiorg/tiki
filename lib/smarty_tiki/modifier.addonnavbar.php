<?php

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_addonnavbar($token) {
	$api = new TikiAddons_Api_NavBar;
	if ($ret = $api->getNavBar($token)) {
		return $ret;
	} else {
		return '';
	}
}
