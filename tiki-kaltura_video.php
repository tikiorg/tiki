<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'tiki-setup.php';
require_once 'lib/videogals/kalturalib.php';

try {
	$kentryType = '';
	$videoId = array();
	if (!empty($_REQUEST['mixId'])) {
		if (is_array($_REQUEST['mixId'])) {
			$videoId = $_REQUEST['mixId'];
		} else {
			$videoId[0] = $_REQUEST['mixId'];
		}
		$kentryType = 'mix';
	}

	if ($user) {
		$kuser = $user;	
	} else {
		$kuser = 'Anonymous'; 
	}
	
	if (!empty($_REQUEST['mediaId'])) {	
		if (is_array($_REQUEST['mediaId'])) {
			$videoId = $_REQUEST['mediaId'];
		} else {
			$videoId[0] = $_REQUEST['mediaId'];
		}
		$kentryType = 'media';
	}

	if (!empty($videoId) && isset($_REQUEST['action'])) {
		$mode = $_REQUEST['action'];
		$smarty->assign('kmode', $mode);
		$smarty->assign('entryType', $kentryType);
	
		switch ($mode) {

			case 'dupl':
				//TODO there must be a way to make this work with non remix type. If not, to remove.
				$access->check_permission(array('tiki_p_upload_videos'));
				if ($kentryType == 'mix') {
					$knewmixEntry = $kalturalib->client->mixing->cloneAction($videoId[0]);
				}
				header('Location: tiki-list_kaltura_entries.php');
				die;
							break;

			case 'delete':
				$access->check_permission(array('tiki_p_delete_videos'));
				$access->check_authenticity();
				if ($kentryType == 'media') {
					foreach ( $videoId as $vi ) {
						$kalturalib->client->media->delete($vi);
					}
				}
				if ($kentryType == 'mix') {
					foreach ( $videoId as $vi ) {
						$kalturalib->client->mixing->delete($vi);
					}					
				}	
				header('Location: tiki-list_kaltura_entries.php');
				die;
							break;

			case 'download':
				$access->check_permission(array('tiki_p_download_videos'));
				$kres = $kalturalib->client->mixing->requestFlattening($videoId[0], 'flv');
	
				header('Location: tiki-kaltura_video.php?videoId=' . $videoId[0]);
				die;
							break;
	
			case 'edit':
				$access->check_permission(array('tiki_p_edit_videos'));
				if ($_REQUEST['update']) {
					$kalturalib->session = $kalturalib->client->session->start($prefs['kaltura_adminSecret'], $kuser, $SESSION_ADMIN, $prefs['kaltura_partnerId'], 86400, 'edit:*');
					$kalturalib->client->setKs($kalturalib->session);
				}
				if ($kentryType == 'mix') {
					$kentry = $kalturalib->client->mixing->get($videoId[0]);
				
					if ($_REQUEST['update']) {
						$kentry = new KalturaPlayableEntry();
						$kentry->name = $_REQUEST['name'];
						$kentry->description = $_REQUEST['description'];
						$kentry->tags = $_REQUEST['tags'];
						$kentry->editorType = $_REQUEST['editor'] === 'kse' ? 1 : 2;
						$kentry->adminTags = $_REQUEST['adminTags'];
						$knewentry = $kalturalib->client->mixing->update($videoId[0], $kentry);
					}
				}
				if ($kentryType == 'media') {
					$kentry = $kalturalib->client->media->get($videoId[0]);
				
					if ($_REQUEST['update']) {
						$kentry = new KalturaPlayableEntry();
						$kentry->name = $_REQUEST['name'];
						$kentry->description = $_REQUEST['description'];
						$kentry->tags = $_REQUEST['tags'];
						$kentry->adminTags = $_REQUEST['adminTags'];
		
						$knewentry = $kalturalib->client->media->update($videoId[0], $kentry);
					}
				}
				if ($_REQUEST['update']) {
					header('Location: tiki-kaltura_video.php?' . $kentryType . 'Id=' . $videoId[0]);
					die;
				}
				$smarty->assign_by_ref('videoId', $videoId[0]);
				$smarty->assign_by_ref('videoInfo', $kentry);
				$smarty->assign_by_ref('kalturaSession', $kalturalib->session);
							break;

			case 'default':
				$smarty->assign('msg', tra('Incorrect param'));
				$smarty->display('error.tpl');
			die;
		}
		
	} else {
		if (isset($videoId[0])) {
			$access->check_permission(array('tiki_p_view_videos'));
			$smarty->assign('kmode', 'view');
			if ($kentryType == 'mix') {
				$kentry = $kalturalib->client->mixing->get($videoId[0]);
			}
	
			if ($kentryType == 'media') {
				$kentry = $kalturalib->client->media->get($videoId[0]);	
			}
			$smarty->assign_by_ref('videoId', $videoId[0]);
			$smarty->assign_by_ref('videoInfo', $kentry);
			$smarty->assign_by_ref('kalturaSession', $kalturalib->session);
		}
		$smarty->assign_by_ref('entryType', $kentryType);
	}

	// Display the template
	$smarty->assign('mid', 'tiki-kaltura_video.tpl');
	$smarty->display('tiki.tpl');

} catch( Exception $e ) {
	$access->display_error(
					'', 
					tr('Communication error'), 
					500, 
					true, 
					tr('Invalid response provided by the Kaltura server. Please retry.') . '<br /><em>' . $e->getMessage() . '</em>' 
	);
}
