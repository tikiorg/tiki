<?php
class BlogLib extends TikiLib {

  function BlogLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to BlogsLib constructor");  
    }
    $this->db = $db;  
  }
  

  function send_trackbacks($id,$trackbacks)
  {
  	// Split to get each URI
	$tracks = explode(',',$trackbacks);  	
	$ret = Array();
  	// Foreach URI
    $post_info=$this->get_post($id);	
    $blog_info=$this->get_blog($post_info['blogId']);
    //Build uri for post
    $parts=parse_url($_SERVER['REQUEST_URI']);
    $uri = httpPrefix().str_replace('tiki-blog_post','tiki-view_blog_post',$parts['path']).'?postId='.$id.'&blogId='.$post_info['blogId'];
    include ("lib/snoopy/Snoopy.class.inc");
    $snoopy = new Snoopy;

  	foreach($tracks as $track) {
  		@$fp=fopen($track,'r');
  		if($fp) {	
  		    $data = '';
  			while(!feof($fp)) {
			  $data.=fread($fp,32767);  			  
  			}
  			fclose($fp);
  			preg_match("/trackback:ping=(\"|\'|\s*)(.+)(\"|\'\s)/",$data,$reqs);
  			if(!isset($reqs[2])) return $ret;
  			@$fp=fopen($reqs[2],'r');
  			if($fp) {
 			  fclose($fp);  		

			  $submit_url = $reqs[2];
		  	  $submit_vars["url"] = $uri;
			  $submit_vars["blog_name"] = $blog_info['title'];
			  $submit_vars["title"] = $post_info['title']?$post_info['title']:date("d/m/Y [h:i]",$post_info['created']);
			  $submit_vars["title"].= ' '.tra('by').' '.$post_info['user'];
			  $submit_vars["excerpt"] = substr($post_info['data'],0,200);
			  $snoopy->submit($submit_url,$submit_vars);
			  $back = $snoopy->results;
			  if(!strstr('<error>1</error>',$back)) {
			 	$ret[]=$track;    
			  }
  			}
  		}						
  	}						
    return $ret;
  }
  
  function add_trackback_from($postId,$url,$title='',$excerpt='',$blog_name='')
  {
    if(!$this->getOne("select count(*) from tiki_blog_posts where postId=$postId")) return false;
    $tbs = $this->get_trackbacks_from($postId);
    $aux = Array( 'title'=>$title,'excerpt'=>$excerpt,'blog_name'=>$blog_name);
    $tbs[$url]=$aux;
    $st = addslashes(serialize($tbs));
    $query = "update tiki_blog_posts set trackbacks_from='$st' where postId=$postId";
    $this->query($query);
    return true;
  }

  function get_trackbacks_from($postId)
  {
    $st = $this->db->getOne("select trackbacks_from from tiki_blog_posts where postId=$postId");
    return unserialize($st); 
  }

  function get_trackbacks_to($postId)
  {
    $st = $this->db->getOne("select trackbacks_to from tiki_blog_posts where postId=$postId");
    return unserialize($st); 
  }

  function clear_trackbacks_from($postId)
  {
    $empty = addslashes(serialize(Array()));
    $query = "update tiki_blog_posts set trackbacks_from = '$empty' where postId=$postId";
    $this->query($query);
  }

  function clear_trackbacks_to($postId)
  {
    $empty = addslashes(serialize(Array()));
    $query = "update tiki_blog_posts set trackbacks_to = '$empty' where postId=$postId";
    $this->query($query);
  }
  
  function add_blog_hit($blogId)
  {
  	global $count_admin_pvs;
  	global $user;
    if($count_admin_pvs == 'y' || $user!='admin') {
      $query = "update tiki_blogs set hits = hits+1 where blogId=$blogId";
      $result = $this->query($query);
    }
    return true;
  }
  
  function insert_post_image($postId,$filename,$filesize,$filetype,$data)
  {
    $data = addslashes($data);
    $query = "insert into tiki_blog_posts_images(postId,filename,filesize,filetype,data)
    values($postId,'$filename',$filesize,'$filetype','$data')";
    $this->query($query);
  }
  
  function get_post_image($imgId)
  {
    $query = "select * from tiki_blog_posts_images where imgId=$imgId";
  	$result = $this->query($query);  
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function get_post_images($postId)
  {
    $query = "select postId,filename,filesize,imgId from tiki_blog_posts_images where postId=$postId";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $imgId=$res['imgId'];
      $res['link']="<img src='tiki-view_blog_post_image.php?imgId=$imgId' border='0' alt='image' />";
      $parts = parse_url($_SERVER['REQUEST_URI']);
	  $path=str_replace('tiki-blog_post.php','tiki-view_blog_post_image.php',$parts['path']);
      $res['absolute']=httpPrefix().$path."?imgId=$imgId";
      $ret[] = $res;
    }
    return $ret;
  }
  
  function remove_post_image($imgId)
  {
    $query = "delete from tiki_blog_posts_images where imgId=$imgId";
    $this->query($query);
  }


  function replace_blog($title,$description,$user,$public,$maxPosts,$blogId,$heading,$use_title,$use_find,$allow_comments)
  {
    $title = addslashes($title);
    $description = addslashes($description);
    $heading=addslashes($heading);
    $now = date("U");
    if($blogId) {
      $query = "update tiki_blogs set title='$title',description='$description',user='$user',public='$public',lastModif=$now,maxPosts=$maxPosts,heading='$heading',use_title='$use_title',use_find='$use_find',allow_comments='$allow_comments' where blogId=$blogId";
      $result = $this->query($query);
    } else {
      $query = "insert into tiki_blogs(created,lastModif,title,description,user,public,posts,maxPosts,hits,heading,use_title,use_find,allow_comments)
                       values($now,$now,'$title','$description','$user','$public',0,$maxPosts,0,'$heading','$use_title','$use_find','$allow_comments')";
      $result = $this->query($query);
      $query2 = "select max(blogId) from tiki_blogs where lastModif=$now";
      $blogId=$this->getOne($query2);
    }

    return $blogId;
  }

  
  function list_blog_posts($blogId, $offset = 0,$maxRecords = -1,$sort_mode = 'created_desc', $find='', $date='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where blogId=$blogId and (data like '%".$find."%') ";
    } else {
      $mid=" where blogId=$blogId ";
    }
    if($date) {
      $mid.=" and  created<=$date ";
    }
    $query = "select * from tiki_blog_posts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_blog_posts $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $hash=md5('post'.$res["postId"]);
      $cant_com = $this->getOne("select count(*) from tiki_comments where object='$hash'");
      $res["comments"]=$cant_com;
      $res['trackbacks_from']=unserialize($res['trackbacks_from']);
      if(!is_array($res['trackbacks_from'])) $res['trackbacks_from']=Array();
      $res['trackbacks_from_count']=count(array_keys($res['trackbacks_from']));
      $res['trackbacks_to']=unserialize($res['trackbacks_to']);
      $res['trackbacks_to_count']=count($res['trackbacks_to']);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function list_all_blog_posts($offset = 0,$maxRecords = -1,$sort_mode = 'created_desc', $find='', $date='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (data like '%".$find."%') ";
    } else {
      $mid="";
    }
    if($date) {
      if($mid) {
      $mid.=" and  created<=$date ";
      } else {
      $mid.=" where created<=$date ";
      }
    }
    $query = "select * from tiki_blog_posts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_blog_posts $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $query2 = "select title from tiki_blogs where blogId=".$res["blogId"];
      $title = $this->getOne($query2);
      $res["blogtitle"]=$title;
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function blog_post($blogId,$data,$user,$title='',$trackbacks='')
  {
    // update tiki_blogs and call activity functions
	global $smarty;
	global $feature_user_watches;
    $tracks=addslashes(serialize(explode(',',$trackbacks)));
    $title=addslashes($title);
    $data = strip_tags($data, '<a><b><i><h1><h2><h3><h4><h5><h6><ul><li><ol><br><p><table><tr><td><img><pre>');
    $data=addslashes($data);
    $now = date("U");
    $query = "insert into tiki_blog_posts(blogId,data,created,user,title) values($blogId,'$data',$now,'$user','$title')";
    $result = $this->query($query);
    $query = "select max(postId) from tiki_blog_posts where created=$now and user='$user'";
    $id = $this->getOne($query);
    // Send trackbacks recovering only succesful trackbacks
    $trackbacks = addslashes(serialize($this->send_trackbacks($id,$trackbacks)));
    // Update post with trackbacks succesfully sent
    $query = "update tiki_blog_posts set trackbacks_from='', trackbacks_to = '$trackbacks' where postId=$id";
    $this->query($query);
    $query = "update tiki_blogs set lastModif=$now,posts=posts+1 where blogId=$blogId";
    $result = $this->query($query);
    $this->add_blog_activity($blogId);
    if($feature_user_watches == 'y') {
        $nots = $this->get_event_watches('blog_post',$blogId);
		foreach($nots as $not) {
			$smarty->assign('mail_site',$_SERVER["SERVER_NAME"]);
	        $smarty->assign('mail_title',$title);
	        $smarty->assign('mail_blogid',$blogId);
	        $smarty->assign('mail_postid',$id);
	        $smarty->assign('mail_date',date("U"));
	        $smarty->assign('mail_user',$user);
	        $smarty->assign('mail_data',$data);
	        $smarty->assign('mail_hash',$not['hash']);
	        $foo = parse_url($_SERVER["REQUEST_URI"]);
		    $machine =httpPrefix().$foo["path"];
	        $smarty->assign('mail_machine',$machine);
	        $parts = explode('/',$foo['path']);
	        if(count($parts)>1) unset($parts[count($parts)-1]);
	        $smarty->assign('mail_machine_raw',httpPrefix().implode('/',$parts));
	        $mail_data = $smarty->fetch('mail/user_watch_blog_post.tpl');
	        @mail($not['email'], tra('Blog post').' '.$title, $mail_data);          
        }
	}    

    return $id;
  }


  function remove_blog($blogId)
  {
    $query = "delete from tiki_blogs where blogId=$blogId";
    $result = $this->query($query);
    $query = "delete from tiki_blog_posts where blogId=$blogId";
    $result = $this->query($query);
    $this->remove_object('blog',$blogId);
    return true;
  }

  function remove_post($postId)
  {
    $query = "select blogId from tiki_blog_posts where postId=$postId";
    $blogId = $this->getOne($query);
    if($blogId) {
      $query = "delete from tiki_blog_posts where postId=$postId";
      $result = $this->query($query);
      $query = "update tiki_blogs set posts=posts-1 where blogId=$blogId";
      $result = $this->query($query);
    }
    $query = "delete from tiki_blog_posts_images where postId=$postId";
    $this->query($query);
    return true;
  }

  function get_post($postId)
  {
    $query = "select * from tiki_blog_posts where postId=$postId";
    $result = $this->query($query);

    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
	  if(!$res['trackbacks_from']) $res['trackbacks_from'] = serialize(Array());
	  if(!$res['trackbacks_to']) $res['trackbacks_to'] = serialize(Array());
      $res['trackbacks_from_count']=count(array_keys(unserialize($res['trackbacks_from'])));
      $res['trackbacks_from']=unserialize($res['trackbacks_from']);
      $res['trackbacks_to']=unserialize($res['trackbacks_to']);  
      $res['trackbacks_to_count']=count($res['trackbacks_to']);
    } else {
      return false;
    }
    return $res;
  }

  function update_post($postId,$data,$user,$title='',$trackbacks='')
  {
    $data = addslashes($data);
	$trackbacks=addslashes(serialize($this->send_trackbacks($postId,$trackbacks)));
    $title= addslashes($title);
    $query = "update tiki_blog_posts set trackbacks_to='$trackbacks',data='$data',user='$user',title='$title' where postId=$postId";
    $result = $this->query($query);
  }


  function list_user_posts($user,$offset = 0,$maxRecords = -1,$sort_mode = 'created_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where user=$user and (data like '%".$find."%') ";
    } else {
      $mid=' where user=$user ';
    }
    $query = "select * from tiki_blog_posts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_blog_posts $mid";
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

  function add_blog_activity($blogId)
  {

    //Caclulate activity, update tiki_blogs and purge activity table
    $today = mktime(0,0,0,date("m"),date("d"),date("Y"));
    $day0 = $today - (24*60*60);
    $day1 = $today - (2*24*60*60);
    $day2 = $today - (3*24*60*60);
    // Purge old activity
    $query="delete from tiki_blog_activity where day<$day2";
    $result = $this->query($query);
    // Register new activity
    $query = "select * from tiki_blog_activity where blogId=$blogId and day=$today";
    $result = $this->query($query);
    if($result->numRows()) {
      $query = "update tiki_blog_activity set posts=posts+1 where blogId=$blogId and day=$today";
    } else {
      $query = "insert into tiki_blog_activity(blogId,day,posts) values($blogId,$today,1)";
    }
    $result = $this->query($query);
    // Calculate activity
    $query = "select posts from tiki_blog_activity where blogId=$blogId and day=$today";
    $vtoday = $this->getOne($query);
    $query = "select posts from tiki_blog_activity where blogId=$blogId and day=$day0";
    $day0 = $this->getOne($query);
    $query = "select posts from tiki_blog_activity where blogId=$blogId and day=$day1";
    $day1 = $this->getOne($query);
    $query = "select posts from tiki_blog_activity where blogId=$blogId and day=$day2";
    $day2 = $this->getOne($query);
    $activity = (2 * $vtoday) + ($day0) + (0.5 * $day1) + (0.25 * $day2);
    // Update tiki_blogs with activity information
    $query = "update tiki_blogs set activity=$activity where blogId=$blogId";
    $result = $this->query($query);
  }

  
  
}

$bloglib= new BlogLib($dbTiki);
?>