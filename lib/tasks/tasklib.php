<?php
 
/* Task properties:
   user, taskId, title, description, date, status, priority, completed, percentage
*/    
  
 
class TaskLib extends TikiLib {

  function TaskLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to TaskLib constructor");  
    }
    $this->db = $db;  
  }
  
  function get_task($user, $taskId)
  {
    $query = "select * from tiki_user_tasks where user='$user' and taskId='$taskId'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;	
  }
  
  function update_task_percentage($user,$taskId,$perc)
  {
  	$query = "update tiki_user_tasks set percentage=$perc where user='$user' and taskId=$taskId";
  	$this->query($query);
  }
  
  function open_task($user,$taskId)
  {
    $query = "update tiki_user_tasks set completed=0, status='o', percentage=0 where user='$user' and taskId=$taskId";
    $this->query($query);
  }
  
  
  
  
}

$tasklib= new TaskLib($dbTiki);
?>