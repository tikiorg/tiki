<?php
class MiniCalLib extends TikiLib {

  function MiniCalLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to MiniCalLib constructor");  
    }
    $this->db = $db;  
  }
  
  // Returns an array where each member of the array has:
  // start: unix timestamp of the interval start time
  // end  : unix timestamp of the interval end time
  // events : array of events for the slot listing:
  			  // title, description and duration
  function minical_events_by_slot($user,$start,$end,$interval)
  {
    // since interval is in hour convert it to seconds
    $interval = $interval * 60 * 60;
    $slots = Array();
    while($start<=$end) {
      $aux=Array();
      $aux['start']=$start;
      $end_p = $start+$interval;
      $aux['end']=$end_p;
      $query = "select * from tiki_minical_events where user='$user' and start>=$start and start<$end_p order by start asc";
      //print($query);print("<br/>");
      $result = $this->query($query);
      $events=Array();
	  while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
	    $events[] = $res;
	  }      
	  $aux['events']=$events;
	  $slots[]=$aux;
	  $start+= $interval;
    }
    return $slots;
  }
  
  function minical_list_events($user,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_desc"," desc",$sort_mode);
    $sort_mode = str_replace("_asc"," asc",$sort_mode);
    if($find) {
      $mid=" and (name like '%".$find."%' or url like '%".$find."%')";  
    } else {
      $mid=" "; 
    }
    $query = "select * from tiki_minical_events where 
user='$user' $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_minical_events where 
user='$user' $mid";
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
  
  function minical_get_event($user,$eventId)
  {
    $query = "select * from tiki_minical_events where 
user='$user' and eventId='$eventId'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;	
  }
  
  
  function minical_replace_event($user,$eventId,$title,$description,$start,$duration)
  {
    $title = addslashes($title);	
    $description = addslashes($description);
    $now = date("U");
    if($eventId) {
      $query = "update tiki_minical_events set
      title='$title', description='$description',start=$start,duration=$duration
      where user='$user' and 
      eventId=$eventId";	
      $this->query($query);
      return $eventId;
    } else {
      $query = "insert into tiki_minical_events(user,title,description,start,duration)
      values('$user','$title','$description',$start,$duration)";
      $this->query($query);
      $Id = $this->getOne("select max(eventId) from 
tiki_minical_events where user='$user' and start=$start");
      return $Id;
    }
  }
   
  function minical_remove_event($user,$eventId)
  {
    $query = "delete from tiki_minical_events where user='$user' 
    and eventId=$eventId";
    $this->query($query);  	
  }	
}

$minicallib= new MiniCalLib($dbTiki);
?> 
