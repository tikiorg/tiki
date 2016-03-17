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
	$smarty->assign('msg', tra('This feature is disabled') . ': feature_accounting');
	$smarty->display('error.tpl');
	die;
}

$globalperms = Perms::get();
$objectperms = Perms::get(array( 'type' => 'accounting book', 'object' => $bookId ));

if (!($globalperms->acct_book or $objectperms->acct_book_stack)) {
	$smarty->assign('msg', tra('You do not have the right to book into the stack'));
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

if (!isset($_REQUEST['stackId'])) {
	$stackId = 0;
} else {
	$stackId = $_REQUEST['stackId'];
}

if (isset($_REQUEST['hideform'])) {
	$smarty->assign('hideform', $_REQUEST['hideform']);
} else {
	$smarty->assign('hideform', 0);
}

$book = $accountinglib->getBook($bookId);
$smarty->assign('book', $book);

$accounts = $accountinglib->getAccounts($bookId, $all = true);
$smarty->assign('accounts', $accounts);

if (isset($_REQUEST['action'])) {
	check_ticket('accounting');
	if ($_REQUEST['action'] == 'book') {
		if ($stackId == 0) {
			// new entry
			$result = $accountinglib->stackBook(
				$bookId,
				$_REQUEST['stackDate'],
				$_REQUEST['stackDescription'],
				$_REQUEST['debitAccount'],
				$_REQUEST['creditAccount'],
				$_REQUEST['debitAmount'],
				$_REQUEST['creditAmount'],
				$_REQUEST['debitText'],
				$_REQUEST['creditText']
			);
		} else {
			// modify old entry
			$result = $accountinglib->stackUpdate(
				$bookId,
				$stackId,
				$_REQUEST['stackDate'],
				$_REQUEST['stackDescription'],
				$_REQUEST['debitAccount'],
				$_REQUEST['creditAccount'],
				$_REQUEST['debitAmount'],
				$_REQUEST['creditAmount'],
				$_REQUEST['debitText'],
				$_REQUEST['creditText']
			);
		}
		if (is_numeric($result)) {
			if (isset($_REQUEST['statementId'])) {
				$accountinglib->updateStatementStack($bookId, $_REQUEST['statementId'], $result);
			}
			$stackId = 0; //success means we can create a new entry
		}
	} elseif ($_REQUEST['action'] == 'delete') {
		$result = $accountinglib->stackDelete($bookId, $stackId);
		$stackId = 0;
	} elseif ($_REQUEST['action'] == 'confirm') {
		$result = $accountinglib->stackConfirm($bookId, $stackId);
		$stackId = 0;
	} else {
		// unknown action = nothing
		$result = 0;
	}
} else {
	$result = 0;
}

if (is_array($result)) {
	$smarty->assign('errors', $result);
	$smarty->assign('stackId', $stackId);
	$smarty->assign('stackDate', $_REQUEST['stackDate']);
	$smarty->assign('stackDescription', $_REQUEST['stackDescription']);
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
	if ($stackId!=0) {
		$stackEntry = $accountinglib->getStackTransaction($bookId, $_REQUEST['stackId']);
		$smarty->assign('stackId', $stackId);
		$smarty->assign('stackDate', $stackEntry['stackDate']);
		$smarty->assign('stackDescription', $stackEntry['stackDescription']);
		$debitAccount = array();
		$debitAmount = array();
		$debitText = array();

		for ($i=0, $iCountStackEntryDebit = count($stackEntry['debit']); $i<$iCountStackEntryDebit; $i++) {
			$debitAccount[] = $stackEntry['debit'][$i]['stackItemAccountId'];
			$debitAmount[] = $stackEntry['debit'][$i]['stackItemAmount'];
			$debitText[] = $stackEntry['debit'][$i]['stackItemText'];
		}

		$creditAccount = array();
		$creditAmount = array();
		$creditText = array();

		for ($i=0, $iCountStackEntryCredit = count($stackEntry['credit']); $i < $iCountStackEntryCredit; $i++) {
			$creditAccount[] = $stackEntry['credit'][$i]['stackItemAccountId'];
			$creditAmount[] = $stackEntry['credit'][$i]['stackItemAmount'];
			$creditText[] = $stackEntry['credit'][$i]['stackItemText'];
		}

		$smarty->assign('debitAccount', $debitAccount);
		$smarty->assign('creditAccount', $creditAccount);
		$smarty->assign('debitAmount', $debitAmount);
		$smarty->assign('creditAmount', $creditAmount);
		$smarty->assign('debitText', $debitText);
		$smarty->assign('creditText', $creditText);
	} else {
		$smarty->assign('stackId', $stackId);
		$smarty->assign('debitAccount', array(''));
		$smarty->assign('creditAccount', array(''));
		$smarty->assign('debitAmount', array(''));
		$smarty->assign('creditAmount', array(''));
		$smarty->assign('debitText', array(''));
		$smarty->assign('creditText', array(''));
	}
}

ask_ticket('accounting');

if ($globalperms->acct_book or $objectperms->acct_book) {
	$smarty->assign('canBook', true);
} else {
	$smarty->assign('canBook', false);
}

$stack=$accountinglib->getStack($bookId);
$smarty->assign('stack', $stack);
$smarty->assign('mid', 'tiki-accounting_stack.tpl');
$smarty->display('tiki.tpl');
