<?php
class NotificationLib extends TikiLib {

  function NotificationLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to NotificationLib constructor");  
    }
    $this->db = $db;  
  }
  
  function list_mail_events($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (event like '%".$find."%' or email like '%".$find."%')";
    } else {
      $mid=" ";
    }
    $query = "select * from tiki_mail_events $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_mail_events $mid";
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
  
  function add_mail_event($event,$object,$email)
  {
    $query = "replace into tiki_mail_events(event,object,email) values('$event','$object','$email')";
    $result = $this->query($query);
  }
  
  function remove_mail_event($event,$object,$email)
  {
    $query = "delete from tiki_mail_events where event='$event' and object='$object' and email='$email'";
    $result = $this->query($query);
  }
  
  function get_mail_events($event,$object)
  {
    $query = "select email from tiki_mail_events where event='$event' and object='$object'";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["email"];
    }
    return $ret;
  }
  
}

$notificationlib= new NotificationLib($dbTiki);
?>