<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

if ($prefs['feature_kaltura'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_kaltura");
	$smarty->display("error.tpl");
	die;
}

include_once ("lib/videogals/KalturaClient_v3.php");
$secret = $prefs['secret'];
$admin_secret = $prefs['adminSecret'];
$partner_id = $prefs['partnerId'];
$SESSION_ADMIN = 2;
$SESSION_USER = 0;

$kconf = new KalturaConfiguration($partner_id);
$kclient = new KalturaClient($kconf);
$ksession = $kclient->session->start($secret,$user,$SESSION_USER,$partner_id,null,"edit:*");

if(!isset($ksession)) {
	$smarty->assign('msg', tra("Could not establish Kaltura session. Try again"));
	$smarty->display('error.tpl');
	die;
}
$kclient->setKs($ksession);

$kentryType = "";
$videoId = array();
if(!empty($_REQUEST['mixId'])){	
	if(is_array($_REQUEST['mixId'])){
		$videoId = $_REQUEST['mixId'];
	}else{
		$videoId[0] = $_REQUEST['mixId'];
	}
	$kentryType = "mix";
}

if(!empty($_REQUEST['mediaId'])){	
	if(is_array($_REQUEST['mediaId'])){
		$videoId = $_REQUEST['mediaId'];
	}else{
		$videoId[0] = $_REQUEST['mediaId'];
	}
	$kentryType = "media";
}

if(!empty($videoId) && isset($_REQUEST['action'])){

	$mode = $_REQUEST['action'];
	$smarty->assign_by_ref('mode',$mode);
	$smarty->assign_by_ref('entryType',$kentryType);
	
	switch($mode){
	
	case 'remix':
		if( $tiki_p_remix_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y' ){
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied: You cannot remix videos"));
			$smarty->display('error.tpl');
			die;
		}else{		
			
				$seflashVars = 'uid=' .$user.
					'&ks=' .$ksession. 
					'&partner_id=' . $partner_id .
					'&subp_id=' . $partner_id .'00'.
					'&backF=CloseClick'.
					'&saveF=SaveClick';
				$editor = $_REQUEST['editor'];	
				if($kentryType == "mix"){
					$seflashVars = $seflashVars.
						'&kshow_id=entry-' . $videoId[0].
						'&entry_id='. $videoId[0];	
				}
				if($kentryType == "media"){
					$kentry = $kclient->media->get($videoId[0]);
					$knewmixEntry = new KalturaMixEntry();
					$knewmixEntry->name = "Remix of ".$kentry->name;
					$knewmixEntry->editorType = 1; //SIMPLE
					$knewmixEntry = $kclient->mixing->add($knewmixEntry);

					$kclient->mixing->appendMediaEntry($knewmixEntry->id,$videoId[0]);

					header("Location: tiki-kaltura_video.php?action=remix&mixId=".$knewmixEntry->id);
				}
		$smarty->assign_by_ref('seflashVars',$seflashVars);
		$smarty->assign_by_ref('editor',$editor);
		$smarty->assign_by_ref('videoId',$videoId[0]);
			
		}
		break;
	case 'dupl':
		if( $tiki_p_uplaod_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y' ){
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied: You cannot upload videos"));
			$smarty->display('error.tpl');
			die;
		}else{
			if($kentryType == "mix"){
				$knewmixEntry = $kclient->mixing->cloneAction($videoId[0]);
			}
		}
		header ('Location: tiki-list_kaltura_entries.php');
		die;
		
		break;	
	case 'revert':
		if($tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y' ){
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied: You cannot change video version"));
			$smarty->display('error.tpl');
			die;
		} else {		
			$koldentry = $kclient->mixing->get($videoId[0],$_REQUEST['version']);
			$koldentry = new KalturaMixEntry();
			$knewentry = new KalturaMixEntry();
			$knewentry->name = $koldentry->name;
			$knewentry->description = $koldentry->description;
			$knewentry->userId = $koldentry->userId;
			$knewentry->tags = $koldentry->tags;
			$knewentry->adminTags = $koldentry->adminTags;
			$knewentry->groupId = $koldentry->groupId;
			$knewentry->partnerData = $koldentry->partnerData;
			$knewentry->licenseType = $koldentry->licenseType;
			$knewentry->editorType = $koldentry->editorType;
			$knewentry->dataContent =$koldentry->dataContent;

			$knewentry = $kclient->mixing->update($koldentry->id,$knewentry);
				$smarty->assign_by_ref('videoId',$knewentry->id);
				$smarty->assign_by_ref('videoInfo',$knewentry);
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
				if($kentryType == "media"){
					foreach( $videoId as $vi ) {
						$kclient->media->delete($vi);
					}
				}
				if($kentryType == "mix"){
					foreach( $videoId as $vi ) {
						$kclient->mixing->delete($vi);
					}					
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
			
			$kres = $kclient->mixing->requestFlattening($videoId[0],'flv');

			header ('Location: tiki-kaltura_video.php?videoId='.$videoId[0]);
			die;
		}
		break;
	case 'edit':

		if($tiki_p_edit_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y' ){
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied: You cannot edit video information"));
			$smarty->display('error.tpl');
			die;
		} else {
				if($kentryType == "mix"){
					$kentry = $kclient->mixing->get($videoId[0]);
					
				if($_REQUEST['update']){
					
					$kentry = $kclient->mixing->get($videoId[0]);
					$kentry->name = $_REQUEST['name'];
					$kentry->description = $_REQUEST['description'];
					$kentry->tags = $_REQUEST['tags'];
					$kentry->adminTags = $_REQUEST['adminTags'];
					$kentry->id = null;
					$kentry->partnerId = null;
					$kentry->status = null;
					$kentry->createdAt = null;
					$kentry->rank = null;
					$kentry->totalRank = null;
					$kentry->votes = null;
					$kentry->downloadUrl = null;
					$kentry->version = null;
					$kentry->thumbnailUrl = null;
					$kentry->plays = null;
					$kentry->views = null;
					$kentry->duration = null;
					$kentry->hasRealThumbnail = null;
					
					$knewentry = $kclient->mixing->update($videoId[0],$kentry);
				}
				}
				if($kentryType == "media"){
				$kentry = $kclient->media->get($videoId[0]);
				if($_REQUEST['update']){
					$kentry->name = $_REQUEST['name'];
					$kentry->description = $_REQUEST['description'];
					$kentry->tags = $_REQUEST['tags'];
					$kentry->adminTags = $_REQUEST['adminTags'];
					$kentry->id = null;
					$kentry->partnerId = null;
					$kentry->status = null;
					$kentry->createdAt = null;
					$kentry->rank = null;
					$kentry->totalRank = null;
					$kentry->votes = null;
					$kentry->downloadUrl = null;
					$kentry->version = null;
					$kentry->thumbnailUrl = null;
					$kentry->plays = null;
					$kentry->views = null;
					$kentry->duration = null;
					$kentry->hasRealThumbnail = null;
					$kentry->searchText = null;
					$kentry->mediaType = null;
					$kentry->sourceType = null;
					$kentry->searchProviderType = null;
					$kentry->searchProviderId = null;
					$kentry->dataUrl = null;
					
					$knewentry = $kclient->media->update($videoId[0],$kentry);
				}
				}

			if($_REQUEST['update']){
				
				
				header ('Location: tiki-kaltura_video.php?'.$kentryType.'Id='.$videoId[0]);
				die;
			}
		}
				$smarty->assign_by_ref('videoId',$videoId[0]);
				$smarty->assign_by_ref('videoInfo',$kentry);

		break;
	case 'default':
		
		$smarty->assign('msg', tra("Incorrect param"));
		$smarty->display('error.tpl');
		die;
		
	}
		
}else{

	if(isset($videoId[0])){
		if($tiki_p_view_videos != 'y' && $tiki_p_admin_kaltura != 'y' && $tiki_p_admin != 'y' ){
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied: You cannot view video"));
			$smarty->display('error.tpl');
			die;
		}
    
	$smarty->assign('mode', 'view');
	if($kentryType == "mix"){
		$kentry = $kclient->mixing->get($videoId[0]);
	}
	
	if($kentryType == "media"){
		$kentry = $kclient->media->get($videoId[0]);	
	}
	$smarty->assign_by_ref('videoId',$videoId[0]);
	$smarty->assign_by_ref('videoInfo',$kentry);
	}
	$smarty->assign_by_ref('entryType',$kentryType);
}

// Display the template
	$smarty->assign('mid','tiki-kaltura_video.tpl');
	$smarty->display("tiki.tpl");
