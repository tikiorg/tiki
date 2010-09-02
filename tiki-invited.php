<?php

require_once ('tiki-setup.php');
if ($prefs['feature_invit'] != 'y') die("feature_invit not enabled");

function tiki_invited() {
	global $smarty, $tikilib, $prefs, $user, $userlib;

	$invit=(int)isset($_REQUEST['invit']) ? $_REQUEST['invit'] : 0;
	$email=isset($_REQUEST['email']) ? $_REQUEST['email'] : null;

	if (($invit <= 0) || empty($email)) die("invalid request");

	$res=$tikilib->query("SELECT * FROM tiki_invited WHERE id_invit=? AND email=? AND used=?",
						 array($invit, $email, "no"));
	$invited=$res->fetchRow();

	if (!is_array($invited)) {
		$error=tra("This invitation does not exist or is deprecated");
		$smarty->assign('error', $error);
		$smarty->assign('mid', 'tiki-invited.tpl');
		$smarty->display("tiki.tpl");
		return;
	}


	$smarty->assign("invit", $invit);
	$smarty->assign("email", $email);

	$res=$tikilib->query("SELECT * FROM tiki_invit WHERE id=?", array($invit));
	$invitrow=$res->fetchRow();
	if (!is_array($invitrow)) die("(bug) This invitation does not exist or is deprecated");


	if (isset($_POST['validate-existing-account'])) {
		
		$groups = $tikilib->getOne("SELECT `tiki_invit`.`groups` FROM `tiki_invited` LEFT JOIN `tiki_invit` ON `tiki_invit`.`id` = `tiki_invited`.`id_invit` WHERE `tiki_invited`.`id` = ?",
									   array($invited['id']));
		$groups = explode(',', $groups);
		foreach ($groups as $group)
			$userlib->assign_user_to_group($user, trim($group));

		$tikilib->query("UPDATE tiki_invited SET used=?, used_on_user=? WHERE id=?", array("logged", $user, $invited['id']));

		if (!empty($invitrow['wikipageafter'])) {
			$redirect=str_replace('tiki-invited.php', 'tiki-index.php?page=', $_SERVER['SCRIPT_URI']).urlencode($invitrow['wikipageafter']);
			header('Location: '.$redirect);
			exit;
		}

		$error=tra("Congratulation ! you are now part of this invitation group(s)");
		$smarty->assign('error', $error);
		$smarty->assign('mid', 'tiki-invited.tpl');
		$smarty->display("tiki.tpl");
		return;
	} else {
		$text=$tikilib->parse_data($invitrow['wikicontent']);
		$text=str_replace('{email}', $invited['email'], $text);
		$text=str_replace('{firstname}', $invited['firstname'], $text);
		$text=str_replace('{lastname}', $invited['lastname'], $text);
		$smarty->assign('parsed', $text);
	}

	$smarty->assign('mid', 'tiki-invited.tpl');
	$smarty->display("tiki.tpl");
}

tiki_invited();

?>