<?php
require_once "dbhandle.php";
require_once "db_ledger.php";
require_once "db_account.php";
require_once "cc_date.php";
require "db_journal.php";

class db_transaction
{
  var $seq;
  var $tr_date;
  var $acct_id;
  var $other_id;
  var $other_domain;
  var $tr_cc_id;
  var $tr_amount;
  var $tr_item;
  var $source_balance;
  var $dest_balance;

  var $errors = "";
  var $tr_type;
  var $journal_source;
  var $journal_dest;

  function db_transaction()
    {
      $this->journal_source = new db_journal();
      $this->journal_dest = new db_journal();
    }

  function create($src_acct_id, $dest_account,$item, $amount, $cc)
    {
      
      $destacct = new db_account();
      
      if ($dest_account == '')
	{
	  $this->errors = "no account entered";
	  return;
	}

      $destacct->loadByid($dest_account);

      if ($destacct->id == '')
	{
	  $this->errors = "account not registered in this system";
	  return;
	}

      $this->other_id = $destacct->id;
      $this->acct_id = $src_acct_id;
      $this->tr_item = $item;
      $this->tr_amount = $amount;
      if ($this->tr_amount <= 0)
	{
	  $this->errors = "amount must be positive";
	  return;
	}

		if ($this->acct_id == $this->other_id)
		{ 
	  $this->errors = "aource and destination identical";
	  return;
	}

      $this->tr_cc_id = $cc;

      // ERROR CHECKING ERROR CHECKING

      $source = new db_ledger();
      $source->loadOne($this->acct_id, 
		       $this->tr_cc_id);

      // Check to make sure source account is registered for cc.
      if ($source->balance == '')
	{
	  $this->errors = "aource account not registered for this cc";
	  return;
	}


      $dest = new db_ledger();
      $dest->loadOne($this->other_id,
		     $this->tr_cc_id);

      // Check to make sure that dest account is registered for cc.
      if ($dest->balance == '')
	{
	  $this->errors = "destination account not registered for this cc";
	  return;
	}

      $source->balance = $source->balance - $this->tr_amount;
      $source->tr_total += $this->tr_amount;
      $source->tr_count++;
      $source->send();
      $this->source_balance = $source->balance;

      $dest->balance = $dest->balance + $this->tr_amount;
      $dest->tr_total += $this->tr_amount;
      $dest->tr_count = $dest->tr_count+1;
      $dest->send();
      $this->dest_balance = $dest->balance;

      $this->send();
    }

  function load($seq)
    {
      $filter = array();
      $filter['seq'] = $seq;

    }

  function send()
    {

      $cc_date = new cc_date();
      $thedate = $cc_date->getmysql();
  
      $this->journal_source->tr_date = $thedate;
      $this->journal_source->acct_id = $this->acct_id;
      $this->journal_source->balance = $this->source_balance;
      $this->journal_source->other_id = $this->other_id;
      $this->journal_source->amount = $this->tr_amount;
      $this->journal_source->item = $this->tr_item;
      $this->journal_source->cc_id = $this->tr_cc_id;
      $this->journal_source->type = "out";
      $this->journal_source->send();

  
      $this->journal_dest->tr_date = $thedate;
      $this->journal_dest->acct_id = $this->other_id;
      $this->journal_dest->balance = $this->dest_balance;
      $this->journal_dest->other_id = $this->acct_id;
      $this->journal_dest->cc_id = $this->tr_cc_id;
      $this->journal_dest->type = "in";
      $this->journal_dest->amount = $this->tr_amount;
      $this->journal_dest->item = $this->tr_item;
      $this->journal_dest->send();
    }
}


?>
