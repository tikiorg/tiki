<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-directory_stats.php 33195 2011-03-02 17:43:40Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_directory_stats_info() {
	return array(
		'name' => tra('Directory Statistics'),
		'description' => tra('Displays statistics about the directory, including the number of sites validated and to validate, the number of categories, of searches and of visited links.'),
		'prefs' => array( 'feature_directory' ),
		'documentation' => 'Module directory_stats',
		'params' => array()
	);
}

function module_directory_stats( $mod_reference, $module_params ) {
	global $prefs, $tikilib, $smarty;
	
	$ranking = $tikilib->dir_stats();
	$smarty->assign('modDirStats', $ranking);
}
