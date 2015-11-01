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
	$smarty->assign('msg', tra('This feature is disabled') . ': feature_accounting');
	$smarty->display('error.tpl');
	die;
}

$globalperms = Perms::get();
$objectperms = Perms::get(array( 'type' => 'accounting book', 'object' => $bookId ));

if (!($globalperms->acct_book or $objectperms->acct_book)) {
	$smarty->assign('msg', tra('You do not have the right to book'));
	$smarty->display('error.tpl');
	die;
}

if (!isset($_REQUEST['bookId'])) {
	$smarty->assign('msg', tra('Missing book id'));
	$smarty->display('error.tpl');
	die;
}
$bookId = $_REQUEST['bookId'];
$smarty->assign('bookId', $bookId);

$book = $accountinglib->getBook($bookId);
$smarty->assign('book', $book);

$accounts = $accountinglib->getAccounts($bookId, $all = true);
$smarty->assign('accounts', $accounts);

if (isset($_REQUEST['book'])) {
	check_ticket('accounting');
	$result = $accountinglib->book(
		$bookId,
		$_REQUEST['journalDate'],
		$_REQUEST['journalDescription'],
		$_REQUEST['debitAccount'],
		$_REQUEST['creditAccount'],
		$_REQUEST['debitAmount'],
		$_REQUEST['creditAmount'],
		$_REQUEST['debitText'],
		$_REQUEST['creditText']
	);
	if (is_numeric($result)) {
		if (isset($_REQUEST['statementId'])) {
			$accountinglib->updateStatement($bookId, $_REQUEST['statementId'], $result);
		}
	}
} else {
	$result = 0;
}

if (is_array($result)) {
	$smarty->assign('errors', $result);
	$smarty->assign('journalDate', $_REQUEST['journalDate']);
	$smarty->assign('journalDescription', $_REQUEST['journalDescription']);
	$smarty->assign('debitAccount', $_REQUEST['debitAccount']);
	$smarty->assign('creditAccount', $_REQUEST['creditAccount']);
	$smarty->assign('debitAmount', $_REQUEST['debitAmount']);
	$smarty->assign('creditAmount', $_REQUEST['creditAmount']);
	$smarty->assign('debitText', $_REQUEST['debitText']);
	$smarty->assign('creditText', $_REQUEST['creditText']);
	if (isset($_REQUEST['statementId'])) {
		$smarty->assign('statementId', $_REQUEST['statementId']);
	}
} else {
	$smarty->assign('debitAccount', array(''));
	$smarty->assign('creditAccount', array(''));
	$smarty->assign('debitAmount', array(''));
	$smarty->assign('creditAmount', array(''));
	$smarty->assign('debitText', array(''));
	$smarty->assign('creditText', array(''));
}

ask_ticket('accounting');

$journal = $accountinglib->getJournal($bookId, '%', '`journalId` DESC', 5);
$smarty->assign('journal', $journal);

$smarty->assign('mid', 'tiki-accounting_entry.tpl');
$smarty->display('tiki.tpl');
