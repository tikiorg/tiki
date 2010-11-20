<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
require_once ('tiki-setup.php');
$access->check_feature('feature_invite');

function tiki_invited() {
	global $smarty, $tikilib, $prefs, $user, $userlib;

	$invite=(int)isset($_REQUEST['invite']) ? $_REQUEST['invite'] : 0;
	$email=isset($_REQUEST['email']) ? $_REQUEST['email'] : null;

	if (($invite <= 0) || empty($email)) die("invalid request");

	$res=$tikilib->query("SELECT * FROM tiki_invited WHERE id_invite=? AND email=? AND used=?",
						 array($invite, $email, "no"));
	$invited=$res->fetchRow();

	if (!is_array($invited)) {
		$error=tra("This invitation does not exist or is deprecated");
		$smarty->assign('error', $error);
		$smarty->assign('mid', 'tiki-invited.tpl');
		$smarty->display("tiki.tpl");
		return;
	}


	$smarty->assign("invite", $invite);
	$smarty->assign("email", $email);

	$res=$tikilib->query("SELECT * FROM tiki_invite WHERE id=?", array($invite));
	$inviterow=$res->fetchRow();
	if (!is_array($inviterow)) die("(bug) This invitation does not exist or is deprecated");


	if (isset($_POST['validate-existing-account'])) {
		
		$groups = $tikilib->getOne("SELECT `tiki_invite`.`groups` FROM `tiki_invited` LEFT JOIN `tiki_invite` ON `tiki_invite`.`id` = `tiki_invited`.`id_invite` WHERE `tiki_invited`.`id` = ?",
									   array($invited['id']));
		$groups = explode(',', $groups);
		foreach ($groups as $group)
			$userlib->assign_user_to_group($user, trim($group));

		$tikilib->query("UPDATE tiki_invited SET used=?, used_on_user=? WHERE id=?", array("logged", $user, $invited['id']));

		if (!empty($inviterow['wikipageafter'])) {
			$_SERVER['SCRIPT_URI'] =  empty($_SERVER['SCRIPT_URI']) ? 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_URI'];			        
			$redirect=str_replace('tiki-invited.php', 'tiki-index.php?page=', $_SERVER['SCRIPT_URI']).urlencode($inviterow['wikipageafter']);
			header('Location: '.$redirect);
			exit;
		}

		$error=tra("Congratulations! You are now part of this invitation group(s)");
		$smarty->assign('error', $error);
		$smarty->assign('mid', 'tiki-invited.tpl');
		$smarty->display("tiki.tpl");
		return;
	} else {
		$text=$tikilib->parse_data($inviterow['wikicontent']);
		$text=str_replace('{email}', $invited['email'], $text);
		$text=str_replace('{firstname}', $invited['firstname'], $text);
		$text=str_replace('{lastname}', $invited['lastname'], $text);
		$smarty->assign('parsed', $text);
	}

	$smarty->assign('mid', 'tiki-invited.tpl');
	$smarty->display("tiki.tpl");
}

tiki_invited();
