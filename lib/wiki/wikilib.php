<?php
 
class WikiLib extends TikiLib {
    
  function WikiLib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }

  // Methods to cache and handle the cached version of wiki pages
  // to prevent parsing large pages.
  function get_cache_info($page)
  {
    $query = "select cache,cache_timestamp from tiki_pages where pageName='$page'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function update_cache($page,$data)
  {
    $now = date('U');
    $data = addslashes($data);
    $query = "update tiki_pages set cache='$data', cache_timestamp=$now where pageName='$page'";
    $result = $this->query($query);
    return true;
  }
  
  function get_attachment_owner($attId)
  {
    return $this->getOne("select user from tiki_wiki_attachments where attId=$attId");
  }

  function remove_wiki_attachment($attId)
  {
    global $w_use_dir;
    $path = $this->getOne("select path from tiki_wiki_attachments where attId=$attId");
    if($path) {
      @unlink($w_use_dir.$path);
    }
    $query = "delete from tiki_wiki_attachments where attId='$attId'";
    $result = $this->query($query);
  }

  function wiki_attach_file($page,$name,$type,$size, $data, $comment, $user,$fhash)
  {
    $data = addslashes($data);
    $page = addslashes($page);
    $name = addslashes($name);
    $comment = addslashes(strip_tags($comment));
    $now = date("U");
    $query = "insert into tiki_wiki_attachments(page,filename,filesize,filetype,data,created,downloads,user,comment,path)
    values('$page','$name',$size,'$type','$data',$now,0,'$user','$comment','$fhash')";
    $result = $this->query($query);
  }


  function list_wiki_attachments($page,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where page='$page' and (filename like '%".$find."%')";
    } else {
      $mid=" where page='$page' ";
    }
    $query = "select user,attId,page,filename,filesize,filetype,downloads,created,comment from tiki_wiki_attachments $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_wiki_attachments $mid";
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
  
  // Functions for wiki page footnotes
  function get_footnote($user,$page)
  {
    $page = addslashes($page);
    $count = $this->getOne("select count(*) from tiki_page_footnotes where user='$user' and pageName='$page'");
    if(!$count) {
      return '';
    } else {
      return $this->getOne("select data from tiki_page_footnotes where user='$user' and pageName='$page'");
    }
  }
  
  function replace_footnote($user,$page,$data)
  {
    $page=addslashes($page);
    $data=addslashes($data);
    $query = "replace into tiki_page_footnotes(user,pageName,data) values('$user','$page','$data')";
    $this->query($query);
  }

  function remove_footnote($user,$page)
  {
    $page=addslashes($page);
    $query = "delete from tiki_page_footnotes where user='$user' and pageName='$page'";
    $this->query($query);
  }  
  
  function wiki_link_structure()
  {
    $query = "select pageName from tiki_pages order by pageName asc";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      print($res["pageName"]." ");
      $page = $res["pageName"];
      $query2 = "select toPage from tiki_links where fromPage='$page'";
      $result2 = $this->query($query2);
      $pages=Array();
      while($res2 = $result2->fetchRow(DB_FETCHMODE_ASSOC)) {
        if( ($res2["toPage"]<>$res["pageName"]) && (!in_array($res2["toPage"],$pages)) ) {
          $pages[]=$res2["toPage"];
          print($res2["toPage"]." ");
        }
      }
      print("\n");
    }
  }

  // Get a listing of orphan pages
  
}

$wikilib= new WikiLib($dbTiki);

?>