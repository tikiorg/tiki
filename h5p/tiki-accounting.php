<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
$smarty->assign('bookId', $bookId);

$globalperms = Perms::get();
$objectperms = Perms::get(array( 'type' => 'accounting book', 'object' => $bookId ));
if (!($globalperms->acct_view or $objectperms->acct_view)) {
	$smarty->assign('msg', tra("You do not have the right view this page"));
	$smarty->display("error.tpl");
	die;		
}

$book=$accountinglib->getBook($bookId);
$smarty->assign('book', $book);

$accounts=$accountinglib->getExtendedAccounts($bookId, true);
$smarty->assign('accounts', $accounts);

if (!isset($_REQUEST['journalLimit'])) {
	$_REQUEST['journalLimit']=-25;
}
$journal=$accountinglib->getJournal($bookId, '%', '`journalId` DESC', $_REQUEST['journalLimit']);
$smarty->assign('journal', $journal);

if ($globalperms->acct_book or $objectperms->acct_book) {
	$smarty->assign('canBook', true);
} else {
	$smarty->assign('canBook', false);
}
if ($globalperms->acct_book_stack or $objectperms->acct_book_stack) {
	$smarty->assign('canStack', true);
} else {
	$smarty->assign('canStack', false);
}
ask_ticket('accounting');


$smarty->assign('mid', 'tiki-accounting.tpl');
$smarty->display("tiki.tpl");
