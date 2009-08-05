<?php

require_once ('tiki-setup.php');
ob_start();
if ($prefs['feature_kaltura'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_kaltura");
	$smarty->display("error.tpl");
	die;
}

include_once ("lib/videogals/videogallib.php");

if ((empty($_REQUEST['videoId']) || isset($_REQUEST["kcw"]))&& $tiki_p_upload_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied: You cannot upload videos"));
	$smarty->display('error.tpl');
	die;
}
$kaltura_conf = kaltura_init_config();
	$kuser = new KalturaSessionUser();
	$kuser->userId = $user;
	$kaltura_client = new KalturaClient($kaltura_conf);
	$kres = $kaltura_client->startSession($kuser, $kaltura_conf->secret,'',"edit:*",'');
	$kaltura_client->setKS($kres['result']['ks']);
		
if (isset($_REQUEST["kcw"])) {
		
	$entries = $_REQUEST["entryId"];
	for($i=0; $i < count($entries); $i++) {
		$videoEntries[$i] = $videogallib->get_video_info($entries[$i],$kaltura_client);	
	}
	$smarty->assign('mode','new_entries');
	$smarty->assign_by_ref('entries',$videoEntries);
}

if (isset($_REQUEST["update"])){
	if($tiki_p_edit_videos == 'y' || $tiki_p_admin_kaltura == 'y' || $tiki_p_admin == 'y'){
		$videogallib->edit_video($_REQUEST['videoId'], $_REQUEST['name'], $_REQUEST['description'], $_REQUEST['tags']);
		header ('Location: tiki-list_kaltura_entries.php');
		die;
	}else{
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("Permission denied: You cannot edit video information"));
		$smarty->display('error.tpl');
		die;
	}
}

$videoId = array();

if(!empty($_REQUEST['videoId']) && isset($_REQUEST['action'])){
	$mode = $_REQUEST['action'];
	$smarty->assign_by_ref('mode',$mode);
	
	if(is_array($_REQUEST['videoId'])){
		$videoId = $_REQUEST['videoId'];
	}else{
		$videoId[] = $_REQUEST['videoId'];
	}
	
	switch($mode){
	
	case 'remix':
	case 'dupl':
		if( $tiki_p_remix_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y' ){
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied: You cannot remix videos"));
			$smarty->display('error.tpl');
			die;
		}else{
				$seflashVars = 'uid=' .$kuser->userId.
					'&ks=' .$kres["result"]["ks"]. 
					'&partner_id=' . $kaltura_conf->partnerId .
					'&subp_id=' . $kaltura_conf->subPartnerId .
					'&backF=Back'.
					'&saveF=Save';

				if(count($videoId) == 1){
					$res = $kaltura_client->getEntry($kuser,$videoId[0],'');
					$mediaType = $res['result']['entry']['mediaType'];
					if($mediaType == 6 ){
						$roughcutId =$videoId[0];							
						if($mode == 'dupl') {
							$kres = $kaltura_client->startAdminSession($kuser, $kaltura_conf->adminSecret,'1',"edit:*",3600);
							$kaltura_client->setKs($kres['result']['ks']);
							$kres = $kaltura_client->cloneRoughcut($kuser,$videoId[0]);
							$roughtcutId = $kres['result']['id'];							
						}
						$seflashVars = $seflashVars.
							'&kshow_id=entry-' . $roughcutId.
							'&entry_id='. $roughcutId;
					}else{
						$kres = $kaltura_client->addRoughcutEntry($kuser,-2);
						$roughcutId = $kres['result']['entry']['id'];
						$kres = $kaltura_client->appendEntryToRoughcut($kuser,$videoId[0],'entry-'.$roughcutId,$roughcutId);
						$seflashVars = $seflashVars.
							'&kshow_id=entry-' . $roughcutId.
							'&entry_id='. $roughcutId;		
					}
				}else{
					$kres = $kaltura_client->addRoughcutEntry($kuser,-2);
					$roughcutId = $kres['result']['entry']['id'];
					for ($i=0; $i < count($videoId); $i++){
						$kres = $kaltura_client->appendEntryToRoughcut($kuser,$videoId[$i],'entry-'.$roughcutId,$roughcutId);
					}
					
					$seflashVars = $seflashVars.
						'&kshow_id=entry-' . $roughcutId.
						'&entry_id='. $roughcutId;	
				}			
		}
		$smarty->assign_by_ref('seflashVars',$seflashVars);
		$smarty->assign_by_ref('videoId',$roughcutId);
		break;
	case 'edit':
		if($tiki_p_edit_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y' ){
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied: You cannot edit video information"));
			$smarty->display('error.tpl');
			die;
		} else {		
				$videoInfo = $videogallib->get_video_info($videoId[0],$kaltura_client);
				if (!$videoInfo) {
					$smarty->assign('msg', tra("Incorrect param"));
					$smarty->display('error.tpl');
					die;
				}
				$smarty->assign_by_ref('videoId',$videoId[0]);
				$smarty->assign_by_ref('videoInfo',$videoInfo);
		}
		break;
	case 'delete':
		if($tiki_p_delete_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y' ){
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied: You cannot delete kaltura video"));
			$smarty->display('error.tpl');
			die;
		} else {
			$area = 'delkalturaentry';
			if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
				key_check($area);
				$videogallib->delete_video($videoId);
		    } else {
				key_get($area);
			}
			
			header ('Location: tiki-list_kaltura_entries.php');
			die;

		}
		break;
	case 'download':
		if($tiki_p_download_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y' ){
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied: You cannot download kaltura video"));
			$smarty->display('error.tpl');
			die;
		} else {
			$kres = $kaltura_client->addDownload($kuser,$videoId[0],'flv');
			print_r($kres);
			$kres = $kaltura_client->getEntry($kuser,$videoId[0],1);
			print_r($kres);
			//header ('Location: tiki-list_kaltura_entries.php');
			//die;
		}
		break;
	case 'default':
		
		$smarty->assign('msg', tra("Incorrect param"));
		$smarty->display('error.tpl');
		die;
		
	}
		
}else{

	if(!empty($_REQUEST['videoId'])){
		$videoId[] = $_REQUEST['videoId'];
		if(!empty($_REQUEST['videoId']) && $tiki_p_view_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y' ){
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied: You cannot view video"));
			$smarty->display('error.tpl');
			die;
		}
	
	$smarty->assign('mode', 'view');
	$videoInfo = $videogallib->get_video_info($videoId[0],$kaltura_client);
	$smarty->assign_by_ref('videoId',$videoId[0]);
	$smarty->assign_by_ref('videoInfo',$videoInfo);
	}
}
$cwflashVars = 'userId=' .$kuser->userId.
	'&sessionId=' .$kres["result"]["ks"]. 
	'&partnerId=' . $kaltura_conf->partnerId .
	'&subPartnerId=' . $kaltura_conf->subPartnerId . 
	'&kshow_id=-1' . 
	'&afterAddEntry=afterAddEntry'.
	'&showCloseButton=false';

$smarty->assign_by_ref('cwflashVars',$cwflashVars);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template

	$smarty->assign('mid','tiki-kaltura_video.tpl');
	$smarty->display("tiki.tpl");
