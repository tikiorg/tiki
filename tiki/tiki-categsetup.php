<?php

// $Header:

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

if ($tiki_p_admin != 'y') {
	$perms = $userlib->get_permissions(0, -1, 'permName_desc', 'categories');
	foreach ($perms["data"] as $perm) {
		$perm = $perm["permName"];
		foreach ($parents as $categId) {
			if ($userlib->object_has_one_permission($categId, 'category')) {
				if ($userlib->object_has_permission($user, $categId, 'category', $perm)) {
					$smarty->assign("$perm", 'y');
			   		$$perm = 'y';
				} else {
					$smarty->assign("$perm", 'n');
					$$perm = 'n';
					// better-sorry-than-safe approach:
					// if a user lacks a given permission regarding a particular category,
					// that category takes precedence when considering if user has that permission
					break 1;
					// break out of one FOREACH loop
				}
			} else {
				$categpath = $categlib->get_category_path($categId);
				$arraysize = count($categpath);
				for ($i=$arraysize-2; $i>=0; $i--) {
					if ($userlib->object_has_one_permission($categpath[$i]['categId'], 'category')) {
						if ($userlib->object_has_permission($user, $categpath[$i]['categId'], 'category', $perm)) {
							$smarty->assign("$perm", 'y');
					   		$$perm = 'y';
					   		break 1;
						} else {
							$smarty->assign("$perm", 'n');
							$$perm = 'n';
							// better-sorry-than-safe approach:
							// if a user lacks a given permission regarding a particular category,
							// that category takes precedence when considering if user has that permission
							break 2;
							// break out of one FOR loop and one FOREACH loop
						}
					}
				}
			}
		}
	}
}
?>