<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $dbTiki;
global $freetaglib;
global $tiki_p_view;
if (!isset($freetaglib) or !is_object($freetaglib)) { include_once 'lib/freetag/freetaglib.php'; }

if( ! empty( $page ) && $tiki_p_view == 'y' )
	$morelikethis = $freetaglib->get_similar( 'wiki page', $page, $module_rows );
else
	$morelikethis = array();


$smarty->assign('modMoreLikeThis', $morelikethis);
$smarty->assign('module_rows', $module_rows);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
