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


  function list_extwiki($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (extwiki like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_extwiki $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_extwiki $mid";
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
  
  function replace_extwiki($extwikiId, $extwiki,$name)
  {
    $extwiki=addslashes($extwiki);
    $name=addslashes($name);
    // Check the name

    if($extwikiId) {
      $query = "update tiki_extwiki set extwiki='$extwiki',name='$name' where extwikiId=$extwikiId";
    } else {
      $query = "replace into tiki_extwiki(extwiki,name)
                values('$extwiki','$name')";
    }
    $result = $this->query($query);
    // And now replace the perm if not created
    $perm_name = 'tiki_p_extwiki_'.$name;
    $query = "replace into users_permissions(permName,permDesc,type,level) values
    ('$perm_name','Can use extwiki $extwiki','extwiki','editor')";
	$this->query($query);
    return true;
  }
  
  function remove_extwiki($extwikiId)
  {
    $info = $this->get_extwiki($extwikiId);
    $perm_name = 'tiki_p_extwiki_'.$info['name'];
    $query = "delete from users_permissions where permName='$perm_name'";
    $this->query($query);
    $query = "delete from tiki_extwiki where extwikiId=$extwikiId";
    $this->query($query);
    return true;
  }
  
  function get_extwiki($extwikiId)
  {
    $query = "select * from tiki_extwiki where extwikiId=$extwikiId";
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
  
  function tag_exists($tag)
  {
    $query = "select distinct tagName from tiki_tags where tagName = '$tag'";
    $result = $this->query($query);
    return $result->numRows($result);
  }
  
  function remove_tag($tagname)
  {
    $query = "delete from tiki_tags where tagName='$tagname'";
    $result = $this->query($query);
    $action = "removed tag: $tagname";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','HomePage',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','')";
    $result = $this->query($query);
    return true;
  }

  function get_tags()
  {
    $query = "select distinct tagName from tiki_tags";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["tagName"];
    }
    return $ret;
  }

  // This function can be used to store the set of actual pages in the "tags"
  // table preserving the state of the wiki under a tag name.
  function create_tag($tagname,$comment='')
  {
    $tagname = addslashes($tagname);
    $comment = addslashes($comment);
    $query = "select * from tiki_pages";
    $result=$this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $query = "replace into tiki_tags(tagName,pageName,hits,data,lastModif,comment,version,user,ip,flag,description)
                values('$tagname','".$res["pageName"]."',".$res["hits"].",'".addslashes($res["data"])."',".$res["lastModif"].",'".$res["comment"]."',".$res["version"].",'".$res["user"]."','".$res["ip"]."','".$res["flag"]."','".$res["description"]."')";
      $result2=$this->query($query);
    }
    $action = "created tag: $tagname";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','HomePage',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','$comment')";
    $result = $this->query($query);
    return true;
  }

  // This funcion recovers the state of the wiki using a tagName from the
  // tags table
  function restore_tag($tagname)
  {
    $query = "update tiki_pages set cache_timestamp=0";
    $this->query($query);
    $query = "select * from tiki_tags where tagName='$tagname'";
    $result=$this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $query = "replace into tiki_pages(pageName,hits,data,lastModif,comment,version,user,ip,flag,description)
                values('".$res["pageName"]."',".$res["hits"].",'".addslashes($res["data"])."',".$res["lastModif"].",'".$res["comment"]."',".$res["version"].",'".$res["user"]."','".$res["ip"]."','".$res["flag"]."','".$res["description"]."')";
      $result2=$this->query($query);
    }
    $action = "recovered tag: $tagname";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','HomePage',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','')";
    $result = $this->query($query);
    return true;
  }  
  
  // Dumps the database to dump/new.tar
  function dump()
  {
    unlink("dump/new.tar");
    $tar = new tar();
    $tar->addFile("styles/main.css");
    // Foreach page
    $query = "select * from tiki_pages";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $pageName = $res["pageName"].'.html';
      $dat = $this->parse_data($res["data"]);
      // Now change index.php?page=foo to foo.html
      // and index.php to HomePage.html
      $dat = preg_replace("/tiki-index.php\?page=([^\'\" ]+)/","$1.html",$dat);
      $dat = preg_replace("/tiki-editpage.php\?page=([^\'\" ]+)/","",$dat);
      //preg_match_all("/tiki-index.php\?page=([^ ]+)/",$dat,$cosas);
      //print_r($cosas);
      $data = "<html><head><title>".$res["pageName"]."</title><link rel='StyleSheet' href='styles/main.css' type='text/css'></head><body><a class='wiki' href='HomePage.html'>home</a><br/><h1>".$res["pageName"]."</h1><div class='wikitext'>".$dat.'</div></body></html>';
      $tar->addData($pageName,$data,$res["lastModif"]);
    }
    $tar->toTar("dump/new.tar",FALSE);
    unset($tar);
    $action = "dump created";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','HomePage',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','')";
    $result=$this->query($query);
  }
  
  
}

$adminlib= new AdminLib($dbTiki);
?>