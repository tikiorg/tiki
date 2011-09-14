<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script will send a 1x1 transparent gif image, close connection and reindex the file corresponding to the id url argument
// The goal is to process reindexation in a background job for which the user won't have to wait
//
// This trick has been found on the official php manual page comments of the register_shutdown_function function

require_once('tiki-setup.php');

// Reindex the file for search
if ( ($id = (int)$_GET['id']) > 0 ) {

	// Check feature
	if ( $prefs['feature_file_galleries'] == 'y'
		&& $prefs['feature_search'] == 'y'
		&& $prefs['feature_search_fulltext'] != 'y'
		&& $prefs['search_refresh_index_mode'] == 'normal'
		&& $prefs['fgal_asynchronous_indexing'] == 'y'
	) {
		require_once('lib/filegals/filegallib.php');
		require_once('lib/search/refresh-functions.php');

		$info = $filegallib->get_file_info($id);

		if ( $info['galleryId'] > 0 ) {
			$gal_info = $filegallib->get_file_gallery($info['galleryId']);
		
			// Check perms
			$tikilib->get_perm_object($info['galleryId'], 'file gallery', $gal_info, true);
	
			if ( $tiki_p_admin_file_galleries == 'y'
				|| ( ( empty($fileInfo['lockedby']) || $fileInfo['lockedby'] == $user ) && $tiki_p_edit_gallery_file == 'y' )
			) { // must be the owner or the locker or have the perms
				error_reporting(0);
				ignore_user_abort(true);
				session_write_close(); // close the session to allow the user to continue browsing
				register_shutdown_function('refresh_index', 'files', $id);
			}
		}
	}
}

// Display the 1x1 transparent gif image
header('Cache-Control: no-cache');
header('Content-type: image/gif');
header('Content-length: 85');
print base64_decode(
	'R0lGODlhAQABALMAAAAAAIAAAACAA'.
	'ICAAAAAgIAAgACAgMDAwICAgP8AAA'.
	'D/AP//AAAA//8A/wD//wBiZCH5BAE'.
	'AAA8ALAAAAAABAAEAAAQC8EUAOw=='
);
flush();
exit;
