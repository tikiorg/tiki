<?php
 
/* Task properties:
   user, taskId, title, description, date, status, priority, completed, percentage
*/    
  
 
class TaskLib extends TikiLib {

  function TaskLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }
  
  function get_task($user, $taskId)
  {
    $query = "select * from tiki_user_tasks where user='$user' and taskId='$taskId'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $res['parsed']=$this->parse_data($res['body']);
    return $res;	
  }
  
  function replace_task($user,$taskId,$title,$description,$date,$status,$priority,$completed,$percentage)
  {
    $title = addslashes($title);	
    $descrpition = addslashes($description);
    if($taskId) {
      $query = "update tiki_user_tasks set
      title = '$title',
      description = '$description',
      date = $date,
      status = '$status',
      priority = $priority,
      percentage = $percentage,
      completed = $completed
      where user='$user' and taskId=$taskId";	
      $this->query($query);
      return $taskId;
    } else {
      $query = "insert into tiki_user_tasks(user,taskId,title,description,date,status,priority,completed,percentage)
      values('$user',$taskId,'$title','$description',$date,'$status',$priority,$completed,$percentage)";	
      $this->query($query);
      $taskId = $this->getOne("select max(taskId) from tiki_user_tasks where user='$user' and title='$title' and date=$date");
      return $taskId;
    }
  }
  
  // Filter by date and use find
  function list_tasks($user,$offset,$maxRecords,$sort_mode,$find,$use_date)
  {
    $now = date("U");
    if($use_date) {
     $prio = " and date<=$now ";
    }
    
    $sort_mode = str_replace("_desc"," desc",$sort_mode);
    $sort_mode = str_replace("_asc"," asc",$sort_mode);
    if($find) {
      $mid=" and (title like '%".$find."%' or description like '%".$find."%')".$prio;  
    } else {
      $mid="".$prio; 
    }
    $query = "select * from tiki_user_tasks where user='$user' $mid order by $sort_mode,taskId desc limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_user_tasks where user='$user' $mid";
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

  
  function remove_task($taskId)
  {
    $query = "delete from tiki_user_tasks where user='$user' and taskId=$taskId";
    $this->query($query);  	
  }
  
}

$tasklib= new TaskLib($dbTiki);
?>