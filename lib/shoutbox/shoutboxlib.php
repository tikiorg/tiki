<?php
class ShoutboxLib extends TikiLib {

  function ShoutboxLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to ShoutboxLib constructor");  
    }
    $this->db = $db;  
  }
  
  function list_shoutbox($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (message like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_shoutbox $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_shoutbox $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      if(!$res["user"]) $res["user"]='Anonymous';
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function replace_shoutbox($msgId,$user,$message)
  {
    $hash = md5($message);
    $cant = $this->getOne("select count(*) from tiki_shoutbox where hash = '$hash' and user='$user'");
    if($cant) return;
    $message=addslashes(strip_tags($message,'<a>'));
    // Check the name
    $now=date("U");
    if($msgId) {
      $query = "update tiki_shoutbox set user='$user', message='$message', hash='$hash' where msgId=$msgId";
    } else {
      $query = "replace into tiki_shoutbox(message,user,timestamp,hash)
                values('$message','$user',$now,'$hash')";
    }
    $result = $this->query($query);
    return true;
  }
  
  function remove_shoutbox($msgId)
  {
    $query = "delete from tiki_shoutbox where msgId=$msgId";
    $result = $this->query($query);
    return true;
  }
  
  function get_shoutbox($msgId)
  {
    $query = "select * from tiki_shoutbox where msgId=$msgId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
}

$shoutboxlib= new ShoutboxLib($dbTiki);
?>