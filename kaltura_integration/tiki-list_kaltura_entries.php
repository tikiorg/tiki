<?php
require_once ('tiki-setup.php');

if ($prefs['feature_kaltura'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_kaltura");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_list_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied: You cannot view this page"));
	$smarty->display('error.tpl');
	die;
}

include_once ("lib/videogals/videogallib.php");

global $user;

$kaltura_conf = kaltura_init_config();
$kuser = new KalturaSessionUser();
$kuser->userId = $user;
$kaltura_client = new KalturaClient($kaltura_conf);
$kres =$kaltura_client->startSession($kuser, $kaltura_conf->secret,false,"");
$kaltura_client->setKS($kres["result"]["ks"]);

$filter = new KalturaEntryFilter();
$sort_mode = '';
if($_REQUEST['sort_mode']){
	$sort_mode = $_REQUEST['sort_mode'];
}else{
	$sort_mode = "desc_created_at";
}
$smarty->assign_by_ref('sort_mode',$sort_mode);
$sort_mode = preg_replace('/desc_/','-',$sort_mode);
$sort_mode = preg_replace('/asc_/','+',$sort_mode);
$filter->orderBy = $sort_mode;

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$filter->multiLikeOrTagsOrName = $find;
$smarty->assign('find', $find);

$page_size = 15;
if($_REQUEST['maxRecords']){
	$page_size = $_REQUEST['maxRecords'];
}	
		
if($_REQUEST['offset']){
	$offset = $_REQUEST['offset'];
	$page = ($offset/$page_size)+1;
}else{
	$offset = 0;
	$page = 1;
}
		
$res = $kaltura_client->listMyEntries($kuser,$filter,true,$page_size,$page,null);

$entries['cant'] = $res['result']['count'];

//echo $entries['cant'];
$entries['data'] = $res['result']['entries'];

$smarty->assign_by_ref('entries',$entries['data']);
$smarty->assign_by_ref('cant',$entries['cant']);
$smarty->assign_by_ref('offset',$offset);
$smarty->assign_by_ref('maxRecords',$res['result']['page_size']);

//print_r($res);
// Display the template
	$smarty->assign('mid', 'tiki-list_kaltura_entries.tpl');
	$smarty->display("tiki.tpl");