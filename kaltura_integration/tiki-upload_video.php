<?php
$section = 'video_galleries';
require_once ('tiki-setup.php');
if ($prefs['feature_categories'] == 'y') {
	include_once ('lib/categories/categlib.php');
}
if ($prefs['feature_file_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries");
	$smarty->display("error.tpl");
	die;
}


//include_once ('lib/filegals/filegallib.php');
//include_once ("lib/videogals/includes.php");
include_once ("lib/videogals/videogallib.php");


if ((empty($_REQUEST['videoId']) || isset($_REQUEST["kcw_next"]) || isset($_REQUEST["update_entries"]))&& $tiki_p_upload_videos != 'y' && $tiki_p_admin_video_galleries != 'y' && $tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied: You cannot upload videos"));
	$smarty->display('error.tpl');
	die;
}

if ((!empty($_REQUEST['videoId']) || isset($_REQUEST["update"])) && $tiki_p_edit_videos != 'y' && $tiki_p_admin_video_galleries != 'y' && $tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied: You cannot edit video information"));
	$smarty->display('error.tpl');
	die;
}


$editMode='info';
if(isset($_REQUEST['edit'])){
		if(!($_REQUEST['edit'] == 'info' || $_REQUEST['edit'] == 'remix')){
		$smarty->assign('msg', tra("Incorrect param"));
		$smarty->display('error.tpl');
		die;
		}
	$editMode = $_REQUEST['edit'];
	if( $editMode == 'remix' && $tiki_p_remix_videos != 'y' && $tiki_p_admin_video_galleries != 'y' && $tiki_p_admin != 'y' ){
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("Permission denied: You cannot remix videos"));
		$smarty->display('error.tpl');
		die;
	}
}
$smarty->assign('editMode',$editMode);

if (!empty($_REQUEST['galleryId']) && (is_numeric($_REQUEST['galleryId']) || is_string($_REQUEST['galleryId']))) {
		$_REQUEST['galleryId'] = array($_REQUEST['galleryId']);
}

if (!empty($_REQUEST['videoId']) && !isset($_REQUEST["update_entries"])&& !isset($_REQUEST["update"])) {
	if (!($videoInfo = $videogallib->get_video_info($_REQUEST['videoId']))) {
		$smarty->assign('msg', tra("Incorrect param"));
		$smarty->display('error.tpl');
		die;
	}

	if (empty($_REQUEST['galleryId'][0])) {
		$_REQUEST['galleryId'][0] = $videoInfo['galleryId'];
	} 
	
	elseif ($_REQUEST['galleryId'][0] != $videoInfo['galleryId']) {
		$smarty->assign('msg', tra("Could not find the video requested"));
		$smarty->display('error.tpl');
		die;
	}
	//print_r($videoInfo);
	$smarty->assign_by_ref('videoInfo',$videoInfo);
	$editVideoId = $_REQUEST['videoId'];
	$smarty->assign('editVideoId',$editVideoId);
}

$entryId = $videogallib->get_entry_from_video($editVideoId);
$smarty->assign_by_ref('entryId',$entryId);

$smarty->assign_by_ref('galleryId', $_REQUEST['galleryId'][0]);
$galleries = $tikilib->list_visible_file_galleries(0, -1, 'name_asc', $user, '');

$smarty->assign_by_ref('galleries', $galleries['data']);

$kaltura_conf = kaltura_init_config();
		$kuser = new KalturaSessionUser();
		$kuser->userId = $user;
		$kaltura_client = new KalturaClient($kaltura_conf);
		$kres =$kaltura_client->start($kuser, $kaltura_conf->secret,'',"edit:*",'');
		
//print_r($kres);		
$cwflashVars = 'userId=' .$kuser->userId.
			'&sessionId=' .$kres["result"]["ks"]. 
			'&partnerId=' . $kaltura_conf->partnerId .
			'&subPartnerId=' . $kaltura_conf->subPartnerId . 
			'&kshow_id=-2' . 
			'&afterAddEntry=afterAddEntry'.
			'&showCloseButton=false';


$seflashVars = 'uid=' .$kuser->userId.
			'&ks=' .$kres["result"]["ks"]. 
			'&partner_id=' . $kaltura_conf->partnerId .
			'&subp_id=' . $kaltura_conf->subPartnerId . 
			'&kshow_id=entry-' . $entryId.
			'&entry_id='. $entryId.
			'&backF=Back'.
			'&saveF=Save';


$smarty->assign_by_ref('cwflashVars',$cwflashVars);
$smarty->assign_by_ref('seflashVars',$seflashVars);

$smarty->assign('kcw_ui_conf_id',"36200");
		
// Process an upload or update here
if (isset($_REQUEST["kcw_next"])) {
	check_ticket('upload-video');

	$entries = $_REQUEST["entryId"];
	$gallery = "0";
	if(isset($_REQUEST["gallery"]))  {
		$gallery = $_REQUEST["gallery"];
	}
		
	$smarty->assign_by_ref('galleryId', $_REQUEST["gallery"]);
	//print_r($entries);
	$videoEntries =array();
	for($i=0; $i < count($_REQUEST["entryId"]); $i++) {
		$res = $kaltura_client->getEntryRoughcuts($kuser,$_REQUEST["entryId"][$i]);
		print_r($res);
		$roughcutId=$res['result']['roughcuts'][0]['id'];
		$tmp = $videogallib->get_video_info($videogallib->insert_video($gallery,$roughcutId,$user));
		$videoEntries[$i] = $tmp;
		/*$videoEntries[$i]["name"] = "asd";
		$videoEntries[$i]["description"] = "asdaaaa";
		$videoEntries[$i]["tags"] = "amn";
		$videoEntries[$i]["thumbnail"] = "pics/large/kaltura48x48.png";*/
	}
	$smarty->assign('editEntries',"true");
	$smarty->assign_by_ref('videoEntries',$videoEntries);
	//print_r($videoEntries);
}
if (isset($_REQUEST["update"])) {
	
	check_ticket('upload-video');
	$videogallib->edit_video($_REQUEST["videoId"], $_REQUEST["name"][0], $_REQUEST["description"][0], $_REQUEST["tags"][0]);
	$videogallib->move_video($_REQUEST["videoId"], $_REQUEST["galleryId"][0]);

}

if (isset($_REQUEST["update_entries"])) {
	check_ticket('upload-video');
		//print_r($_REQUEST["name"]);

		for($i=0; $i < count($_REQUEST["entryId"]); $i++){
			$videogallib->edit_video($_REQUEST["videoId"][$i], $_REQUEST["name"][$i], $_REQUEST["description"][$i], $_REQUEST["tags"][$i]);
			
			if(isset($_REQUEST["galleryId"][$i])){
			$videogallib->move_video($_REQUEST["videoId"][$i], $_REQUEST["galleryId"][$i]);
			}
		}
}

if ($tiki_p_admin_video_galleries == 'y' || $tiki_p_admin == 'y') {
	$users = $tikilib->list_users(0, -1, 'login_asc', '', false);
	$smarty->assign_by_ref('users', $users['data']);
}

ask_ticket('upload-video');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
if ($prefs['javascript_enabled'] != 'y' or !isset($_REQUEST["upload"])) {
	$smarty->assign('mid','tiki-upload_video.tpl');
	$smarty->display("tiki.tpl");
	
}
?>
