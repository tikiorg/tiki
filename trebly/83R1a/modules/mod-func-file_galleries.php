<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-file_galleries.php 37872 2011-10-02 17:47:25Z chealer $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_file_galleries_info() {
	return array(
		'name' => tra('File Galleries'),
		'description' => tra('Displays links to file galleries.'),
		'prefs' => array( 'feature_file_galleries' ),
		'params' => array(),
	);
}

function module_file_galleries( $mod_reference, $module_params ) {
	global $filegallib; include_once ('lib/filegals/filegallib.php');
	global $smarty;
	
	$all_galleries = $filegallib->getSubGalleries();
	
	if ( isset($all_galleries) && is_array($all_galleries) && count($all_galleries) > 0 ) {
		$treeData = $filegallib->getFilegalsTree();
		$subGalleries = $filegallib->getSubGalleries( 0 );
		$treeData['tree']['data'] = $subGalleries['data'];
		$smarty->assign_by_ref('tree', $treeData['tree']);
	}
}
