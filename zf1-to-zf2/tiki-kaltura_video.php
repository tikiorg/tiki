<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'tiki-setup.php';

$access->check_feature('feature_kaltura');

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

	if (!empty($_REQUEST['mediaId'])) {
		if (is_array($_REQUEST['mediaId'])) {
			$videoId = $_REQUEST['mediaId'];
		} else {
			$videoId[0] = $_REQUEST['mediaId'];
		}
		$kentryType = 'media';
	}

	$mode = null;
	if (!empty($videoId) && isset($_REQUEST['action'])) {
		$mode = $_REQUEST['action'];
		$smarty->assign('kmode', $mode);
		$smarty->assign('entryType', $kentryType);

		switch ($mode) {

			case 'dupl':
				//TODO there must be a way to make this work with non remix type. If not, to remove.
				$access->check_permission(array('tiki_p_upload_videos'));
				if ($kentryType == 'mix') {
					$kalturalib = TikiLib::lib('kalturauser');
					$knewmixEntry = $kalturalib->cloneMix($videoId[0]);
				}
				header('Location: tiki-list_kaltura_entries.php');
				exit;

			case 'delete':
				$access->check_permission(array('tiki_p_delete_videos'));
				$access->check_authenticity();
				if ($kentryType == 'media') {
					$kalturalib = TikiLib::lib('kalturauser');
					foreach ( $videoId as $vi ) {
						$kalturalib->deleteMedia($vi);
					}
				}
				if ($kentryType == 'mix') {
					$kalturalib = TikiLib::lib('kalturauser');
					foreach ( $videoId as $vi ) {
						$kalturalib->deleteMix($vi);
					}
				}
				header('Location: tiki-list_kaltura_entries.php');
				exit;

			case 'download':
				$access->check_permission(array('tiki_p_download_videos'));
				$kalturalib = TikiLib::lib('kalturauser');
				$kres = $kalturalib->flattenVideo($videoId[0]);

				header('Location: tiki-kaltura_video.php?videoId=' . $videoId[0]);
				exit;

			case 'edit':
				$access->check_permission(array('tiki_p_edit_videos'));
				$kalturaadminlib = TikiLib::lib('kalturaadmin');

				if ($kentryType == 'mix') {
					$kentry = $kalturaadminlib->getMix($videoId[0]);
					if ($_REQUEST['update']) {
						$knewentry = $kalturaadminlib->updateMix(
							$videoId[0],
							array(
								'name' => $_REQUEST['name'],
								'description' => $_REQUEST['description'],
								'tags' => $_REQUEST['tags'],
								'editorType' => $_REQUEST['editor'] === 'kse' ? 1 : 2,
								'adminTags' => $_REQUEST['adminTags'],
							)
						);
					}
				}
				if ($kentryType == 'media') {
					$kentry = $kalturaadminlib->getMedia($videoId[0]);
					if ($_REQUEST['update']) {
						$knewentry = $kalturaadminlib->updateMedia(
							$videoId[0], array(
								'name' => $_REQUEST['name'],
								'description' => $_REQUEST['description'],
								'tags' => $_REQUEST['tags'],
								'adminTags' => $_REQUEST['adminTags'],
							)
						);
					}
				}
				if ($_REQUEST['update']) {
					header('Location: tiki-kaltura_video.php?' . $kentryType . 'Id=' . $videoId[0]);
					exit;
				}
				$smarty->assign('videoId', $videoId[0]);
				$smarty->assign('videoInfo', $kentry);
				$smarty->assign('kalturaSession', $kalturaadminlib->getSessionKey());
				break;

			case 'default':
				$smarty->assign('msg', tra('Incorrect param'));
				$smarty->display('error.tpl');
				exit;
		}

	} else {
		if (isset($videoId[0])) {
			$access->check_permission(array('tiki_p_view_videos'));
			$smarty->assign('kmode', 'view');
			$kalturalib = TikiLib::lib('kalturauser');

			if ($kentryType == 'mix') {
				$kentry = $kalturalib->getMix($videoId[0]);
			}

			if ($kentryType == 'media') {
				$kentry = $kalturalib->getMedia($videoId[0]);
			}
			$smarty->assign('videoId', $videoId[0]);
			$smarty->assign('videoInfo', $kentry);
			$smarty->assign('kalturaSession', $kalturalib->getSessionKey());
		}
		$smarty->assign('entryType', $kentryType);
	}

	if ($mode == 'edit' && !empty($prefs['kaltura_kdpEditUIConf'])) {
		$kaltura_kdpId = $prefs['kaltura_kdpEditUIConf'];
	} else {
		$kaltura_kdpId = $prefs['kaltura_kdpUIConf'];
	}
	$smarty->assign('kaltura_kdpId', $kaltura_kdpId);

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
