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
$book=$accountinglib->getBook($bookId);
$smarty->assign('book', $book);

$globalperms = Perms::get();
$objectperms = Perms::get(array( 'type' => 'accounting book', 'object' => $bookId ));

if (!isset($_REQUEST['action'])) {
	$_REQUEST['action']='';
}

if ($_REQUEST['action']!='new' and !isset($_REQUEST['accountId'])) {
	$smarty->assign('msg', tra("Missing account id"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('action', $_REQUEST['action']);
if ($_REQUEST['action']=='' or $_REQUEST['action']=='view') {
	if (!($globalperms->acct_view or $objectperms->acct_view or
		  $globalperms->acct_book or $objectperms->acct_book)) {
		$smarty->assign('msg', tra("You do not have the rights to view this account"));
		$smarty->display("error.tpl");
		die;
	}
} else {
	if (!($globalperms->acct_manage_accounts or $objectperms->acct_manage_accounts)) {
		$smarty->assign('msg', tra("You do not have the rights to manage accounts"));
		$smarty->display("error.tpl");
		die;
	}
}
$accountId=$_REQUEST['accountId'];
$smarty->assign('accountId', $accountId);

$journal=$accountinglib->getJournal($bookId, $accountId);
$smarty->assign('journal', $journal);
switch ($_REQUEST['action']) {
	case 'edit' :
		$template="tiki-accounting_account_form.tpl";
		if (isset($_REQUEST['accountName'])) {
			if (!isset($_REQUEST['newAccountId'])) {
				$_REQUEST['newAccountId']=$accountId;
			}
			$result=$accountinglib->updateAccount(
				$bookId,
				$accountId,
				$_REQUEST['newAccountId'],
				$_REQUEST['accountName'],
				$_REQUEST['accountNotes'],
				$_REQUEST['accountBudget'],
				$_REQUEST['accountLocked'],
				0 /*$_REQUEST['accountTax'] */
			);
			if ($result!==true) {
				$smarty->assign('errors', $result);
			} else {
				$smarty->assign('action', 'view');
				$template="tiki-accounting_account_view.tpl";
			}
		}
		$account=$accountinglib->getAccount($bookId, $accountId, true);
		$smarty->assign('account', $account);
		break;
	case 'new' :
		$template="tiki-accounting_account_form.tpl";
		if (isset($_REQUEST['accountName'])) {
			$result=$accountinglib->createAccount(
				$bookId,
				$_REQUEST['newAccountId'], $_REQUEST['accountName'],
				$_REQUEST['accountNotes'], $_REQUEST['accountBudget'],
				$_REQUEST['accountLocked'], 0 /*$_REQUEST['accountTax'] */
			);
			if ($result!==true) {
				$smarty->assign('errors', $result);
			} else {
				$smarty->assign('action', 'view');
				$template="tiki-accounting_account_view.tpl";
			}
			$account=array(
				'accountBookId' => $bookId,
				'accountId' => $_REQUEST['newAccountId'],
				'accountName' => $_REQUEST['accountName'],
				'accountNotes' => $_REQUEST['accountNotes'],
				'accountBudget' => $_REQUEST['accountBudget'],
				'accountLocked' => $_REQUEST['accountLocked'],
				'accountTax' => $_REQUEST['accountTax'],
				'changeable' => true
			);
		} else {
			$account=array('changeable' => true);
		}
		$smarty->assign('account', $account);
		break;
	case 'lock':
		$accountinglib->changeAccountLock($bookId, $accountId);
		$account=$accountinglib->getAccount($bookId, $accountId, true);
		$smarty->assign('account', $account);
		$template="tiki-accounting_account_view.tpl";
		break;
	case 'delete' :
		$account=$accountinglib->getAccount($bookId, $accountId, true);
		$smarty->assign('account', $account);
		$result=$accountinglib->deleteAccount($bookId, $accountId);
		if ($result===true) {
			$template="tiki-accounting_account_deleted.tpl";
		} else {
			$smarty->assign('errors', $result);
			$account=$accountinglib->getAccount($bookId, $accountId, true);
			$smarty->assign('account', $account);
			$template="tiki-accounting_account_form.tpl";
		}
		break;
	default :
		$account=$accountinglib->getAccount($bookId, $accountId, true);
		$smarty->assign('account', $account);
		$template="tiki-accounting_account_view.tpl";
}
ask_ticket('accounting');
$smarty->assign('mid', $template);
$smarty->display("tiki.tpl");
