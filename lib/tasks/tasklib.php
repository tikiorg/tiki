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
    return $res;	
  }
  
  
  
  function open_task($user,$taskId)
  {
    $query = "update tiki_user_tasks set completed=0, status='o', percentage=0 where user='$user' and taskId=$taskId";
    $this->query($query);
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
  
  
  
  
}

$tasklib= new TaskLib($dbTiki);
?>