<?php
class Lslib {

  function Lslib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to Lslib constructor");  
    }
    $this->db = $db;
  }

  // Queries the database reporting an error if detected
  function query($query,$reporterrors=true) {
    $result = $this->db->query($query);
    if(DB::isError($result) && $reporterrors) $this->sql_error($query,$result);
    return $result;
  }

  // Gets one column for the database.
  function getOne($query,$reporterrors=true) {
    $result = $this->db->getOne($query);
    if(DB::isError($result) && $reporterrors) $this->sql_error($query,$result);
    return $result;
  }
  
  // Reports SQL error from PEAR::db object.
  function sql_error($query, $result)
  {
    trigger_error("MYSQL error:  ".$result->getMessage()." in query:<br/>".$query."<br/>",E_USER_WARNING);
    die;
  }

  function set_operator_id($reqId,$senderId)
  {
  	$query = "update tiki_live_support_requests set operator_id = '$senderId' where reqId='$reqId'";
  	$this->query($query);
  }

  function set_user_id($reqId,$senderId)
  {
  	$query = "update tiki_live_support_requests set user_id = '$senderId' where reqId='$reqId'";
  	$this->query($query);
  }


  function new_user_request($user,$tiki_user,$email,$reason)
  {
    $reqId = md5(uniqid('.'));
    $user = addslashes($user);
    $tiki_user = addslashes($tiki_user);
    $email = addslashes($email);
    $reason = addslashes($reason);
    $now = date("U");
  	$query = "insert into tiki_live_support_requests(reqId,user,tiki_user,email,reason,req_timestamp,status,timestamp,operator,chat_started,chat_ended,operator_id,user_id)
  	values('$reqId','$user','$tiki_user','$email','$reason',$now,'active',$now,'',0,0,'','')";
  	$this->query($query);
  	return $reqId;
  }
  
  function get_last_request()
  {
  	$x =  $this->getOne("select max(timestamp) from tiki_live_support_requests");
  	if($x) return $x; else return 0;
  }
  
  function get_max_active_request()
  {
  	return $this->getOne("select max(reqId) from tiki_live_support_requests where status='active'");
  }
  
  // Remove active requests 
  function purge_requests()
  {
	$now = date("U");
	$min = $now - 60*2; // 1 minute = timeout.
	$query = "update tiki_live_support_requests set status='timeout' where timestamp < $min";
	$this->query($query);  
  }
  
  // Get status for request
  function get_request_status($reqId)
  {
  	return $this->getOne("select status from tiki_live_support_requests where reqId='$reqId'");
  }

  function set_request_status($reqId,$status)
  {
  	$query = "update tiki_live_support_requests set status='$status' where reqId='$reqId'";
  	$this->query($query);
  }
  
  
  // Get request information
  function get_request($reqId)
  {
  	$query = "select * from tiki_live_support_requests where reqId='$reqId'";
  	$result = $this->query($query);
	$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
	return $res;  	
  }
  
  /*
	accepted_requests integer(10),
	status varchar(20),
	longest_chat integer(10),
	shortest_chat integer(10),
	average_chat integer(10),
	last_chat integer(14),
	time_online integer(10),
	votes integer(10),
	points integer(10),
	status_since integer(14),
	primary key(user)
  */
  function set_operator_status($user,$status)
  {
  	$now = date("U");
  	// If switching to offline then sum online time for this operator
  	if($status == 'offline') {
  		$query = "update tiki_live_support_operators set time_online = $now - status_since where user='$user' and status='online'";
  		$this->query($query);
  	}
  	$query = "update tiki_live_support_operators set status='$status', status_since=$now where user='$user'";
	$this->query($query);  	
  }
  
  function get_operator_status($user)
  {
  	$status = $this->getOne("select status from tiki_live_support_operators where user='$user'");
  	if(!$status) $status = 'offline';
  	return $status;
  }
  
  // Accepts a request, change status to op_accepted
  function operator_accept($reqId,$user,$operator_id)
  {
  	$now = date("U");
  	$query = "update tiki_live_support_requests set operator_id='$operator_id',operator='$user',status='op_accepted',timestamp=$now,chat_started=$now where reqId='$reqId'";
  	$this->query($query);
  	$query = "update tiki_live_support_operators set accepted_requests = accepted_requests + 1 where operator='$user'";
  	$this->query($query);
  }
  
  
  function user_close_request($reqId)
  {
  	if(!$reqId) return;
  	$now = date("U");
  	$query = "update tiki_live_support_requests set status='user closed',timestamp=$now,chat_ended=$now where reqId='$reqId'";
  	$this->query($query);
  }
  
  function operator_close_request($reqId)
  {
  	if(!$reqId) return;
  	$now = date("U");
  	$query = "update tiki_live_support_requests set status='operator closed',timestamp=$now,chat_ended=$now where reqId='$reqId'";
  	$this->query($query);
  }
  
  function get_requests($status)
  {
  	$this->purge_requests();
  	$query = "select * from tiki_live_support_requests where status='$status'";
  	$result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    	$ret[] = $res;
    }
    return $ret;
  }
  
  //EVENT HANDLING
  function get_new_events($reqId,$senderId,$last)
  {
  	$query = "select * from tiki_live_support_events where senderId='$senderId' and reqId='$reqId' and eventId>$last";
  	$result = $this->query($query);
    $ret = '';
    $ret='<?xml version="1.0" ?>';
    $ret.='<events>';
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    	$ret .= '<event>'.'<data>'.$res['data'].'</data></event>';
    }
    $ret.='</events>';
	return $ret;  
  }
  
  function get_last_event($reqId,$senderId)
  {
  	return $this->getOne("select max(seqId) from tiki_live_support_events where senderId<>'$senderId' and reqId='$reqId'");
  }
  
  function get_event($reqId,$event,$senderId)
  {
  	return $this->getOne("select data from tiki_live_support_events where senderId<>'$senderId' and reqId='$reqId' and seqId=$event");
  }
  
  function put_message($reqId,$msg,$senderId)
  {
  	$now = date("U");
  	$seq = $this->getOne("select max(seqId) from tiki_live_support_events where reqId='$reqId'");
  	if(!$seq) $seq = 0;
  	$seq++;
  	$query = "insert into tiki_live_support_events(seqId,reqId,type,senderId,data,timestamp)
  	values($seq,'$reqId','msg','$senderId','$msg',$now)";
  	$this->query($query);
  }
  
  function operators_online()
  {
  	return $this->getOne("select count(*) from tiki_live_support_operators where status='online'");
  }    

}

$lslib= new Lslib($dbTiki);
?>
