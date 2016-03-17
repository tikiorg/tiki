<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'tiki-setup.php';
$access->check_feature('feature_kaltura');
$access->check_permission(array('tiki_p_list_videos'));
//get_strings tra('List Media')

$mediaTypeAsString['2'] = 'Image';
$mediaTypeAsString['1'] = 'Video';
$mediaTypeAsString['5'] = 'Audio';

$statusAsString  = array(
	-2 => tra('Error importing'),
 	-1 => tra('Error converting'),
	0 => tra('Importing'),
	1 => tra('Processing'),
	2 => tra('Ready'),
	3 => tra('Deleted'),
	4 => tra('Pending'),
	5 => tra('Pending moderation'),
	6 => tra('Blocked'),
);



if (!isset($_REQUEST['list'])) {
	$_REQUEST['list'] = 'media'; // default media since mix is relegated	
}

try {
	if (isset($_REQUEST['action'])) {
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
		$entryType = $_REQUEST['list'];

		switch ($_REQUEST['action']) {

			case tra('Delete'):
				$access->check_permission(array('tiki_p_delete_videos'));
				$access->check_authenticity();
				$kalturalib = TikiLib::lib('kalturauser');

				if ($kentryType == 'media') {
					foreach ( $videoId as $vi ) {
						$kalturalib->deleteMedia($vi);
					}
					header('Location: tiki-list_kaltura_entries.php?list=media');
					die;
				}
					
				if ($kentryType == 'mix') {
					foreach ( $videoId as $vi ) {
						$kalturalib->deleteMix($vi);
					}					
					header('Location: tiki-list_kaltura_entries.php?list=mix');
					die;
				}
				break;

			case 'default':
				$smarty->assign('msg', tra('Invalid action'));
				$smarty->display('error.tpl');
				die;
		}
	}

	$sort_mode = $jitRequest->sort_mode->word() ?: 'desc_createdAt';

	$smarty->assign_by_ref('sort_mode', $sort_mode);
	$sort_mode = preg_replace('/desc_/', '-', $sort_mode);
	$sort_mode = preg_replace('/asc_/', '+', $sort_mode);

	$find = $jitRequest->find->text();
	$smarty->assign('find', $find);

	$page_size = $jitRequest->maxRecords->int() ?: $prefs['maxRecords'];
	$offset = max(0, $jitRequest->offset->int());
	$page = ($offset/$page_size) + 1;

	if ( $_REQUEST['list'] == 'mix' or !isset($_REQUEST['list']) ) {
		if ($_REQUEST['view'] != 'browse') {
			$kalturaadminlib = TikiLb::lib('kalturaadmin');
			$kmixlist = $kalturaadminlib->listMix($sort_mode, $page, $page_size, $find);

			for ($i =0 ; $i < $kmixlist->totalCount; $i++) {
				$kmixlist->objects[$i]->createdAt = date('d M Y h:i A', $kmixlist->objects[$i]->createdAt);
				$domdoc = new DOMDocument;
				$domdoc->loadXML($kmixlist->objects[$i]->dataContent);
				$xpath = new DOMXpath($domdoc);
				$elements = $xpath->query('/xml/MetaData/PuserId');
				foreach ($elements as $element) {
					$nodes = $element->childNodes;
					foreach ($nodes as $node) {
						$modifiedBy[$i] = $node->nodeValue;
					}
				}

				$elements = $xpath->query('/xml/MetaData/UpdatedAt');
				foreach ($elements as $element) {
					$nodes = $element->childNodes;
					foreach ($nodes as $node) {
						$modifiedAt[$i] = date('d M Y h:i A', $node->nodeValue);
					}
				}
			}

		} else {
			$kmixlist = $kalturaadminlib->listMix($sort_mode, $page, $page_size, $find);
		}

		$smarty->assign('klist', $kmixlist->objects);
		$smarty->assign('cant', $kmixlist->totalCount);
		$smarty->assign('entryType', 'mix');
		$smarty->assign('view', $_REQUEST['view']);
		$smarty->assign('modifiedAt', $modifiedAt);
		$smarty->assign('modifiedBy', $modifiedBy);
	}

	if ($_REQUEST['list'] == 'media') {

		$kalturaadminlib = TikiLib::lib('kalturaadmin');
		if ($jitRequest->view->alpha() != 'browse') {
			$kmedialist = $kalturaadminlib->listMedia($sort_mode, $page, $page_size, $find);

			for ($i =0 ; $i < $kmedialist->totalCount; $i++) {
				$kmedialist->objects[$i]->mediaType = $mediaTypeAsString[$kmedialist->objects[$i]->mediaType];
				$kmedialist->objects[$i]->statusString = $statusAsString[$kmedialist->objects[$i]->status];
			}
		} else {
			$kmedialist = $kalturaadminlib->listMedia($sort_mode, $page, $page_size, $find);
		}
		$smarty->assign('klist', $kmedialist->objects);
		$smarty->assign('cant', $kmedialist->totalCount);
		$smarty->assign('entryType', 'media');
		$smarty->assign('view', $jitRequest->view->alpha());
	}

	$smarty->assign('offset', $offset);
	$smarty->assign('maxRecords', $page_size);
	// Display the template
	$smarty->assign('mid', 'tiki-list_kaltura_entries.tpl');
	$smarty->display('tiki.tpl');
} catch( Exception $e ) {
	$access->display_error('', tra('Communication error'), 500, true, tra('Invalid response provided by the Kaltura server. Please retry.') . '<br /><em>' . $e->getMessage() . '</em>');
}

