<?php
include_once('lib/diff.php');
require_once('lib/Date.php');

// This class is included by all the Tiki php scripts, so it's important
// to keep the class as small as possible to improve performance.
// What goes in this class:
// * generic functions that MANY scripts must use
// * shared functions (marked as /*shared*/) are functions that are
//   called from Tiki modules.

class TikiLib {
  var $db;  // The PEAR db object used to access the database
  var $buffer;
  var $flag;
  var $parser;
  var $pre_handlers=Array();
  var $pos_handlers=Array();

  // Constructor receiving a PEAR::Db database object.
  function TikiLib($db)
  {
    if(!$db) {
      die("Invalid db object passed to TikiLib constructor");
    }
    $this->db = $db;
  }

  // This is only for performance collection of all queries
  // uncomment it if you want to profile queries
  /*
  function query($query) {
    //for performance stats
    list($micro,$sec)=explode(' ',microtime());
    $query_start=$sec+$micro;
    $result = $this->db->query($query);
    list($micro,$sec)=explode(' ',microtime());
    $query_stop=$sec+$micro;
    //$fpq=fopen("/tmp/tikiquerystats",'a');
    //fwrite($fpq,$query_stop-$query_start."\t".$query."\n");
    //fclose($fpq);
    $qdiff=$query_stop-$query_start;
    if(DB::isError($result)) $this->sql_error($query,$result);
    $querystat="insert into tiki_querystats values(1,'".addslashes($query)."',$qdiff)";
    $qresult=$this->db->query($querystat);
    if(DB::isError($qresult)) {
      $querystat="update tiki_querystats set qcount=qcount+1, qtime=qtime+$qdiff where qtext='".addslashes($query)."'";
      $qresult=$this->db->query($querystat);
    }
    return $result;
  }
  */

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

  /*shared*/ function get_dsn_by_name($name) 
  {
    return $this->getOne("select dsn from tiki_dsn where name='$name'");
  }
  
  
  /*shared*/ function check_rules($user,$section)
  {
  	// Admin is never banned
  	if($user == 'admin' ) return false;
  	$ips = explode('.',$_SERVER["REMOTE_ADDR"]);
  	$now = date("U");
  	$query = "select tb.message,tb.user,tb.ip1,tb.ip2,tb.ip3,tb.ip4,tb.mode from tiki_banning tb, tiki_banning_sections tbs where tbs.banId=tb.banId and tbs.section='$section' and ( (tb.use_dates = 'n') or (tb.date_from <= $now and tb.date_to >= $now))";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    	if(!$res['message']) {
    		$res['message']=tra('You are banned from').':'.$section;
    	}
    	if($user && $res['mode'] == 'user') {
    		// check user
    		$pattern = '/'.$res['user'].'/';
    		if(preg_match($pattern,$user)) {
    			return $res['message'];
    		}
    	} else {
    		// check ip
    		if(count($ips)==4) {
    			if(
    				($ips[0] == $res['ip1'] || $res['ip1']=='*')
    				&&
    				($ips[1] == $res['ip2'] || $res['ip2']=='*')
    				&&
    				($ips[2] == $res['ip3'] || $res['ip3']=='*')
    				&&
    				($ips[3] == $res['ip4'] || $res['ip4']=='*')
    			) {
    				return $res['message'];
    			}
    				
    		}
    	}
    }
  	return false;
  }

  /*shared*/ function replace_note($user,$noteId,$name,$data)
  {
    $name = addslashes($name);	
    $data = addslashes($data);
    $now = date("U");
    $size = strlen($data);
    if($noteId) {
      $query = "update tiki_user_notes set
      name = '$name',
      data = '$data',
      size = $size,
      lastModif = $now
      where user='$user' and noteId=$noteId";	
      $this->query($query);
      return $noteId;
    } else {
      $query = "insert into tiki_user_notes(user,noteId,name,data,created,lastModif,size)
      values('$user',$noteId,'$name','$data',$now,$now,$size)";
      $this->query($query);
      $noteId = $this->getOne("select max(noteId) from tiki_user_notes where user='$user' and name='$name' and created=$now");
      return $noteId;
    }
  }
  
  /*shared*/ function add_user_watch($user,$event,$object,$type,$title,$url)
  {
    global $userlib;
  	$object=addslashes($object);
  	$hash=md5(uniqid('.'));
  	$email = $userlib->get_user_email($user);
  	$query = "replace into tiki_user_watches(user,event,object,email,hash,type,title,url)
  	values('$user','$event','$object','$email','$hash','$type','$title','$url')";
  	$this->query($query);
  	return true;
  }
  
  /*shared*/ function remove_user_watch_by_hash($hash) {
  	$query = "delete from tiki_user_watches where hash='$hash'";
  	$this->query($query);
  }

  
  /*shared*/ function remove_user_watch($user,$event,$object)
  {
  	$object=addslashes($object);
  	$query = "delete from tiki_user_watches where user='$user' and event='$event' and object='$object'";
  	$this->query($query);
  }
  
  /*shared*/ function get_user_watches($user,$event='')
  {
    $mid='';
    if($event) {
      $mid = " and event='$event' ";
    }
    $query = "select * from tiki_user_watches where user='$user' $mid";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
	return $ret;    
  }
  
  /*shared*/ function get_watches_events()
  {
    $query = "select distinct(event) from tiki_user_watches";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['event'];
    }
	return $ret;    
  }

  /*shared*/ function get_user_event_watches($user,$event,$object)
  {
   
    $query = "select * from tiki_user_watches where user='$user' and event='$event' and object='$object'";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
	return $res;    
  }

  /*shared*/ function get_event_watches($event,$object)
  {
	   
    $ret = Array();
    $query = "select * from tiki_user_watches where event='$event' and object='$object'";
    $result = $this->query($query);
    if(!$result->numRows()) return $ret;
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
	return $ret;    
  }


  /*shared*/ function replace_task($user,$taskId,$title,$description,$date,$status,$priority,$completed,$percentage)
  {
    $title = addslashes($title);	
    $description = addslashes($description);
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
   
  /*shared*/ function complete_task($user,$taskId)
  {
    $now = date("U");
    $query = "update tiki_user_tasks set completed=$now, status='c', percentage=100 where user='$user' and taskId=$taskId";
    $this->query($query);
  }
  
  /*shared*/ function remove_task($user,$taskId)
  {
    $query = "delete from tiki_user_tasks where user='$user' and taskId=$taskId";
    $this->query($query);  	
  }

  /*shared*/ function list_tasks($user,$offset,$maxRecords,$sort_mode,$find,$use_date,$pdate)
  {
    $now = date("U");
    if($use_date=='y') {
     $prio = " and date<=$pdate ";
    } else {
     $prio = '';
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

  /*shared*/ function dir_stats()
  {
    $aux=Array();
    $aux["valid"] = $this->db->getOne("select count(*) from tiki_directory_sites where isValid='y'");
    $aux["invalid"] = $this->db->getOne("select count(*) from tiki_directory_sites where isValid='n'");
    $aux["categs"] = $this->db->getOne("select count(*) from tiki_directory_categories");
    $aux["searches"] = $this->db->getOne("select sum(hits) from tiki_directory_search");
    $aux["visits"] = $this->db->getOne("select sum(hits) from tiki_directory_sites");
    return $aux;
  }
  
  /*shared*/ function dir_list_all_valid_sites2($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where isValid='y' and (name like '%".$find."%' or description like '%".$find."%')";  
    } else {
      $mid=" where isValid='y' "; 
    }
    
    $query = "select * from tiki_directory_sites $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_directory_sites $mid";
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
  
  
  /*shared*/ function user_unread_messages($user)
  {
    $cant = $this->getOne("select count(*) from messu_messages where user='$user' and isRead='n'");
    return $cant;
  }

  /*shared*/ function get_online_users()
  {
    $query = "select user from tiki_sessions where user<>''";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res['user_information'] = $this->get_user_preference($res['user'],'user_information','public');
      $ret[] = $res;
    }
    return $ret;
  }


  /*shared*/ function get_user_items($user)
  {
    $items = Array();
    $query = "select ttf.trackerId, tti.itemId from tiki_tracker_fields ttf, tiki_tracker_items tti, tiki_tracker_item_fields ttif where ttf.fieldId=ttif.fieldId and ttif.itemId=tti.itemId and type='u' and tti.status='o' and value='$user'";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $itemId=$res["itemId"];
      $trackerId=$res["trackerId"];
      // Now get the isMain field for this tracker
      $fieldId=$this->getOne("select fieldId from tiki_tracker_fields ttf where isMain='y' and trackerId=$trackerId");
      // Now get the field value
      $value = $this->getOne("select value from tiki_tracker_item_fields where fieldId=$fieldId and itemId=$itemId");
      $tracker = $this->getOne("select name from tiki_trackers where trackerId=$trackerId");
      $aux["trackerId"]=$trackerId;
      $aux["itemId"]=$itemId;
      $aux["value"]=$value;
      $aux["name"]=$tracker;
      if(!in_array($itemId,$items)) {
        $ret[]=$aux;
        $items[]=$itemId;
      }
    }

    $groups = $this->get_user_groups($user);
    foreach($groups as $group) {
      $query = "select ttf.trackerId, tti.itemId from tiki_tracker_fields ttf, tiki_tracker_items tti, tiki_tracker_item_fields ttif where ttf.fieldId=ttif.fieldId and ttif.itemId=tti.itemId and type='g' and tti.status='o' and value='$group'";
      $result = $this->query($query);
      while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
        $itemId=$res["itemId"];
        $trackerId=$res["trackerId"];
        // Now get the isMain field for this tracker
        $fieldId=$this->getOne("select fieldId from tiki_tracker_fields ttf where isMain='y' and trackerId=$trackerId");
        // Now get the field value
        $value = $this->getOne("select value from tiki_tracker_item_fields where fieldId=$fieldId and itemId=$itemId");
        $tracker = $this->getOne("select name from tiki_trackers where trackerId=$trackerId");
        $aux["trackerId"]=$trackerId;
        $aux["itemId"]=$itemId;
        $aux["value"]=$value;
        $aux["name"]=$tracker;
        if(!in_array($itemId,$items)) {
          $ret[]=$aux;
          $items[]=$itemId;
        }
      }

    }

    return $ret;
  }
  
  /*shared*/ function get_actual_content($contentId)
  {
    $data ='';
    $now = date("U");
    $query = "select max(publishDate) from tiki_programmed_content where contentId=$contentId and publishDate<=$now";
    $res = $this->getOne($query);
    if(!$res) return '';
    $query = "select data from tiki_programmed_content where contentId=$contentId and publishDate=$res";
    $data = $this->getOne($query);
    return $data;
  }
  

  /*shared*/ function get_quiz($quizId) 
  {
    $query = "select * from tiki_quizzes where quizId=$quizId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  /*shared*/ function compute_quiz_stats()
  {
    $query = "select quizId from tiki_user_quizzes";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $quizId = $res["quizId"];
      $quizName = $this->getOne("select name from tiki_quizzes where quizId=$quizId");
      $timesTaken = $this->getOne("select count(*) from tiki_user_quizzes where quizId=$quizId");
      $avgpoints = $this->getOne("select avg(points) from tiki_user_quizzes where quizId=$quizId");
      $maxPoints = $this->getOne("select max(maxPoints) from tiki_user_quizzes where quizId=$quizId");
      $avgavg = $avgpoints/$maxPoints*100;
      $avgtime = $this->getOne("select avg(timeTaken) from tiki_user_quizzes where quizId=$quizId");
      $query2 = "replace into tiki_quiz_stats_sum(quizId,quizName,timesTaken,avgpoints,avgtime,avgavg)
      values($quizId,'$quizName',$timesTaken,$avgpoints,$avgtime,$avgavg)";
      $result2 = $this->query($query2);
    }
  }

  /*shared*/ function list_quizzes($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
    $mid=" where (name like '%".$find."%' or description like '%".$find."%')";
    } else {
      $mid=" ";
    }
    $query = "select * from tiki_quizzes $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_quizzes $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["questions"]=$this->getOne("select count(*) from tiki_quiz_questions where quizId=".$res["quizId"]);
      $res["results"]=$this->getOne("select count(*) from tiki_quiz_results where quizId=".$res["quizId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*shared*/ function list_quiz_sum_stats($offset,$maxRecords,$sort_mode,$find)
  {
    $this->compute_quiz_stats();
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
    $mid="  (quizName like '%".$find."%'";
    } else {
      $mid="  ";
    }
    $query = "select * from tiki_quiz_stats_sum $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_quiz_stats_sum $mid";
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

  /*shared*/ function list_surveys($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
    $mid=" where (name like '%".$find."%' or description like '%".$find."%')";
    } else {
      $mid=" ";
    }
    $query = "select * from tiki_surveys $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_surveys $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["questions"]=$this->getOne("select count(*) from tiki_survey_questions where surveyId=".$res["surveyId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*shared*/ function list_tracker_items($trackerId,$offset,$maxRecords,$sort_mode,$fields,$status='')
  {
    $filters=Array();
    if($fields) {
      for($i=0;$i<count($fields["data"]);$i++) {
        $fieldId=$fields["data"][$i]["fieldId"];
        $type=$fields["data"][$i]["type"];
        $value=$fields["data"][$i]["value"];
        $aux["value"]=$value;
        $aux["type"]=$type;
        $filters[$fieldId]=$aux;
      }
    }

    $sort_mode = str_replace("_"," ",$sort_mode);
    $mid=" where trackerId=$trackerId ";
    if($status) {
      $mid.=" and status='$status' ";
    }
    $query = "select * from tiki_tracker_items $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_tracker_items $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $fields=Array();
      $itid=$res["itemId"];
      $query2="select ttif.fieldId,name,value,type,isTblVisible,isMain from tiki_tracker_item_fields ttif, tiki_tracker_fields ttf where ttif.fieldId=ttf.fieldId and itemId=".$res["itemId"]." order by fieldId asc";
      $result2 = $this->query($query2);
      $pass=true;
      while($res2 = $result2->fetchRow(DB_FETCHMODE_ASSOC)) {
        // Check if the field is visible!
        $fieldId=$res2["fieldId"];
        if(count($filters)>0) {
          if($filters["$fieldId"]["value"]) {
            if($filters["$fieldId"]["type"]=='a' || $filters["$fieldId"]["type"]=='t' ) {
              if(!strstr($res2["value"],$filters["$fieldId"]["value"])) $pass=false;
            } else {
              if($res2["value"]!=$filters["$fieldId"]["value"]) $pass=false;
            }
          }
        }
        $fields[]=$res2;
      }
      $res["field_values"]=$fields;
      $res["comments"]=$this->getOne("select count(*) from tiki_tracker_item_comments where itemId=$itid");
      if($pass) $ret[] = $res;
    }
    //$ret=$this->sort_items_by_condition($ret,$sort_mode);
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*shared*/ function get_user_avatar($user)
  {
    if(empty($user)) return '';
    if(!$this->user_exists($user)) {
      return '';
    }
    $type = $this->getOne("select avatarType from users_users where login='$user'");
    $libname = $this->getOne("select avatarLibName from users_users where login='$user'");
    $ret='';
    switch($type) {
      case 'n':
        $ret = '';
        break;
      case 'l':
        $ret = "<img border='0' width='45' height='45' src='".$libname."' />";
        break;
      case 'u':
        $ret = "<img border='0' width='45' height='45' src='tiki-show_user_avatar.php?user=$user' />";
        break;
    }
    return $ret;
  }

  /*shared*/ function get_forum_sections()
  {
    $query = "select distinct section from tiki_forums where section<>''";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res["section"];
    }
    return $ret;
  }

  /* Referer stats */
  /*shared*/ function register_referer($referer)
  {
     $referer = addslashes($referer);
     $now=date("U");
     $cant = $this->getOne("select count(*) from tiki_referer_stats where referer='$referer'");
     if($cant) {
       $query = "update tiki_referer_stats set hits=hits+1,last=$now where referer='$referer'";
     } else {
       $query = "insert into tiki_referer_stats(referer,hits,last) values('$referer',1,$now)";
     }
     $result = $this->query($query);
  }
 
  // File attachments functions for the wiki ////
  /*shared*/ function add_wiki_attachment_hit($id)
  {
    if($count_admin_pvs == 'y' || $user!='admin') {
      $query = "update tiki_wiki_attachments set downloads=downloads+1 where attId=$id";
      $result = $this->query($query);
    }
    return true;
  }
  
  /*shared*/ function get_wiki_attachment($attId)
  {
    $query = "select * from tiki_wiki_attachments where attId=$attId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  /*shared*/ function get_random_image($galleryId = -1)
  {
    $whgal = "";
    if (((int)$galleryId) != -1) { $whgal = " where galleryId = " . $galleryId; }
    $query = "select count(*) from tiki_images" . $whgal;
    $cant = $this->getOne($query);
    $pick = rand(0,$cant-1);
    $ret = Array();
    $query = "select imageId,galleryId,name from tiki_images" . $whgal . " limit $pick,1";
    $result=$this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $ret["galleryId"] = $res["galleryId"];
    $ret["imageId"] = $res["imageId"];
    $ret["name"] = $res["name"];
    $query = "select name from tiki_galleries where galleryId = " . $res["galleryId"];
    $ret["gallery"] = $this->getOne($query);
    return($ret);
  }

  /*shared*/   function get_gallery($id)
  {
    $query = "select * from tiki_galleries where galleryId='$id'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  // Last visit module ////
  /*shared*/ function get_news_from_last_visit($user)
  {
    if(!$user) return false;
    $last = $this->getOne("select lastLogin from users_users where login='$user'");
    $ret = Array();
    $ret["lastVisit"] = $last;
    $ret["images"] = $this->getOne("select count(*) from tiki_images where created>$last");
    $ret["pages"] = $this->getOne("select count(*) from tiki_pages where lastModif>$last");
    $ret["files"]  = $this->getOne("select count(*) from tiki_files where created>$last");
    $ret["comments"]  = $this->getOne("select count(*) from tiki_comments where commentDate>$last");
    $ret["users"]  = $this->getOne("select count(*) from users_users where registrationDate>$last");
    return $ret;
  }
  
  // Templates ////
  /*shared*/ function list_templates($section,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" and (content like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select name,created,tcts.templateId from tiki_content_templates tct, tiki_content_templates_sections tcts where tcts.templateId=tct.templateId and section='$section' $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_content_templates tct, tiki_content_templates_sections tcts where tcts.templateId=tct.templateId and section='$section' $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $query2= "select section from tiki_content_templates_sections where templateId=".$res["templateId"];
      $result2 = $this->query($query2);
      $sections = Array();
      while($res2 = $result2->fetchRow(DB_FETCHMODE_ASSOC)) {
        $sections[] = $res2["section"];
      }
      $res["sections"]=$sections;
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*shared*/ function get_template($templateId)
  {
    $query = "select * from tiki_content_templates where templateId=$templateId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  // templates ////

  /*shared*/ function list_games($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (gameName like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_games $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_games $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $parts=explode('.',$res["gameName"]);
      $res["thumbName"]=$parts[0];
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*shared*/ function pick_cookie()
  {
    $cant = $this->getOne("select count(*) from tiki_cookies");
    if(!$cant) return '';
    $bid = rand(0,$cant-1);
    $cookie = $this->getOne("select cookie from tiki_cookies limit $bid,1");
    $cookie = str_replace("\n","",$cookie);
    return '<i>"'.$cookie.'"</i>';
  }

  // Stats ////
  /*shared*/ function add_pageview()
  {
    $dayzero = mktime(0,0,0,date("m"),date("d"),date("Y"));
    $cant = $this->getOne("select count(*) from tiki_pageviews where day=$dayzero");
    if($cant) {
      $query = "update tiki_pageviews set pageviews=pageviews+1 where day=$dayzero";
    } else {
      $query = "replace into tiki_pageviews(day,pageviews) values($dayzero,1)";
    }
    $result = $this->query($query);
  }

  function get_pv_chart_data($days)
  {
    $now = mktime(0,0,0,date("m"),date("d"),date("Y"));
    $dfrom = $now-(7*24*60*60);
    $query = "select pageviews from tiki_pageviews where day<=$now and day>=$dfrom";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $data=Array("",$res["pageviews"]);
      $ret[]=$data;
    }

   return $ret;
  }

  function get_usage_chart_data()
  {
    $this->compute_quiz_stats();
    $data=Array();
    $data[]=Array("wiki",$this->getOne("select sum(hits) from tiki_pages"));
    $data[]=Array("img-g",$this->getOne("select sum(hits) from tiki_galleries"));
    $data[]=Array("file-g",$this->getOne("select sum(hits) from tiki_file_galleries"));
    $data[]=Array("faqs",$this->getOne("select sum(hits) from tiki_faqs"));
    $data[]=Array("quizzes",$this->getOne("select sum(timesTaken) from tiki_quiz_stats_sum"));
    $data[]=Array("arts",$this->getOne("select sum(reads) from tiki_articles"));
    $data[]=Array("blogs",$this->getOne("select sum(hits) from tiki_blogs"));
    $data[]=Array("forums",$this->getOne("select sum(hits) from tiki_forums"));
    $data[]=Array("games",$this->getOne("select sum(hits) from tiki_games"));
   return $data;
  }


  // User assigned modules ////
  /*shared*/ function get_user_id($user)
  {
    $id = $this->db->getOne("select userId from users_users where login='$user'");
    if(DB::isError($id)) return false;
    return $id;
  }

  /*shared*/ function get_user_groups($user)
  {
    $userid = $this->get_user_id($user);
    $query = "select groupName from users_usergroups where userId='$userid'";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["groupName"];
    }
    $ret[] = "Anonymous";
    return $ret;
  }
  
  // Functions for FAQs ////
  /*shared*/ function list_faqs($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (title like '%".$find."%' or description like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_faqs $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_faqs $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["suggested"]=$this->getOne("select count(*) from tiki_suggested_faq_questions where faqId=".$res["faqId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  /*shared */ function get_faq($faqId)
  {
    $query = "select * from tiki_faqs where faqId=$faqId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  // End Faqs ////

  /*shared*/ function genPass()
  {
        $vocales="aeiouAEIOU";
        $consonantes="bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ0123456789_";
        $r='';
        for($i=0; $i<8; $i++){
                if ($i%2){
                        $r.=$vocales{rand(0,strlen($vocales)-1)};
                }else{
                        $r.=$consonantes{rand(0,strlen($consonantes)-1)};
                }
        }
        return $r;
  }

  
  // This function calculates the pageRanks for the tiki_pages
  // it can be used to compute the most relevant pages
  // according to the number of links they have
  // this can be a very interesting ranking for the Wiki
  // More about this on version 1.3 when we add the pageRank
  // column to tiki_pages
  function pageRank($loops=16)
  {
    $query = "select pageName from tiki_pages";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res["pageName"];
    }
    // Now calculate the loop
    $pages = Array();
    foreach ($ret as $page) {
      $val = 1/count($ret);
      $pages[$page] = $val;
      $query = "update tiki_pages set pageRank=$val where pageName='".addslashes($page)."'";
      $result = $this->query($query);
    }
    for($i=0;$i<$loops;$i++) {
      foreach($pages as $pagename => $rank) {
        // Get all the pages linking to this one
        $query = "select fromPage from tiki_links where toPage = '".addslashes($pagename)."'";
        $result = $this->query($query);

        $sum = 0;
        while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
          $linking = $res["fromPage"];
          $q2 = "select count(*) from tiki_links where fromPage='".addslashes($linking)."'";
          $cant = $this->getOne($q2);
          if($cant==0) $cant=1;
          $sum += $pages[$linking] / $cant;
        }
         $val = (1-0.85)+0.85 * $sum;
         $pages[$pagename] = $val;
         $query = "update tiki_pages set pageRank=$val where pageName='".addslashes($pagename)."'";
         $result = $this->query($query);

        // Update
      }
    }
    arsort($pages);
    return $pages;
  }

  // Spellchecking routine
  // Parameters:
  // what: what to spell check (a text)
  // where: where to replace (maybe the same text)
  // language: language to use
  // element: element where the text is going to be replaced (a textarea or similar)
  /*shared*/ function spellcheckreplace($what,$where,$language,$element)
  {
    global $smarty;
    $trl='';
    $words = preg_split("/\s/",$what);
    foreach($words as $word) {
    if(preg_match("/^[A-Z]?[a-z]+$/",$word) && strlen($word)>1) {
      $result = $this->spellcheckword($word,$language);
        if(count($result)>0) {
          // Replace the word with a warning color in the edit_data
          // Prepare the replacement
          $sugs = $result[$word];
          $first=1;
          $repl='';

          $popup_text='';
          //foreach($sugs as $sug=>$lev) {
          //  if($first) {
          //    $repl.=' <span style="color:red;">'.$word.'</span>'.'<a title="'.$sug.'" style="text-decoration: none; color:red;" href="javascript:replaceSome(\'editwiki\',\''.$word.'\',\''.$sug.'\');">.</a>';
          //    $first = 0;
          //  } else {
          //    $repl.='<a title="'.$sug.'" style="text-decoration: none; color:red;" href="javascript:replaceSome(\'editwiki\',\''.$word.'\',\''.$sug.'\');">.</a>';
          //    //$repl.='|'.'<a style="color:red;" href="javascript:replaceSome(\'editwiki\',\''.$word.'\',\''.$sug.'\');">'.$sug.'</a>';
          //  }
          //}
          //if($repl) {
          //  $repl.=' ';
          //}
          if(count($sugs)>0) {
            $asugs = array_keys($sugs);
            for($i=0;$i<count($asugs)&&$i<5;$i++) {
              $sug = $asugs[$i];
              //$repl.="<script>param_${word}_$i = new Array(\\\"$element\\\",\\\"$word\\\",\\\"$sug\\\");</script><a href=\\\"javascript:replaceLimon(param_${word}_$i);\\"."\">$sug</a><br/>";
              $repl.="<a href=\\\"javascript:param=doo_${word}_$i();replaceLimon(param);\\\">$sug</a><br/>";
              $trl.="<script>function doo_${word}_$i(){ aux = new Array(\"$element\",\"$word\",\"$sug\"); return aux;}</script>";

            }
            //$popup_text = " <a title=\"".$sug."\" style=\"text-decoration:none; color:red;\" onClick='"."return overlib(".'"'.$repl.'"'.",STICKY,CAPTION,".'"'."SpellChecker suggestions".'"'.");'>".$word.'</a> ';
            $popup_text = " <a title='$sug' style='text-decoration:none; color:red;' onClick='return overlib(\"".$repl."\",STICKY,CAPTION,\"Spellchecker suggestions\");'>$word</a> ";
          }
          //print("popup: <pre>".htmlentities($popup_text)."</pre><br/>");
          if($popup_text) {
            $where = preg_replace("/\s$word\s/",$popup_text,$where);
          } else {
            $where = preg_replace("/\s$word\s/",' <span style="color:red;">'.$word.'</span> ',$where);
          }
          $smarty->assign('trl',$trl);
          //$parsed = preg_replace("/\s$word\s/",' <a style="color:red;">'.$word.'</a> ',$parsed);
        }
      }
    }
    return $where;
  }

  /*shared*/ function spellcheckword($word,$lang)
  {
    include_once("bablotron.php");
    $b = new bablotron($this->db,$lang);
    $result = $b->spellcheck_word($word);
    return $result;
  }

  function diff2($page1,$page2)
  {
      $page1 = split("\n",$page1);
      $page2 = split("\n",$page2);
      $z = new WikiDiff($page1, $page2);
      if ($z->isEmpty()) {
    $html = '<hr><br/>[' . tra("Versions are identical") . ']<br/><br/>';
      } else {
    //$fmt = new WikiDiffFormatter;
    $fmt = new WikiUnifiedDiffFormatter;
    $html = $fmt->format($z, $page1);
      }
      return $html;
  }

  /*shared*/ function get_forum($forumId)
  {
    $query = "select * from tiki_forums where forumId='$forumId'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  /*shared*/ function list_all_forum_topics($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" and (title like '%".$find."%' or data like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_comments,tiki_forums where object=md5(concat('forum',forumId)) and parentId=0 $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_comments,tiki_forums where object=md5(concat('forum',forumId)) and parentId=0 $mid order by $sort_mode limit $offset,$maxRecords";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $now = date("U");
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*shared*/ function list_forum_topics($forumId,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" and (title like '%".$find."%' or data like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_comments,tiki_forums where object=md5(concat('forum',$forumId)) and parentId=0 $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_comments,tiki_forums where object=md5(concat('forum',$forumId)) and parentId=0 $mid order by $sort_mode limit $offset,$maxRecords";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $now = date("U");
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*shared*/ function list_forums($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (name like '%".$find."%' or description like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_forums $mid order by $sort_mode limit $offset,$maxRecords";
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
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*shared*/ function remove_object($type,$id)
  {
    $this->uncategorize_object($type,$id);
    // Now remove comments
    $object = md5($type.$id);
    $query = "delete from tiki_comments where object='$object'";
    $result = $this->query($query);
    // Remove individual permissions for this object if they exist
    $query = "delete from users_objectpermissions where objectId='$object' and objectType='$type'";
    $result = $this->query($query);
    return true;
   }
   
  /*shared*/ function uncategorize_object($type,$id)
  {
    $id=addslashes($id);
    $query = "select catObjectId from tiki_categorized_objects where type='$type' and objId='$id'";
    $catObjectId = $this->getOne($query);
    if($catObjectId) {
      $query = "delete from tiki_category_objects where catObjectId=$catObjectId";
      $result = $this->query($query);
      $query = "delete from tiki_categorized_objects where catObjectId=$catObjectId";
      $result = $this->query($query);
    }
  }


  /*shared*/ function list_received_pages($offset,$maxRecords,$sort_mode='pageName_asc',$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (pagename like '%".$find."%' or data like '%".$find."% ')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_received_pages $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_received_pages $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      if($this->page_exists($res["pageName"])) {
        $res["exists"]='y';
      } else {
        $res["exists"]='n';
      }
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }


  // Functions for polls ////
  /*shared*/ function get_poll($pollId)
  {
    $query = "select * from tiki_polls where pollId=$pollId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  //This should be moved to a poll module (currently in tiki-setup.php
  /*shared*/ function poll_vote($pollId,$optionId)
  {
    $query = "update tiki_poll_options set votes=votes+1 where optionId=$optionId";
    $result = $this->query($query);
    $query = "update tiki_polls set votes=votes+1 where pollId=$pollId";
    $result = $this->query($query);
  }

  // end polls ////

  
  // Functions for the menubuilder and polls////
  /*Shared*/ function get_menu($menuId)
  {
    $query = "select * from tiki_menus where menuId=$menuId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  /*shared*/ function list_menu_options($menuId,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where menuId=$menuId and (name like '%".$find."%' or url like '%".$find."%')";
    } else {
      $mid=" where menuId=$menuId ";
    }
    $query = "select * from tiki_menu_options $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_menu_options $mid";
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
  // Menubuilder ends ////

  // User voting system ////
  // Used to vote everything (polls,comments,files,submissions,etc) ////
  // Checks if a user has voted
  /*shared*/ function user_has_voted($user,$id)
  {
    // If user is not logged in then check the session
    if(!$user) {
      $votes = $_SESSION["votes"];
      if(in_array($id,$votes)) {
        $ret = true;
      } else {
        $ret = false;
      }
    } else {
      $query = "select user from tiki_user_votings where user='$user' and id='$id'";
      $result = $this->query($query);
      if($result->numRows()) {
        $ret = true;
      } else {
        $ret = false;
      }
    }
    return $ret;
  }

  // Registers a user vote
  /*shared*/ function register_user_vote($user,$id)
  {
    // If user is not logged in then register in the session
    if(!$user) {
      $_SESSION["votes"][]=$id;
    } else {
      $query = "replace into tiki_user_votings(user,id) values('$user','$id')";
      $result = $this->query($query);

    }
  }

  // FILE GALLERIES ////
  /*shared*/ function list_files($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (name like '%".$find."%' or description like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select fileId,name,description,created,filename,filesize,user,downloads from tiki_files $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_files $mid";
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

  /*shared*/ function get_file($id)
  {
    $query = "select path,galleryId,filename,filetype,data from tiki_files where fileId='$id'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  /*Shared*/ function get_files($offset,$maxRecords,$sort_mode,$find,$galleryId)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where galleryId=$galleryId and (name like '%".$find."%' or description like '%".$find."%')";
    } else {
      $mid="where galleryId=$galleryId";
    }
    $query = "select fileId,name,description,created,filename,filesize,user,downloads from tiki_files $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_files $mid";
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

  /*shared*/ function add_file_hit($id)
  {
    if($count_admin_pvs == 'y' || $user!='admin') {
      $query = "update tiki_files set downloads=downloads+1 where fileId=$id";
      $result = $this->query($query);
    }
    return true;
  }

  /*shared*/function add_file_gallery_hit($id)
  {
    if($count_admin_pvs == 'y' || $user!='admin') {
      $query = "update tiki_file_galleries set hits=hits+1 where galleryId=$id";
      $result = $this->query($query);
    }
    return true;
  }

  

  /*shared*/ function get_file_gallery($id)
  {
    $query = "select * from tiki_file_galleries where galleryId='$id'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  

  /*shared*/ function list_visible_file_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user, $find)
  {
    // If $user is admin then get ALL galleries, if not only user galleries are shown
    $sort_mode = str_replace("_"," ",$sort_mode);
    $old_sort_mode ='';
    if(in_array($sort_mode,Array('files desc','files asc'))) {
      $old_offset = $offset;
      $old_maxRecords = $maxRecords;
      $old_sort_mode = $sort_mode;
      $sort_mode ='user desc';
      $offset = 0;
      $maxRecords = -1;
    }

    // If the user is not admin then select it's own galleries or public galleries
    if($user != 'admin') {
      $whuser = " and (user='$user' or public='y')";
    } else {
      $whuser = "";
    }

    if($find) {
      if(empty($whuser)) {
        $whuser = " and (name like '%".$find."%' or description like '%".$find.".%')";
      } else {
        $whuser .= " and (name like '%".$find."%' or description like '%".$find.".%')";
      }
    }

    $query = "select * from tiki_file_galleries where visible='y' $whuser order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_file_galleries where visible='y' $whuser";
    $result = $this->query($query);
    $result_cant = $this->query($query_cant);
    $res2 = $result_cant->fetchRow();
    $cant = $res2[0];
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["name"] = $res["name"];
      $gid = $res["galleryId"];
      $aux["id"] = $gid;
      $aux["visible"] = $res["visible"];
      $aux["galleryId"] = $res["galleryId"];
      $aux["description"] = $res["description"];
      $aux["created"] = $res["created"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["user"] = $res["user"];
      $aux["hits"] = $res["hits"];
      $aux["public"] = $res["public"];
      $aux["files"] = $this->getOne("select count(*) from tiki_files where galleryId='$gid'");
      $ret[] = $aux;
    }
    if($old_sort_mode == 'files asc') {
      usort($ret,'compare_files');
    }
    if($old_sort_mode == 'files desc') {
      usort($ret,'r_compare_files');
    }

    if(in_array($old_sort_mode,Array('files desc','files asc'))) {
      $ret = array_slice($ret, $old_offset, $old_maxRecords);
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function logui($line) {
    $fw=fopen("log.txt","a+");
    fputs($fw,$line."\n");
    fclose($fw);
  }

  // Semaphore functions ////
  function get_semaphore_user($semName)
  {
    return $this->getOne("select user from tiki_semaphores where semName='$semName'");
  }
  
  function semaphore_is_set($semName,$limit)
  {

    $now=date("U");
    $lim=$now-$limit;
    $query = "delete from tiki_semaphores where semName='$semName' and timestamp<$lim";
    $result = $this->query($query);
    $query = "select semName from tiki_semaphores where semName='$semName'";
    $result = $this->query($query);
    return $result->numRows();
   }

  function semaphore_set($semName)
  {
	global $user;
    if($user == '') {
      $user = 'anonymous';
    }
    $now=date("U");
//    $cant=$this->getOne("select count(*) from tiki_semaphores where semName='$semName'");
    $query = "delete from tiki_semaphores where semName='$semName'";
    $this->query($query);
    $query = "replace into tiki_semaphores(semName,timestamp,user) values('$semName',$now,'$user')";
    $result = $this->query($query);
    return $now;
  }

  function semaphore_unset($semName,$lock)
  {
    $query = "delete from tiki_semaphores where semName='$semName' and timestamp=$lock";
    $result = $this->query($query);
  }
  
  // Hot words methods ////
  /*shared*/ function get_hotwords()
  {
    $query = "select * from tiki_hotwords";
    $result = $this->query($query);
    $ret= Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[$res["word"]]=$res["url"];
    }
    return $ret;
  }

  // BLOG METHODS ////
  /*shared*/ function list_blogs($offset = 0,$maxRecords = -1,$sort_mode = 'created_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (title like '%".$find."%' or description like '%".$find."%') ";
    } else {
      $mid='';
    }
    $query = "select * from tiki_blogs $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_blogs $mid";
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
  
  /*shared*/ function get_blog($blogId)
  {
    $query = "select * from tiki_blogs where blogId=$blogId";
    $result = $this->query($query);

    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    } else {
      return false;
    }
    return $res;
  }
  
  /*shared*/function list_user_blogs($user,$include_public=false)
  {
    $query = "select * from tiki_blogs where user='$user'";
    if($include_public) {
      $query.=" or public='y'";
    }
    $result = $this->query($query);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    return $ret;
  }

  /*shared*/ function list_posts($offset = 0,$maxRecords = -1,$sort_mode = 'created_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (data like '%".$find."%') ";
    } else {
      $mid='';
    }
    $query = "select * from tiki_blog_posts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_blog_posts $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $blogId=$res["blogId"];
      $query = "select title from tiki_blogs where blogId=$blogId";
      $hash=md5('postId'.$res["postId"]);
      $cant_com = $this->getOne("select count(*) from tiki_comments where object='$hash'");
      $res["comments"]=$cant_com;
      $res["blogTitle"]=$this->getOne($query);
      $res["size"]=strlen($res["data"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }


  // CMS functions -ARTICLES- & -SUBMISSIONS- ////
  /*shared*/ function list_articles($offset = 0,$maxRecords = -1,$sort_mode = 'publishDate_desc', $find='', $date='',$user,$type='',$topicId='')
  {
    global $userlib;
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (title like '%".$find."%' or heading like '%".$find."%' or body like '%".$find."%') ";
    } else {
      $mid='';
    }
    if($date) {
      if($mid) {
        $mid.=" and  publishDate<=$date ";
      } else {
        $mid=" where publishDate<=$date ";
      }
    }
    if($type) {
      if($mid) {
        $mid.=" and type='$type'";
      } else {
        $mid=" where type='$type'";
      }
    }
    if($topicId) {
      if($mid) {
        $mid.=" and topicId=$topicId";
      } else {
        $mid=" where topicId=$topicId";
      }
    }
    $query = "select * from tiki_articles $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_articles $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["entrating"]=floor($res["rating"]);
      $add=1;
      if($userlib->object_has_one_permission($res["topicId"],'topic')) {
        if(!$userlib->object_has_permission($user,$res["topicId"],'topic','tiki_p_topic_read')) {
          $add=0;
        }
      }
      if(empty($res["body"])) {
        $res["isEmpty"] = 'y';
      } else {
        $res["isEmpty"] = 'n';
      }
      if(strlen($res["image_data"])>0) {
        $res["hasImage"] = 'y';
      } else {
        $res["hasImage"] = 'n';
      }
      if($add) {
         $ret[] = $res;
      }
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*shared*/ function list_submissions($offset = 0,$maxRecords = -1,$sort_mode = 'publishDate_desc', $find='', $date='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (title like '%".$find."%' or heading like '%".$find."%' or body like '%".$find."%') ";
    } else {
      $mid='';
    }
    if($date) {
      if($mid) {
        $mid.=" and  publishDate<=$date ";
      } else {
        $mid=" where publishDate<=$date ";
      }
    }
    $query = "select * from tiki_submissions $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_submissions $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["entrating"]=floor($res["rating"]);
      if(empty($res["body"])) {
        $res["isEmpty"] = 'y';
      } else {
        $res["isEmpty"] = 'n';
      }
      if(strlen($res["image_data"])>0) {
        $res["hasImage"] = 'y';
      } else {
        $res["hasImage"] = 'n';
      }
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function get_article($articleId)
  {
    $query = "select * from tiki_articles where articleId=$articleId";
    $result = $this->query($query);

    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      $res["entrating"]=floor($res["rating"]);
    } else {
      return false;
    }
    return $res;
  }

  function get_submission($subId)
  {
    $query = "select * from tiki_submissions where subId=$subId";
    $result = $this->query($query);

    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      $res["entrating"]=floor($res["rating"]);
    } else {
      return false;
    }
    return $res;
  }

  function replace_article ($title,$authorName,$topicId,$useImage,$imgname,$imgsize,$imgtype,$imgdata,$heading,$body,$publishDate,$user,$articleId,$image_x,$image_y,$type,$rating=0,$isfloat='n')
  {
    $title = addslashes($title);
    $heading = addslashes($heading);
    $authorName = addslashes($authorName);
    $imgdata = addslashes($imgdata);
    $imgname = addslashes($imgname);
    $body = addslashes($body);
    $hash = md5($title.$heading.$body);
    $now = date("U");
    $query = "select name from tiki_topics where topicId = $topicId";
    $topicName = $this->getOne($query);
    $topicName = addslashes($topicName);
    $size = strlen($body);
    if($articleId) {
      // Update the article
      $query = "update tiki_articles set
                title = '$title',
                authorName = '$authorName',
                topicId = $topicId,
                topicName = '$topicName',
                size = $size,
                useImage = '$useImage',
                image_name = '$imgname',
                image_type = '$imgtype',
                image_size = '$imgsize',
                image_data = '$imgdata',
                isfloat = '$isfloat',
                image_x = $image_x,
                image_y = $image_y,
                heading = '$heading',
                body = '$body',
                publishDate = $publishDate,
                created = $now,
                author = '$user',
                type = '$type',
                rating = $rating
                where articleId = $articleId";
      $result = $this->query($query);

    } else {
      // Insert the article
      $query = "insert into tiki_articles(title,authorName,topicId,useImage,image_name,image_size,image_type,image_data,publishDate,created,heading,body,hash,author,reads,votes,points,size,topicName,image_x,image_y,type,rating,isfloat)
                         values('$title','$authorName',$topicId,'$useImage','$imgname','$imgsize','$imgtype','$imgdata',$publishDate,$now,'$heading','$body','$hash','$user',0,0,0,$size,'$topicName',$image_x,$image_y,'$type',$rating,'$isfloat')";
      $result = $this->query($query);

      $query2 = "select max(articleId) from tiki_articles where created = $now and title='$title' and hash='$hash'";
      $articleId=$this->getOne($query2);
    }
    return $articleId;
  }

  /*shared*/ function get_topic_image($topicId)
  {
    $query = "select image_name,image_size,image_type,image_data from tiki_topics where topicId=$topicId";
    $result = $this->query($query);

    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  /*shared*/ function get_featured_links($max=10)
  {
    $query = "select * from tiki_featured_links where position>0 order by position asc limit 0,$max";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    return $ret;
  }

  function update_session($sessionId)
  {
    global $user;
    $now = date("U");
    $oldy = $now-(5*60);
    $query = "replace into tiki_sessions(sessionId,timestamp,user) values('$sessionId',$now,'$user')";
    $result = $this->query($query);
    $query = "delete from tiki_sessions where timestamp<$oldy";
    $result = $this->query($query);
    return true;
  }

  function count_sessions()
  {
    $query = "select count(*) from tiki_sessions";
    $cant = $this->getOne($query);
    return $cant;
  }

  /*shared*/ function get_assigned_modules($position)
  {
    $query = "select params,name,title,position,ord,cache_time,rows,groups from tiki_modules where position='$position' order by ord asc";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      if($res["groups"]) {
        $grps = unserialize($res["groups"]);
        $res["module_groups"]='';
        foreach($grps as $grp) {
          $res["module_groups"].=" $grp ";
        }
      } else {
        $res["module_groups"]='&nbsp;';
      }
      $ret[] = $res;
    }
    return $ret;
  }

  /*shared*/ function is_user_module($name)
  {
    $name=addslashes($name);
    $query = "select name from tiki_user_modules where name='$name'";
    $result = $this->query($query);
    return $result->numRows();
  }

  

  /*shared*/ function get_user_module($name)
  {
    $name=addslashes($name);
    $query = "select * from tiki_user_modules where name='$name'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  

  function cache_links($links)
  {
    $cachepages = $this->get_preference("cachepages",'y');
    if($cachepages != 'y') return false;
    foreach($links as $link) {
      if(!$this->is_cached($link)) {
        $this->cache_url($link);
      }
    }
  }

  function get_links($data)
  {
    $links = Array();
    if(preg_match_all("/\[([^\[\|\]]+)(\||\])/",$data,$r1)) {

      $res = $r1[1];
      $links = array_unique($res);
    }

    return $links;
  }

  function get_links_nocache($data)
  {
    $links = Array();
    if(preg_match_all("/\[([^\]]+)/",$data,$r1)) {
      $res = Array();
      foreach($r1[1] as $alink) {
        $parts = explode('|',$alink);
        if(isset($parts[1])&& $parts[1] == 'nocache' ) {
          $res[] = $parts[0];
        } else {
          if(isset($parts[2]) && $parts[2] == 'nocache') {
            $res[] = $parts[0];
          }
        }
      }

      $links = array_unique($res);
    }

    return $links;
  }

	//cache
  function is_cached($url)
  {
    if(strstr($url,"tiki-index")) {
      return false;
    }
    if(strstr($url,"tiki-edit")) {
      return false;
    }
    $url=addslashes($url);
    $query = "select cacheId from tiki_link_cache where url='$url'";
    $result = $this->query($query);
    $cant = $result->numRows();
    return $cant;
  }

  function list_cache($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (url like '%".$find."%') ";
    } else {
      $mid="";
    }
    $query = "select cacheId,url,refresh from tiki_link_cache $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_link_cache $mid";
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

  function cache_url($url)
  {
    $url=addslashes($url);
    // This function stores a cached representation of a page in the cache
    // Check if the URL is not already cached
    //if($this->is_cached($url)) return false;
    @$fp = fopen($url,"r");
    if(!$fp) return false;
    $data = '';
    while(!feof($fp)) {
      $data .= fread($fp,4096);
    }
    fclose($fp);
    // Check for META tags with equiv
    if(0){
    print("Len: ".strlen($data)."<br/>");
    preg_match_all("/\<meta([^\>\<\n\t]+)/i",$data,$reqs);
    foreach($reqs[1] as $meta)
    {
      print("Un meta: $meta<br/>");
      if(stristr($meta,'refresh')) {
        print("Es refresh<br/>");
        preg_match("/url=([^ \"\'\n\t]+)/i",$meta,$urls);
        if(strlen($urls[1])) {
          $urli=$urls[1];
          print("URL: $urli<br/>");
        }
      }
    }
    print("pepe");
    }
    $data = addslashes($data);
    $refresh = date("U");
    $query = "insert into tiki_link_cache(url,data,refresh) values('$url','$data',$refresh)";
    $result = $this->query($query);
    return true;
  }

  function refresh_cache($cacheId)
  {
    $query = "select url from tiki_link_cache where cacheId=$cacheId";
    $url = $this->getOne($query);
    @$fp = fopen($url,"r");
    if(!$fp) return false;
    $data = '';
    while(!feof($fp)) {
      $data .= fread($fp,4096);
    }
    fclose($fp);
    $data = addslashes($data);
    $refresh = date("U");
    $query = "update tiki_link_cache set data='$data', refresh=$refresh where cacheId=$cacheId";
    $result = $this->query($query);
    return true;
  }

  function remove_cache($cacheId)
  {
    $query = "delete from tiki_link_cache where cacheId=$cacheId";
    $result = $this->query($query);
    return true;
  }

  function get_cache($cacheId)
  {
    $query = "select * from tiki_link_cache where cacheId=$cacheId";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  function get_cache_id($url)
  {
    if(!$this->is_cached($url)) return false;
    $query = "select cacheId from tiki_link_cache where url='$url'";
    $id = $this->getOne($query);
    return $id;
  }

  
  function vote_page($page, $points)
  {
    $page = addslashes($page);
    $query = "update pages set points=points+$points, votes=votes+1 where pageName='$page'";
    $result = $this->query($query);
  }

  function get_votes($page)
  { 
    $page = addslashes($page);
    $query = "select points,votes from pages where pageName='$page'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  // This funcion return the $limit most accessed pages
  // it returns pageName and hits for each page
  /*shared*/ function get_top_pages($limit)
  {
    $query = "select pageName, hits from tiki_pages order by hits desc limit 0,$limit";
    $result=$this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["pageName"] = $res["pageName"];
      $aux["hits"] = $res["hits"];
      $ret[] = $aux;
    }
    return $ret;
  }

  // Returns the name of "n" random pages
  /*shared*/ function get_random_pages($n)
  {
    $query = "select count(*) from tiki_pages";
    $cant = $this->getOne($query);
    // Adjust the limit if there are not enough pages
    if($cant<$n) $n=$cant;
    // Now that we know the number of pages to pick select n random positions from 0 to cant
    $positions = Array();
    for ($i=0;$i<$n;$i++)
    {
      $pick = rand(0,$cant-1);
      if(!in_array($pick,$positions)) $positions[]=$pick;
    }
    // Now that we have the positions we just build the data
    $ret = Array();
    for ($i=0; $i<count($positions);$i++) {
      $index = $positions[$i];
      $query = "select pageName from tiki_pages limit $index,1";
      $name = $this->getOne($query);
      $ret[]=$name;
    }
    return $ret;
  }

  // Removes all the versions of a page and the page itself
  /*shared*/ function remove_all_versions($page,$comment='')
  {
    $page = addslashes($page);
    $this->invalidate_cache($page);
    $query = "delete from tiki_pages where pageName = '$page'";
    $result = $this->query($query);
    $query = "delete from tiki_history where pageName = '$page'";
    $result = $this->query($query);
    $query = "delete from tiki_links where fromPage = '$page'";
    $result = $this->query($query);
    $action="Removed";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$page',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','$comment')";
    $result = $this->query($query);
    $this->remove_object('wiki page',$page);
    return true;
  }
  

  function remove_user($user)
  {
    $query = "delete from users_users where login = '$user'";
    $result =  $this->query($query);
    return true;
  }

  function user_exists($user)
  {
    $query = "select login from users_users where login='$user'";
    $result = $this->query($query);
    if($result->numRows()) return true;
    return false;
  }

  function add_user($user, $pass, $email)
  {
    $user = addslashes($user);
    $pass = addslashes($pass);
    $email = addslashes($email);
    if(user_exists($user)) return false;
    $query = "insert into users_users(login,password,email) values('$user','$pass','$email')";
    $result = $this->query($query);
    $action = "user $user added";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','HomePage',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','')";
    $result=$this->query($query);
    return true;
  }

  function get_user_password($user)
  {
    return $this->getOne("select password from users_users where binary login='$user'");
  }

  function get_user_email($user)
  {
    return $this->getOne("select email from users_users where binary login='$user'");
  }

  function get_user_info($user)
  {
    $query = "select login, email, lastLogin from tiki_users where user='$user'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $aux = Array();
    $aux["user"] = $res["user"];
    $user = $aux["user"];
    $aux["email"] = $res["email"];
    $aux["lastLogin"] = $res["lastLogin"];
    // Obtain lastChanged
    $query2 = "select count(*) from tiki_pages where user='$user'";
    $result2 = $this->query($query2);
    $res2 = $result2->fetchRow();
    $aux["versions"] = $res2[0];
    // Obtain versions
    $query3 = "select count(*) from tiki_history where user='$user'";
    $result3 = $this->query($query3);
    $res3 = $result3->fetchRow();
    $aux["lastChanged"] = $res3[0];
    $ret[] = $aux;
    return $aux;
  }

  /*shared*/ function list_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user, $find)
  {
    // If $user is admin then get ALL galleries, if not only user galleries are shown
    global $tiki_p_admin_galleries;
    $sort_mode = str_replace("_"," ",$sort_mode);
    $old_sort_mode ='';
    if(in_array($sort_mode,Array('images desc','images asc'))) {
      $old_offset = $offset;
      $old_maxRecords = $maxRecords;
      $old_sort_mode = $sort_mode;
      $sort_mode ='user desc';
      $offset = 0;
      $maxRecords = -1;
    }

    // If the user is not admin then select it's own galleries or public galleries
     if (($tiki_p_admin_galleries == 'y') or ($user == 'admin')) {
       $whuser = "";
    } else {
      $whuser = "where user='$user' or public='y'";
     }

    if($find) {
      if(empty($whuser)) {
        $whuser = "where name like '%".$find."%' or description like '%".$find.".%'";
      } else {
        $whuser .= " and name like '%".$find."%' or description like '%".$find.".%'";
      }
    }
    // If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    $query = "select * from tiki_galleries $whuser order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_galleries $whuser";
    $result = $this->query($query);
    $result_cant = $this->query($query_cant);
    $res2 = $result_cant->fetchRow();
    $cant = $res2[0];
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["name"] = $res["name"];
      $gid = $res["galleryId"];
      $aux["visible"]=$res["visible"];
      $aux["id"] = $gid;
      $aux["galleryId"] = $res["galleryId"];
      $aux["description"] = $res["description"];
      $aux["created"] = $res["created"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["user"] = $res["user"];
      $aux["hits"] = $res["hits"];
      $aux["public"] = $res["public"];
      $aux["theme"] = $res["theme"];
      $aux["images"] = $this->getOne("select count(*) from tiki_images where galleryId='$gid'");
      $ret[] = $aux;
    }
    if($old_sort_mode == 'images asc') {
      usort($ret,'compare_images');
    }
    if($old_sort_mode == 'images desc') {
      usort($ret,'r_compare_images');
    }

    if(in_array($old_sort_mode,Array('images desc','images asc'))) {
      $ret = array_slice($ret, $old_offset, $old_maxRecords);
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*shared*/ function list_visible_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user, $find)
  {
    // If $user is admin then get ALL galleries, if not only user galleries are shown
    $sort_mode = str_replace("_"," ",$sort_mode);
    $old_sort_mode ='';
    if(in_array($sort_mode,Array('images desc','images asc'))) {
      $old_offset = $offset;
      $old_maxRecords = $maxRecords;
      $old_sort_mode = $sort_mode;
      $sort_mode ='user desc';
      $offset = 0;
      $maxRecords = -1;
    }

    // If the user is not admin then select it's own galleries or public galleries
    if($user != 'admin') {
      $whuser = " and (user='$user' or public='y') ";
    } else {
      $whuser = "";
    }

    if($find) {
      if(empty($whuser)) {
        $whuser = " and (name like '%".$find."%' or description like '%".$find.".%')";
      } else {
        $whuser .= " and (name like '%".$find."%' or description like '%".$find.".%')";
      }
    }
    // If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    $query = "select * from tiki_galleries where visible='y' $whuser order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_galleries where visible='y' $whuser";
    $result = $this->query($query);
    $result_cant = $this->query($query_cant);
    $res2 = $result_cant->fetchRow();
    $cant = $res2[0];
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["name"] = $res["name"];
      $gid = $res["galleryId"];
      $aux["visible"]=$res["visible"];
      $aux["id"] = $gid;
      $aux["galleryId"] = $res["galleryId"];
      $aux["description"] = $res["description"];
      $aux["created"] = $res["created"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["user"] = $res["user"];
      $aux["hits"] = $res["hits"];
      $aux["public"] = $res["public"];
      $aux["theme"] = $res["theme"];
      $aux["images"] = $this->getOne("select count(*) from tiki_images where galleryId='$gid'");
      $ret[] = $aux;
    }
    if($old_sort_mode == 'images asc') {
      usort($ret,'compare_images');
    }
    if($old_sort_mode == 'images desc') {
      usort($ret,'r_compare_images');
    }

    if(in_array($old_sort_mode,Array('images desc','images asc'))) {
      $ret = array_slice($ret, $old_offset, $old_maxRecords);
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function list_pages($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_desc',$find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($sort_mode == 'size desc') {
      $sort_mode = ' length(data) desc';
    }
    if($sort_mode == 'size asc') {
      $sort_mode = ' length(data) asc';
    }
    $old_sort_mode ='';
    if(in_array($sort_mode,Array('versions desc','versions asc','links asc','links desc','backlinks asc','backlinks desc'))) {
      $old_offset = $offset;
      $old_maxRecords = $maxRecords;
      $old_sort_mode = $sort_mode;
      $sort_mode ='user desc';
      $offset = 0;
      $maxRecords = -1;
    }

    if($find) {
      $mid=" where pageName like '%".$find."%' ";
    } else {
      $mid="";
    }

    // If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    $query = "select pageName, hits, length(data) as len ,lastModif, user, ip, comment, version, flag from tiki_pages $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_pages $mid";
    $result = $this->query($query);
    $result_cant = $this->query($query_cant);
    $res2 = $result_cant->fetchRow();
    $cant = $res2[0];
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["pageName"] = $res["pageName"];
      $page = $aux["pageName"];
      $page_as=addslashes($page);
      $aux["hits"] = $res["hits"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["user"] = $res["user"];
      $aux["ip"] = $res["ip"];
      $aux["len"] = $res["len"];
      $aux["comment"] = $res["comment"];
      $aux["version"] = $res["version"];
      $aux["flag"] = $res["flag"] == 'L' ? tra('locked') : tra('unlocked');
      $aux["versions"] = $this->getOne("select count(*) from tiki_history where pageName='$page_as'");
      $aux["links"] = $this->getOne("select count(*) from tiki_links where fromPage='$page_as'");
      $aux["backlinks"] = $this->getOne("select count(*) from tiki_links where toPage='$page_as'");
      $ret[] = $aux;
    }
    // If sortmode is versions, links or backlinks sort using the ad-hoc function and reduce using old_offse and old_maxRecords
    if($old_sort_mode == 'versions asc') {
      usort($ret,'compare_versions');
    }
    if($old_sort_mode == 'versions desc') {
      usort($ret,'r_compare_versions');
    }
    if($old_sort_mode == 'links desc') {
      usort($ret,'compare_links');
    }
    if($old_sort_mode == 'links asc') {
      usort($ret,'r_compare_links');
    }
    if($old_sort_mode == 'backlinks desc') {
      usort($ret,'compare_backlinks');
    }
    if($old_sort_mode == 'backlinks asc') {
      usort($ret,'r_compare_backlinks');
    }
    if(in_array($old_sort_mode,Array('versions desc','versions asc','links asc','links desc','backlinks asc','backlinks desc'))) {
      $ret = array_slice($ret, $old_offset, $old_maxRecords);
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function get_users($offset = 0,$maxRecords = -1,$sort_mode = 'user_desc')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    $old_sort_mode ='';
    if(in_array($sort_mode,Array('versions desc','versions asc','changed asc','changed desc'))) {
      $old_offset = $offset;
      $old_maxRecords = $maxRecords;
      $old_sort_mode = $sort_mode;
      $sort_mode ='user desc';
      $offset = 0;
      $maxRecords = -1;
    }
    // Return an array of users indicating name, email, last changed pages, versions, lastLogin
    $query = "select user, email, lastLogin from tiki_users order by $sort_mode limit $offset,$maxRecords";
    $cant = $this->getOne("select count(*) from tiki_users");
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["user"] = $res["user"];
      $user = $aux["user"];
      $aux["email"] = $res["email"];
      $aux["lastLogin"] = $res["lastLogin"];
      // Obtain lastChanged
      $aux["versions"] = $this->getOne("select count(*) from tiki_pages where user='$user'");
      // Obtain versions
      $aux["lastChanged"] = $this->getOne("select count(*) from tiki_history where user='$user'");
      $ret[] = $aux;
    }
    if($old_sort_mode == 'changed asc') {
      usort($ret,'compare_changed');
    }
    if($old_sort_mode == 'changed desc') {
      usort($ret,'r_compare_changed');
    }
    if($old_sort_mode == 'versions asc') {
      usort($ret,'compare_versions');
    }
    if($old_sort_mode == 'versions desc') {
      usort($ret,'r_compare_versions');
    }
    if(in_array($old_sort_mode,Array('versions desc','versions asc','changed asc','changed desc'))) {
      $ret = array_slice($ret, $old_offset, $old_maxRecords);
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function get_all_preferences()
  {
    $query = "select name,value from tiki_preferences";
    $result = $this->query($query);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[$res["name"]] = $res["value"];
    }
    return $ret;
  }

  function get_preference($name, $default='')
  {
    static $preferences;

    if (!isset($preferences[$name])) {
      $query = "select value from tiki_preferences where name='$name'";
      $result = $this->query($query);
      if($result->numRows()) {
        $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $preferences[$name] = $res["value"];
      } else {
        $preferences[$name] = $default;
      }
    }

    return $preferences[$name];
  }

  function set_preference($name, $value)
  {
    global $preferences;
    @unlink('templates_c/preferences.php');
    //refresh cache
    if(isset($preferences[$name])) {
      unset ($preferences[$name]);
      $preferences[$name] = $value;
    }

    $name = addslashes($name);
    $value = addslashes($value);
    $query = "replace into tiki_preferences(name,value) values('$name','$value')";
    $result = $this->query($query);
    return true;
  }

  function get_user_preference($user, $name, $default='')
  {
    global $user_preferences;
    if (!isset($user_preferences[$user][$name])) {
      $query = "select value from tiki_user_preferences where prefName='$name' and user='$user'";
      $result = $this->query($query);
      if($result->numRows()) {
        $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $user_preferences[$user][$name] = $res["value"];
      } else {
        $user_preferences[$user][$name] = $default;
      }
    }
    return $user_preferences[$user][$name];
  }

  function set_user_preference($user, $name, $value)
  {
  	global $user_preferences;
	$user_preferences[$user][$name]=$value;    
    $name = addslashes($name);
    $value = addslashes($value);
    $query = "replace into tiki_user_preferences(user,prefName,value) values('$user','$name','$value')";
    $result = $this->query($query);
    return true;
  }

  function validate_user($user,$pass)
  {
    $query = "select user from tiki_users where user='$user' and password='$pass'";
    $result = $this->query($query);
    if($result->numRows()) {
      $t = date("U");
      $query = "update tiki_users set lastLogin='$t' where user='$user'";
      $result = $this->query($query);
      return true;
    }
    return false;
  }

  // Like pages are pages that share a word in common with the current page
  function get_like_pages($page)
  {
    preg_match_all("/([A-Z])([a-z]+)/",$page,$words);
    // Add support to ((x)) in either strict or full modes
    preg_match_all("/(([A-Za-z]|[\x80-\xFF])+)/",$page,$words2);
    $words=array_unique(array_merge($words[0], $words2[0]));
    $exps = Array();
    foreach($words as $word) {
      $exps[] = "pageName like '%$word%'";
    }
    $exp = implode(" or ",$exps);
    $query = "select pageName from tiki_pages where $exp";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["pageName"];
    }
    return $ret;
  }

  // Returns information about a specific version of a page
  function get_version($page, $version)
  {
    $page = addslashes($page);
    $query = "select * from tiki_history where pageName='$page' and version=$version";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  // Returns all the versions for this page
  // without the data itself
  function get_page_history($page)
  {
    $page = addslashes($page);
    $query = "select pageName, description, version, lastModif, user, ip, data, comment from tiki_history where pageName='$page' order by version desc";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["version"] = $res["version"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["user"] = $res["user"];
      $aux["ip"] = $res["ip"];
      $aux["data"] = $res["data"];
      $aux["pageName"] = $res["pageName"];
      $aux["description"] = $res["description"];
      $aux["comment"] = $res["comment"];
      //$aux["percent"] = levenshtein($res["data"],$actual);
      $ret[]=$aux;
    }
    return $ret;
  }

  function is_locked($page)
  {
    $page = addslashes($page);
    $query = "select flag from tiki_pages where pageName='$page'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    if($res["flag"]=='L') return true;
    return false;
  }

  function lock_page($page)
  {
    global $user;
    $page = addslashes($page);
    $query = "update tiki_pages set flag='L' where pageName='$page'";
    $result = $this->query($query);
    if(isset($user)) {
    	$query = "update tiki_pages set user='$user' where pageName='$page'";
    	$result = $this->query($query);
    }
    return true;
  }

  function unlock_page($page)
  {
    $page = addslashes($page);
    $query = "update tiki_pages set flag='' where pageName='$page'";
    $result = $this->query($query);
    return true;
  }
  
  

  // Returns backlinks for a given page
  function get_backlinks($page)
  {
    $query = "select fromPage from tiki_links where toPage = '$page'";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["fromPage"] = $res["fromPage"];
      $ret[] = $aux;
    }
    return $ret;
  }

  // tikilib.php a Library to access the Tiki's Data Model
  // This implements all the functions needed to use Tiki
  function page_exists($pageName)
  {
    $pageName = addslashes($pageName);
    $query = "select pageName from tiki_pages where pageName = '$pageName'";
    $result = $this->query($query);
    return $result->numRows();
  }

  function page_exists_desc($pageName)
  {
    $pageName = addslashes($pageName);
    $query = "select description from tiki_pages where pageName = '$pageName'";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    if(!$res["description"]) $res["description"]=tra('no description');
    return $res["description"];
  }

  function version_exists($pageName, $version)
  {
    $pageName = addslashes($pageName);
    $query = "select pageName from tiki_history where pageName = '$pageName' and version='$version'";
    $result = $this->query($query);
    return $result->numRows();
  }

  function add_hit($pageName) {
    $pageName = addslashes($pageName);
    $query = "update tiki_pages set hits=hits+1 where pageName = '$pageName'";
    $result = $this->query($query);
    return true;
  }
  
  

  function create_page($name, $hits, $data, $lastModif, $comment, $user='system', $ip='0.0.0.0',$description='')
  {
    // Collect pages before modifying data
    $pages = $this->get_pages($data);
    $name = addslashes($name);
    $description = addslashes($description);
    $data = addslashes($data);
    $comment = addslashes($comment);
    if($this->page_exists($name)) return false;
    $query = "insert into tiki_pages(pageName,hits,data,lastModif,comment,version,user,ip,description,creator) values('$name',$hits,'$data',$lastModif,'$comment',1,'$user','$ip','$description','$user')";
    $result = $this->query($query);
    $this->clear_links($name);
    // Pages are collected before adding slashes
    foreach($pages as $a_page) {
      $this->replace_link($name,$a_page);
    }
    // Update the log
    if($name != 'SandBox') {
      $action = "Created";
      $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$name',$lastModif,'$user','$ip','$comment')";
      $result = $this->query($query);
    }
    return true;
  }

  function get_user_pages($user,$max)
  {
    $query = "select pageName from tiki_pages where user='$user' limit 0,$max";
    $result = $this->query($query);
    $ret=Array();
    while( $res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    return $ret;
  }

  function get_user_galleries($user,$max)
  {
    $query = "select name,galleryId from tiki_galleries where user='$user' limit 0,$max";
    $result = $this->query($query);
    $ret=Array();
    while( $res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    return $ret;
  }

  function get_page_info($pageName)
  {
    $pageName = addslashes($pageName);
    $query = "select * from tiki_pages where pageName='$pageName'";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $ret = $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $ret["pageName"] = $pageName;
    return $ret;
  }

  function how_many_at_start($str,$car)
  {
    $cant =0;
    $i=0;
    while(($i<strlen($str)) && (isset($str{$i})) && ($str{$i}==$car)){
      $i++;
      $cant++;
    }
    return $cant;
  }

  function parse_data_raw($data)
  {
    $data = $this->parse_data($data);
    $data=str_replace("tiki-index","tiki-index_raw",$data);
    return $data;
  }
  
  function add_pre_handler($name)
  {
    if(!in_array($name,$this->pre_handlers)) {
      $this->pre_handlers[]=$name;
    }
  }
  
  function add_pos_handler($name)
  {
    if(!in_array($name,$this->pos_handlers)) {
      $this->pos_handlers[]=$name;
    }
  }
  
  
  //PARSEDATA
  function parse_data($data)
  {
    global $page_regex;
    global $slidemode;
    global $feature_hotwords;
    global $cachepages;
    global $ownurl_father;
    global $feature_drawings;
    global $tiki_p_admin_drawings;
    global $tiki_p_edit_drawings;
    global $feature_hotwords_nw;
    global $feature_wiki_pictures;
    global $tiki_p_upload_picture;
    global $feature_wiki_tables;
    global $page; 
    global $rsslib;
    global $dbTiki;
    global $structlib;
    global $user;

	

	// Process pre_handlers here
	foreach($this->pre_handlers as $handler) {
	  $data = $handler($data);
	}
	

	
    $preparsed=Array();
    
    
    preg_match_all("/\~pp\~((.|\n)*?)\~\/pp\~/",$data,$preparse);
    foreach(array_unique($preparse[1]) as $pp) {
      $key=md5($this->genPass());
      $aux["key"]=$key;
      $aux["data"]=$pp;
      $preparsed[]=$aux;
      $data=str_replace("~pp~$pp~/pp~",$key,$data);
    } 
    
    //Extract noparse sections almost before anything
    $noparsed=Array();
    /*
    preg_match_all("/\~np\~((.|\n)*?)\~\/np\~/",$data,$noparse);
    foreach(array_unique($noparse[1]) as $np) {
      $key=md5($this->genPass());
      $aux["key"]=$key;
      $aux["data"]=$np;
      $noparsed[]=$aux;
      $data=str_replace("~np~$np~/np~",$key,$data);
    }
    */

    
    /* NEW WAY */
    $new_data = '';
    $nopa = '';
    $state = true;
    $skip = false;
    for($i=0;$i<strlen($data);$i++) {
      $tag5 = substr($data,$i,5);
      $tag4 = substr($tag5,0,4);
      $tag1 = substr($tag4,0,1);
      if($state && $tag4 == '~np~') {
        $i+=3;
        $state = false;
        $skip=true;
      }
      if(!$state && ($tag5 == '~/np~')) {
        $state = true;
        $i+=4;
        $skip=true;
        $key = md5($this->genPass());
        $new_data.=$key;
        $aux["key"]=$key;
        $aux["data"]=$nopa;
        $noparsed[]=$aux;
        $nopa='';
      }
      if(!$skip) {
        if($state) {
          $new_data .= $tag1;  
        } else {
          $nopa .= $tag1;
        }
      } else {
        $skip = false;
      }
    }

    $data = $new_data;


    //Extract [link] sections almost before anything
    $noparsedlinks=Array();
    preg_match_all("/\[([^\]]*)\]/",$data,$noparseurl);
    foreach(array_unique($noparseurl[1]) as $np) {
      $key=md5($this->genPass());
      $aux["key"]=$key;
      $aux["data"]=$np;
      $noparsedlinks[]=$aux;
      $data=str_replace("$np",$key,$data);
    }

	//Note \x characters are automatically escaped
	//$data = preg_replace("/\\./","$1",$data);

    if($feature_hotwords_nw == 'y') {
      $hotw_nw = "target='_blank'";
    } else {
      $hotw_nw = '';
    }




    // Now replace a TOC
    preg_match_all("/\{toc\}/",$data,$tocs);
    if(count($tocs[0])>0) {
      include_once("lib/structures/structlib.php");
      if($structlib->page_is_in_structure($page)) {
        $html='';
        if($slidemode) {
        $toc=$structlib->get_subtree_toc_slide($page,$page,$html);
        } else {
        $toc=$structlib->get_subtree_toc($page,$page,$html);
        } 
        $data=str_replace('{toc}',$html,$data);
      }
    }
    //$page='';

    // Now search for plugins
    //$smc = new Smarty_Compiler();
    preg_match_all("/\{([A-Z]+)\(([^\)]*)\)\}/",$data,$plugins);
    
    for($i=count($plugins[0])-1;$i>=0;$i--) {
      $plugin_start = $plugins[0][$i];
      $plugin_end = '{'.$plugins[1][$i].'}';
      $plugin_start_base = '{'.$plugins[1][$i].'(';
      $pos = strpos($data,$plugin_start);
      $pos_end = strpos($data,$plugin_end,$pos);
      if($pos_end>$pos) {
        $plugin_data_len=$pos_end-$pos-strlen($plugins[0][$i]);
        $plugin_data = substr($data,$pos+strlen($plugin_start),$plugin_data_len);
        $php_name = 'lib/wiki-plugins/wikiplugin_'.strtolower($plugins[1][$i]).'.php';
        $func_name = 'wikiplugin_'.strtolower($plugins[1][$i]);
        $params = split(',',trim($plugins[2][$i]));
        $arguments=Array();
        foreach($params as $param) {
          $parts=explode('=>',$param);
          if(isset($parts[0])&&isset($parts[1])) {
            $name=trim($parts[0]);
            $arguments[$name]=trim($parts[1]);
          }
        }
        if(file_exists($php_name)) {
          include_once($php_name);
          $ret = $func_name($plugin_data,$arguments);
          $ret = $this->parse_data($ret);
          $data = substr_replace($data,$ret,$pos,$pos_end - $pos + strlen($plugin_end));
          
        }
      }
    }


    //unset($smc);
    if($feature_wiki_tables != 'new') {
	    // New syntax for tables
	    if (preg_match_all("/\|\|(.*)\|\|/", $data, $tables)) {
	     $maxcols = 1;
	      $cols = array();
	      for($i = 0; $i < count($tables[0]); $i++) {
	        $rows = explode('||', $tables[0][$i]);
	        $col[$i] = array();
	        for ($j = 0; $j < count($rows); $j++) {
	          $cols[$i][$j] = explode('|', $rows[$j]);
	          if (count($cols[$i][$j]) > $maxcols)
	            $maxcols = count($cols[$i][$j]);
	        }
	      }
	      for ($i = 0; $i < count($tables[0]); $i++) {
	        $repl = '<table class="wikitable">';
	        for ($j = 0; $j < count($cols[$i]); $j++) {
	          $ncols = count($cols[$i][$j]);
	          if ($ncols == 1 && !$cols[$i][$j][0])
	            continue;
	          $repl .= '<tr>';
	          for ($k = 0; $k < $ncols; $k++) {
	            $repl .= '<td class="wikicell" ';
	            if ($k == $ncols - 1 && $ncols < $maxcols)
	              $repl .= ' colspan=' . ($maxcols-$k);
	            $repl .= '>' . $cols[$i][$j][$k] . '</td>';
	          }
	          $repl.='</tr>';
	        }
	        $repl.='</table>';
	        $data = str_replace($tables[0][$i],$repl,$data);
	      }
	    }
    } else {
	    // New syntax for tables
	    // REWRITE THIS CODE
	    if (preg_match_all("/\|\|(.*?)\|\|/s", $data, $tables)) {
 	      $maxcols = 1;
	      $cols = array();
	      for($i = 0; $i < count($tables[0]); $i++) {
	        $rows = split("\n|\<br\/\>", $tables[0][$i]);
	        $col[$i] = array();
	        for ($j = 0; $j < count($rows); $j++) {
	          $rows[$j]=str_replace('||','',$rows[$j]);
	          $cols[$i][$j] = explode('|', $rows[$j]);
	          if (count($cols[$i][$j]) > $maxcols)
	            $maxcols = count($cols[$i][$j]);
	        }
	      }
	      for ($i = 0; $i < count($tables[0]); $i++) {
	        $repl = '<table class="wikitable">';
	        for ($j = 0; $j < count($cols[$i]); $j++) {
	          $ncols = count($cols[$i][$j]);
	          if ($ncols == 1 && !$cols[$i][$j][0])
	            continue;
	          $repl .= '<tr>';
	          for ($k = 0; $k < $ncols; $k++) {
	            $repl .= '<td class="wikicell" ';
	            if ($k == $ncols - 1 && $ncols < $maxcols)
	              $repl .= ' colspan=' . ($maxcols-$k);
	            $repl .= '>' . $cols[$i][$j][$k] . '</td>';
	          }
	          $repl.='</tr>';
	        }
	        $repl.='</table>';
	        $data = str_replace($tables[0][$i],$repl,$data);
	      }
	    }
    
    }




    // Now search for images uploaded by users
    if($feature_wiki_pictures=='y') {
      preg_match_all("/\{picture file=([^\}]+)\}/",$data,$pics);
      for($i=0;$i<count($pics[0]);$i++) {
        // Check if the image exists
        $name=$pics[1][$i];
        if(file_exists($name)) {
          // Replace by the img tag to show the image
         $repl = "<img src='$name?nocache=1' alt='$name' />";
        } else {
          $repl=tra('picture not found');
        }
        // Replace by $repl
        $data = str_replace($pics[0][$i],$repl,$data);
      }
    }


	
	// Replace Hotwords
    //$data = stripslashes($data);
    if($feature_hotwords == 'y') {
      $words = $this->get_hotwords();
      foreach($words as $word=>$url) {
        //print("Replace $word by $url<br/>");

        $data  = preg_replace("/ $word /i"," <a class=\"wiki\" href=\"$url\" $hotw_nw>$word</a> ",$data);
        $data  = preg_replace("/([^A-Za-z0-9])$word /i","$1<a class=\"wiki\" href=\"$url\" $hotw_nw>$word</a> ",$data);
        $data  = preg_replace("/ $word([^A-Za-z0-9])/i"," <a class=\"wiki\" href=\"$url\" $hotw_nw>$word</a>$1",$data);

      }
    }

	
    //$data = strip_tags($data);
    // BiDi markers
    $bidiCount = 0;
    $bidiCount = preg_match_all("/(\{l2r\})/",$data,$pages);
    $bidiCount += preg_match_all("/(\{r2l\})/",$data,$pages);

    $data = preg_replace("/\{l2r\}/", "<div dir='ltr'>", $data);
    $data = preg_replace("/\{r2l\}/", "<div dir='rtl'>", $data);
    $data = preg_replace("/\{lm\}/", "&lrm;", $data);
    $data = preg_replace("/\{rm\}/", "&rlm;", $data);
    // smileys
    $data = $this->parse_smileys($data);



    // Replace links to slideshows
    if($feature_drawings == 'y') {
    // Replace drawings
    // Replace rss modules
    $pars=parse_url($_SERVER["REQUEST_URI"]);
    $pars_parts=split('/',$pars["path"]);
    $pars=Array();
    for($i=0;$i<count($pars_parts)-1;$i++) {
      $pars[]=$pars_parts[$i];
    }
    $pars=join('/',$pars);
    if(preg_match_all("/\{draw +name=([A-Za-z_\-0-9]+) *\}/",$data,$draws)) {
      //$this->invalidate_cache($page);
      for($i=0;$i<count($draws[0]);$i++) {
        $id = $draws[1][$i];
        $repl='';
        $name=$id.'.gif';
        if(file_exists("img/wiki/$name")) {
          if($tiki_p_edit_drawings == 'y' || $tiki_p_admin_drawings == 'y') {
            $repl="<a href='#' onClick=\"javascript:window.open('tiki-editdrawing.php?page=$page&amp;path=$pars&amp;drawing={$id}','','menubar=no,width=252,height=25');\"><img border='0' src='img/wiki/$name' alt='click to edit' /></a>";
          } else {
            $repl="<img border='0' src='img/wiki/$name' alt='a drawing' />";
          }
        } else {
          if($tiki_p_edit_drawings == 'y' || $tiki_p_admin_drawings == 'y') {
            $repl="<a class='wiki' href='#' onClick=\"javascript:window.open('tiki-editdrawing.php?page=$page&amp;path=$pars&amp;drawing={$id}','','menubar=no,width=252,height=25');\">click here to create draw $id</a>";
          } else {
            $repl=tra('drawing not found');
          }
        }
        $data = str_replace($draws[0][$i],$repl,$data);
      }
    }
    }

    // Replace cookies
    if(preg_match_all("/\{cookie\}/",$data,$rsss)) {
      for($i=0;$i<count($rsss[0]);$i++) {
        $cookie = $this->pick_cookie();
        $data = str_replace($rsss[0][$i],$cookie,$data);
      }
    }


    // Replace dynamic content occurrences
    if(preg_match_all("/\{content +id=([0-9]+)\}/",$data,$dcs)) {
      for($i=0;$i<count($dcs[0]);$i++) {
        $repl = $this->get_actual_content($dcs[1][$i]);
        $data = str_replace($dcs[0][$i],$repl,$data);
      }
    }
    // Replace Dynamic content with random selection
    if(preg_match_all("/\{rcontent +id=([0-9]+)\}/",$data,$dcs)) {
      for($i=0;$i<count($dcs[0]);$i++) {
        $repl = $this->get_random_content($dcs[1][$i]);
        $data = str_replace($dcs[0][$i],$repl,$data);
      }
    }



    // Replace boxes
    $data = preg_replace("/\^([^\^]+)\^/","<div class='simplebox' align='center'>$1</div>",$data);
    // Replace colors ~~color:text~~
    $data = preg_replace("/\~\~([^\:]+):([^\~]+)\~\~/","<span style='color:$1;'>$2</span>",$data);
    // Underlined text
    $data = preg_replace("/===([^\=]+)===/","<span style='text-decoration:underline;'>$1</span>",$data);
    // Center text
    $data = preg_replace("/::([^\:]+)::/","<div align='center'>$1</div>",$data);

    // Links to internal pages
    // If they are parenthesized then don't treat as links
    // Prevent ))PageName(( from being expanded    \"\'
    //[A-Z][a-z0-9_\-]+[A-Z][a-z0-9_\-]+[A-Za-z0-9\-_]*
    // The first part is now mandatory to prevent [Foo|MyPage] from being converted!
    preg_match_all("/([ \n\t\r\,\;]|^)([A-Z][a-z0-9_\-]+[A-Z][a-z0-9_\-]+[A-Za-z0-9\-_]*)($|[ \n\t\r\,\;\.])/",$data,$pages);
    foreach(array_unique($pages[2]) as $page_parse) {
      if($desc = $this->page_exists_desc($page_parse)) {
        $repl = "<a title='".$desc."' href='tiki-index.php?page=$page_parse' class='wiki'>$page_parse</a>";
      } else {
        $repl = "$page_parse<a href='tiki-editpage.php?page=$page_parse' class='wiki'>?</a>";
      }
      $data = preg_replace("/([ \n\t\r\,\;]|^)$page_parse($|[ \n\t\r\,\;\.])/","$1"."$repl"."$2",$data);
      //$data = str_replace($page_parse,$repl,$data);
    }


    $data = preg_replace("/([ \n\t\r\,\;]|^)\)\)([^\(]+)\(\(($|[ \n\t\r\,\;\.])/","$1"."$2"."$3",$data);
    // New syntax for wiki pages ((name|desc)) Where desc can be anything
    preg_match_all("/\(\(($page_regex)\|(.+?)\)\)/",$data,$pages);
    for($i=0;$i<count($pages[1]);$i++) {
      $pattern = $pages[0][$i];
      //$pattern=str_replace('|','\|',$pattern);
      //$pattern=str_replace('(','\(',$pattern);
      //$pattern=str_replace(')','\)',$pattern);
      $pattern=str_replace('/','\/',preg_quote($pattern));

      $pattern = "/".$pattern."/";
      // Replace links to external wikis

      $repl2=true;
      if(strstr($pages[1][$i],':')) {
        $wexs = explode(':',$pages[1][$i]);
        if(count($wexs)==2) {
          $wkname = $wexs[0];       

          if($this->db->getOne("select count(*) from tiki_extwiki where name='$wkname'")==1) {
			$wkurl = $this->db->getOne("select extwiki from tiki_extwiki where name='$wkname'");
			$wkurl = '<a href="'.str_replace('$page',$wexs[1],$wkurl).'" class="wiki">'.$wexs[1].'</a>';
			$data = preg_replace($pattern,"$wkurl",$data);
			$repl2=false;
          }
        }
      }

      if($repl2) {
	      if($desc = $this->page_exists_desc($pages[1][$i])) {
	      	$uri_ref = "tiki-index.php?page=".urlencode($pages[1][$i]);
	        $repl = "<a title='$desc' href='$uri_ref' class='wiki'>".$pages[5][$i]."</a>";
	      } else {
	      	$uri_ref = "tiki-editpage.php?page=".urlencode($pages[1][$i]);
	        $repl = $pages[5][$i]."<a href='$uri_ref' class='wiki'>?</a>";
	      }
	      $data = preg_replace($pattern,"$repl",$data);
      }
    }

    // New syntax for wiki pages ((name)) Where name can be anything
    preg_match_all("/\(\(($page_regex)\)\)/",$data,$pages);
    foreach(array_unique($pages[1]) as $page_parse) {
      $repl2=true;
      if(strstr($page_parse,':')) {
        $wexs = explode(':',$page_parse);
        if(count($wexs)==2) {
          $wkname = $wexs[0];        
          if($this->db->getOne("select count(*) from tiki_extwiki where name='$wkname'")==1) {
			$wkurl = $this->db->getOne("select extwiki from tiki_extwiki where name='$wkname'");
			$wkurl = '<a href="'.str_replace('$page',$wexs[1],$wkurl).'" class="wiki">'.$wexs[1].'</a>';
			$data = preg_replace("/\(\($page_parse\)\)/","$wkurl",$data);
			$repl2=false;
          }
        }
      }
      if($repl2) {
	      if($desc = $this->page_exists_desc($page_parse)) {
	        $repl = "<a title='$desc' href='tiki-index.php?page=$page_parse' class='wiki'>$page_parse</a>";
	      } else {
	        $repl = "$page_parse<a href='tiki-editpage.php?page=$page_parse' class='wiki'>?</a>";
	      }
	      $data = preg_replace("/\(\($page_parse\)\)/","$repl",$data);
      }
     
    }

    // reinsert hash-replaced links into page
    foreach($noparsedlinks as $np) {
      $data = str_replace($np["key"],$np["data"],$data);
    }

      // TODO: I think this is 1. just wrong and 2. not needed here? remove it?
      // Replace ))Words((
      $data = preg_replace("/\(\(([^\)]+)\)\)/","$1",$data);

    // Images
    preg_match_all("/(\{img [^\}]+})/",$data,$pages);
    foreach(array_unique($pages[1]) as $page_parse) {
      $parts = explode(" ",$page_parse);
      $imgdata = Array();
      $imgdata["src"]='';
      $imgdata["height"]='';
      $imgdata["width"]='';
      $imgdata["link"]='';
      $imgdata["align"]='';
      $imgdata["desc"]='';
      foreach($parts as $part) {
        $part = str_replace('}','',$part);
        $part = str_replace('{','',$part);
        $part = str_replace('\'','',$part);
        $part = str_replace('"','',$part);
        if(strstr($part,'=')) {
            $subs = explode("=",$part,2);
            $imgdata[$subs[0]]=$subs[1];
        }
      }
      //print("todo el tag es: ".$page_parse."<br/>");
      //print_r($imgdata);
      $repl = "<div class=\"innerimg\"><img alt='an image' src='".$imgdata["src"]."' border='0' ";
      if($imgdata["width"]) $repl.=" width='".$imgdata["width"]."'";
      if($imgdata["height"]) $repl.=" height='".$imgdata["height"]."'";
      $repl.= " /></div>";
      if($imgdata["link"]) {
        $repl ="<a href='".$imgdata["link"]."'>".$repl."</a>";
      }
      if($imgdata["desc"]) {
        $repl="<table cellpadding='0' cellspacing='0'><tr><td>".$repl."</td></tr><tr><td class='mini'>".$imgdata["desc"]."</td></tr></table>";
      }
      if($imgdata["align"]) {
        $repl ="<div align='".$imgdata["align"]."'>".$repl."</div>";
      }
      $data = str_replace($page_parse,$repl,$data);
    }

    $links = $this->get_links($data);
    
    $notcachedlinks = $this->get_links_nocache($data);
    
	$cachedlinks = array_diff($links, $notcachedlinks);
	
	$this->cache_links($cachedlinks); 
    
    // Note that there're links that are replaced



    foreach($links as $link) {
      $target='';
      if($this->get_preference('popupLinks','n')=='y') {
        $target='target="_blank"';
      }
	  if(strstr($link,$_SERVER["SERVER_NAME"])) {
		$target='';
	  }
	  if(!strstr($link,'//')) {
	    $target='';
	  }
      if( $this->is_cached($link) && $cachepages == 'y') {
        $cosa="<a class=\"wikicache\" target=\"_blank\" href=\"tiki-view_cache.php?url=$link\">(cache)</a>";
        //$link2 = str_replace("/","\/",$link);
        //$link2 = str_replace("?","\?",$link2);
        //$link2 = str_replace("&","\&",$link2);
	$link2=str_replace("/","\/",preg_quote($link));
        $pattern = "/\[$link2\|([^\]\|]+)\|([^\]]+)\]/";
        $data = preg_replace($pattern,"<a class='wiki' $target href='$link'>$1</a>",$data);
        $pattern = "/\[$link2\|([^\]\|]+)\]/";
        $data = preg_replace($pattern,"<a class='wiki' $target href='$link'>$1</a> $cosa",$data);
        $pattern = "/\[$link2\]/";
        $data = preg_replace($pattern,"<a class='wiki' $target href='$link'>$link</a> $cosa",$data);
      } else {
        //$link2 = str_replace("/","\/",$link);
        //$link2 = str_replace("?","\?",$link2);
        //$link2 = str_replace("&","\&",$link2);
	$link2=str_replace("/","\/",preg_quote($link));
        $pattern = "/\[$link2\|([^\]\|]+)([^\]])*\]/";
        $data = preg_replace($pattern,"<a class='wiki' $target href='$link'>$1</a>",$data);
        $pattern = "/\[$link2\]/";
        $data = preg_replace($pattern,"<a class='wiki' $target href='$link'>$link</a>",$data);
      }
    }
    // Title bars
    //$data = preg_replace("/-=([^=]+)=-/","<div class='titlebar'>$1</div>",$data);
    $data = preg_replace("/-=(.+?)=-/","<div class='titlebar'>$1</div>",$data);



    // tables
    /*
    preg_match_all("/(\%[^\%]+\%)/",$data,$pages);
    foreach(array_unique($pages[1]) as $page_parse) {
      $pagex=substr($page_parse,1,strlen($page_parse)-2);
      $repl='<table cellpadding="0" cellspacing="0" border="1">';
      // First split by lines
      $lines = explode("\\",$pagex);
      foreach ($lines as $line) {
        $repl.='<tr>';
        $columns = explode("&",$line);
        foreach($columns as $column) {
          $repl.='<td valign="top">'.$column.'</td>';
        }
        $repl.='</tr>';
      }
      $repl.='</table>';
      $data = str_replace($page_parse, $repl, $data);
    }
    */




    // Now tokenize the expression and process the tokens
    // Use tab and newline as tokenizing characters as well  ////
    $lines = explode("\n",$data);
    $data = ''; $listbeg='';
    $listlevel = 0;
    $oldlistlevel = 0;
    $listbeg='';
    foreach ($lines as $line) {

      // If the first character is ' ' and we are not in pre then we are in pre
      if(substr($line,0,1)==' ') {
        if($listbeg) {
          while($listlevel>0) {
            $data.=$listbeg;
            $listlevel--;
            $oldlistlevel=0;
          }
          $listbeg='';
        }
        // If the first character is space then
        // change spaces for &nbsp;
        $line = '<font face="courier" size="2">'.str_replace(' ','&nbsp;',substr($line,1)).'</font>';
        $line.='<br/>';
      } else {
        // Replace bold text
        $line = preg_replace("/__(.*?)__/","<b>$1</b>",$line);
        $line = preg_replace("/\'\'(.*?)\'\'/","<i>$1</i>",$line);
        // Replace definition lists
        $line = preg_replace("/^;([^:]+):([^\n]+)/","<dl><dt>$1</dt><dd>$2</dd></dl>",$line);
        if(0) {
        $line = preg_replace("/\[([^\|]+)\|([^\]]+)\]/","<a class='wiki' $target href='$1'>$2</a>",$line);
        // Segundo intento reemplazar los [link] comunes
        $line = preg_replace("/\[([^\]]+)\]/","<a class='wiki' $target href='$1'>$1</a>",$line);
        $line = preg_replace("/\-\=([^=]+)\=\-/","<div class='wikihead'>$1</div>",$line);
        }

        // This line is parseable then we have to see what we have
        if(substr($line,0,3)=='---') {
          if($listbeg) {
            while($listlevel>0) {
            $data.=$listbeg;
            $listlevel--;
            $oldlistlevel=0;
          }
          $listbeg='';
          }
          $line='<hr/>';
        } else {
          if(substr($line,0,1)=='*') {
            // Get the list level examining the number of asterisks

            // If another list had started then end it
            if($listbeg && $listbeg!='</ul>') {
              while($listlevel>0) {
                $data.=$listbeg;
                $listlevel--;
                $oldlistlevel=0;
              }
            }

            $listlevel=$this->how_many_at_start($line,'*');

            // If the list level is new add ul's
            while($listlevel>$oldlistlevel) {
              $data.='<ul>';
              $listbeg='</ul>';
              $oldlistlevel++;
            }

            // If the list level is lower
            while($listlevel<$oldlistlevel) {
              $data.='</ul>';
              $oldlistlevel--;
            }

            $line = '<li>'.substr($line,$listlevel).'</li>';

          } elseif(substr($line,0,1)=='#') {
                    // If another list had started then end it
            if($listbeg && $listbeg!='</ol>') {
              while($listlevel>0) {
                $data.=$listbeg;
                $listlevel--;
                $oldlistlevel=0;
              }
            }

            $listlevel=$this->how_many_at_start($line,'#');

            // If the list level is new add ul's
            while($listlevel>$oldlistlevel) {

              $data.='<ol>';
              $listbeg='</ol>';
              $oldlistlevel++;
            }

            // If the list level is lower
            while($listlevel<$oldlistlevel) {
              $data.='</ol>';
              $oldlistlevel--;
            }

            //

            $line = '<li>'.substr($line,$listlevel).'</li>';

          } elseif(substr($line,0,3)=='!!!') {
            $line = '<h3>'.substr($line,3).'</h3>';
          } elseif(substr($line,0,2)=='!!') {
            $line = '<h2>'.substr($line,2).'</h2>';
          } elseif(substr($line,0,1)=='!') {
            $line = '<h1>'.substr($line,1).'</h1>';
          } else {
            if($listbeg) {
              while($listlevel>0) {
              $data.=$listbeg;
              $listlevel--;
              $oldlistlevel=0;
              }
              $listbeg='';
            } else {
              $line.='<br/>';
            }
          }
        }
      }
      $data.=$line;
    }


    
    // Replace rss modules
    if(preg_match_all("/\{rss +id=([0-9]+) *(max=([0-9]+))? *\}/",$data,$rsss)) {
	  if(!isset($rsslib)) {
	  include('lib/rss/rsslib.php');
	  }

      for($i=0;$i<count($rsss[0]);$i++) {
        $id = $rsss[1][$i];
        $max = $rsss[3][$i];
        if(empty($max)) $max=99;
        $rssdata = $rsslib->get_rss_module_content($id);
        $items = $rsslib->parse_rss_data($rssdata);
        $repl='';
        for($j=0;$j<count($items) && $j<$max;$j++) {
         $repl.='<li><a target="_blank" href="'.$items[$j]["link"].'" class="wiki">'.$items[$j]["title"].'</a></li>';
        }
        $repl='<ul>'.$repl.'</ul>';
        $data = str_replace($rsss[0][$i],$repl,$data);
      }
    }



    // Close BiDi DIVs if any
    for ($i = 0; $i < $bidiCount; $i++) {
      $data.="</div>";
    }


    foreach($noparsed as $np) {
      $data = str_replace($np["key"],$np["data"],$data);
    }
    

    foreach($preparsed as $pp) {
      $data = str_replace($pp["key"],"<pre>".$pp["data"]."</pre>",$data);
    }

	// Process pos_handlers here
	foreach($this->pos_handlers as $handler) {
	  $data = $handler($data);
	}
    return $data;
  }


  



  function parse_smileys($data)
  {
    global $feature_smileys;
    if($feature_smileys == 'y') {
    $data = preg_replace("/\(:([^:]+):\)/","<img alt=\"$1\" src=\"img/smiles/icon_$1.gif\" />",$data);
    }
    return $data;
  }

  function parse_comment_data($data)
  {
     $data = preg_replace("/\[([^\|\]]+)\|([^\]]+)\]/","<a class=\"commentslink\" href=\"$1\">$2</a>",$data);
      // Segundo intento reemplazar los [link] comunes
     $data = preg_replace("/\[([^\]\|]+)\]/","<a class=\"commentslink\" href=\"$1\">$1</a>",$data);
     // Llamar aqui a parse smileys
     $data = $this->parse_smileys($data);
     $data = preg_replace("/---/","<hr/>",$data);
     // Reemplazar --- por <hr/>
     return $data;
  }

  function get_pages($data) {
    global $page_regex;
    preg_match_all("/([ \n\t\r\,\;]|^)?([A-Z][a-z0-9_\-]+[A-Z][a-z0-9_\-]+[A-Za-z0-9\-_]*)($|[ \n\t\r\,\;\.])/",$data,$pages);
    preg_match_all("/\(\(($page_regex)\)\)/",$data,$pages2);
    preg_match_all("/\(\(($page_regex)\|(.+?)\)\)/",$data,$pages3);
    $pages = array_unique(array_merge($pages[2],$pages2[1],$pages3[1]));
    return $pages;
  }

  function clear_links($page) {
    $page = addslashes($page);
    $query = "delete from tiki_links where fromPage='$page'";
    $result = $this->query($query);
  }

  function replace_link($pageFrom, $pageTo) {
    $pageFrom=addslashes($pageFrom);
    $pageTo=addslashes($pageTo);
    $query = "replace into tiki_links(fromPage,toPage) values('$pageFrom','$pageTo')";
    $result = $this->query($query);
  }

  function invalidate_cache($page) {
    $page = addslashes($page);
    $query = "update tiki_pages set cache_timestamp=0 where pageName='$page'";
    $this->query($query);
  }

  function update_page($pageName,$edit_data,$edit_comment, $edit_user, $edit_ip,$description='',$minor=false)
  {
    global $smarty;
    global $dbTiki;
    global $notificationlib;
    global $feature_user_watches;
    include_once('lib/notifications/notificationlib.php');
    $this->invalidate_cache($pageName);
    // Collect pages before modifying edit_data (see update of links below)
    $pages = $this->get_pages($edit_data);
    $edit_data = addslashes($edit_data);
    $description = addslashes($description);
    $edit_comment = addslashes($edit_comment);
    if(!$this->page_exists($pageName)) return false;
    $t = date("U");
    // Get this page information
    $info = $this->get_page_info($pageName);
    // Store the old version of this page in the history table
    $version = $info["version"];
    $lastModif = $info["lastModif"];
    $user = $info["user"];
    $ip = $info["ip"];
    $comment = $info["comment"];
    $data = addslashes($info["data"]);
    // WARNING: POTENTIAL BUG
    // The line below is not consistent with the rest of Tiki
    // (I commented it out so it can be further examined by CVS change control)
    //$pageName=addslashes($pageName);
    // But this should work (comment added by redflo):
    $pageName_sl=addslashes($pageName);
    $comment=addslashes($comment);
    $version += 1;
    if(!$minor) {
      $query = "insert into tiki_history(pageName, version, lastModif, user, ip, comment, data, description)
              values('$pageName_sl',$version,$lastModif,'$user','$ip','$comment','$data','$description')";
      if($pageName != 'SandBox') {
        $result = $this->query($query);
      }
    
    // Update the pages table with the new version of this page
    
    //$edit_data = addslashes($edit_data);
      $emails = $notificationlib->get_mail_events('wiki_page_changes','wikipage'.$pageName);
      foreach($emails as $email) {
        $smarty->assign('mail_site',$_SERVER["SERVER_NAME"]);
        $smarty->assign('mail_page',$pageName);
        $smarty->assign('mail_date',date("U"));
        $smarty->assign('mail_user',$edit_user);
        $smarty->assign('mail_comment',$edit_comment);
        $smarty->assign('mail_last_version',$version);
        $smarty->assign('mail_data',$edit_data);
        $foo = parse_url($_SERVER["REQUEST_URI"]);
	    $machine =httpPrefix().$foo["path"];
        $smarty->assign('mail_machine',$machine);
        $smarty->assign('mail_pagedata',$edit_data);
        $mail_data = $smarty->fetch('mail/wiki_change_notification.tpl');
        @mail($email, tra('Wiki page').' '.$pageName.' '.tra('changed'), $mail_data);
      }
      if($feature_user_watches == 'y') {
        $nots = $this->get_event_watches('wiki_page_changed',$pageName);
        
		foreach($nots as $not) {
			$smarty->assign('mail_site',$_SERVER["SERVER_NAME"]);
	        $smarty->assign('mail_page',$pageName);
	        $smarty->assign('mail_date',date("U"));
	        $smarty->assign('mail_user',$edit_user);
	        $smarty->assign('mail_comment',$edit_comment);
	        $smarty->assign('mail_last_version',$version);
	        $smarty->assign('mail_data',$edit_data);
	        $smarty->assign('mail_hash',$not['hash']);
	        $foo = parse_url($_SERVER["REQUEST_URI"]);
		    $machine =httpPrefix().$foo["path"];
	        $smarty->assign('mail_machine',$machine);
	        $parts = explode('/',$foo['path']);
	        if(count($parts)>1) unset($parts[count($parts)-1]);
	        $smarty->assign('mail_machine_raw',httpPrefix().implode('/',$parts));
	        $smarty->assign('mail_pagedata',$edit_data);
	        $mail_data = $smarty->fetch('mail/user_watch_wiki_page_changed.tpl');
	        @mail($not['email'], tra('Wiki page').' '.$pageName.' '.tra('changed'), $mail_data);          
        }
      }
    }  
    $query = "update tiki_pages set description='$description', data='$edit_data', comment='$edit_comment', lastModif=$t, version=$version, user='$edit_user', ip='$edit_ip' where pageName='$pageName_sl'";
    $result = $this->query($query);
    // Parse edit_data updating the list of links from this page
    $this->clear_links($pageName);
    // Pages collected above
    foreach($pages as $page) {
      $this->replace_link($pageName,$page);
    }
    // Update the log
    if($pageName != 'SandBox' && !$minor) {
      $action = "Updated";
      $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$pageName_sl',$t,'$edit_user','$edit_ip','$edit_comment')";
      $result = $this->query($query);
      $maxversions = $this->get_preference("maxVersions",0);
      if($maxversions) {
        // Select only versions older than keep_versions days
        $keep = $this->get_preference('keep_versions',0);
        $now = date("U");
        $oktodel = $now - ($keep * 24 * 3600);
        $query = "select pageName,version from tiki_history where pageName='$pageName_sl' and lastModif<=$oktodel order by lastModif desc limit $maxversions,-1";
        $result = $this->query($query);
        $toelim = $result->numRows();
        while($res= $result->fetchRow(DB_FETCHMODE_ASSOC)) {
          $page = $res["pageName"];
          $version = $res["version"];
          $query = "delete from tiki_history where pageName='$pageName_sl' and version='$version'";
          $this->query($query);
        }
      }
    }
  }

  function update_page_version($pageName,$version,$edit_data,$edit_comment, $edit_user, $edit_ip,$lastModif,$description='')
  {
    global $smarty;
    $pageName = addslashes($pageName);
    if($pageName=='SandBox') return;
    // Collect pages before modifying edit_data
    $pages = $this->get_pages($edit_data);
    $edit_data = addslashes($edit_data);
    $description = addslashes($description);
    $edit_comment = addslashes($edit_comment);
    if(!$this->page_exists($pageName)) return false;
    $t = date("U");
    $query = "delete from tiki_history where pageName='$pageName' and version=$version";
    $result = $this->query($query);
    $query = "insert into tiki_history(pageName, version, lastModif, user, ip, comment, data,description)
              values('$pageName',$version,$lastModif,'$edit_user','$edit_ip','$edit_comment','$edit_data','$description')";
    $result = $this->query($query);

    //print("version: $version<br/>");
    // Get this page information
    $info = $this->get_page_info($pageName);
    if($version>=$info["version"]) {
      $query = "update tiki_pages set data='$edit_data', comment='$edit_comment', lastModif=$t, version=$version, user='$edit_user', ip='$edit_ip', description='$description' where pageName='$pageName'";
      $result = $this->query($query);
      // Parse edit_data updating the list of links from this page
      $this->clear_links($pageName);
      // Pages are collected at the top of the function before adding slashes
      foreach($pages as $page) {
         $this->replace_link($pageName,$page);
      }
    }
  }

  // This function get the last changes from pages from the last $days days
  // if days is 0 this gets all the registers
  // function parameters modified by ramiro_v on 11/03/2002
  function get_last_changes($days, $offset=0, $limit=-1, $sort_mode = 'lastModif_desc', $findwhat='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
  // section added by ramiro_v on 11/03/2002 begins here
    if($findwhat == '') {
      $where=" where 1";
    } else {
      $where=" where pageName like '%" . $findwhat . "%' or user like '%" . $findwhat . "%' or comment like '%" . $findwhat . "%'";
    }
  // section added by ramiro_v on 11/03/2002 ends here

    if($days) {
      $toTime = mktime(23,59,59,date("m"),date("d"),date("Y"));
      $fromTime = $toTime - (24*60*60*$days);
      $where = $where . " and lastModif>=$fromTime and lastModif<=$toTime";
    }

    $query = "select action, lastModif, user, ip, pageName,comment from tiki_actionlog " . $where . " order by $sort_mode limit $offset,$limit";
    $query_cant = "select count(*) from tiki_actionlog " . $where;
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array(); $r=Array();
    while($res=$result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $r["action"] = $res["action"];
      $r["lastModif"] = $res["lastModif"];
      $r["user"] = $res["user"];
      $r["ip"] = $res["ip"];
      $r["pageName"] = $res["pageName"];
      $r["comment"] = $res["comment"];
      $ret[]=$r;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  # TODO move all of these date/time functions to a static class: TikiDate

  function get_timezone_list($use_default = false) {
    static $timezone_options;

    if (!$timezone_options) {
      $timezone_options = array();
      if ($use_default)
        $timezone_options['default'] = '-- Use Default Time Zone --';
      foreach ($GLOBALS['_DATE_TIMEZONE_DATA'] as $tz_key => $tz) {
        $offset = $tz['offset'];
        $absoffset = abs($offset /= 60000);
        $plusminus = $offset < 0 ? '-' : '+';
        $gmtoff = sprintf("GMT%1s%02d:%02d", $plusminus, $absoffset / 60, $absoffset - (intval($absoffset / 60) * 60));
        $tzlongshort = $tz['longname'] . ' (' . $tz['shortname'] . ')';
        $timezone_options[$tz_key] = sprintf('%-28.28s: %-36.36s %s', $tz_key, $tzlongshort, $gmtoff);
      }
    }

    return $timezone_options;
  }

  function get_server_timezone() {
    static $server_timezone;

    if (!$server_timezone) {
      $server_time = new Date();
      $server_timezone = $server_time->tz->getID();
    }

    return $server_timezone;
  }

  # TODO rename get_site_timezone()
  function get_display_timezone($user = false) {
    static $display_timezone = false;

    if (!$display_timezone) {
      $server_time = $this->get_server_timezone();
      if ($user) {
        $display_timezone = $this->get_user_preference($user, 'display_timezone');
        if (!$display_timezone || $display_timezone == 'default') {
          $display_timezone = $this->get_preference('display_timezone', $server_time);
        }
      } else {
        $display_timezone = $this->get_preference('display_timezone', $server_time);
      }
    }

    return $display_timezone;
  }

  function get_long_date_format() {
    static $long_date_format = false;

    if (!$long_date_format)
      $long_date_format = $this->get_preference('long_date_format', '%A %d ' . tra('of') . ' %B, %Y');
    return $long_date_format;
  }

  function get_short_date_format() {
    static $short_date_format = false;

    if (!$short_date_format)
      $short_date_format = $this->get_preference('short_date_format', '%a %d ' . tra('of') . ' %b, %Y');
    return $short_date_format;
  }

  function get_long_time_format() {
    static $long_time_format = false;

    if (!$long_time_format)
      $long_time_format = $this->get_preference('long_time_format', '%H:%M:%S %Z');
    return $long_time_format;
  }

  function get_short_time_format() {
    static $short_time_format = false;

    if (!$short_time_format)
      $short_time_format = $this->get_preference('short_time_format', '%H:%M %Z');
    return $short_time_format;
  }

  function get_long_datetime_format() {
    static $long_datetime_format = false;

    if (!$long_datetime_format)
      $long_datetime_format = $this->get_long_date_format() . ' [' . $this->get_long_time_format() . ']';
    return $long_datetime_format;
  }

  function get_short_datetime_format() {
    static $short_datetime_format = false;

    if (!$short_datetime_format)
      $short_datetime_format = $this->get_short_date_format() . ' [' . $this->get_short_time_format() . ']';
    return $short_datetime_format;
  }

  function server_time_to_site_time($timestamp, $user = false) {
  $date = new Date($timestamp);
  $date->setTZbyID($this->get_server_timezone());
  $date->convertTZbyID($this->get_display_timezone($user));
    return $date->getTime();
  }

  /**

  */
  function get_site_date($timestamp, $user = false) {
    static $localed = false;

Debug::d('get_site_date()');

    if (!$localed) {
      $this->set_locale($user);
      $localed = true;
    }

    $original_tz = date('T', $timestamp);

    $rv = strftime($format, $timestamp);
    $rv .= " =timestamp\n";
    $rv .= strftime('%Z', $timestamp);
    $rv .= " =strftime('%Z')\n";
    $rv .= date('T', $timestamp);
    $rv .= " =date('T')\n";

    $date =& new Date($timestamp);

    # Calling new Date() changes the timezone of the $timestamp var!
    # so we only change the timezone to UTC if the original TZ wasn't UTC
    # to begin with.
    # This seems really buggy, but I don't have time to delve into right now.

    $rv .= date('T', $timestamp);
    $rv .= " =date('T')\n";

    $rv .= $date->format($format);
    $rv .= " =new Date()\n";

    $rv .= date('T', $timestamp);
    $rv .= " =date('T')\n";

    if ($original_tz == 'UTC') {
      $date->setTZbyID('UTC');
      $rv .= $date->format($format);
      $rv .= " =setTZbyID('UTC')\n";
    }

    $tz_id = $this->get_display_timezone($user);
    if ($date->tz->getID() != $tz_id) {
      # let's convert to the displayed timezone
      $date->convertTZbyID($tz_id);
      $rv .= $date->format($format);
      $rv .= " =convertTZbyID($tz_id)\n";
    }

	Debug::d($rv);

    #return $rv;

    # if ($format == "%b %e, %Y")
    #   $format = $tikilib->get_short_date_format();
    return $date;
  }

  # TODO rename to server_time_to_site_time()

  function get_site_time($timestamp, $user = false) {
#print "<pre>get_site_time()</pre>";
    $date = $this->get_site_date($timestamp, $user);
    return $date->getTime();
  }

  function date_format($format, $timestamp, $user = false) {
#print "<pre>date_format()</pre>";
    $date = $this->get_site_date($timestamp, $user);
    return $date->format($format);
  }

  function get_long_date($timestamp, $user = false) {
    return $this->date_format($this->get_long_date_format(), $timestamp, $user);
  }

  function get_short_date($timestamp, $user = false) {
    return $this->date_format($this->get_short_date_format(), $timestamp, $user);
  }

  function get_long_time($timestamp, $user = false) {
    return $this->date_format($this->get_long_time_format(), $timestamp, $user);
  }

  function get_short_time($timestamp, $user = false) {
    return $this->date_format($this->get_short_time_format(), $timestamp, $user);
  }

  function get_long_datetime($timestamp, $user = false) {
    return $this->date_format($this->get_long_datetime_format(), $timestamp, $user);
  }

  function get_short_datetime($timestamp, $user = false) {
    return $this->date_format($this->get_short_datetime_format(), $timestamp, $user);
  }

  function get_site_timezone_shortname($user = false) {
    static $timezone_shortname;

    if (!$timezone_shortname) {
      $date = $this->get_site_date(date('U'), $user);
      $timezone_shortname = $date->format('%Z');
    }

    return $timezone_shortname;
  }

  function get_server_timezone_shortname($user = false) {
    static $timezone_shortname;

    if (!$timezone_shortname) {
      $timezone_shortname = date('%Z');
    }

    return $timezone_shortname;
  }

  /**
    get_site_time_difference - Return the number of seconds needed to add to a
    'system' time to return a 'site' time.
  */
  function get_site_time_difference($user = false) {
    static $difference = false;

    if ($difference === false) {
      $server_tzid  = $this->get_server_timezone();
      $site_tzid  = $this->get_display_timezone($user);
#print "<pre>";
#printf("server_tzid='%s', site_tzid='%s'", $server_tzid, $site_tzid);
      $server_tz  =& new Date_TimeZone($server_tzid);
      $site_tz    =& new Date_TimeZone($site_tzid);
#printf("server_tz=");
#print_r($server_tz);
#printf("site_tz=");
#print_r($site_tz);
      $now =& new Date();
      $server_offset = $server_tz->getOffset($now);
      $site_offset = $site_tz->getOffset($now);
#printf("server_offset='%s', site_offset='%s'", $server_offset, $site_offset);
      $difference = intval(($site_offset - $server_offset) / 1000);
#printf("difference=%s", $difference);
    }

    return $difference;
  }


  /**
    Timezone saavy replacement for mktime()
  */
  function make_time($hour, $minute, $second, $month, $day, $year, $timezone_id = false) {
    global $user; # ugh!

    if ($year <= 69)
      $year += 2000;
    if ($year <= 99)
      $year += 1900;

    $date = new Date();
    $date->setHour($hour);
    $date->setMinute($minute);
    $date->setSecond($second);
    $date->setMonth($month);
    $date->setDay($day);
    $date->setYear($year);
    #$rv = sprintf("make_time(): $date->format(%D %T %Z)=%s<br/>\n", $date->format('%D %T %Z'));
    #print "<pre> make_time() start";
    #print_r($date);
    if ($timezone_id)
      $date->setTZbyID($timezone_id);
    #print_r($date);
    #$rv .= sprintf("make_time(): $date->format(%D %T %Z)=%s<br/>\n", $date->format('%D %T %Z'));
    #print $rv;
    return $date->getTime();
  }

  /**
    Timezone saavy replacement for mktime()
  */
  function make_server_time($hour, $minute, $second, $month, $day, $year, $timezone_id = false) {
    global $user; # ugh!

    if ($year <= 69)
      $year += 2000;
    if ($year <= 99)
      $year += 1900;

    $date = new Date();
    $date->setHour($hour);
    $date->setMinute($minute);
    $date->setSecond($second);
    $date->setMonth($month);
    $date->setDay($day);
    $date->setYear($year);
    #print "<pre> make_server_time() start\n";
    #print_r($date);
    if ($timezone_id)
      $date->setTZbyID($timezone_id);
    #print_r($date);
    $date->convertTZbyID($this->get_server_timezone());
    #print_r($date);
    #print "make_server_time() end\n</pre>";

    return $date->getTime();
  }

  /**
  Per http://www.w3.org/TR/NOTE-datetime
  */
  function get_iso8601_datetime($timestamp, $user = false) {
    return $this->date_format('%Y-%m-%dT%H:%M:%S%O', $timestamp, $user);
  }

  function get_rfc2822_datetime($timestamp = false, $user = false) {
    if (!$timestamp)
      $timestamp = time();

    # rfc2822 requires dates to be en formatted
    $saved_locale = @setlocale(0);
    @setlocale('en_US');
    #was return date('D, j M Y H:i:s ', $time) . $this->timezone_offset($time, 'no colon');
    $rv = $this->date_format('%a, %e %b %Y %H:%M:%S', $timestamp, $user) .
    $this->get_rfc2822_timezone_offset($timestamp, $user);

    # switch back to the 'saved' locale
    if ($saved_locale)
      @setlocale($saved_locale);

    return $rv;
  }

  function get_rfc2822_timezone_offset($time = false, $no_colon = false, $user = false) {
    if ($time === false)
      $time = time();
    $secs = $this->date_format('%Z', $time, $user);
    if ($secs < 0) {
      $sign = '-';
      $secs = -$secs;
    }
    else {
      $sign = '+';
    }
    $colon = $no_colon ? '' : ':';
    $mins = intval(($secs + 30) / 60);

    return sprintf("%s%02d%s%02d", $sign, $mins / 60, $colon, $mins % 60);
  }

  function get_language($user = false) {
    static $language = false;

    if (!$language) {
      if ($user) {
        $language = $this->get_user_preference($user, 'language', 'en');
        if (!$language || $language == 'default')
          $language = $this->get_preference('language', 'en');
      } else
        $language = $this->get_preference('language', 'en');
    }

    return $language;
  }

  function get_locale($user = false) {
    # TODO move to admin preferences screen
    static $locales = array(
      'de' => 'de_DE',
      'dk' => 'da_DK',
      'en' => 'en_US',
      'fr' => 'fr_FR',
      'he' => 'he_IL', # hebrew
      'it' => 'it_IT', # italian
      'pl' => 'pl_PL', # polish
      'po' => 'po',
      'ru' => 'ru_RU',
      'sp' => 'es_ES',
      'sw' => 'sw_SW',	# swahili
      'tw' => 'tw_TW',
    );
    if (!$locale) {
      if (isset($locales[$this->get_language($user)]))
        $locale = $locales[$this->get_language($user)];
#print "<pre>get_locale(): locale=$locale\n</pre>";
    }
    
    return $locale;
  }

  function set_locale($user = false) {
    static $locale = false;

    if (!$locale) {
      # breaks the RFC 2822 code
      $locale = @setlocale(LC_TIME, $this->get_locale($user));
#print "<pre>set_locale(): locale=$locale\n</pre>";
    }

    return $locale;
  }

} //end of class

function compare_links($ar1,$ar2) {
  return $ar1["links"] - $ar2["links"];
}

function compare_backlinks($ar1,$ar2) {
  return $ar1["backlinks"] - $ar2["backlinks"];
}

function r_compare_links($ar1,$ar2) {
  return $ar2["links"] - $ar1["links"];
}

function r_compare_backlinks($ar1,$ar2) {
  return $ar2["backlinks"] - $ar1["backlinks"];
}

function compare_images($ar1,$ar2) {
  return $ar1["images"] - $ar2["images"];
}

function r_compare_images($ar1,$ar2) {
  return $ar2["images"] - $ar1["images"];
}
function compare_files($ar1,$ar2) {
  return $ar1["files"] - $ar2["files"];
}

function r_compare_files($ar1,$ar2) {
  return $ar2["files"] - $ar1["files"];
}

function compare_versions($ar1,$ar2) {
  return $ar1["versions"] - $ar2["versions"];
}

function r_compare_versions($ar1,$ar2) {
  return $ar2["versions"] - $ar1["versions"];
}

function compare_changed($ar1, $ar2) {
  return $ar1["lastChanged"] - $ar2["lastChanged"];
}

function r_compare_changed($ar1, $ar2) {
  return $ar2["lastChanged"] - $ar1["lastChanged"];
}


function chkgd2() {
  if (!isset($_SESSION['havegd2'])) {
#   TODO test this logic in PHP 4.3
#   if (version_compare(phpversion(), "4.3.0") >= 0) {
#     $_SESSION['havegd2'] = true;
#   } else {
      ob_start();
      phpinfo(INFO_MODULES);
      $_SESSION['havegd2'] = preg_match('/GD Version.*2.0/', ob_get_contents());
      ob_end_clean();
#    }
  }
  return $_SESSION['havegd2'];
}


function httpScheme() {
  return 'http' . ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 's' : '');
}

function httpPrefix() {
  if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
    $rv = 'https://' . $_SERVER['SERVER_NAME'];
    if ($_SERVER['SERVER_PORT'] != 443)
      $rv .= ':' . $_SERVER['SERVER_PORT'];
  } else {
    $rv = 'http://' . $_SERVER['SERVER_NAME'];
    if ($_SERVER['SERVER_PORT'] != 80)
      $rv .= ':' . $_SERVER['SERVER_PORT'];
  }
  return $rv;
}

class Debug {
	function d($m) {
		if (!isset($REQUEST['_d']))
			return;

		echo "\n<pre>\n",$m,"\n</pre>\n";
	}
}

if(!function_exists('file_get_contents')) {
	function file_get_contents($f) {
	   ob_start();
	   $retval = @readfile($f);
	   if (false !== $retval) { // no readfile error
	     $retval = ob_get_contents();
	   }
	   ob_end_clean();
	  return $retval;
	}
}

?>
