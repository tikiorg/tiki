<?php

// A library to handle comments on objetcs (notes, articles, etc)
// This is just a test

class Comments {
  var $db;  // The PEAR db object used to access the database
    
  function Comments($db) 
  {
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }
  
  function sql_error($query, $result) 
  {
    trigger_error("MYSQL error:  ".$result->getMessage()." in query:<br/>".$query."<br/>",E_USER_WARNING);
    die;
  }
    
  function get_comment_father($id) {
    $query = "select parentId from tiki_comments where threadId=$id";
    $ret = $this->db->getOne($query);
    return $ret;
  }
  
  function count_comments($objectId) 
  {
    $hash = md5($objectId);   
    $query = "select count(*) from tiki_comments where object='$hash'";
    $cant = $this->db->getOne($query);
    return $cant;
  }
  
    
  function get_comment_replies($id,$sort_mode,$threshold=0) {
    $query = "select threadId,title,userName,points,commentDate,parentId from tiki_comments where average>=$threshold and parentId=$id order by $sort_mode,commentDate desc";
    $result = $this->db->query($query);
    $retval=Array();
    $retval["numReplies"]=$result->numRows();
    if(DB::isError($result)) $this->sql_error($query, $result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    $retval["replies"]=$ret;
    return $retval;
  }
    
  /*****************/
  function get_comments($objectId, $parentId, $offset = 0,$maxRecords = -1,$sort_mode = 'commentDate_desc', $find='', $threshold=0)
  {
    $hash = md5($objectId);   
   
    if($sort_mode == 'points_desc') {
      $sort_mode = 'average_desc';
    }
   
    $sort_mode = str_replace("_"," ",$sort_mode);
    $query = "select count(*) from tiki_comments where object='$hash' and average<$threshold";
    
    $below = $this->db->getOne($query);
    if($find) {
      $mid=" where average>=$threshold and object='$hash' and parentId=$parentId and title like '%".$find."%' or data like '%".$find."%' ";  
    } else {
      $mid=" where average>=$threshold and object='$hash' and parentId=$parentId "; 
    }
    $query = "select * from tiki_comments $mid order by $sort_mode limit $offset,$maxRecords";
    
    $query_cant = "select count(*) from tiki_comments $mid";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Get the grandfather
      if($res["parentId"]>0) {
        $res["grandFather"]=$this->get_comment_father($res["parentId"]);
      } else {
        $res["grandFather"]=0;
      }
      // Get the replies
      $replies = $this->get_comment_replies($res["threadId"],$sort_mode,$threshold);
      $res["replies"]=$replies;
      if(empty($res["data"])) {
        $res["isEmpty"] = 'y'; 
      } else {
        $res["isEmpty"] = 'n'; 
      }
      //$res["average"]=$res["points"]/$res["votes"];
      $res["average"] = $res["average"];
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["below"] = $below;
    $retval["cant"] = $cant;
    return $retval;
  }

  function post_new_comment($objectId,$parentId,$userName, $title, $data)
  {
    // Check for duplicates.
    $title = addslashes(strip_tags($title));
    $data = addslashes($data);
    if(!$userName) {
      $userName = tra('Anonymous');
    }
    $hash=md5($title.$data);
    $query = "select threadId from tiki_comments where hash='$hash'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    if(!$result->numRows()) {
      $now = date("U");
      $object = md5($objectId);
      $query = "insert into tiki_comments(object,commentDate,userName,title,data,votes,points,hash,parentId,average)
                          values('$object',$now,'$userName','$title','$data',0,0,'$hash',$parentId,0)";
      
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
    }
    return true;                      
  }
  
  function remove_comment($threadId) 
  {
    $query = "delete from tiki_comments where threadId='$threadId'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    return true;
  }
  
  function vote_comment($threadId, $user, $vote) 
  {
  
    // Select user points for the user who is voting (it may be anonymous!)
    $query = "select points,voted from tiki_userpoints where user='$user'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      $user_points = $res["points"];
      $user_voted = $res["voted"];
    } else {
      $user_points = 0;
      $user_voted = 0;
    }
    
    // Calculate vote weight (the Karma System)
    if($user_voted==0) {
      $user_weight = 1;
    } else {
      $user_weight = $user_points/$user_voted;
    }
    $vote_weight = ($vote * $user_weight) / 5;
    //print("User weight: $user_weight<br/>");
    //print("Vote: $vote vote_weight: $vote_weight<br/>");
    
    // Get the user that posted the comment being voted
    $query = "select userName from tiki_comments where threadId=$threadId";
    $comment_user = $this->db->getOne($query);
        
    if($comment_user && ($comment_user==$user)) {
      // The user is voting a comment posted by himself then bail out
      return false;
    }
    
    //print("Comment user: $comment_user<br/>");
    if($comment_user) {
      // Update the user points adding this new vote
      $query = "select user from tiki_userpoints where user='$comment_user'";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
      if($result->numRows()) {
        $query = "update tiki_userpoints set points = points + $vote, voted=voted+1 where user='$user'";
      } else {
        $query = "insert into tiki_userpoints(user,points,voted) values('$comment_user',$vote,1)";
      }
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "update tiki_comments set points = points + $vote_weight, votes = votes+1 where threadId=$threadId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "update tiki_comments set average = points/votes where threadId=$threadId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    return true;
  }
  
}
?>