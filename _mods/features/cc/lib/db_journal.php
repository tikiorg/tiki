<?php
require_once "dbhandle.php";

class db_journal
{
  var $seq = "";
  var $tr_date = "";
  var $acct_id = "";
  var $other_id = "";
  var $other_domain = "";
  var $cc_id = "";
  var $amount = "";
  var $item = "";
  var $type = "";
  var $balance = "";

  function send()
    {
        $dbh = new dbhandle();      
	$hashvalues['tr_date'] = $this->tr_date;
	$hashvalues['acct_id'] = $this->acct_id;
	$hashvalues['other_id']= $this->other_id;
	$hashvalues['other_domain'] = $this->other_domain;
	$hashvalues['cc_id'] = $this->cc_id;
	$hashvalues['amount'] = $this->amount;
	$hashvalues['item'] = $this->item;
	$hashvalues['type'] = $this->type;
	$hashvalues['balance'] = $this->balance;
	//	echo "<PRE>";
	//	var_dump($hashvalues);
	//	echo "</PRE>";
	$this->seq = $dbh->ship("cc_transaction", "id", $hashvalues);
    }


}
