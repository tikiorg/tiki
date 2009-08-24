<?php
include_once('tiki-setup.php');
if (!($tiki_p_admin == 'y' || $tiki_p_admin_users == 'y')) { // temporary patch: tiki_p_admin includes tiki_p_admin_users but if you don't clean the temp/cache each time you sqlupgrade the perms setting is not synchornous with the cache
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
$view[] = 'tiki_p_view_trackers';
$edit[] = 'tiki_p_modify_tracker_items';
$edit[] = 'tiki_p_create_tracker_items';
$edit[] = 'tiki_p_modify_tracker_items_pending';
$edit[] = 'tiki_p_modify_tracker_items_closed';

$view[] = 'tiki_p_view_image_gallery';
$view[] = 'tiki_p_download_files';
$edit[] = 'tiki_p_upload_images';

$view[] = 'tiki_p_view_file_gallery';
$view[] = 'tiki_p_view_fgal_explorer';
$view[] = 'tiki_p_view_fgal_path';
$edit[] = 'tiki_p_upload_files';

$view[] = 'tiki_p_read_article';
$edit[] = 'tiki_p_edit_article';
$edit[] = 'tiki_p_submit_article';

$view[] = 'tiki_p_forum_read';
$edit[] = 'tiki_p_forum_post_topic';
$edit[] = 'tiki_p_forum_post';

$view[] = 'tiki_p_read_blog';
$edit[] = 'tiki_p_create_blogs';
$edit[] = 'tiki_p_blog_post';

$view[] = 'tiki_p_view';
$edit[] = 'tiki_p_edit';
$edit[] = 'tiki_p_remove';
$edit[] = 'tiki_p_wiki_attach_files';

$view[] = 'tiki_p_view_faqs';

$view[] = 'tiki_p_subscribe_newsletters';

$view[] = 'tiki_p_view_calendar';
$view[] = 'tiki_p_view_events';
$view[] = 'tiki_p_view_tiki_calendar';
$edit[] = 'tiki_p_add_events';
$edit[] = 'tiki_p_change_events';

$view[] = 'tiki_p_view_directory';

$view[] = 'tiki_p_view_freetags';

$view[] = 'tiki_p_view_sheet';

$view[] = 'tiki_p_view_shoutbox';

$view[] = 'tiki_p_view_html_pages';

$view[] = 'tiki_p_view_category';

$query = 'SELECT * FROM `users_objectpermissions` WHERE `permName` = ?';
$insert = 'INSERT into `users_objectpermissions` (`groupName`, `permName`, `objectType`, `objectId`) values (?,?,?,?)';
$result = $tikilib->query($query, array('tiki_p_view_categorized'));
while ($res = $result->fetchRow() ) {
	foreach ($view as $perm) {
		echo "INSERT into `users_objectpermissions` (`groupName`, `permName`, `objectType`, `objectId`) values ('".$res['groupName']."','$perm','category','".$res['objectId']."')<br />";
	}
		foreach ($edit as $perm) {
		echo "INSERT into `users_objectpermissions` (`groupName`, `permName`, `objectType`, `objectId`) values ('".$res['groupName']."','$perm','category','".$res['objectId']."')<br />";
	}
}