<?php
 
class MailinLib extends TikiLib {
    
  function MailinLib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to MailinLib constructor");  
    }
    $this->db = $db;  
  }


  function list_mailin_accounts($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (account like '%".$find."%')";  
    } else {
      $mid="  "; 
    }
    $query = "select * from tiki_mailin_accounts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_mailin_accounts $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function list_active_mailin_accounts($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where active='y' and (account like '%".$find."%')";  
    } else {
      $mid=" where active='y'"; 
    }
    $query = "select * from tiki_mailin_accounts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_mailin_accounts $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function replace_mailin_account($accountId, $account, $pop, $port, $username, $pass,$smtp,$useAuth,$smtpPort,$type,$active)
  {
    $account=addslashes($account);
    $username=addslashes($username);
    $pass=addslashes($pass);
    // Check the name
 
    if($accountId) {
      $query = "update tiki_mailin_accounts set account='$account', pop='$pop', port=$port,smtpPort=$smtpPort,username='$username', pass='$pass',smtp='$smtp',useAuth='$useAuth',type='$type',active='$active' where accountId=$accountId";
      $result = $this->query($query);
    } else {
      $query = "replace into tiki_mailin_accounts(account,pop,port,username,pass,smtp,useAuth,smtpPort,type,active)
                values('$account','$pop',$port,'$username','$pass','$smtp','$useAuth',$smtpPort,'$type','$active')";
                $result = $this->query($query);
    }
  
    return true;
  }
    
  function remove_mailin_account($accountId) 
  {
    $query = "delete from tiki_mailin_accounts where accountId=$accountId";
    $result = $this->query($query);
    return true;
  }
  
  function get_mailin_account($accountId)
  {
    $query = "select * from tiki_mailin_accounts where accountId=$accountId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
   
  
}

$mailinlib = new MailinLib($dbTiki);



?>