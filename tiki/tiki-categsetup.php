<?php

// $Header:

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

if ($tiki_p_admin != 'y' && isset($categId) && $userlib->object_has_one_permission($categId, 'category')) {
	$perms = $userlib->get_permissions(0, -1, 'permName_desc', 'categories');
	foreach ($perms["data"] as $perm) {
		$perm = $perm["permName"];
		if ($userlib->object_has_permission($user, $categId, 'category', $perm)) {
			$smarty->assign("$perm", 'y');
    		$$perm = 'y';
		} else {
			$smarty->assign("$perm", 'n');
			$$perm = 'n';
		}
	}
}
?>