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
  
  function post_support_message($username,$user,$user_email,$title,$data,$priority,$module,$resolution,$assigned_to='')
  {
  	die("MISSING CODE");
  }
  
  function list_support_messages($offset,$maxRecords,$sort_mode,$find,$where)
  {
    
    $sort_mode = str_replace("_desc"," desc",$sort_mode);
    $sort_mode = str_replace("_asc"," asc",$sort_mode);
    if($find) {
      $mid=" where (data like '%".$find."%' or username like '%".$find."%')";  
    } else {
      $mid=""; 
    }
    if($where) {
    	if($mid) {
    		$mid = ' and '.$where;
    	} else {
    		$mid = ' where '.$where;
    	}
    }
    $query = "select * from tiki_live_support_messages $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_user_notes $mid";
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
  
  function get_modules()
  {
  	$query = "select * from tiki_live_support_modules";
  	$result = $this->query($query);	
  	$ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    return $ret;
  }

}

$lsadminlib= new LsAdminlib($dbTiki);
?>
