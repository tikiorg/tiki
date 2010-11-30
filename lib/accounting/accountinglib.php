<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id $

// this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

require_once ('lib/logs/logslib.php');

/**
 * This number will be added to the members userID to get the members account number. Currently this is hard coded in some views and here
 */
define('ACCOUNTING_MEMBER_BASE',40000);


/**
 * Basic functions used by the accounting feature
 *
 * <p>This file contains all functions used by more than one file from the ccsg_accounting feature.
 * This feature is a simple accounting/bookkeeping function.</p>
 *
 * @package	accounting
 * @author	Joern Ott <white@ott-service.de>
 * @version	1.2
 * @date	2010-11-16
 * @copyright	LGPL
 */

class AccountingLib extends LogsLib
{

	/**
	 * Lists all books available to a user
	 * @param	string	$order	sorting order
	 * @return	array			list of books (complete table structure) 
	 */
	function listBooks($order='bookId ASC') {
		$query="SELECT * FROM tiki_acct_book ORDER BY $order";
		return $this->fetchAll($query,array());
	}

	/**
	 * 
	 * Creates a new book and gives full permissions to the creator
	 * @param	string	$bookName			descriptive name of the book
	 * @param	date	$bookStartDate		first permitted date for the book
	 * @param	date	$bookEndDate		last permitted date for the book
	 * @param	string	$bookCurrency		up to 3 letter cuurency code
	 * @param	int		$bookCurrencyPos	where should the currency symbol appear -1=before, 1=after
	 * @param	int		$bookDecimals		number of decimal points
	 * @param	string	$bookDecPoint		separator for the decimal point
	 * @param	string	$bookThousand		separator for the thousands
	 * @param	string	$exportSeparator	separator between fields when exporting CSV
	 * @param	string	$exportEOL			end of line definition, either CR, LF or CRLF
	 * @param	string	$exportQuote		Quote character to enclose strings in CSV
	 * @param	bool	$bookClosed			true, if the book is closed (no more changes)
	 * @return	int/string					bookId on success, error message otherwise 
	 */
	function createBook($bookName, $bookStartDate, $bookEndDate,
						$bookCurrency, $bookCurrencyPos=-1,
						$bookDecimals, $bookDecPoint, $bookThousand,
						$exportSeparator, $exportEOL, $exportQuote,
						$bookAutoTax='y', $bookClosed=false) {
		global $userlib, $user;
		if (strlen($bookName)==0) {
			return "The book must have a name";
		}
		if (strtotime($bookStartDate) === false) {
			return "Invalid start date";
		}
		if (strtotime($bookEndDate) === false) {
			return "Invalid end date";
		}
		$query="INSERT INTO `tiki_acct_book` (`bookName`, `bookClosed`, `bookStartDate`, `bookEndDate`,
							`bookCurrency`, `bookCurrencyPos`, `bookDecimals`, `bookDecPoint`, `bookThousand`,
							`exportSeparator`, `exportEOL`, `exportQuote`, `bookAutoTax`)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$res=$this->query($query,array($bookName, $bookClosed, $bookStartDate, $bookEndDate,
									   $bookCurrency, $bookCurrencyPos,
									   $bookDecimals, $bookDecPoint, $bookThousand,
									   $exportSeparator, $exportEOL, $exportQuote,
									   $bookAutoTax));
		$bookId=$this->lastInsertId();
		$this->createTax($bookId,tra('No automated tax'),0,'n');
		$groupId=$bookId;
		do {
			//make sure we don't have that group already
			$groupname="accounting_book_$groupId";
			$groupexists=$userlib->group_exists($groupname);
			if ($groupexists) {
				$groupId++;
			}
		} while ($groupexists);
		if ($groupId!=$bookId) {
			$query="UPDATE `tiki_acct_book` SET `bookId=? WHERE `bookId`=?";
			$res=$this->query($query,array($groupId,$bookId));
			$bookId=$groupId;
		}
		$userlib->add_group($groupname);
		$userlib->assign_user_to_group($user,$groupname);
		$userlib->assign_object_permission($groupname, $bookId, 'accounting book', 'tiki_p_acct_view');
		$userlib->assign_object_permission($groupname, $bookId, 'accounting book', 'tiki_p_acct_book');
		$userlib->assign_object_permission($groupname, $bookId, 'accounting book', 'tiki_p_acct_manage_accounts');
		$userlib->assign_object_permission($groupname, $bookId, 'accounting book', 'tiki_p_acct_book_stack');
		$userlib->assign_object_permission($groupname, $bookId, 'accounting book', 'tiki_p_acct_book_import');
		$userlib->assign_object_permission($groupname, $bookId, 'accounting book', 'tiki_p_acct_manage_template');
		return $bookId;
	}
	
	/**
	 * 
	 * Returns the details for a book with a given bookId
	 * @param	int	$bookId	Id of the book to retrieve the data for
	 * @return	array		Array with book details
	 */
	function getBook($bookId) {
		$query="SELECT * FROM `tiki_acct_book` WHERE `bookId`=?";
		$res=$this->query($query,array($bookId));
		return $res->fetchRow();
	}
	
	/**
	 * Returns the complete journal for a given account, if none is provided, the whole journal will be fetched
	 *
	 * @param	int		$bookId		id of the current book
	 * @param	int		$accountId	account for which we should display the journal, defaults to '%' (all accounts)
	 * @param	string	$order		sorting order
	 * @param	int		$limit		max number of records to fetch, defaults to 0 = all
	 * @return	array/bool			journal with all posts, false on errors
	 */
	function getJournal($bookId, $accountId='%', $order='`journalId` ASC', $limit=0) {	  
		$journal=array();
	  
		if ($limit!=0) {
			if ($limit<0) $order=str_replace("ASC","DESC",$order);
			$order.=" LIMIT ".abs($limit);
		}
		if ($accountId=='%') {
			$query="SELECT `journalId`, `journalDate`, `journalDescription`, `journalCancelled`
				FROM `tiki_acct_journal`
				WHERE `journalBookId`=?
				ORDER BY $order";
			$res =$this->query($query,array($bookId));
		} else {
			$query="SELECT `journalId`, `journalDate`, `journalDescription`, `journalCancelled`
				FROM `tiki_acct_journal`
				INNER JOIN `tiki_acct_item` ON `tiki_acct_journal`.`journalId`=`tiki_acct_item`.`itemJournalId`
				WHERE `journalBookId`=? AND `itemAccountId` LIKE ?
				GROUP BY `journalId`, `journalDate`, `journalDescription`, `journalCancelled`
				ORDER BY $order";
			$res =$this->query($query,array($bookId, $accountId));
		}
		if ($res===false) return false;
		while ($row = $res->fetchRow()) {
			$query="SELECT * FROM `tiki_acct_item` WHERE `itemJournalId`=? AND `itemType`=? ORDER BY `itemAccountId` ASC";
		    $row['debit'] =$this->fetchAll($query,array($row['journalId'],-1));
		    $row['debitcount']=count($row['debit']);
		    $row['credit'] =$this->fetchAll($query,array($row['journalId'],1));
		    $row['creditcount']=count($row['credit']);
		    $row['maxcount']=max($row['creditcount'],$row['debitcount']);
		    $journal[]=$row;
		}
		return $journal;
	}
	
	/**
	 * Returns the totals for a given book and account
	 *
	 * @param	int		$bookId		id of the current book
	 * @param	int		$accountId	account for which we should fetch the totals, defaults to '%' (all accounts)
	 * @return	array	array with three elements debit, credit and the total (credit-debit)
	 */
	function getJournalTotals($bookId, $accountId='%') {	  
		$journal=array();
	  
		$query="SELECT `itemAccountId`, SUM(`itemAmount`*IF(`itemType`<0,1,0)) AS debit, sum(`itemAmount`*IF(`itemType`>0,1,0)) AS credit
	          FROM `tiki_acct_journal`
	          INNER JOIN `tiki_acct_item` ON `tiki_acct_journal`.`journalId`=`tiki_acct_item`.`itemJournalId`
	          WHERE `journalBookId`=? AND `itemAccountId` LIKE ?";
		$res=$this->query($query,array($bookId, $accountId));
		$totals =$res->fetchRow();
		$totals['total']=$totals['credit']-$totals['debit'];
		return $totals;
	}
	
	/**
	 * Returns a list of accounts as defined in table tiki_acct_account
	 *
	 * @param	int		$bookId	id of the book to retrieve the accounts for
	 * @param	string	$order	order of items, defaults to accountId
	 * @param	boolean	$all	true = fetch all accounts, false = fetch only unlocked accounts 
	 * @return	array			list of accounts
	 */
	function getAccounts($bookId, $order="`accountId` ASC", $all=false) {
		$query='SELECT * FROM `tiki_acct_account` WHERE `accountBookId`=? '. ($all?'':'AND `accountLocked`=0 '). "ORDER BY $order";
		return $this->fetchAll($query,array($bookId));
	} //getAccounts
	
	/**
	 * Returns an extended list of accounts with totals
	 *
	 * @param	int		$bookId		id of the book to fetch the account list for
	 * @param	bool	$all		true = fetch all accounts or false = only unlocked accounts, defaults to false
	 * @return	array	list of accounts
	 */
	function getExtendedAccounts($bookId,$all=false) {
		$allcond=$all? '':' AND accountLocked=0';
		$query="SELECT accountBookId, accountId, accountName, accountNotes, accountBudget, accountLocked, 
	  		  SUM(itemAmount*IF(itemType<0,1,0)) AS debit, SUM(itemAmount*IF(itemType>0,1,0)) AS credit
	          FROM tiki_acct_account
	          LEFT JOIN tiki_acct_journal ON tiki_acct_account.accountBookId=tiki_acct_journal.journalBookId
	          LEFT JOIN tiki_acct_item ON tiki_acct_journal.journalId=tiki_acct_item.itemJournalId
	          	AND tiki_acct_account.accountId=tiki_acct_item.itemAccountId 
	          WHERE tiki_acct_account.accountBookId=? $allcond
	          GROUP BY accountId, accountName, accountNotes, accountBudget, accountLocked";
		return $this->fetchAll($query,array($bookId));
	}//getExtendedAccounts
	
	/**
	 * Returns an array with all data from the account
	 *
	 * @param	int		$bookId				id of the current book
	 * @param	int		$accountId			account id to retrieve
	 * @param	boolean	$checkChangeable	perform check, if the account is changeable
	 * @return	array	account data or false on error
	 */
	function getAccount($bookId, $accountId, $checkChangeable=true) {
		$query="SELECT * FROM `tiki_acct_account` WHERE `accountbookId`=? AND `accountId`=?";
		$res=$this->query($query,array($bookId, $accountId));
		$account=$res->fetchRow();
		if ($checkChangeable) $account['changeable']=$this->accountChangeable($bookId, $accountId);
		return $account;
	} //getAccount
	
	/**
	 * Checks if this accountId can be changed or the account can be deleted.
	 * This can only be done, if the account has not been used -> no posts exist for the account
	 *
	 * @param	int	$bookId		id of the current book
	 * @param	int	$accountId	account id to check
	 * @return	boolean	true, if the account can be changed/deleted
	 */
	function accountChangeable($bookId, $accountId) {
		$query="SELECT Count(`itemAccountId`) AS posts
			FROM `tiki_acct_journal`
			INNER JOIN `tiki_acct_item` ON `tiki_acct_journal`.`journalId`=`tiki_acct_item`.`itemJournalId`
	  		WHERE `journalBookId`=? and `itemAccountId`=?";
		$res=$this->query($query,array($bookId, $accountId));
		$posts=$res->fetchRow();
		return ($posts['posts']==0);
	} //accountChangeable
	
	/**
	 * Creates an account with the given information
	 *
	 * @param	int		$bookId			id of the current book
	 * @param	int		$accountId		id of the account to create
	 * @param	string	$accountName	name of the account to create
	 * @param	string	$accountNotes	notes for this account
	 * @param	float	$accountBudget	planned budget for the account
	 * @param	boolean	$accountLocked	can this account be used, 0=unlocked, 1=locked
 	 * @param	int		$accountTax		taxId for tax automation
	 * @return	array/boolean			list of errors or true on success
	 */
	function createAccount( $bookId, $accountId, $accountName, $accountNotes,
							$accountBudget, $accountLocked, $accountTax=0) {
		$errors=$this->validateId('accountId', $accountId, 'tiki_acct_account',false, 'accountBookId', $bookId);
		if ($accountName=='') $errors[]=tra('Account name must not be empty.');
		$cleanbudget=$this->cleanupAmount($bookId, $accountBudget);
		if ($cleanbudget==='') $errors[]=tra('Budget is not a valid amount: '). $accountBudget;
		if ($accountLocked!=0 and $accountLocked!=1) $errors[]=tra('Locked must be either 0 or 1.');
		if ($accountTax!=0) {
			$errors=array_merge($errors,$this->validateId('taxId',$accountTax,'tiki_acct_tax',true, 'taxBookId', $bookId));
		}
		if (count($errors)!=0) return $errors;
		$query='INSERT INTO tiki_acct_account SET accountBookId=?, accountId=?, accountName=?,
				accountNotes=?, accountBudget=?, accountLocked=?, accountTax=?';
		$res=$this->query($query, array($bookId, $accountId, $accountName, $accountNotes, $cleanbudget,
							$accountLocked, $accountTax));
		if ($res===false) {
			$errors[]=tra('Error creating account') & " $accountId: ".$this->ErrorNo().": ".$tikilib->ErrorMsg()."<br /><pre>$query</pre>";
			return $errors;
	  }
	  return true;
	} //createAccount

	/**
	 * Unlocks or locks an account which means it can not be used accidentally for booking
	 * 
	 * @param int		$bookId		current book
	 * @param int		$accountId	account to lock
	 */
	function changeAccountLock($bookId, $accountId) {
	  $query="UPDATE `tiki_acct_account` SET `accountLocked` = NOT `accountLocked` 
	  		WHERE `accountBookId`=? AND `accountId`=?";
	  $res=$this->query($query,array($bookId,$accountId));
	} //changeAccountLock
	
	/**
	 * Updates an account with the given information
	 *
	 * @param	int		$bookId			id of the current book
	 * @param	int		$accountId		original id of the account
	 * @param	int		$newAccountId	new id of the account (only if the account is changeable)
	 * @param	string	$accountName	name of the account
	 * @param	string	$accountNotes	notes for the account
	 * @param	float	$accountBudget	planned yearly budget for the account
	 * @param	boolean	$accountLocked	can this account be used 0=unlocked, 1=locked
 	 * @param	int		$accountTax		id of the auto tax type, defaults to 0
	 * @return	array/boolean			list of errors, true on success
	 */
	function updateAccount($bookId, $accountId, $newAccountId, $accountName, $accountNotes,
						   $accountBudget, $accountLocked, $accountTax=0) {
		$errors=$this->validateId('accountId',$newAccountId,'tiki_acct_account',true,'accountBookId', $bookId);
		if ($accountId!=$newAccountId) {
			if (!$this->accountChangeable($bookId, $accountId)) $errors[]=tra('AccountId %0 is already in use and must not be changed. Please disable it if it is no longer needed.',$args=array($accountId));
		}
		if ($accountName==='') $errors[]=tra('Account name must not be empty.');
		$cleanbudget=$this->cleanupAmount($bookId,$accountBudget);
		if ($cleanbudget==='') $errors[]=tra('Budget is not a valid amount: '). $cleanbudget;
		if ($locked!=0 and $locked!=1) $errors[]=tra('Locked must be either 0 or 1.');
		if ($accountTax!=0) {
			$errors=array_merge($errors,$this->validateId('taxId',$accountTax,'tiki_acct_tax',true, 'taxBookId', $bookId));
		}
		if (count($errors)!=0) return $errors;
		
		$query="UPDATE tiki_acct_account SET accountId=?, accountName=?, 
				accountNotes=?, accountBudget=?, accountLocked=?, accountTax=?
				WHERE accountBookId=? AND accountId=?";
		$res=$this->query($query, array($newAccountId, $accountName, $accountNotes, $cleanbudget,
	  									$accountLocked,$accountTax, $bookId, $accountId));
		if ($res===false) {
			$errors[]=tra('Error updating account') & " $accountId: ".$this->ErrorNo().": ".$this->ErrorMsg()."<br /><pre>$query</pre>";
			return $errors;
		}
		
		return true;
	} //updateAccount
	
	/**
	 * Delete an account (if deleteable)
	 *
	 * @param	int		$bookId		current book
	 * @param	int		$accountId	account id to delete
	 * @param	bool	$checkChangeable	check, if the account is unused and can be deleted
	 * @return	mixed	array with errors or true, if deletion was successful
	 */
	function deleteAccount($bookId, $accountId, $checkChangeable=true) {
		$errors=array();
		if (!$this->accountChangeable($bookId, $accountId)) {
			$errors[]=tra('Account is already in use and must not be deleted. Please disable it, if it is no longer needed.');
			return $errors;
		}
		$query="DELETE FROM `tiki_acct_account` WHERE `accountBookId`=? AND `accountId`=?";
		$res=$this->query($query,array($bookId,$accountId));
		return true;
	} //deleteAccount

	/**
	 * 
	 * Do a manual rollback, if the creation of a complete booking fails.
	 * This is a workaround for missing transaction support
	 * @param	int		$journalId	id of the entry to roll back
	 * @return	string				Text messages stating the success/failure of the rollback
	 */
	function manualRollback($journalId) {
		$errors=array();
		$query="DELETE FROM `tiki_acct_item` WHERE `itemJournalId=?";
		$res=$this->query($query,array($journalId));
		$rollback =($res!==false);	
		$query="DELETE FROM `tiki_acct_journal` WHERE `journalId`=?";
		$res=$this->query($query,array($journalId));
		$rollback=$rollback and ($res!==false);
		if (!$rollback) {
			return tra('Rollback failed, inconsistent database: Cleanup needed for journalId')." $journalId";
		} else {
			return tra('successfully rolled back #')." $journalId"; 
		}
	}
	
	/**
	 * books a simple transaction
	 *
	 * @param	int		$bookId				id of the current book
	 * @param	date	$journalDate		date of the transaction
	 * @param	string	$journalDescription	description of this transaction
	 * @param	int		$debitAccount		account to debit
	 * @param	int		$creditAccount		account to credit
	 * @param	double	$amount				amount to transfer between the accounts
	 * @param	string	$debitText			text for the debit post, defaults to an empty string
	 * @param	string	$creditText			text for the credit post, defaults to an empty string
	 * @return	int/array					list of errors or journalId on success
	 */
	function simpleBook($bookId, $journalDate, $journalDescription, $debitAccount, $creditAccount,
						$amount, $debitText='', $creditText='') {
		$errors=array();
		//$this->beginTransaction();
		$query="INSERT INTO `tiki_acct_journal` (`journalBookId`, `journalDate`, `journalDescription`,
				`journalCancelled`, `journalTs`)
				VALUES (?,?,?,0,NOW())";
		$res=$this->query($query,array($bookId, $journalDate, $journalDescription));
		if ($res===false) {
			$errors[]=tra('Booking error creating journal entry').$this->ErrorNo().": ".$this->ErrorMsg()."<br /><pre>$query</pre>";
			$this->rollback();
			return $errors;
		}
		$journalId=$this->lastInsertId();
	  
		$query="INSERT INTO `tiki_acct_item` (`itemJournalId`, `itemAccountId`, `itemType`,
				`itemAmount`, `itemText`, `itemTs`)
				VALUES (?, ?, ?, ?, ?, NOW())";
		$res=$tikilib->query($query,array($journalId, $debitAccount, -1, $amount, $debitText));
		if ($res===false) {
	    	$errors[]=tra('Booking error creating debit entry').$this->ErrorNo().": ".$this->ErrorMsg()."<br /><pre>$query</pre>";
			//$this->rollback();
			$errors[]=$this->manualRollback($journalId);
		  	return $errors;
		}
		$res=$tikilib->query($query,array($journalId,$creditAccount,1,$amount,$creditText));
		if ($res===false) {
			$errors[]=tra('Booking error creating credit entry').$tikilib->ErrorNo().": ".$tikilib->ErrorMsg()."<br /><pre>$query</pre>";
			//$this->rollback();
			$errors[]=$this->manualRollback($journalId);
			return $errors;
		}
	  	// everything ok
	  	//$this->commit();
		return $journalId;
	}// simplebook
	
	/**
	 * books a complex transaction with multiple accounts on one side
	 *
	 * @param	int		$bookId				id of the current book
	 * @param	date	$journalDate		date of the transaction
	 * @param	string	$journalDescription	description of this transaction
	 * @param	mixed	$debitAccount		account(s) to debit
	 * @param	mixed	$creditAccount		account(s) to credit
	 * @param	mixed	$debitAmount		amount(s) on debit side
	 * @param	mixed	$creditAmount		amount(s) on credit side
	 * @param	mixed	$debitText			text(s) for the debit post, defaults to an empty string
	 * @param	mixed	$creditText			text(s) for the credit post, defaults to an empty string
	 *
	 * @return	int/array					journalID or list of errors
	 */
	function book($bookId, $journalDate, $journalDescription, $debitAccount, $creditAccount,
				  $debitAmount, $creditAmount, $debitText=array(), $creditText=array()) {
		$errors=array();
		if (!is_array($debitAccount)) $debitAccount=array($debitAccount);
		if (!is_array($creditAccount)) $creditAccount=array($creditAccount);
		if (!is_array($debitAmount)) $debitAmount=array($debitAmount);
		if (!is_array($creditAmount)) $creditAmount=array($creditAmount);
		if (!is_array($debitText)) $debitText=array($debitText);
		if (!is_array($creditText)) $creditText=array($creditText);
	
		if (count($debitAccount)!=count($debitAmount) or count($debitAccount)!=count($debitText)) {
			$errors[]=tra('Number of debit entries differ: ') . count($debitAccount) . '/' . count($debitAmount) . '/' . count($debitText);
		}
		if (count($creditAccount)!=count($creditAmount) or count($creditAccount)!=count($creditText)) {
			$errors[]=tra('Number of credit entries differ: ') . count($creditAccount) . '/' . count($creditAmount) . '/' . count($creditText);
		}
		if (count($debitAccount)>1 and count($creditAccount)>1) {
			$errors[]=tra('Splitting is only allowed on one side.');
		}
		$checkamount=0;
		for ($i=0;$i<count($debitAmount);$i++) {
			$a=$this->cleanupAmount($bookId, $debitAmount[$i]);
			if (!is_numeric($a) or $a<=0) {
				$errors[]=tra('Invalid debit amount ').$debitAmount[$i];
			} else {
				$checkamount-=$a;
			}
			if (!is_numeric($debitAccount[$i])) {
				$errors[]=tra('Invalid debit account number ') . $debitAccount[$i];
			} 
		}
		for ($i=0;$i<count($creditAmount);$i++) {
			$a=$this->cleanupAmount($bookId,$creditAmount[$i]);
			if (!is_numeric($a) or $a<=0) {
				$errors[]=tra('Invalid credit amount ').$creditAmount[$i];
			} else {
				$checkamount+=$a;
			}
			if (!is_numeric($creditAccount[$i])) {
				$errors[]=tra('Invalid credit account number ') . $creditAccount[$i];				
			}
		}
		if ($checkamount!=0) {
			$errors[]=tra('Difference between debit and credit amounts ').$checkamount;	
		}
		if (count($errors)>0) return $errors;

		//$this->beginTransaction();
		$query="INSERT INTO `tiki_acct_journal` (`journalBookId`, `journalDate`, `journalDescription`,
				`journalCancelled`, `journalTs`)
				VALUES (?,?,?,0,NOW())";
		$res=$this->query($query,array($bookId, $journalDate, $journalDescription));
		if ($res===false) {
			$errors[]=tra('Booking error creating journal entry').$tikilib->ErrorNo().": ".$tikilib->ErrorMsg()."<br /><pre>$query</pre>";
			return $errors;
		}
		
		$journalId=$this->lastInsertId();
	  
		$query="INSERT INTO `tiki_acct_item` (`itemJournalId`, `itemAccountId`, `itemType`,
				`itemAmount`, `itemText`, `itemTs`)
				VALUES (?, ?, ?, ?, ?, NOW())";			  
		for ($i=0;$i<count($debitAccount);$i++) {
			$a=$this->cleanupAmount($bookId, $debitAmount[$i]);
			$res=$this->query($query,array($journalId, $debitAccount[$i], -1, $a, $debitText[$i]));
			if ($res===false) {
				$errors[]=tra('Booking error creating debit entry').$tikilib->ErrorNo().": ".$tikilib->ErrorMsg()."<br /><pre>$query</pre>";
				//$this->rollback();
				$errors[]=$this->manualRollback($journalId);
				return $errors;
			}
		}
		for ($i=0;$i<count($creditAccount);$i++) {
			$a=$this->cleanupAmount($bookId, $creditAmount[$i]);
			$res=$this->query($query,array($journalId, $creditAccount[$i], 1, $a, $creditText[$i]));
			if ($res===false) {
				$errors[]=tra('Booking error creating credit entry').$tikilib->ErrorNo().": ".$tikilib->ErrorMsg()."<br /><pre>$query</pre>";
				//$this->rollback();
				$errors[]=$this->manualRollback($journalId);
				return $errors;
			}
		}
		// everything ok
		//$this->commit();
		return $journalId;
	}// book
	
	/**
	 * Returns a list of bankaccounts which are related to internal accounts
	 * @param	int		$bookId		id if the current book
	 *
	 * @return	array				list of accounts
	 */
	function getBankAccounts($bookId) {
		$query="SELECT * FROM `tiki_acct_bankaccount` INNER JOIN `tiki_acct_account` 
				ON `tiki_acct_bankaccount`.`bankBookId` = `tiki_acct_account`.`accountBookId` AND
				`tiki_acct_bankaccount`.`bankAccountId`=`tiki_acct_account`.`accountId`
				WHERE `tiki_acct_bankaccount`.`bankBookId`=?";
		return $this->fetchAll($query,array($bookId));
	}//getBankAccounts
	
	/**
	 * Returns a list of bank statements which have been uploaded but not yet been processed
	 *
	 * @param	int		$bookId		id of the current book
	 * @param	int		$accountId	id of the account to fetch the statements for
	 * @return	array/bool			list of statements or false if an error occurred
	 */
	function getOpenStatements($bookId, $accountId) {
		$query="SELECT * FROM `tiki_acct_statement`
			WHERE `statementJournalId`=0 AND `statementStackId`=0
			AND `statementBookId`=? AND `statementAccountId`=?";
		return $this->fetchAll($query,array($bookId,$accountId));
	}//getOpenStatements
	
	/**
	 * Returns the statement with the given Id from the list of statements
	 *
	 * @param	int	$statetmentId	id of the statement to retrieve
	 * @return	array	statement data or false on error
	 */
	function getStatement($statementId) {
		$query="SELECT * FROM `tiki_acct_statement` WHERE `statementId`=?";
		$res=$this->query($query,array($statementId));
		if ($res===false) return $res;
		return $res->fetchRow();
	}//getStatement
	
	/**
	 * Returns the import specification for a given accountId
	 * @param	int		$bookId		id of the current book
	 * @param	int		$accountId	id of the account we want the specs for	
	 * @return	array/bool			list of statements or false
	 */
	function getBankAccount($bookId, $accountId) {
		$query="SELECT * FROM `tiki_acct_bankaccount` WHERE bankBookId=? and bankAccountId=?";
		$res=$tikilib->query($query,array($bookId, $accountId));
		if ($res===false) return $res;
		return $res->fetchRow();
	}//getBankAccount
	
	/**
	 * Splits a header line into a matching array according to the specifications
	 *
	 * @param	string	$header		line containing headers
	 * @param	array	$defs		file definitions
	 * @return	array	list of statements
	 */
	function analyzeHeader($header, $defs) {
		$cols=explode($defs['bankDelimeter'],$header);
		$columns=array();
	  
		for ($i=0;$i<sizeof($cols);$i++) {
			switch($cols[$i]) {
				case $defs['fieldNameAccount']		: $columns['accountId']=$i;
													break;
				case $defs['fieldNameBookingDate']	: $columns['bookingDate']=$i;
													break;
				case $defs['fieldNameValueDate']	: $columns['valueDate']=$i;
													break;
				case $defs['fieldNameBookingText']	: $columns['bookingText']=$i;
													break;
				case $defs['fieldNameReason']		: $columns['reason']=$i;
													break;
				case $defs['fieldNameCounterpartName']	: $columns['counterpartName']=$i;
													break;
				case $defs['fieldNameCounterpartAccount']	: $columns['counterpartAccount']=$i;
													break;
				case $defs['fieldNameCounterpartBankcode']	: $columns['counterpartBankcode']=$i;
													break;
				case $defs['fieldNameAmount']		: $columns['amount']=$i;
													break;
				case $defs['fieldNameAmountSign']	: $columns['amountSign']=$i;
													break;
			}
		}
		return $columns;
	}//analyzeHeader
	
	/**
	 * updates journalId in the given statement
	 *
	 * @param	int		$statementId	id of the statement to update
	 * @param	int		$journalId		id of the entry in the journal which was caused by this statement
	 * @return	array/boolean			list of errors, empty if no errors were found
	 */
	function updateStatement($statementId, $journalId) {
		$errors=array();
	  
		$query="UPDATE `tiki_acct_statements` SET `statementJournalId`=?
				WHERE `statementId`=?";
		$res=$this->query($query,array($journalId,$statementId));
		if ($res===false) {
	    	$errors[]=tra('Error while updating statement:').$this->ErrorNo().": ".$this->ErrorMsg()."<br /><pre>$query</pre>";
	    	return $errors;
		}
		return true;
	}//updateStatement
	

	
	/**
	 * 
	 * Creates a tax setting for automated tax deduction/splitting
	 * @param int		$bookId
	 * @param string	$taxText
	 * @param double	$taxAmount
	 * @param string	$taxIsFix
	 * @return			id of the newly created tax
	 */
	function createTax($bookId, $taxText, $taxAmount,$taxIsFix='n') {
		$query="INSERT INTO `tiki_acct_tax` (`taxBookId`, `taxText`, `taxAmount`, `taxIsFix`)
				VALUES (?, ?, ?, ?)";
		$res=$this->query($query,array($bookId, $taxText, $taxAmount, $taxIsFix));
		return $this->lastInsertId();
	}
	
	/**
	 * removes all unnecessary thousand markers and replaces local decimal characters with "." to enable handling as numbers.
	 *
	 * @param	int		$bookId		id of the current book
	 * @param	string	$amount		date of the transaction
	 * @return	string/float		Returns a float or an empty string if the source is not numeric
	 */
	function cleanupAmount($bookId, $amount) {
		$book=$this->getBook($bookId);
		$a=str_replace($book['bookDecimals'],'.',str_replace($book['bookThousand'],'',$amount));
		if (!is_numeric($a)) return '';
		return floatval($a);
	}//cleanupAmount
	
	/**
	 * Checks the existence/non-existence of a numerical id in the given table
	 *
	 * @param	string	$idname		name of the id field in the table
	 * @param	int	$id		the id to check
	 * @param	string	$table		the table to search
	 * @param	boolean	$exists		true if a record must exist, false if it must not	
	 *
	 * @global	object	$tikilib	contains required database functions
	 * @return	array	Returns aa array of errors (empty if none occurred)
	 */
	function validateId($idname, $id, $table, $exists=true, $bookIdName='', $bookId=0) {
		$errors=array();
		if (!is_numeric($id)) {
			$errors[]="$idname ($id) ". tra('is is not a number.');
		} else {
			if ($id<=0) {
				$errors[]="$idname ".tra('must be >0.');
			} else {
				$query="SELECT $idname FROM $table WHERE $idname=$id";
				if ($bookIdName!='') {
					$query.=" AND $bookIdName=$bookId";
				}
		      	$res=$this->query($query);
				if ($res===false) {
					$errors[]=tra('Error checking') & " $idname: ".$tikilib->ErrorNo().": ".$tikilib->ErrorMsg()."<br /><pre>$query</pre>";
				} else {
					if ($exists) {
						if ($res->numRows()==0) $errors[]="$idname ".tra('does not exist.');
					} else {
						if ($res->numRows()>0) $errors[]="$idname $accountId ".tra('already exists');
					} //existence
				} // query
			} // 0
		} // numeric
	  	return $errors;
	} // validateId
}

global $accountinglib;

$accountinglib = new AccountingLib;
