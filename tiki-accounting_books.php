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
if (!isset($_REQUEST['action'])) {
	$_REQUEST['action']='';
}
switch ($_REQUEST['action']) {
	case 'create' :
					$access->check_user($user);
					$access->check_permission('tiki_p_acct_create_book',tra('Create a new book'));
					$bookId=$accountinglib->createBook($_REQUEST['bookName'],
								$_REQUEST['bookStartDate'], $_REQUEST['bookEndDate'], 
								$_REQUEST['bookCurrency'], $_REQUEST['bookCurrencyPos'],
								$_REQUEST['bookDecimals'], $_REQUEST['bookDecPoint'],
								$_REQUEST['bookThousand'], $_REQUEST['exportSeparator'],
								$_REQUEST['exportEOL'], $_REQUEST['exportQuote']);
					if (!is_numeric($bookId)) {
						$errors[]=tra($bookId);
						$smarty->assign('errors',$errors);
						$smarty->assign('bookName',$_REQUEST['bookName']);
						$smarty->assign('bookStartDate',$_REQUEST['bookStartDate']);
						$smarty->assign('bookEndDate',$_REQUEST['bookEndDate']);
						$smarty->assign('bookCurrency',$_REQUEST['bookCurrency']);
						$smarty->assign('bookCurrencyPos',$_REQUEST['bookCurrencyPos']);
						$smarty->assign('bookDecimals',$_REQUEST['bookDecimals']);
						$smarty->assign('bookDecPoint',$_REQUEST['bookDecPoint']);
						$smarty->assign('bookThousand',$_REQUEST['bookThousand']);
						$smarty->assign('exportSeparator',$_REQUEST['exportSeparator']);
						$smarty->assign('exportEOL',$_REQUEST['exportEOL']);
						$smarty->assign('exportQuote',$_REQUEST['exportQuote']);
					}
					break;
	case 'close'  :
					break;
	case 'view'   :
					break;
	default ://list
}
$books=$accountinglib->listBooks();
$filtered = Perms::filter(array( 'type' => 'accounting book' ), 
			'object',
			$books,
			array( 'object' => 'bookName' ),
			'acct_view' );
$smarty->assign('books',$books);
ask_ticket('accounting');
$smarty->assign('mid','tiki-accounting_books.tpl');
$smarty->display("tiki.tpl");