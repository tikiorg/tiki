<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-pagesetup.php,v 1.18 2007-03-06 19:29:50 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if ($tiki_p_admin != 'y' && isset($page) && $userlib->object_has_one_permission($page, 'wiki page')) {
	$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'wiki');
	$object_has_perms = true;
	if ($userlib->object_has_permission($user, $page, 'wiki page', 'tiki_p_admin_wiki')) {
		foreach ($perms["data"] as $perm) {
			$perm = $perm["permName"];
			$smarty->assign("$perm", 'y');
			$$perm = 'y';
		}
	} else {
		foreach ($perms["data"] as $perm) {
			$perm = $perm["permName"];
			if ($userlib->object_has_permission($user, $page, 'wiki page', $perm)) {
 	 			$smarty->assign("$perm", 'y');
	    			$$perm = 'y';
			} else {
 	 			$smarty->assign("$perm", 'n');
	    			$$perm = 'n';
			}
		}
	}
} else {
	$object_has_perms = false;
}
?>
