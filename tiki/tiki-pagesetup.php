<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-pagesetup.php,v 1.8 2004-03-18 22:23:04 chealer Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//This file determines permissions in the Wiki area when page-specific permissions are set

//I can't figure out how calling this page without $page set would make sense Chealer 20040318
if ($tiki_p_admin != 'y' && isset($page) && $userlib->object_has_one_permission($page, 'wiki page')) {
	$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'wiki');
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
}
?>
