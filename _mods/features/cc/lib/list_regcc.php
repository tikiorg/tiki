<?php
require_once "listbrowser.php";
require_once "dbhandle.php";

class list_regcc extends listbrowser
{

  function list_regcc()
    {
      $this->listbrowser();
      $this->setLink("cc.php?page=registeruserforcc");
      $this->setIdvar("cc_id");
      $this->setTable("registeredcc");
      $this->setNoLimit();
    }

  function setRegister($acct_id)
    {
      $filters = array();
      $filters['acct_id'] = $acct_id;
      $this->setFilter($filters);
    }

  function getHtmlSelect()
    {
      return $this->getSelect("cc_id","cc_id", "cc_id");
    }
}

?>