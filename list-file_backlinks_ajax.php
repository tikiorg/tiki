<?php
// $Id:$
require_once('tiki-setup.php');
if ( $prefs['feature_file_galleries'] != 'y' || $prefs['feature_jquery'] != 'y' || $prefs['feature_jquery_autocomplete'] != 'y') {
	/* echo '{}'; */
	exit;
}
global $filegallib; include_once('lib/filegals/filegallib.php');
if (empty($_REQUEST['fileId'])) {
	/* echo '{}'; */
	exit;
}
$info = $tikilib->get_file($_REQUEST['fileId']);
if (empty($info)) {
	/* echo '{}'; */
	exit;
}
$perms = Perms::get(array('type'=>'file gallery', 'object'=>$info['galleryId']));
if (!$perms->list_file_gallery) {
	/* echo '{}'; */
	exit;
}
$backlinks = $filegallib->getFileBacklinks($_REQUEST['fileId']);
$smarty->assign_by_ref('backlinks', $backlinks);
echo $smarty->fetch('file_backlinks.tpl'); 
/*
header( 'Content-Type: application/json' );
echo json_encode( $backlinks );
*/
