<?php
 
class NlLib {
  var $db;  // The PEAR db object used to access the database
  var $tikilib; // The tikilib object
  
  function NlLib($db,$tikilib) 
  {
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
    $this->tikilib = $tikilib;
  }
  
  function sql_error($query, $result) 
  {
    trigger_error("MYSQL error:  ".$result->getMessage()." in query:<br/>".$query."<br/>",E_USER_WARNING);
    die;
  }
   
  function replace_newsletter($nlId,$name,$description,$allowAnySub,$frequency)
  {
    $name = addslashes($name);
    $description = addslashes($description);
    if($nlId) {
      // update an existing quiz
      $query = "update tiki_newsletters set 
      name = '$name',
      description = '$description',
      allowAnySub = '$allowAnySub',
      frequency = $frequency
      where nlId = $nlId";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
    } else {
      // insert a new quiz
      $now = date("U");
      $query = "insert into tiki_newsletters(name,description,allowAnySub,frequency,lastSent,editions,users,created)
      values('$name','$description','$allowAnySub',$frequency,$now,0,0,$now)";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
      $queryid = "select max(nlId) from tiki_newsletters where created=$now";
      $nlId = $this->db->getOne($queryid);  
    }
    return $nlId;
  }

  
  function get_newsletter($nlId) 
  {
    $query = "select * from tiki_newsletters where nlId=$nlId";
    $result = $this->db->query($query);
    if(!$result->numRows()) return false;
    if(DB::isError($result)) $this->sql_error($query, $result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
   
  function list_newsletters($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
    $mid=" where (name like '%".$find."%' or description like '%".$find."%')";  
    } else {
      $mid=" "; 
    }
    $query = "select * from tiki_newsletters $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_newsletters $mid";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  

    
  function remove_newsletter($nlId)
  {
    $query = "delete from tiki_newsletters where nlId=$nlId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "delete from tiki_newsletter_subscriptions where nlId=$nlId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $this->tikilib->remove_object('newsletter',$nlId);
    return true;    
  }
  
}

$nllib= new NlLib($dbTiki,$tikilib);
?>