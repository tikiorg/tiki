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