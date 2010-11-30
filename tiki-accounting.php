<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: $

$section = 'accounting';
require_once ('tiki-setup.php');
require_once ('lib/accounting/accountinglib.php');


// Feature available?
if ($prefs['feature_accounting'] !='y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_accounting");
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST['bookId'])) {
	$smarty->assign('msg', tra("Missing book id"));
	$smarty->display("error.tpl");
	die;
}
$bookId=$_REQUEST['bookId'];
$smarty->assign('bookId',$bookId);
$bookPermissions=$userlib->get_object_permissions_for_user ($bookId, 'accounting book', $user);
//print_r('<pre>');
//print_r($bookPermissions);
//print_r($tiki_p_acct_view);
//print_r('</pre>');
$book=$accountinglib->getBook($bookId);
$smarty->assign('book',$book);
//print_r('<pre style="color: red;">');
//print_r($book);
//print_r('</pre>');

$accounts=$accountinglib->getExtendedAccounts($bookId,true);
$smarty->assign('accounts',$accounts);
//print_r('<pre style="color: blue;">');
//print_r($accounts);
//print_r('</pre>');

if (!isset($_REQUEST['journalLimit'])) {
	$_REQUEST['journalLimit']=-25;
}
$journal=$accountinglib->getJournal($bookId,'%','`journalId` DESC',$_REQUEST['journalLimit']);
$smarty->assign('journal',$journal);

ask_ticket('accounting');

$template='tiki-accounting.tpl';

$smarty->assign('mid',$template);
$smarty->display("tiki.tpl");
