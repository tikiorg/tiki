<?php
class LsAdminlib extends Tikilib{

  function LsAdminlib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to LsAdminlib constructor");  
    }
    $this->db = $db;
  }



  function add_operator($user)
  {
  	$query = "replace into tiki_live_support_operators(user,accepted_requests,status,longest_chat,shortest_chat,average_chat,last_chat,time_online,votes,points,status_since)
  														values('$user',0,'offline',0,0,0,0,0,0,0,0)";
 	$this->query($query); 					
  }
  
  function remove_operator($user)
  {
  	$query = "delete from tiki_live_support_operators where user='$user'";
  	$this->query($query);
  }  
  
  function is_operator($user)
  {
  	return $this->getOne("select count(*) from tiki_live_support_operators where user='$user'");
  }

  function get_operators($status)
  {
  	$query = "select * from tiki_live_support_operators where status='$status'";
  	$result = $this->query($query);
  	$ret = Array();
  	$now = date("U");
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    	$res['elapsed']=$now - $res['status_since'];
    	$ret[] = $res;
    }
    return $ret;
  }

}

$lsadminlib= new LsAdminlib($dbTiki);
?>
