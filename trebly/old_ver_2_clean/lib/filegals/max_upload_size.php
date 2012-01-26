<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Try to detect a file max size due to upload or memory limits
// (to do an insert into the database, the data has to be put in memory by PHP)
//
global $tikilib, $smarty;
@$max_upload_size = $tikilib->return_bytes(ini_get('upload_max_filesize'));
@$post_max_size = $tikilib->return_bytes(ini_get('post_max_size'));
$max_upload_size_comment = tra("This is the value of your server's PHP '%s' setting");

if ( $post_max_size > 0 && ( $post_max_size < $max_upload_size || $max_upload_size == 0 ) ) {
	$max_upload_size = $post_max_size;
	$max_upload_size_comment = sprintf($max_upload_size_comment, 'post_max_size');	
} else {
	$max_upload_size_comment = sprintf($max_upload_size_comment, 'upload_max_filesize');	
}

// Get memory limit
@$memory_limit_ini = ini_get('memory_limit');
$memory_limit = $tikilib->return_bytes($memory_limit_ini);

// Try to detect current memory usage or set it arbitrary to 10MB
@$current_memory_usage = function_exists('memory_get_usage') ? (int)memory_get_usage(true) : 10 * 1024 * 1024;

if ( $prefs['fgal_use_db'] == 'y' && (empty($podCastGallery) || ! $podCastGallery) ) {

	if ( $memory_limit > 0 ) {

		// Estimate available memory for file upload.
		// The result is divided by 3, because the file has to be stored twice in memory :
		//    one copy when reading the file, and two other modified copies in ADODB when adding quotes to the query variables
		//    ( due to functions like mysqli_real_escape_string that takes 200% more memory)
		// We also reduce of a memory size of 3 MB (which is an approximation too) that may be necessary for other tasks to work
		//
		$remaining_memory = max(0, ((int)($memory_limit - $current_memory_usage) / 3) - (3 * 1024 * 1024));

		if ( $max_upload_size > $remaining_memory ) {
			$max_upload_size = $remaining_memory;
			$max_upload_size_comment = tra('This is an approximation based on your server memory limit:').' '.$memory_limit_ini;
		}
	}
}

$smarty->assign("max_upload_size_comment", $max_upload_size_comment);
$smarty->assign("max_upload_size", "$max_upload_size");
