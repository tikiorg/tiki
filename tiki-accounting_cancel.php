<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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

if (!isset($_REQUEST['journalId'])) {
	$smarty->assign('msg', tra("Missing journal id"));
	$smarty->display("error.tpl");
	die;
}
$journalId=$_REQUEST['journalId'];
$smarty->assign('journalId', $journalId);

$globalperms = Perms::get();
$objectperms = Perms::get(array( 'type' => 'accounting book', 'object' => $bookId ));
if (!($globalperms->acct_view or $objectperms->acct_book)) {
	$smarty->assign('msg', tra("You do not have the right to cancel transactions"));
	$smarty->display("error.tpl");
	die;		
}

$book=$accountinglib->getBook($bookId);
$smarty->assign('book', $book);

$entry=$accountinglib->getTransaction($bookId, $journalId);
if ($entry===false) {
	$smarty->assign('msg', tra("Error retrieving data from journal"));
	$smarty->display("error.tpl");
	die;
}
$smarty->assign('entry', $entry);
$accountinglib->cancelTransaction($bookId, $journalId);

$smarty->assign('mid', 'tiki-accounting_cancel.tpl');
$smarty->display("tiki.tpl");
