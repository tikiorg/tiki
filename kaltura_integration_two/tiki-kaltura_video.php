<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
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

if(!isset($kres["result"]["ks"])) {
	$smarty->assign('msg', tra("Could not establish Kaltura session. Try again"));
	$smarty->display('error.tpl');
	die;
}
	
if (isset($_REQUEST["kcw"])) {
		
	$entries = $_REQUEST["entryId"];
	for($i=0; $i < count($entries); $i++) {
		
		$kres= $kaltura_client->getEntry ( $kuser,$entries[$i],1);

		
		if(!isset($kres['result']['entry'])){
			$smarty->assign('msg', tra("Could not get newly added media information"));
			$smarty->display('error.tpl');
		die;
		}
		$videoEntries[$i] = $kres['result']['entry'];	
	}
	$smarty->assign('mode','new_entries');
	$smarty->assign_by_ref('entries',$videoEntries);
}

if (isset($_REQUEST["update"])){
	if($tiki_p_edit_videos == 'y' || $tiki_p_admin_kaltura == 'y' || $tiki_p_admin == 'y'){
		
		$entry = new KalturaEntry();
		$entry->name= $_REQUEST['name'];
		$entry->description = $_REQUEST['description'];
		$entry->tags = $_REQUEST['tags'];
		$kres= $kaltura_client->updateEntry($kuser,$_REQUEST['videoId'],$entry);
		if(!isset($kres['result']['entry'])){
			$smarty->assign('msg', tra("Failed to update information. Try again"));
			$smarty->display('error.tpl');
		die;
		}	
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
					'&backF=CloseClick'.
					'&saveF=SaveClick';
				$editor = $_REQUEST['editor'];

				if(count($videoId) == 1){
					$res = $kaltura_client->getEntry($kuser,$videoId[0],'');
					$mediaType = $res['result']['entry']['mediaType'];
					if($mediaType == 6 ){
						$roughcutId =$videoId[0];							
						if($mode == 'dupl') {
							$kres = $kaltura_client->startAdminSession($kuser, $kaltura_conf->adminSecret,'1',"edit:*",'');
							if(!isset($kres["result"]["ks"])) {
								$smarty->assign('msg', tra("Could not establish Kaltura session. Try again"));
								$smarty->display('error.tpl');
								die;
							}							
							$kaltura_client->setKs($kres['result']['ks']);
							$kres = $kaltura_client->cloneRoughcut($kuser,$videoId[0]);
							if(!isset($kres["result"]["entry"]["id"])) {
								$smarty->assign('msg', tra("Failed to duplicate the remix. Try again"));
								$smarty->display('error.tpl');
								die;
							}
							$roughtcutId = $kres['result']['result']['id'];							
						}
						$seflashVars = $seflashVars.
							'&kshow_id=entry-' . $roughcutId.
							'&entry_id='. $roughcutId;
					}else{
						$kres = $kaltura_client->addRoughcutEntry($kuser,-2);
						if(!isset($kres["result"]["entry"]["id"])) {
							$smarty->assign('msg', tra("Failed to start the remix. Try again"));
							$smarty->display('error.tpl');
							die;
						}
						$roughcutId = $kres['result']['entry']['id'];
						$kres = $kaltura_client->appendEntryToRoughcut($kuser,$videoId[0],'entry-'.$roughcutId,$roughcutId);
						$seflashVars = $seflashVars.
							'&kshow_id=entry-' . $roughcutId.
							'&entry_id='. $roughcutId;		
					}
				}else{
					$kres = $kaltura_client->addRoughcutEntry($kuser,-2);
					if(!isset($kres["result"]["id"])) {
						$smarty->assign('msg', tra("Failed to start the remix. Try again"));
						$smarty->display('error.tpl');
						die;
					}
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
		$smarty->assign_by_ref('editor',$editor);
		break;
	case 'edit':
		if($tiki_p_edit_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y' ){
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied: You cannot edit video information"));
			$smarty->display('error.tpl');
			die;
		} else {		
				$kres= $kaltura_client->getEntry($kuser,$videoId[0],1);
				if(empty($kres["result"]["entry"])) {
					$smarty->assign('msg', tra("Could not get media information"));
					$smarty->display('error.tpl');
					die;
				}
				$videoInfo = $kres['result']['entry'];
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
					for($i=0; $i < count($videoId); $i++) {

						$kres = $kaltura_client->startAdminSession($kuser, $kaltura_conf->adminSecret,1,"edit:*",null);
						$kaltura_client->setKS($kres['result']['ks']);
						$kres= $kaltura_client->deleteEntry($kuser,$videoId[$i]);
					}
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
			header ('Location: tiki-list_kaltura_entries.php');
			die;
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
	$kres= $kaltura_client->getEntry ( $kuser,$videoId[0],1);
	$videoInfo = $kres['result']['entry'];
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
