<?php
require_once "listbrowser.php";
require_once "dbhandle.php";

class db_tr_summary extends listbrowser
{

  function db_tr_summary()
    {
      $this->listbrowser();
      $this->setTable("tr_summary");
      $this->setNoLimit();
      $this->setLink("");
    }

  function setAcct($acct_id)
    {
      $filters = array();
      $filters['acct_id']= $acct_id;
      $this->setFilter($filters);
    }
}

?>