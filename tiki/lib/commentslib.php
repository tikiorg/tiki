<?php

// A library to handle comments on object (notes, articles, etc)
// This is just a test

class Comments extends TikiLib {
#  var $db;  // The PEAR db object used to access the database
    
  function Comments($db) 
  {
    if(!$db) {
      die("Invalid db object passed to CommentsLib constructor");  
    }
    $this->db = $db;  
  }
  
  /* Functions for the forums */
     
  function replace_forum($forumId, $name, $description, $controlFlood,$floodInterval, 
                         $moderator, $mail, $useMail,
                         $usePruneUnreplied, $pruneUnrepliedAge,
                         $usePruneOld, $pruneMaxAge, $topicsPerPage,
                         $topicOrdering, $threadOrdering,$section,
                         $topics_list_reads,$topics_list_replies,$topics_list_pts,$topics_list_lastpost,$topics_list_author,$vote_threads,
                         $show_description,
                         $inbound_address,$outbound_address,
                         $topic_smileys, $topic_summary,
                         $ui_avatar, $ui_flag, $ui_posts, $ui_email, $ui_online)
  {
    $name = addslashes($name);
    $description = addslashes($description);
    $section = addslashes($section);
    $inbound_address = addslashes($inbound_address);
    $outbound_address  = addslashes($outbound_address);
     	
    if($forumId) {
      $query = "update tiki_forums set
                name = '$name',  	
                description = '$description',
                controlFlood = '$controlFlood',
                floodInterval = $floodInterval,
                moderator = '$moderator',
                mail = '$mail',
                useMail = '$useMail',
                section = '$section',
                usePruneUnreplied = '$usePruneUnreplied',
                pruneUnrepliedAge = $pruneUnrepliedAge,
                usePruneOld = '$usePruneOld',
                vote_threads = '$vote_threads',
                topics_list_reads = '$topics_list_reads',
                topics_list_replies = '$topics_list_replies',
                show_description = '$show_description',
                inbound_address = '$inbound_address',
                outbound_address = '$outbound_address',
                topic_smileys = '$topic_smileys',
                topic_summary = '$topic_summary',
                ui_avatar = '$ui_avatar',
                ui_flag = '$ui_flag',
                ui_posts = '$ui_posts',
                ui_email = '$ui_email',
                ui_online = '$ui_online',
                topics_list_pts = '$topics_list_pts',
                topics_list_lastpost = '$topics_list_lastpost',
                topics_list_author = '$topics_list_author',
                topicsPerPage = $topicsPerPage,
                topicOrdering = '$topicOrdering',
                threadOrdering = '$threadOrdering',
                pruneMaxAge = $pruneMaxAge
                where forumId = $forumId";
      $result = $this->query($query);
    } else{
      $now = date("U");
      $query = "insert into tiki_forums(name, description, created, lastPost, threads,
                comments, controlFlood,floodInterval, moderator, hits, mail, useMail, usePruneUnreplied,
                pruneUnrepliedAge, usePruneOld,pruneMaxAge, topicsPerPage, topicOrdering, threadOrdering,section,
                topics_list_reads,topics_list_replies,topics_list_pts,topics_list_lastpost,topics_list_author,vote_threads,show_description,
                inbound_address,outbound_address,
                topic_smileys,topic_summary,
                ui_avatar,ui_flag,ui_posts,ui_email,ui_online) 
                values ('$name','$description',$now,$now,0,
                        0,'$controlFlood',$floodInterval,'$moderator',0,'$mail','$useMail','$usePruneUnreplied',
                        $pruneUnrepliedAge,  '$usePruneOld',
                        $pruneMaxAge, $topicsPerPage,
                        '$topicOrdering','$threadOrdering','$section',
                        '$topics_list_reads','$topics_list_replies','$topics_list_pts','$topics_list_lastpost','$topics_list_author','$vote_threads','$show_description',
                        '$inbound_address','$outbound_address',
                        '$topic_smileys','$topic_summary',
                        '$ui_avatar','$ui_flag','$ui_posts','$ui_email','$ui_online') ";
     $result = $this->query($query);
     $forumId=$this->getOne("select max(forumId) from tiki_forums where name='$name' and created=$now"); 
    }	
    return $forumId;
  }           
  
  function get_forum($forumId) 
  {
    $query = "select * from tiki_forums where forumId='$forumId'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function remove_forum($forumId) 
  {
    $query = "delete from tiki_forums where forumId=$forumId";
    $result = $this->query($query);
    // Now remove all the messages for the forum
    $objectId = md5('forum'.$forumId);	
    $query = "delete from tiki_comments where object='$objectId'";
    $result = $this->query($query);
    return true;
  }              

  function list_forums($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where name like '%".$find."%' or description like '%".$find."%'";  
    } else {
      $mid=""; 
    }
    $query = "select * from tiki_forums $mid order by section asc,$sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_forums $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $now = date("U");
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $forum_age = ceil(($now - $res["created"])/(24*3600));
      $res["age"] = $forum_age;
      if($forum_age) {
        $res["posts_per_day"] = $res["comments"]/$forum_age;
      } else {
        $res["posts_per_day"] =0;
      }
      // Now select users
      $objectId=md5('forum'.$res["forumId"]);
      $query = "select distinct(username) from tiki_comments where object='$objectId'";
      $result2 = $this->query($query);
      $res["users"] = $result2->numRows();
      if($forum_age) {
        $res["users_per_day"] = $res["users"]/$forum_age;
      } else {
        $res["users_per_day"] =0;
      }
      
      $query2= "select * from tiki_comments,tiki_forums where object=md5(concat('forum',forumId)) and commentDate=".$res["lastPost"];
      $result2 = $this->query($query2);
      $res2 = $result2->fetchRow(DB_FETCHMODE_ASSOC);
      $res["lastPostData"]=$res2;
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function list_forums_by_section($section,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where section='$section' name like '%".$find."%' or description like '%".$find."%'";  
    } else {
      $mid=" where section='$section' "; 
    }
    $query = "select * from tiki_forums $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_forums";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $now = date("U");
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $forum_age = ceil(($now - $res["created"])/(24*3600));
      $res["age"] = $forum_age;
      if($forum_age) {
        $res["posts_per_day"] = $res["comments"]/$forum_age;
      } else {
        $res["posts_per_day"] =0;
      }
      // Now select users
      $objectId=md5('forum'.$res["forumId"]);
      $query = "select distinct(username) from tiki_comments where object='$objectId'";
      $result2 = $this->query($query);
      $res["users"] = $result2->numRows();
      if($forum_age) {
        $res["users_per_day"] = $res["users"]/$forum_age;
      } else {
        $res["users_per_day"] =0;
      }
      
      $query2= "select * from tiki_comments,tiki_forums where object=md5(concat('forum',forumId)) and commentDate=".$res["lastPost"];
      $result2 = $this->query($query2);
      $res2 = $result2->fetchRow(DB_FETCHMODE_ASSOC);
      $res["lastPostData"]=$res2;
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function user_can_post_to_forum($user, $forumId)
  {
    // Check flood interval for the forum
    $forum = $this->get_forum($forumId);
    if($forum["controlFlood"]!='y') return true;
    if($user) {
      $objectId = md5('forum'.$forumId);
      $query = "select max(commentDate) from tiki_comments where object='$objectId' and userName='$user'";
      $maxDate = $this->getOne($query);
      if(!$maxDate) {
        return true;
      }
      $now = date("U");
      if($maxDate + $forum["floodInterval"]>$now) {
        return false;	
      } else {
        return true;
      }
    } else {
      // Anonymous users
      if(!isset($_SESSION["lastPost"])) {
        return true;
      } else {
        $now = date("U");
        if($_SESSION["lastPost"] + $forum["floodInterval"] > $now) {
          return false;
        } else {
          return true;       
        }
      }
    }
  }
  
  function register_forum_post($forumId,$parentId)
  {
    $now = date("U");
    if(!$parentId) {	 
      $query = "update tiki_forums set threads=threads+1, comments=comments+1 where forumId=$forumId";	
    } else {
      $query = "update tiki_forums set comments=comments+1 where forumId=$forumId";		
    }
    $result = $this->query($query);
  
    $lastPost = $this->getOne("select max(commentDate) from tiki_comments,tiki_forums where object=md5(concat('forum',forumId)) and forumId=$forumId");
    $query="update tiki_forums set lastPost=$lastPost where forumId=$forumId";
    $result = $this->query($query);
    
    $this->forum_prune($forumId);
    return true;
  }
  
  function register_remove_post($forumId, $parentId)
  {
      $this->forum_prune($forumId);
  }
  
  
  
  function forum_add_hit($forumId)
  {
    $query = "update tiki_forums set hits=hits+1 where forumId=$forumId";
    $result = $this->query($query);
    $this->forum_prune($forumId);
    return true;
  }
  
  function comment_add_hit($threadId)
  {
    $query = "update tiki_comments set hits=hits+1 where threadId=$threadId";
    $result = $this->query($query);
    //$this->forum_prune($forumId);
    return true;
  }
  
  function forum_prune($forumId)
  {
    $forum = $this->get_forum($forumId);
    $objectId = md5('forum'.$forumId);	
    if($forum["usePruneUnreplied"]=='y') {
      $age = $forum["pruneUnrepliedAge"]; 	
      // Get all unreplied threads
      // Get all the top_level threads
      $now = date("U");
      $oldage = $now - $age;
      $query = "select threadId from tiki_comments where parentId=0  and commentDate<$oldage";
      $result = $this->query($query);
      while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {  
        // Check if this old top level thread has replies
        $id = $res["threadId"];
        $query2 = "select count(*) from tiki_comments where parentId=$id";
        $cant = $this->getOne($query2);
        if($cant == 0) {
          // Remove this old thread without replies
          $query3 = "delete from tiki_comments where threadId = $id";
          $result3 = $this->query($query3);
          // This is just to be sure
          $query3 = "delete from tiki_comments where parentId = $id";
          $result3 = $this->query($query3);
        }	
      }
    }
    
    if($forum["usePruneOld"]=='y') {
      $maxAge = $forum["pruneMaxAge"];
      $old = date("U") - $maxAge;
      $query = "delete from tiki_comments where object='$objectId' and commentDate<$old";
      $result = $this->query($query);
    }
    
    // Recalculate comments and threads
    $query = "select count(*) from tiki_comments where object='$objectId'";
    $comments = $this->getOne($query);
    $query = "select count(*) from tiki_comments where object='$objectId' and parentId=0";
    $threads = $this->getOne($query);
    $query = "update tiki_forums set comments=$comments, threads=$threads where forumId=$forumId";
    $result = $this->query($query);
    return true;
  }
  
  // FORUMS END
  
    
 
  
  function get_comment($id) 
  {
    $query = "select * from tiki_comments where threadId='$id'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $res["parsed"] = $this->parse_comment_data($res["data"]);
    return $res;
  }

    
  function get_comment_father($id) {
    $query = "select parentId from tiki_comments where threadId=$id";
    $ret = $this->getOne($query);
    return $ret;
  }
  
  function count_comments($objectId) 
  {
    $hash = md5($objectId);   
    $query = "select count(*) from tiki_comments where object='$hash'";
    $cant = $this->getOne($query);
    return $cant;
  }
  
  
    
  function get_comment_replies($id,$sort_mode,$offset,$max,$threshold=0) {
    $query = "select threadId,title,userName,points,commentDate,parentId from tiki_comments where average>=$threshold and parentId=$id order by $sort_mode,commentDate desc limit $offset,$max";
    $result = $this->query($query);
    $retval=Array();
    $retval["numReplies"]=$result->numRows();
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    $retval["replies"]=$ret;
    return $retval;
  }
  
  function parse_smileys($data) 
  {
    global $feature_smileys;
    if($feature_smileys == 'y') {
    $data = preg_replace("/\(:([^:]+):\)/","<img alt=\"$1\" src=\"img/smiles/icon_$1.gif\" />",$data);
    }
    return $data;
  }
  
  function pick_cookie()
  {
    $cant = $this->getOne("select count(*) from tiki_cookies");
    if(!$cant) return '';
    $bid = rand(0,$cant-1);
    $cookie = $this->getOne("select cookie from tiki_cookies limit $bid,1");
    $cookie = str_replace("\n","",$cookie);
    return 'Cookie: '.$cookie.'';    
  }
  
  function parse_comment_data($data)
  {
     global $feature_forum_parse;
     global $tikilib;
     
     if($feature_forum_parse == 'y') {
       return $tikilib->parse_data($data);
     }

     // Cookies
     
     if(preg_match_all("/\{cookie\}/",$data,$rsss)) {
      for($i=0;$i<count($rsss[0]);$i++) {
        $cookie = $this->pick_cookie();
        $data = str_replace($rsss[0][$i],$cookie,$data);
      }
     }
     
  
     $data = preg_replace("/\[([^\|\]]+)\|([^\]]+)\]/","<a class='commentslink' href='$1'>$2</a>",$data);
      // Segundo intento reemplazar los [link] comunes
     $data = preg_replace("/\[([^\]\|]+)\]/","<a class='commentslink' href='$1'>$1</a>",$data);
     
     // Llamar aqui a parse smileys
     $data = $this->parse_smileys($data);
     $data = preg_replace("/---/","<hr/>",$data);
     // Reemplazar --- por <hr/>
     return nl2br($data);
  }    
  
  /*****************/
    
  function get_comments($objectId, $parentId, $offset = 0,$maxRecords = -1,$sort_mode = 'commentDate_desc', $find='', $threshold=0)
  {
    $hash = md5($objectId);   
   
    if($sort_mode == 'points_desc') {
      $sort_mode = 'average_desc';
    }
    $sort_mode = str_replace("_"," ",$sort_mode);
    $old_sort_mode ='';
    if(in_array($sort_mode,Array('replies desc','replies asc','lastPost desc','lastPost asc'))) {
      $old_offset = $offset;
      $old_maxRecords = $maxRecords;
      $old_sort_mode = $sort_mode;
      $sort_mode ='title desc';
      $offset = 0;
      $maxRecords = -1;
    }
    
    $query = "select count(*) from tiki_comments where object='$hash' and average<$threshold";
    $below = $this->getOne($query);
    if($find) {
      $mid=" where type='s' and average>=$threshold and object='$hash' and parentId=$parentId and (title like '%".$find."%' or data like '%".$find."%') ";  
    } else {
      $mid=" where type='s' and average>=$threshold and object='$hash' and parentId=$parentId "; 
    }
    $query = "select * from tiki_comments $mid order by $sort_mode limit $offset,$maxRecords";
    //print("$query<br/>");
    $query_cant = "select count(*) from tiki_comments $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret1 = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Get the last reply
      $tid = $res["threadId"];
      $query = "select max(commentDate) from tiki_comments where parentId='$tid'";
      $res["lastPost"]=$this->getOne($query);
      if(!$res["lastPost"]) $res["lastPost"]=$res["commentDate"];
      // Get the grandfather
      if($res["parentId"]>0) {
        $res["grandFather"]=$this->get_comment_father($res["parentId"]);
      } else {
        $res["grandFather"]=0;
      }
      $res["parsed"] = $this->parse_comment_data($res["data"]);
      // Get the replies
      $replies = $this->get_comment_replies($res["threadId"],$sort_mode,0,-1,$threshold);
      $res["replies"]=$replies;
      if(empty($res["data"])) {
        $res["isEmpty"] = 'y'; 
      } else {
        $res["isEmpty"] = 'n'; 
      }
      //$res["average"]=$res["points"]/$res["votes"];
      $res["average"] = $res["average"];
      $ret1[] = $res;
    }
      
    // Now the non-sticky
    $ret = Array();
    if($find) {
      $mid=" where type<>'s' and average>=$threshold and object='$hash' and parentId=$parentId and (title like '%".$find."%' or data like '%".$find."%') ";  
    } else {
      $mid=" where type<>'s' and average>=$threshold and object='$hash' and parentId=$parentId "; 
    }
    $query = "select * from tiki_comments $mid order by $sort_mode limit $offset,$maxRecords";
    //print("$query<br/>");
    $query_cant = "select count(*) from tiki_comments $mid";
    $result = $this->query($query);
    $cant += $this->getOne($query_cant);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Get the last reply
      $tid = $res["threadId"];
      $query = "select max(commentDate) from tiki_comments where parentId='$tid'";
      $res["lastPost"]=$this->getOne($query);
      if(!$res["lastPost"]) $res["lastPost"]=$res["commentDate"];
      
      $query2 = "select * from tiki_comments where parentId='$tid' and commentDate=".$res["lastPost"];
      $result2 = $this->query($query2);
      $res2 = $result2->fetchRow(DB_FETCHMODE_ASSOC);
      $res["lastPostData"]=$res2;
      
      // Get the grandfather
      if($res["parentId"]>0) {
        $res["grandFather"]=$this->get_comment_father($res["parentId"]);
      } else {
        $res["grandFather"]=0;
      }
      $res["parsed"] = $this->parse_comment_data($res["data"]);
      // Get the replies
      $replies = $this->get_comment_replies($res["threadId"],$sort_mode,0,-1,$threshold);
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

    if($old_sort_mode == 'replies asc') {
      usort($ret,'compare_replies');  
    }
    if($old_sort_mode == 'replies desc') {
      usort($ret,'r_compare_replies');
    }
        
    if($old_sort_mode == 'lastPost asc') {
      usort($ret,'compare_lastPost');  
    }
    if($old_sort_mode == 'lastPost desc') {
      usort($ret,'r_compare_lastPost');
    }
    
    if(in_array($old_sort_mode,Array('replies desc','replies asc','lastPost desc','lastPost asc'))) {
      $ret = array_slice($ret, $old_offset, $old_maxRecords);    
    }    
    
    $ret = array_merge($ret1,$ret);
    
    $retval = Array();
    $retval["data"] = $ret;
    $retval["below"] = $below;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  

  function update_comment($threadId,$title,$data,$type='n',$summary='',$smiley='') 
  {
     $title = addslashes(strip_tags($title));
     $data = addslashes($data);
     $summary = addslashes($summary);
     $query="update tiki_comments set title='$title', data='$data', type='$type', summary='$summary',smiley='$smiley' where threadId=$threadId";
     $result = $this->query($query);
  }

  function post_new_comment($objectId,$parentId,$userName, $title, $data,$type='n',$summary='',$smiley='')
  {
  	$summary = addslashes($summary);
    if(!$userName) {
      $_SESSION["lastPost"]=date("U");
    }
    // Check for duplicates.
    $title = addslashes(strip_tags($title));
    $data = addslashes($data);
  	$summary = addslashes($summary);    
    if(!$userName) {
      $userName = tra('Anonymous');
    }
    $hash=md5($title.$data);
    $query = "select threadId from tiki_comments where hash='$hash'";
    $result = $this->query($query);
    if(!$result->numRows()) {
      $now = date("U");
      $object = md5($objectId);
      $query = "insert into tiki_comments(object,commentDate,userName,title,data,votes,points,hash,parentId,average,hits,type,summary,smiley)
                          values('$object',$now,'$userName','$title','$data',0,0,'$hash',$parentId,0,0,'$type','$summary','$smiley')";
      
      $result = $this->query($query);
    }
    return true;                      
  }
  
  function remove_comment($threadId) 
  {
    $query = "delete from tiki_comments where threadId='$threadId' or parentId='$threadId'";
    $result = $this->query($query);
    return true;
  }
  
  function vote_comment($threadId, $user, $vote) 
  {
  
    // Select user points for the user who is voting (it may be anonymous!)
    $query = "select points,voted from tiki_userpoints where user='$user'";
    $result = $this->query($query);
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
    $comment_user = $this->getOne($query);
        
    if($comment_user && ($comment_user==$user)) {
      // The user is voting a comment posted by himself then bail out
      return false;
    }
    
    //print("Comment user: $comment_user<br/>");
    if($comment_user) {
      // Update the user points adding this new vote
      $query = "select user from tiki_userpoints where user='$comment_user'";
      $result = $this->query($query);
      if($result->numRows()) {
        $query = "update tiki_userpoints set points = points + $vote, voted=voted+1 where user='$user'";
      } else {
        $query = "insert into tiki_userpoints(user,points,voted) values('$comment_user',$vote,1)";
      }
    }
    $result = $this->query($query);
    $query = "update tiki_comments set points = points + $vote_weight, votes = votes+1 where threadId=$threadId";
    $result = $this->query($query);
    $query = "update tiki_comments set average = points/votes where threadId=$threadId";
    $result = $this->query($query);
    return true;
  }
  
}

function compare_replies($ar1,$ar2) {
  return $ar1["replies"]["numReplies"] - $ar2["replies"]["numReplies"];   
}
function compare_lastPost($ar1,$ar2) {
  return $ar1["lastPost"] - $ar2["lastPost"];   
}
function r_compare_replies($ar1,$ar2) {
  return $ar2["replies"]["numReplies"] - $ar1["replies"]["numReplies"];   
}
function r_compare_lastPost($ar1,$ar2) {
  return $ar2["lastPost"] - $ar1["lastPost"];   
}

?>