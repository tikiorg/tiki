<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.

require_once ('tiki-setup.php');
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

global $tikilib, $wikilib;
require_once('lib/wiki/wikilib.php');

require_once('lib/filegals/filegallib.php');
$filegallib = new FileGalLib;
if(isset($_REQUEST['file_upload']) && $_REQUEST['file_upload']==='y') {
	global $prefs, $user;

	$f_msgs = array();
	$file = realpath(trim($_REQUEST['file']));
	if (!file_exists($file)) {
		$f_msgs[] = tra('File does not exist');
	} else {
		$file_info = pathinfo($file);
		$size = filesize($file);
		$type = get_mime_type($file);

		if ($_REQUEST['upload_type'] === 'upl_gal') {
			$gal_info = $filegallib->get_file_gallery_info($_REQUEST['file_gal']);
			$savedir = $prefs['fgal_use_dir'];
			if ($savedir) {
				$data = null;
				$fhash = find_unique_name($savedir, $gal_info['name']);
				copy($file, $savedir . $fhash);
				$filegallib->insert_file($gal_info['galleryId'], $file_info['basename'], '', $file_info['basename'], $data, $size, $type, $user, $fhash, '');
				$f_msgs[] = tra('File successfully imported in gallery.');
			}
		} else if ($_REQUEST['upload_type'] === 'upl_att') {
			if (!empty($_REQUEST['page_name'])) {
				$fhash = $tikilib->get_attach_hash_file_name($file_info['basename']);
				if ($fhash) {
					copy($file, $prefs['w_use_dir'] . $fhash);
					$wikilib->wiki_attach_file($_REQUEST['page_name'], $file_info['basename'], $type, $size, ($prefs['w_use_db'] === 'dir')?'': $ret['data'], $_REQUEST["attach_comment"], $user, $fhash);
					$f_msgs[] = tra('File successfully attached to the page.');
				}
			}
		}
	}

}

function find_unique_name($directory, $start)
{
	$fhash = md5($start);

	while (file_exists($directory . $fhash)) {
		$fhash = md5(uniqid($fhash));
	}

	return $fhash;
}
function get_mime_type($file)
{
    $mime_types = array(
        "pdf"=>"application/pdf"
        ,"exe"=>"application/octet-stream"
        ,"zip"=>"application/zip"
        ,"docx"=>"application/msword"
        ,"doc"=>"application/msword"
        ,"xls"=>"application/vnd.ms-excel"
        ,"ppt"=>"application/vnd.ms-powerpoint"
        ,"gif"=>"image/gif"
        ,"png"=>"image/png"
        ,"jpeg"=>"image/jpg"
        ,"jpg"=>"image/jpg"
        ,"mp3"=>"audio/mpeg"
        ,"wav"=>"audio/x-wav"
        ,"mpeg"=>"video/mpeg"
        ,"mpg"=>"video/mpeg"
        ,"mpe"=>"video/mpeg"
        ,"mov"=>"video/quicktime"
        ,"avi"=>"video/x-msvideo"
        ,"3gp"=>"video/3gpp"
        ,"css"=>"text/css"
        ,"jsc"=>"application/javascript"
        ,"js"=>"application/javascript"
        ,"php"=>"text/html"
        ,"htm"=>"text/html"
        ,"html"=>"text/html"
    );
    $extension = strtolower(end(explode('.',$file)));
    return $mime_types[$extension];
}

if (isset($_REQUEST['new_prefs'])) {
	$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
	$in = array();
	$out = array();
	foreach ($listgroups['data'] as $gr) {
		if ($gr['groupName'] == 'Anonymous') {
			continue;
		}

		if ($gr['registrationChoice'] == 'y'
				&& isset($_REQUEST['registration_choices'])
				&& !in_array($gr['groupName'], $_REQUEST['registration_choices'])
		) {
			// deselect
			$out[] = $gr['groupName'];
		} elseif ($gr['registrationChoice'] != 'y'
						&& isset($_REQUEST['registration_choices'])
						&& in_array($gr['groupName'], $_REQUEST['registration_choices'])
		) { //select
			$in[] = $gr['groupName'];
		}
	}
	check_ticket('admin-inc-general');
	$pref_toggles = array(
		'feature_wiki_1like_redirection',
	);
	foreach ($pref_toggles as $toggle) {
		simple_set_toggle($toggle);
	}
	$pref_byref_values = array(
		'server_timezone',
	);
	foreach ($pref_byref_values as $britem) {
		byref_set_value($britem);
	}
	$tikilib->set_preference('display_timezone', $tikilib->get_preference('server_timezone'));
	// Special handling for tied fields: tikiIndex, urlIndex and useUrlIndex
}

$smarty->assign('now', $tikilib->now);

if (!empty($_REQUEST['testMail'])) {
	include_once('lib/webmail/tikimaillib.php');
	$mail = new TikiMail();
	$mail->setSubject(tra('Tiki Email Test'));
	$mail->setText(tra('Tiki Test email from:') . ' ' . $_SERVER['SERVER_NAME']);
	if (!$mail->send(array($_REQUEST['testMail']))) {
		$msg = tra('Unable to send mail');
		if ($tiki_p_admin == 'y') {
			$mailerrors = print_r($mail->errors, true);
			$msg .= $mailerrors;
		}
		$smarty->assign('error_msg', $msg);
	} else {
		 add_feedback('testMail', tra('Test mail sent to') . ' ' . $_REQUEST['testMail'], 3);
	}
}
$engine_type = getCurrentEngine();
$smarty->assign('db_engine_type', $engine_type);

$file_gals = $filegallib->list_file_galleries();
$smarty->assign('file_gals', $file_gals['data']);
$all_pages = $tikilib->get_all_pages();
$smarty->assign('all_pages', $all_pages);
$smarty->assign('f_msgs', $f_msgs);

ask_ticket('admin-inc-general');
