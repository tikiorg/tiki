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
  function attach_file($threadId,$qId,$name,$type,$size, $data, $fhash, $dir, $forumId) 
  {
  	$name = addslashes($name);
  	$data =addslashes($data);
  	$now = date("U");
  	if($fhash) {
  		// Do not store data if we have a file
  		$data = '';
  	}

  	$query = "insert into tiki_forum_attachments(threadId,qId,filename,filetype,filesize,data,path,created,dir,forumId)
  	values($threadId,$qId,'$name','$type',$size,'$data','$fhash',$now,'$dir',$forumId)";
  	$this->query($query);
  	// Now the file is attached and we can proceed.
  }
  
  function get_thread_attachments($threadId,$qId)
  {
  	if($threadId) {
  		$cond = " where threadId=$threadId ";
  	} else {
  		$cond = " where qId=$qId ";
  	}
  	$query = "select filename,filesize,attId from tiki_forum_attachments $cond";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    	$ret[] = $res;
    }
    return $ret;
  }
  
  function get_thread_attachment($attId)
  {
  	$query = "select * from tiki_forum_attachments where attId=$attId";
  	$result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
  	$forum_info = $this->get_forum($res['forumId']);
  	
    $res['forum_info']=$forum_info;
    return $res;	
  }
  
  function remove_thread_attachment($attId)
  {
  	$query = "delete from tiki_forum_attachments where attId=$attId";
  	$this->query($query);
  }
  
  function parse_output(&$obj, &$parts,$i) {  
	  if(!empty($obj->parts)) {    
	    for($i=0; $i<count($obj->parts); $i++)      
	      parse_output($obj->parts[$i], $parts,$i);  
	  }else{    
	    $ctype = $obj->ctype_primary.'/'.$obj->ctype_secondary;    
	    switch($ctype) {    
	      case 'text/plain':      
	        if(!empty($obj->disposition) AND $obj->disposition == 'attachment') {        
	          $names=split(';',$obj->headers["content-disposition"]);        
	          $names=split('=',$names[1]);        
	          $aux['name']=$names[1];        
	          $aux['content-type']=$obj->headers["content-type"];        
	          $aux['part']=$i;        
	          $parts['attachments'][] = $aux;      
	        }else{        
	          $parts['text'][] = $obj->body;      
	        }      
	        break;    
	      case 'text/html':      
	        if(!empty($obj->disposition) AND $obj->disposition == 'attachment') {        
	          $names=split(';',$obj->headers["content-disposition"]);        
	          $names=split('=',$names[1]);        
	          $aux['name']=$names[1];        
	          $aux['content-type']=$obj->headers["content-type"];        
	          $aux['part']=$i;        
	          $parts['attachments'][] = $aux;      
	        }else{        
	          $parts['html'][] = $obj->body;      
	        }      
	        break;    
	      default:            
	        $names=split(';',$obj->headers["content-disposition"]);      
	        $names=split('=',$names[1]);      
	        $aux['name']=$names[1];      
	        $aux['content-type']=$obj->headers["content-type"];      
	        $aux['part']=$i;      
	        $parts['attachments'][] = $aux;    
	    } 
	  }
  }

  function process_inbound_mail($forumId)
  {
  	 require_once ("lib/webmail/pop3.php");
	 require_once ("lib/webmail/mimeDecode.php");
	 include_once ("lib/webmail/class.rc4crypt.php");
	 include_once ("lib/webmail/htmlMimeMail.php");
  	 $info = $this->get_forum($forumId);
  	 if(!$info["inbound_pop_server"]) return;
	 $pop3=new POP3($info["inbound_pop_server"],$acc["inbound_pop_user"],$acc["inbound_pop_password"]);  
	 if(!$pop3) return;
     $pop3->Open();  
     $s = $pop3->Stats() ;  
     $mailsum = $s["message"];  
     for($i=1;$i<=$mailsum;$i++) {      
       $aux = $pop3->ListMessage($i);	        	
  	   if(empty($aux["sender"]["name"])) $aux["sender"]["name"]=$aux["sender"]["email"];      	
  	   $title = addslashes(trim($aux['subject']));
  	   $email = $aux["sender"]["email"];
  	   $full = $message["full"];  
       $params = array('input' => $full,
                  'crlf'  => "\r\n", 
                  'include_bodies' => TRUE,
                  'decode_headers' => TRUE, 
                  'decode_bodies'  => TRUE
                  );  
       $output = Mail_mimeDecode::decode($params);    
       parse_output($output, $parts,0);  
       if(isset($parts["text"][0])) $body=$parts["text"][0];
      
       //Todo: check permissions
       
       $object = md5('forum'.$forumId);
       // Determine if this is a topic or a thread
       $parentId = $this->getOne("select threadId from tiki_comments where object='$object' and parentId=0 and locate(title,'$title')");
       if(!$parentId) $parentId=0;
       
       // Determine user from email
       $userName = $this->getOne("select login from users_users where email='$email'");
       if(!$userName) $user='';
       
       // post
       $this->post_new_comment($object,$parentId,$userName, $title, $body,$type='n',$summary='',$smiley='');
       
       $pop3->DeleteMessage($i);      
     }
     $pop3->close();
   }
  
  
  /* queue management */
  
  function replace_queue($qId,$forumId,$object,$parentId,$user,$title,$data,$type='n',$topic_smiley='',$summary='',$topic_title='') 
  {
  	// timestamp
  	$hash = md5($object);
  	$title =  addslashes($title);
  	$topic_title =  addslashes($topic_title);  	
  	$data = addslashes($data);
  	$summary = addslashes($summary);
  	$hash2 = md5($title.$data);
  	if($qId==0 && $this->getOne("select count(*) from tiki_forums_queue where hash='$hash2'")) return false;
  	$now = date("U");
  	if($qId) {
  	  $query = "update tiki_forums_queue set
  	    object = '$hash',
  	    parentId=$parentId,
  	    user='$user',
  	    title='$title',
  	    data='$data',
  	    forumId='$forumId',
  	    type='$type',
  	    hash='$hash2',
  	    topic_title='$topic_title',
  	    topic_smiley='$topic_smiley',
  	    summary = '$summary',
  	    timestamp = $now
  	    where qId=$qId
  	  ";
  	  $this->query($query);
  	  return $qId;
  	} else {
  	  $query = "insert into tiki_forums_queue(object,parentId,user,title,data,type,topic_smiley,summary,timestamp,topic_title,hash,forumId)
  	  values('$hash',$parentId,'$user','$title','$data','$type','$topic_smiley','$summary',$now,'$topic_title','$hash2',$forumId)";
  	  $this->query($query);
  	  $qId = $this->getOne("select max(qId) from tiki_forums_queue where hash='$hash2' and timestamp=$now");
  	}
	return $qId;
  }
  
  function get_num_queued($object)
  {
    $hash = md5($object);
	return $this->getOne("select count(*) from tiki_forums_queue where object='$hash'");  	
  }
  
  function list_forum_queue($object,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" and title like '%".$find."%' or data like '%".$find."%'";  
    } else {
      $mid=""; 
    }
    $hash = md5($object);
    $query = "select * from tiki_forums_queue where object='$hash' $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_forums $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $now = date("U");
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    	$res['parsed']=$this->parse_comment_data($res['data']);
    	$res['attachments']=$this->get_thread_attachments(0,$res['qId']);
        $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  

  
  function queue_get($qId)
  {
  	$query = "select * from tiki_forums_queue where qId=$qId";
  	$result = $this->query($query);
  	$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
  	$res['attchments']=$this->get_thread_attachments(0,$res['qId']);
  	return $res;
  }
  
  function remove_queued($qId)
  {
  	$query = "delete from tiki_forums_queue where qId=$qId";
  	$this->query($query);
  	$query = "delete from tiki_forum_attachments where qId=$qId";
  	$this->query($query);
  }
  
  //Approve queued message -> post as new comment
  //post_new_comment($objectId,$parentId,$userName, $title, $data,$type='n',$summary='',$smiley='')
  function approve_queued($qId)
  {
  	$info = $this->queue_get($qId);
	$threadId = $this->post_new_comment('forum'.$info['forumId'],$info['parentId'],$info['user'], $info['title'], $info['data'],$info['type'],$info['summary'],$info['topic_smiley']);
	$this->remove_queued($qId);
	if($threadId) {
		$query = "update tiki_forum_attachments set threadId=$threadId where qId=$qId";
		$this->query($query);
		$query = "delete from tiki_forum_attachments where qId=$qId";
		$this->query($query);
	}
  }
	
  function get_forum_topics($forumId)
  {
  	$hash = md5('forum'.$forumId);
  	$query = "select * from tiki_comments where object='$hash' and parentId=0";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $ret[] = $res;
    }	
    return $ret;
  }		
     
  function replace_forum($forumId, $name, $description, $controlFlood,$floodInterval, 
                         $moderator, $mail, $useMail,
                         $usePruneUnreplied, $pruneUnrepliedAge,
                         $usePruneOld, $pruneMaxAge, $topicsPerPage,
                         $topicOrdering, $threadOrdering,$section,
                         $topics_list_reads,$topics_list_replies,$topics_list_pts,$topics_list_lastpost,$topics_list_author,$vote_threads,
                         $show_description,
                         $inbound_pop_server,$inbound_pop_port,$inbound_pop_user,$inbound_pop_password,$outbound_address,
                         $topic_smileys, $topic_summary,
                         $ui_avatar, $ui_flag, $ui_posts, $ui_level,$ui_email, $ui_online,
                         $approval_type,
                         $moderator_group,
                         $forum_password, $forum_use_password,
                         $att,$att_store,$att_store_dir,$att_max_size)
  {
    $name = addslashes($name);
    $moderator_group = addslashes($moderator_group);
    $description = addslashes($description);
    $section = addslashes($section);
    $inbound_pop_server = addslashes($inbound_pop_server);
    $inbound_pop_user = addslashes($inbound_pop_user);
    $inbound_pop_password = addslashes($inbound_pop_password);
    
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
                inbound_pop_server = '$inbound_pop_server',
                inbound_pop_port = $inbound_pop_port,
                inbound_pop_user = '$inbound_pop_user',
                inbound_pop_password = '$inbound_pop_password',
                outbound_address = '$outbound_address',
                topic_smileys = '$topic_smileys',
                topic_summary = '$topic_summary',
                ui_avatar = '$ui_avatar',
                ui_flag = '$ui_flag',
                ui_posts = '$ui_posts',
                ui_level = '$ui_level',
                ui_email = '$ui_email',
                ui_online = '$ui_online',
                approval_type = '$approval_type',
                moderator_group = '$moderator_group',
                forum_password = '$forum_password',
                forum_use_password = '$forum_use_password',
                att = '$att',
                att_store = '$att_store',
                att_store_dir = '$att_store_dir',
                att_max_size = $att_max_size,
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
                inbound_pop_server,inbound_pop_port,inbound_pop_user,inbound_pop_password,outbound_address,
                topic_smileys,topic_summary,
                ui_avatar,ui_flag,ui_posts,ui_level,ui_email,ui_online,approval_type,moderator_group,forum_password,forum_use_password,att,att_store,att_store_dir,att_max_size) 
                values ('$name','$description',$now,$now,0,
                        0,'$controlFlood',$floodInterval,'$moderator',0,'$mail','$useMail','$usePruneUnreplied',
                        $pruneUnrepliedAge,  '$usePruneOld',
                        $pruneMaxAge, $topicsPerPage,
                        '$topicOrdering','$threadOrdering','$section',
                        '$topics_list_reads','$topics_list_replies','$topics_list_pts','$topics_list_lastpost','$topics_list_author','$vote_threads','$show_description',
                        '$inbound_pop_server',$inbound_pop_port,'$inbound_pop_user','$inbound_pop_password',$outbound_address',
                        '$topic_smileys','$topic_summary',
                        '$ui_avatar','$ui_flag','$ui_posts','$ui_level','$ui_email','$ui_online','$approval_type','$moderator_group','$forum_password','$forum_use_password','$att','$att_store','$att_store_dir',$att_max_size) ";
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
    $query = "delete from tiki_forum_attachments where forumId=$forumId";
    $this->query($query);
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
    
    $res['user_posts']=$this->getOne("select posts from tiki_user_postings where user='".$res['userName']."'");
    $res['user_level']=$this->getOne("select level from tiki_user_postings where user='".$res['userName']."'");
    if($this->get_user_preference($res['userName'],'email is public','n')=='y') {

      $res['user_email']=$this->getOne("select email from users_users where login='".$res['userName']."'");
    } else {
      $res['user_email']='';
    }
    $res['attachments']=$this->get_thread_attachments($res['threadId'],0);
    $res['user_online']='n';
    if($res['userName']) {
    	$res['user_online']=$this->getOne("select count(*) from tiki_sessions where user='".$res['userName']."'")?'y':'n';
    } 
    
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
    $query = "select threadId,title,userName,points,commentDate,parentId from tiki_comments where parentId=$id and average>=$threshold order by $sort_mode,commentDate desc limit $offset,$max";
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
    
  function get_comments($objectId, $parentId, $offset = 0,$maxRecords = -1,$sort_mode = 'commentDate_desc', $find='', $threshold=0,$id=0)
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
	if($id) {
	  $extra = " and $id ";
	} else {
	  $extra = '';
	}   
    $query = "select count(*) from tiki_comments where object='$hash' and average<$threshold";
    $below = $this->getOne($query);
    if($find) {
      $mid=" where object='$hash' and parentId=$parentId and type='s' and average>=$threshold and (title like '%".$find."%' or data like '%".$find."%') ";  
    } else {
      $mid=" where object='$hash' and parentId=$parentId and type='s' and average>=$threshold "; 
    }
    $query = "select * from tiki_comments $mid $extra order by $sort_mode,threadId limit $offset,$maxRecords";
    //print("$query<br/>");
    $query_cant = "select count(*) from tiki_comments $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret1 = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Get the last reply
      $tid = $res["threadId"];
      $res['user_posts']=$this->getOne("select posts from tiki_user_postings where user='".$res['userName']."'");
      $res['user_level']=$this->getOne("select level from tiki_user_postings where user='".$res['userName']."'");
      if($this->get_user_preference($res['userName'],'email is public','n')=='y') {
      	$res['user_email']=$this->getOne("select email from users_users where login='".$res['userName']."'");
      } else {
      	$res['user_email']='';
      }
      $res['user_online']='n';
      if($res['userName']) {
    	$res['user_online']=$this->getOne("select count(*) from tiki_sessions where user='".$res['userName']."'")?'y':'n';
      } 
	  $res['attachments']=$this->get_thread_attachments($res['threadId'],0);
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
      $mid=" where object='$hash' and parentId=$parentId and type<>'s' and average>=$threshold and (title like '%".$find."%' or data like '%".$find."%') ";  
    } else {
      $mid=" where object='$hash' and parentId=$parentId and type<>'s' and average>=$threshold "; 
    }
    $query = "select * from tiki_comments $mid order by $sort_mode limit $offset,$maxRecords";
    //print("$query<br/>");
    $query_cant = "select count(*) from tiki_comments $mid";
    $result = $this->query($query);
    $cant += $this->getOne($query_cant);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Get the last reply
      $tid = $res["threadId"];
      $res['user_posts']=$this->getOne("select posts from tiki_user_postings where user='".$res['userName']."'");
      $res['user_level']=$this->getOne("select level from tiki_user_postings where user='".$res['userName']."'");
      $res['user_email']=$this->getOne("select email from users_users where login='".$res['userName']."'");
      $res['user_online']='n';
      if($res['userName']) {
    	$res['user_online']=$this->getOne("select count(*) from tiki_sessions where user='".$res['userName']."'")?'y':'n';
      } 
	  $res['attachments']=$this->get_thread_attachments($res['threadId'],0);
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
  
  function lock_comment($threadId)
  {
  	$query = "update tiki_comments set type='l' where threadId=$threadId";
  	$this->query($query);
  }	 
  
  function set_comment_object($threadId,$object)
  {
  	$hash = md5($object);
  	$query = "update tiki_comments set object='$hash' where threadId=$threadId or parentId=$threadId";
  	$this->query($query);
  } 
  
  function set_parent($threadId,$parentId)
  {
    $query = "update tiki_comments set parentId=$parentId where threadId=$threadId";
  	$this->query($query);
  } 


  function unlock_comment($threadId)
  {
  	$query = "update tiki_comments set type='n' where threadId=$threadId";
  	$this->query($query);
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
    } else {
      $now = date("U");
      if($this->db->getOne("select count(*) from tiki_user_postings where user='$userName'")) {
        $query = "update tiki_user_postings set last=$now, posts = posts + 1 where user='$userName'";
        $this->query($query);
      } else {
        $posts = $this->db->getOne("select count(*) from tiki_comments where userName='$userName'");
        if(!$posts) $posts=1;
      	$query = "insert into tiki_user_postings(user,first,last,posts) values('$userName',$now,$now,$posts)";
      	$this->query($query);
      }
      // Calculate max
      $max = $this->getOne("select max(posts) from tiki_user_postings");
      $min = $this->getOne("select min(posts) from tiki_user_postings");
      if($min==0) $min=1;
      $ids = $this->getOne("select count(*) from tiki_user_postings");
      $tot = $this->getOne("select sum(posts) from tiki_user_postings");
      $average = $tot/$ids;
      $range1 = ($min+$average)/2;
      $range2 = ($max+$average)/2;
      
      $posts = $this->db->getOne("select posts from tiki_user_postings where user='$userName'");
      
      if ($posts == $max) {
        $level = 5;
      }  elseif($posts > $range2) {
      	$level = 4;
      } elseif($posts>$average) {
      	$level = 3;
      } elseif($posts>$range1) {
      	$level = 2;
      } else {
      	$level = 1;
      }
      
      $query = "update tiki_user_postings set level=$level where user='$userName'";
      $this->query($query);
     
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
    } else {
      return false;	
    }
    $threadId = $this->getOne("select threadId from tiki_comments where hash='$hash'");
    return $threadId;                      
  }
  
  function remove_comment($threadId) 
  {
    $query = "delete from tiki_comments where threadId='$threadId' or parentId='$threadId'";
    $result = $this->query($query);
    $query = "delete from tiki_forum_attachments where threadId=$threadId";
    $this->query($query);
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
