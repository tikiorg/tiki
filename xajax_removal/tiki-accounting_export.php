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

if (!isset($_REQUEST['what'])) {
	$smarty->assign('msg', tra("Don't know what to export"));
	$smarty->display("error.tpl");
	die;
}
$bookId=$_REQUEST['bookId'];
$smarty->assign('bookId',$bookId);
$what=$_REQUEST['what'];
$smarty->assign('what',$what);

$globalperms = Perms::get();
$objectperms = Perms::get( array( 'type' => 'accounting book', 'object' => $bookId ) );
if (!($globalperms->acct_view or $objectperms->acct_view)) {
	$smarty->assign('msg', tra("You do not have the right export/view this data"));
	$smarty->display("error.tpl");
	die;		
}

$book=$accountinglib->getBook($bookId);
$smarty->assign('book',$book);

if (!isset($_REQUEST['action'])) {
	$_REQUEST['action']='settings';
}

if ($_REQUEST['action']=='export') {
	$prefs['log_tpl']='n'; // Necessary to get a working css
	$separator=(isset($_REQUEST['separator'])?$_REQUEST['separator']:';');
	$smarty->assign('separator',$separator);
	$eol=(isset($_REQUEST['eol'])?$_REQUEST['eol']:"\n");
	$smarty->assign('eol',preg_replace(array("/CR/","/LF/"),array("\r","\n"),$eol));
	$quote=(isset($_REQUEST['quote'])?$_REQUEST['quote']:'"');
	$smarty->assign('quote',$quote);
	header('Content-type: text/plain');
	switch ($what) {
		case "accounts" : 	header('Content-disposition: attachment; filename="accounts.csv"');
							$accounts=$accountinglib->getExtendedAccounts($bookId,true);
							$smarty->assign('accounts',$accounts);
							$smarty->display("tiki-accounting_accounts_csv.tpl");
							die();
		case "journal"  :	header('Content-disposition: attachment; filename="journal.csv"');
							if (isset($_REQUEST['accountId'])) {
								$accountId=$_REQUEST['accountId'];
							} else {
								$accountId='%';
							}
							$journal=$accountinglib->getJournal($bookId, $accountId,'`journalId` ASC');
							$smarty->assign('journal',$journal);
							$smarty->display("tiki-accounting_journal_csv.tpl");
							die();
	}
} else {
	$smarty->assign('mid','tiki-accounting_export.tpl');
	$smarty->display("tiki.tpl");	
}
