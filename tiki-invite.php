<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
require_once ('tiki-setup.php');
$access->check_feature('feature_invite');
$access->check_permission('tiki_p_invite');

require_once ('lib/webmail/tikimaillib.php');

@ini_set('max_execution_time', 0); //will not work if safe_mode is on
$prefs['feature_wiki_protect_email'] = 'n'; //not to alter the email

/* csv format: lastname,firstname,mail */
/**
 * @param $bloc
 * @return array
 */
function parsemails_csv($bloc)
{
	$results=array();
	$lines = preg_split('/[\n\r]+/', $bloc);
	foreach ($lines as $line) {
		$l=explode(',', $line);
		$r=array();
		$r['lastname']=trim($l[0]);
		$r['firstname']=trim($l[1]);
		$r['email']=trim($l[2]);
		if (strpos($r['email'], '@') !== FALSE)
			$results[]=$r;
	}
	return $results;
}

/* everything format */
/**
 * @param $bloc
 * @return array
 */
function parsemails_all($bloc)
{
	$bloc=str_replace("\r\n", "\n", $bloc);
	$bloc=str_replace("\n\r", "\n", $bloc);
	$bloc=str_replace("\r", "\n", $bloc);
	$mails = preg_split('/[^a-zA-Z0-9@._-]/', $bloc);

	$results=array();
	foreach ($mails as $m) {
		$m=trim($m);
		if (strpos($m, '@') === FALSE) continue;
		if (strpos($m, '.') === FALSE) continue;
		$r=array();
		$r['lastname']='';
		$r['firstname']='';
		$r['email']=$m;
		$results[]=$r;
	}
	return $results;
}

$previous=array();
$res=$tikilib->query("SELECT * FROM `tiki_invite` ORDER by `ts` DESC");
while (is_array($row = $res->fetchRow())) {
    $row['datetime'] = strftime('%c', $row['ts']);
    $previous[$row['id']] = $row;
}
$smarty->assign('previous', $previous);

if (isset($_REQUEST['loadprevious']) && !empty($_REQUEST['loadprevious']) && isset($previous[(int)$_REQUEST['loadprevious']])) {
    $prev=$previous[(int)$_REQUEST['loadprevious']];
    $res=$tikilib->query("SELECT * FROM `tiki_invited` WHERE id_invite=?", array((int)$_REQUEST['loadprevious']));
    $prev_invited="";
    while(is_array($row = $res->fetchRow())) $prev_invited.=$row['lastname'].','.$row['firstname'].','.$row['email']."\n";

    $_REQUEST['emailslist'] = $prev_invited;
    $_REQUEST['emailslist_format'] = 'csv';
    $_REQUEST['emailsubject'] = $prev['emailsubject'];
    $_REQUEST['emailcontent'] = $prev['emailcontent'];
    $_REQUEST['wikicontent'] = $prev['wikicontent'];
    $_REQUEST['wikipageafter'] = $prev['wikipageafter'];
    $_REQUEST['invitegroups'] = explode(',', $prev['groups']);
}

$user_details = $userlib->get_user_details($user);
$allgroups = $userlib->get_groups(0, -1, 'groupName_desc', '', '', 'n');
$invitegroups=array();
foreach ($allgroups['data'] as $agroup)
    $invitegroups[$agroup['groupName']] = $agroup['groupDesc'];
$smarty->assign("invitegroups", $invitegroups);
$smarty->assign("usergroups", $user_details['groups']);


if (isset($_REQUEST['send'])) {

    $_text=$_REQUEST["emailcontent"];
    $_text=str_replace("\r\n", "\n", $_text);
    $_text=str_replace("\n\r", "\n", $_text);
    $_text=str_replace("\r", "\n", $_text);

    $mails=$_REQUEST["emailslist"];

	switch($_REQUEST['emailslist_format']) {
		case 'all':
			$emails=parsemails_all($mails);
			break;
		case 'csv':
			$emails=parsemails_csv($mails);
			break;
		default: $emails=array();
	}

    $igroups=$_REQUEST['invitegroups'];

	if (!empty($_REQUEST['confirm'])) {
		$tikilib->query(
			"INSERT INTO `tiki_invite` (inviter, groups, ts, emailsubject,emailcontent,wikicontent,wikipageafter) VALUES (?,?,?,?,?,?,?)",
			array(
				$user,
				count($igroups) ? implode(',', $igroups) : null,
				$tikilib->now,
				$_REQUEST['emailsubject'],
				$_REQUEST['emailcontent'],
				$_REQUEST['wikicontent'],
				empty($_REQUEST['wikipageafter']) ? NULL : $_REQUEST['wikipageafter']
			)
		);
      $res=$tikilib->query("SELECT MAX(id) as `id` FROM `tiki_invite` WHERE `inviter`=? AND `ts`=?", array($user, $tikilib->now));
        $row=$res->fetchRow(); $id=$row['id'];

      foreach ($emails as $m)
			$tikilib->query(
				"INSERT INTO `tiki_invited` (id_invite, email, firstname, lastname, used) VALUES (?,?,?,?,?)",
				array($id, $m['email'], $m['firstname'], $m['lastname'], "no")
			);

		  $_SERVER['SCRIPT_URI'] =  empty($_SERVER['SCRIPT_URI']) ? 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_URI'];
        foreach ($emails as $m) {
            $mail = new TikiMail();
            $mail->setFrom($prefs['sender_email']);
            $mail->setSubject($_REQUEST["emailsubject"]);
            $url=str_replace('tiki-invite.php', 'tiki-invited.php', $_SERVER['SCRIPT_URI'])
                .'?invite='.$id.'&email='.urlencode($m['email']);
            $text=$_text;
            $text=str_replace('{link}', $url, $text);
            $text=str_replace('{email}', $m['email'], $text);
            $text=str_replace('{firstname}', $m['firstname'], $text);
            $text=str_replace('{lastname}', $m['lastname'], $text);
            $mail->setText($text);
            $mail->send(array($m['email']));
        }

        $smarty->assign('sentresult', true);
	}
   $smarty->assign('emails', $emails);
}


$smarty->assign('mid', 'tiki-invite.tpl');
$smarty->display("tiki.tpl");
