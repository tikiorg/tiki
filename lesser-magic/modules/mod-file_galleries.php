<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $filegallib; include_once ('lib/filegals/filegallib.php');

$all_galleries = $filegallib->getFileGalleriesData();
$smarty->assign_by_ref('all_galleries', $all_galleries['data']);

if ( isset($all_galleries) && is_array($all_galleries) && count($all_galleries) > 0 ) {
	$phplayersTreeData = $filegallib->getFilegalsTreePhplayers();
	$smarty->assign_by_ref('tree', $phplayersTreeData['tree']);
	$smarty->assign_by_ref('expanded', $phplayersTreeData['expanded']);
}
