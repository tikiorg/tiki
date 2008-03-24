<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $dbTiki;
global $freetaglib;
global $tiki_p_freetags_tag, $tiki_p_edit, $tiki_p_view;

if (!isset($freetaglib) or !is_object($freetaglib)) { include_once 'lib/freetag/freetaglib.php'; }

if( $tiki_p_edit == 'y' && $tiki_p_freetags_tag == 'y' && ! empty( $page ) && isset( $_POST['mod_add_tags'] ) )
{
	$freetaglib->tag_object( $user, $page, 'wiki page', $_POST['tags'] );
	header( "Location: {$_SERVER['REQUEST_URI']}" );
	exit;
}

if( ! empty( $page ) && $tiki_p_view == 'y' )
	$currenttags = $freetaglib->get_tags_on_object( $page, 'wiki page' );
else
	$currenttags = array();

$smarty->assign('modFreetagsCurrent', $currenttags);
?>
