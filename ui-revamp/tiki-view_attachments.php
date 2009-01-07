<?php

require_once 'tiki-setup.php';

if (!isset(
	$_REQUEST['objectName']) || empty($_REQUEST['objectType']) || empty($_REQUEST['objectId'])) {
	$smarty->assign('msg', tra("Not enough information to display this page"));

	$smarty->display("error.tpl");
	die;
}

switch( $_REQUEST['objectType'] ) {
case 'wiki':
case 'wiki page':
case 'wiki_page':
	$perms = array(
		'tiki_p_wiki_view_attachments',
		'tiki_p_wiki_admin_attachments',
		'tiki_p_wiki_attach_files',
	);

	foreach( $perms as $p ) {
		$$p = $tikilib->user_has_perm_on_object($user, $_REQUEST['objectId'], $_REQUEST['objectType'], $p ) ? 'y' : 'n';
		$smarty->assign( $p, $$p );
	}

	if( $prefs['feature_wiki_attachments'] != 'y' 
	 || $tiki_p_wiki_view_attachments != 'y' ) {
		$smarty->assign('msg', tra("Impossible to view comments."));
		$smarty->display("error.tpl");
		die;
	}

	require_once 'lib/wiki/wikilib.php';
	$atts = $wikilib->list_wiki_attachments($_REQUEST['objectId'],0,-1);

	break;

default:
	$smarty->assign('msg', tra("Unsupported type."));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('pagemd5', '');
$smarty->assign( 'atts', $atts['data'] );
$smarty->assign( 'atts_count', $atts['cant'] );
$smarty->assign( 'attach_box', 'y' );
$smarty->assign( 'atts_show', true );
$smarty->assign( 'nohide_atts', true );

require_once('TikiPageControls.php');
if( $controls = TikiPageControls::factory( $_REQUEST['objectType'], $_REQUEST['objectId'], $_REQUEST['objectName'] ) ) {
	$controls->setMode('attach');
	$controls->build();
	$smarty->assign('object_page_controls', $controls);
}

$smarty->assign('mid','tiki-view_attachments.tpl');
$smarty->display("tiki.tpl");

?>
