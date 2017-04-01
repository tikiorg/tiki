<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
if ($access->ticketMatch()) {
	if (empty($_REQUEST['registration_choices'])) {
		$_REQUEST['registration_choices'] = array();
	}
	$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
	$in = array();
	$out = array();
	foreach ($listgroups['data'] as $gr) {
		if ($gr['groupName'] == 'Anonymous') {
			continue;
		}
		if ($gr['registrationChoice'] == 'y' && !in_array($gr['groupName'], $_REQUEST['registration_choices'])) {
			// deselect
			$out[] = $gr['groupName'];
		} elseif ($gr['registrationChoice'] != 'y' && in_array($gr['groupName'], $_REQUEST['registration_choices'])) {
			//select
			$in[] = $gr['groupName'];
		}
	}
	if (count($in)) {
		$userlib->set_registrationChoice($in, 'y');
	}
	if (count($out)) {
		$userlib->set_registrationChoice($out, null);
	}
	if (count($in) || count($out)) {
		add_feedback('registration_choices', tra('registration choices'), 2);
	}
	if (!empty($_REQUEST['refresh_email_group'])) {
		$nb = $userlib->refresh_set_email_group();
		if ($nb > 0) {
			Feedback::success(tra(sprintf(tra("%d users were assigned to groups based on user emails matching the patterns defined for the groups."), $nb)));
		} else {
			Feedback::note(tra("No user emails matched the group pattern definitions, or the matching users were already assigned, or email patterns have not been set for any groups."));
		}
	}
}

$smarty->assign('gd_lib_found', function_exists('gd_info') ? 'y' : 'n');


if ($prefs['feature_antibot'] === 'y' && $prefs['captcha_questions_active'] !== 'y' && $prefs['recaptcha_enabled'] !== 'y') {
	// check Zend captcha will work
	$captcha = new Zend\Captcha\Dumb;

	try {
		$captchaId = $captcha->getId();	// simple test for missing random generator
	} catch (Exception $e) {
		Feedback::error(tr('This method of captcha is not supported by your server, please select another or upgrade.')
			. ' ' . $e->getMessage());
	}

}

$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
$smarty->assign("listgroups", $listgroups['data']);

global $blackL;
$blackL->charnum = 0;
$blackL->special = 0;
$blackL->length = 0;
$blackL->limit = 0;

if ($access->ticketMatch()) {
	if (isset($_POST['uploadIndex'])){
		if ($_FILES['passwordlist']['error'] === 4) Feedback::error(tr('You need to select a file to upload.'));
		else if ($_FILES['passwordlist']['error']) Feedback::error(tr('File Upload Error: ' . $_FILES['passwordlist']['error']));
		else{  // if file has been uploaded, and there are no errors, then index the file in the databse.
			$blackL->deletePassIndex();
			$blackL->createPassIndex();
			$blackL->loadPassIndex($_FILES['passwordlist']['tmp_name'],$_POST['loaddata']);
			Feedback::success(tra('Uploaded file has been populated into database and indexed. Ready to generate password lists.'));
		}
	} else if (isset($_POST['saveblacklist']) || isset($_POST['viewblacklist'])) {

		if (isset($_POST['charnum'])) {
			$blackL->charnum = 1;
		}
		if (isset($_POST['special'])) {
			$blackL->special = 1;
		}
		if (isset($_POST['length'])) {
			$blackL->length = 0;
		}
		if (isset($_POST['limit'])) {
			$blackL->limit = 0;
		}

		$blackL->length = $_POST['length'];
		$blackL->limit = $_POST['limit'];

		if (isset($_POST['viewblacklist'])) {  // if viewing the password list, enter plain text mode, spit out passwords, then exit.

			header('Content-type: text/plain');
			$blackL->generatePassList(false);
			exit;
		}
		// else if save blacklist chosen
		if ($blackL->generatePassList(true)) {
			$filename = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$blackL->generateBlacklistName();
			$smarty->assign('sucess_message', 'Passwod Blacklist Saved to Disk');
			$blackL->set_preference('pass_blacklist_file', $blackL->generateBlacklistName(false));
			$blackL->loadBlacklist($filename);
		}else Feedback::error(tr('Unable to Write Password File to Disk'));

	}else if (isset($_POST['deleteIndex'])){

		$blackL->deletePassIndex();
	}
}


$smarty->assign('file_using',$blackL->whatFileUsing());
$smarty->assign('length',$blackL->length);
$smarty->assign('charnum',$blackL->charnum);
$smarty->assign('special',$blackL->special);
$smarty->assign('limit',$blackL->limit);

$smarty->assign('num_indexed',$blackL->passIndexNum());
