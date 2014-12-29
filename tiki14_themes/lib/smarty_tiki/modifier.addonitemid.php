<?php

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
        header("location: index.php");
        exit;
}

function smarty_modifier_addonitemid($token) {

	$api = new TikiAddons_Api;
        return $api->getItemIdFromToken($token);

}
