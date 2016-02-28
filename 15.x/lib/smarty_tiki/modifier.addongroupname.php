<?php

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_addongroupname($token) {
	$api = new TikiAddons_Api_Group;
	if ($ret = $api->getOrganicGroupName($token)) {
		return $ret;
	} else {
		return $token;
	}
}
