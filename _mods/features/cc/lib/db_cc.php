<?php
require_once "dbhandle.php";

class db_cc
{
  var $id = "";
  var $cc_name = "";
  var $cc_description = "";
  var $requires_approval = "";
  var $owner_id = "";
  var $errors = "";

  function db_cc() { }

  function load($id) {
      $this->id = $id;
      $dbh = new dbhandle();      
      $filters = array();
      $filters['id'] = $id;
      $data = $dbh->getOne("cc_cc", $filters);

      $this->cc_name = $data[cc_name];
      $this->cc_description = $data[cc_description];
      $this->requires_approval = $data[requires_approval];
      $this->owner_id = $data[owner_id];
    }

  function establish($owner_id,$id,$name,$description,$approval) {
      $this->owner_id = $owner_id;
      $this->id = $id;
      $this->cc_name= $name;
      $this->cc_description = $description;
      $this->requires_approval = $approval;
    }

  function send() {
      $dbh = new dbhandle();      
      $hashvalues = array();
      $hashvalues['id'] = $this->id;
      $hashvalues['cc_name'] = $this->cc_name;
      $hashvalues['cc_description'] = $this->cc_description;
      $hashvalues['requires_approval'] = $this->requires_approval;
      $hashvalues['owner_id'] = $this->owner_id;
      $this->id = $dbh->insert("cc_cc", $hashvalues);
    }

  function getid() {
      return $this->id;
    }
    
  function getcc_name() {
      return $this->cc_name;
    }

  function getcc_description() {
      return $this->cc_description;
    }

  function getRequires_approval() {
      return $this->requires_approval;
    }

}


?>
