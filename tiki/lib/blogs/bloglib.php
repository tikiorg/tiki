<?php
class BlogLib extends TikiLib {

  function BlogLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }
  
  
    function add_blog_hit($blogId)
  {
    $query = "update tiki_blogs set hits = hits+1 where blogId=$blogId";
    $result = $this->query($query);
    return true;
  }

  function replace_blog($title,$description,$user,$public,$maxPosts,$blogId)
  {
    $title = addslashes($title);
    $description = addslashes($description);
    $now = date("U");
    if($blogId) {
      $query = "update tiki_blogs set title='$title',description='$description',user='$user',public='$public',lastModif=$now,maxPosts=$maxPosts where blogId=$blogId";
      $result = $this->query($query);
    } else {
      $query = "insert into tiki_blogs(created,lastModif,title,description,user,public,posts,maxPosts,hits)
                       values($now,$now,'$title','$description','$user','$public',0,$maxPosts,0)";
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

  function blog_post($blogId,$data,$user)
  {
    // update tiki_blogs and call activity functions
    $data = strip_tags($data, '<a><b><i><h1><h2><h3><h4><h5><h6><ul><li><ol><br><p><table><tr><td><img><pre>');
    $data=addslashes($data);
    $now = date("U");
    $query = "insert into tiki_blog_posts(blogId,data,created,user) values($blogId,'$data',$now,'$user')";
    $result = $this->query($query);
    $query = "select max(postId) from tiki_blog_posts where created=$now and user='$user'";
    $id = $this->getOne($query);
    $query = "update tiki_blogs set lastModif=$now,posts=posts+1 where blogId=$blogId";
    $result = $this->query($query);
    $this->add_blog_activity($blogId);
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
    return true;
  }

  function get_post($postId)
  {
    $query = "select * from tiki_blog_posts where postId=$postId";
    $result = $this->query($query);

    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    } else {
      return false;
    }
    return $res;
  }

  function update_post($postId,$data,$user)
  {
    $data = addslashes($data);
    $query = "update tiki_blog_posts set data='$data',user='$user' where postId=$postId";
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