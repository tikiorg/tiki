<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-pagesetup.php,v 1.6 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-pagesetup.php,v 1.6 2003-08-07 04:33:57 rossta Exp $
$check = isset($page);

$ppps = array(
	'tiki_p_view',
	'tiki_p_edit',
	'tiki_p_rollback',
	'tiki_p_remove',
	'tiki_p_rename',
	'tiki_p_lock',
	'tiki_p_admin_wiki',
	'tiki_p_view_attachments'
);

// If we are in a page then get individual permissions
foreach ($allperms as $vperm) {
	$perm = $vperm["permName"];

	if (in_array($perm, $ppps)) {
		if ($tiki_p_admin != 'y') {
			// Check for individual permissions if this is a page
			if ($check) {
				if ($userlib->object_has_one_permission($page, 'wiki page')) {
					if ($userlib->object_has_permission($user, $page, 'wiki page', $perm)) {
						$$perm = 'y';

						$smarty->assign("$perm", 'y');
					} else {
						$$perm = 'n';

						$smarty->assign("$perm", 'n');
					}
				}
			}
		} else {
			$$perm = 'y';

			$smarty->assign("$perm", 'y');
		}
	}
}

?>