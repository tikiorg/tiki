<?php
class AdminLib extends TikiLib {

  function AdminLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to AdminLib constructor");  
    }
    $this->db = $db;  
  }
  
  function list_dsn($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (dsn like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_dsn $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_dsn $mid";
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
  
  function replace_dsn($dsnId, $dsn,$name)
  {
    $dsn=addslashes($dsn);
    $name=addslashes($name);
    // Check the name

    if($dsnId) {
      $query = "update tiki_dsn set dsn='$dsn',name='$name' where dsnId=$dsnId";
    } else {
      $query = "replace into tiki_dsn(dsn,name)
                values('$dsn','$name')";
    }
    $result = $this->query($query);
    // And now replace the perm if not created
    $perm_name = 'tiki_p_dsn_'.$name;
    $query = "replace into users_permissions(permName,permDesc,type,level) values
    ('$perm_name','Can use dsn $dsn','dsn','editor')";
	$this->query($query);
    return true;
  }
  
  function remove_dsn($dsnId)
  {
    $info = $this->get_dsn($dsnId);
    $perm_name = 'tiki_p_dsn_'.$info['name'];
    $query = "delete from users_permissions where permName='$perm_name'";
    $this->query($query);
    $query = "delete from tiki_dsn where dsnId=$dsnId";
    $this->query($query);
    return true;
  }
  
  function get_dsn($dsnId)
  {
    $query = "select * from tiki_dsn where dsnId=$dsnId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  
  function remove_unused_pictures()
  {
    $query = "select data from tiki_pages";
    $result = $this->query($query);
    $pictures=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      preg_match_all("/\{picture file=([^\}]+)\}/",$res["data"],$pics);
      foreach(array_unique($pics[1]) as $pic) {
        $pictures[]=$pic;
      }
    }
    $h = opendir("img/wiki_up");
    while (($file = readdir($h)) !== false) {
      if(is_file("img/wiki_up/$file")&&($file!='license.txt')) {
        $filename="img/wiki_up/$file";
        if(!in_array($filename,$pictures)) {
          @unlink($filename);
        }
      }
    }
    closedir($h);
  }
  
  function remove_orphan_images()
  {
    $merge  = Array();
    // Find images in tiki_pages
    $query = "select data from tiki_pages";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      preg_match_all("/src=\"([^\"]+)\"/",$res["data"],$reqs1);
      preg_match_all("/src=\'([^\']+)\'/",$res["data"],$reqs2);
      preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/",$res["data"],$reqs3);
      $merge = array_merge($merge, $reqs1[1],$reqs2[1],$reqs3[1]);
      $merge = array_unique($merge);
    }

    // Find images in Tiki articles
    $query = "select body from tiki_articles";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      preg_match_all("/src=\"([^\"]+)\"/",$res["body"],$reqs1);
      preg_match_all("/src=\'([^\']+)\'/",$res["body"],$reqs2);
      preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/",$res["body"],$reqs3);
      $merge = array_merge($merge, $reqs1[1],$reqs2[1],$reqs3[1]);
      $merge = array_unique($merge);
    }

    // Find images in tiki_submissions
    $query = "select body from tiki_submissions";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      preg_match_all("/src=\"([^\"]+)\"/",$res["body"],$reqs1);
      preg_match_all("/src=\'([^\']+)\'/",$res["body"],$reqs2);
      preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/",$res["body"],$reqs3);
      $merge = array_merge($merge, $reqs1[1],$reqs2[1],$reqs3[1]);
      $merge = array_unique($merge);
    }

    // Find images in tiki_blog_posts
    $query = "select data from tiki_blog_posts";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      preg_match_all("/src=\"([^\"]+)\"/",$res["data"],$reqs1);
      preg_match_all("/src=\'([^\']+)\'/",$res["data"],$reqs2);
      preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/",$res["data"],$reqs3);
      $merge = array_merge($merge, $reqs1[1],$reqs2[1],$reqs3[1]);
      $merge = array_unique($merge);
    }

    $positives = Array();
    foreach($merge as $img) {
      if(strstr($img,'show_image')) {
        preg_match("/id=([0-9]+)/",$img,$rq);
        $positives[] = $rq[1];
      }
    }

    $query = "select imageId from tiki_images where galleryId=0";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $id = $res["imageId"];
      if(!in_array($id,$positives)) {
        $this->remove_image($id);
      }
    }

  }
  
  
  
}

$adminlib= new AdminLib($dbTiki);
?>