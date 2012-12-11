<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'tiki-setup.php';
$access->check_feature('feature_kaltura');
require_once 'lib/videogals/kalturalib.php';
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

if (empty($kalturalib) || !$kalturalib->testSetup()) {
	$smarty->assign('msg', tra("You need to set your Kaltura account details: ") . '<a href="tiki-admin.php?page=video">' . tra('here') . '</a>');
	$smarty->display('error.tpl');
	die;
}
	
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
			if ($kentryType == 'media') {
				foreach ( $videoId as $vi ) {
					$kalturalib->client->media->delete($vi);
				}
				header('Location: tiki-list_kaltura_entries.php?list=media');
				die;
			}
				
			if ($kentryType == 'mix') {
				foreach ( $videoId as $vi ) {
					$kalturalib->client->mixing->delete($vi);
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

$sort_mode = '';

if ($_REQUEST['sort_mode']) {
	$sort_mode = $_REQUEST['sort_mode'];
} else {
	$sort_mode = 'desc_createdAt';
}

$smarty->assign_by_ref('sort_mode', $sort_mode);
$sort_mode = preg_replace('/desc_/', '-', $sort_mode);
$sort_mode = preg_replace('/asc_/', '+', $sort_mode);


if (isset($_REQUEST['find'])) {
	$find = $_REQUEST['find'];
} else {
	$find = '';
}
$smarty->assign('find', $find);

if ($_REQUEST['maxRecords']) {
	$page_size = $_REQUEST['maxRecords'];
} else {
	$page_size = $prefs['maxRecords'];
}

if ($_REQUEST['offset']) {
	$offset = $_REQUEST['offset'];
	$page = ($offset/$page_size) + 1;
} else {
	$offset = 0;
	$page = 0;
}

if ( $_REQUEST['list'] == 'mix' or !isset($_REQUEST['list']) ) {

	$kpager = new KalturaFilterPager();
	$kpager->pageIndex = $page;
	$kpager->pageSize = $page_size;

	$kfilter = new KalturaMixEntryFilter();
	$kfilter->orderBy = $sort_mode;
	$kfilter->nameMultiLikeOr = $find;
	
	if ($_REQUEST['view'] != 'browse') {
		// Get user's kaltura mix entries	
		$kmixlist = $kalturaadminlib->client->mixing->listAction($kfilter, $kpager);

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
		$offset = $_REQUEST['offset'];
		$kpager = new KalturaFilterPager();
		$kpager->pageIndex = ($offset/$page_size) + 1;
		$kpager->pageSize = $page_size;
		$kmixlist = $kalturaadminlib->client->mixing->listAction($kfilter, $kpager);
	}

	$smarty->assign_by_ref('klist', $kmixlist->objects);
	$smarty->assign_by_ref('cant', $kmixlist->totalCount);
	$smarty->assign('entryType', 'mix');
	$smarty->assign_by_ref('view', $_REQUEST['view']);
	$smarty->assign_by_ref('modifiedAt', $modifiedAt);
	$smarty->assign_by_ref('modifiedBy', $modifiedBy);
}

if ($_REQUEST['list'] == 'media') {

	$kfilter = new KalturaMediaEntryFilter();
	$kfilter->orderBy = $sort_mode;
	$kfilter->nameMultiLikeOr = $find;
	$kfilter->statusIn = '-1,-2,0,1,2';

	$kpager = new KalturaFilterPager();
	$kpager->pageIndex = $page;
	$kpager->pageSize = $page_size;

	if ($_REQUEST['view'] != 'browse') {
		$kmedialist = $kalturaadminlib->client->media->listAction($kfilter, $kpager);

		for ($i =0 ; $i < $kmedialist->totalCount; $i++) {
			$kmedialist->objects[$i]->mediaType = $mediaTypeAsString[$kmedialist->objects[$i]->mediaType];
			$kmedialist->objects[$i]->statusString = $statusAsString[$kmedialist->objects[$i]->status];
		}
	} else {

		$offset = $_REQUEST['offset'];
		$kpager = new KalturaFilterPager();
		$kpager->pageIndex = ($offset/$page_size) + 1;
		$kpager->pageSize = $page_size;
		$kmedialist = $kalturaadminlib->client->media->listAction($kfilter, $kpager);
	}
	$smarty->assign_by_ref('klist', $kmedialist->objects);
	$smarty->assign_by_ref('cant', $kmedialist->totalCount);
	$smarty->assign('entryType', 'media');
	$smarty->assign_by_ref('view', $_REQUEST['view']);
}

$smarty->assign_by_ref('offset', $offset);
$smarty->assign_by_ref('maxRecords', $page_size);
// Display the template
$smarty->assign('mid', 'tiki-list_kaltura_entries.tpl');
$smarty->display('tiki.tpl');
} catch( Exception $e ) {
	$access->display_error('', tra('Communication error'), 500, true, tra('Invalid response provided by the Kaltura server. Please retry.') . '<br /><em>' . $e->getMessage() . '</em>');
}

