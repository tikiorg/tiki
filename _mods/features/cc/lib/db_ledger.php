<?php

require_once "dbhandle.php";

class db_ledger
{
  var $seq = "";
  var $acct_id = "";
  var $cc_id = "";
  var $balance = "";
  var $tr_total = 0;
  var $tr_count =0;
  var $errors = "";

  function db_ledger()
    {
    }

  function create($acct_id, $cc_id)
      {
      $this->acct_id = $acct_id;
      $this->cc_id = $cc_id;
      $this->balance= 0;
      $this->tr_total = 0;
      $this->tr_count =0;
  		}
	
  function send()
       {
   	   $dbh = new dbhandle();      
   	   $hashvalues = array();
   	   if ($this->seq != '')
				$hashvalues['seq'] = $this->seq;
	      $hashvalues['acct_id'] = $this->acct_id;
   	   $hashvalues['cc_id'] = $this->cc_id;
   	   $hashvalues['balance'] = $this->balance;
   	   $hashvalues['tr_total'] = $this->tr_total;
   	   $hashvalues['tr_count'] = $this->tr_count;
 	  	   $this->seq = $dbh->ship("cc_ledger", "seq", $hashvalues);
		 }

  function loadOne($acct_id,$ccid)
    {
      $dbh = new dbhandle();      
      $filters = array();
      $filters['acct_id'] = $acct_id;
      $filters['cc_id'] = $ccid;
      $results = $dbh->getOne("cc_ledger", $filters);
      $this->seq = $results['seq'];
      $this->acct_id = $acct_id;
      $this->cc_id = $ccid;
      $this->balance = $results['balance'];
      $this->tr_total = $results['tr_total'];
      $this->tr_count = $results['tr_count'];
    }
}
?>
