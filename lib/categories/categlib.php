<?php
class CategLib extends TikiLib {

  function CategLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to CategLib constructor");  
    }
    $this->db = $db;  
  }
  
  function list_all_categories($offset,$maxRecords,$sort_mode='name_asc',$find,$type,$objid)
  {
    $cats = $this->get_object_categories($type,$objid);
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (name like '%".$find."%' or description like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_categories $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_categories $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    if(in_array($res["categId"],$cats)) {
      $res["incat"]='y';
    } else {
      $res["incat"]='n';
    }
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function get_category_path_admin($categId)
  {
    $path = '';
    $info = $this->get_category($categId);
    $path = '<a class="categpath" href=tiki-admin_categories.php?parentId="'.$info["categId"].'">'.$info["name"].'</a>';
    while($info["parentId"]!=0) {
      $info = $this->get_category($info["parentId"]);
      $path = $path = '<a class="categpath" href=tiki-admin_categories.php?parentId="'.$info["categId"].'">'.$info["name"].'</a>'.'>'.$path;
    }
    return $path;
  }
  
  function get_category_path_browse($categId)
  {
    $path = '';
    $info = $this->get_category($categId);
    $path = '<a class="categpath" href=tiki-browse_categories.php?parentId="'.$info["categId"].'">'.$info["name"].'</a>';
    while($info["parentId"]!=0) {
      $info = $this->get_category($info["parentId"]);
      $path = $path = '<a class="categpath" href=tiki-browse_categories.php?parentId="'.$info["categId"].'">'.$info["name"].'</a>'.'>'.$path;
    }
    return $path;
  }
  
  function get_category($categId)
  {
    $query = "select * from tiki_categories where categId=$categId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  
  function remove_category($categId)
  {
    // Delete the category
    $query = "delete from tiki_categories where categId=$categId";
    $result = $this->query($query);
    // Remove objects for this category
    $query = "select catObjectId from tiki_category_objects where categId=$categId";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $object = $res["catObjectId"];
      $query2 = "delete from tiki_categorized_objects where catObjectId=".addslashes($object);
      $result2 = $this->query($query2);

    }
    $query = "delete from tiki_category_objects where categId=$categId";
    $result = $this->query($query);
    $query = "select categId from tiki_categories where parentId=$categId";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Recursively remove the subcategory
      $this->remove_category($res["categId"]);
    }
    return true;
  }
  
    function update_category($categId,$name,$description)
  {
    $name = addslashes($name);
    $description = addslashes($description);
    $query = "update tiki_categories set name='$name', description='$description' where categId=$categId";
    $result = $this->query($query);
  }

  function add_category($parentId,$name,$description)
  {
    $name = addslashes($name);
    $description = addslashes($description);
    $query = "insert into tiki_categories(name,description,parentId,hits) values('$name','$description',$parentId,0)";
    $result = $this->query($query);
  }

  function is_categorized($type,$objId)
  {
    $objId=addslashes($objId);
    $query = "select catObjectId from tiki_categorized_objects where type='$type' and objId='$objId'";
    $result = $this->query($query);
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      return $res["catObjectId"];
    } else {
      return 0;
    }
  }

  function add_categorized_object($type,$objId,$description,$name,$href)
  {
    $description = addslashes(strip_tags($description));
    $name = addslashes(strip_tags($name));
    $objId=addslashes($objId);
    $now = date("U");
    $href=addslashes($href);
    $query = "insert into tiki_categorized_objects(type,objId,description,name,href,created,hits)
    values('$type','$objId','$description','$name','$href',$now,0)";
    $result = $this->query($query);
    $query = "select catObjectId from tiki_categorized_objects where created=$now and type='$type' and objId='$objId'";
    $id = $this->getOne($query);
    return $id;
  }

  function categorize($catObjectId,$categId)
  {
    $query = "replace into tiki_category_objects(catObjectId,categId) values($catObjectId,$categId)";
    $result = $this->query($query);
  }

  function get_category_descendants($categId)
  {
    $query = "select categId from tiki_categories where parentId=$categId";
    $result = $this->query($query);
    $ret = Array($categId);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["categId"];
      $aux = $this->get_category_descendants($res["categId"]);
      $ret = array_merge($ret,$aux);
    }
    $ret=array_unique($ret);
    return $ret;
  }

  function list_category_objects_deep($categId,$offset,$maxRecords,$sort_mode='pageName_asc',$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    $des = $this->get_category_descendants($categId);
    $cond="where (";
    $first=1;
    foreach($des as $ades) {
      if($first) {
        $cond.=" (tbl1.categId=$ades) ";
        $first=0;
      } else {
        $cond.=" or (tbl1.categId=$ades) ";
      }
    }
    $cond.=" )";
    if($find) {
      $mid=" and (name like '%".$find."%' or description like '%".$find."% ')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_category_objects tbl1,tiki_categorized_objects tbl2 $cond and tbl1.catObjectId=tbl2.catObjectId $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select distinct(tbl1.catObjectId) from tiki_category_objects tbl1,tiki_categorized_objects tbl2 $cond and tbl1.catObjectId=tbl2.catObjectId $mid";
    $result = $this->query($query);
    $result2 = $this->query($query_cant);
    $cant = $result2->numRows();
    $cant2 = $this->getOne("select count(*) from tiki_category_objects tbl1,tiki_categorized_objects tbl2 $cond and tbl1.catObjectId=tbl2.catObjectId $mid");
    $ret = Array();
    $objs = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      if(!in_array($res["catObjectId"],$objs)) {
        $ret[] = $res;
        $objs[] = $res["catObjectId"];
      }
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    $retval["cant2"] = $cant2;
    return $retval;
  }

  function list_category_objects($categId,$offset,$maxRecords,$sort_mode='pageName_asc',$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" and (name like '%".$find."%' or description like '%".$find."% ')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_category_objects tbl1,tiki_categorized_objects tbl2 where tbl1.catObjectId=tbl2.catObjectId and categId=$categId $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select distinct(tbl1.catObjectId) from tiki_category_objects tbl1,tiki_categorized_objects tbl2 where tbl1.catObjectId=tbl2.catObjectId and categId=$categId $mid";
    $result = $this->query($query);
    $result2 = $this->query($query_cant);
    $cant = $result2->numRows();
    $cant2 = $this->getOne("select count(*) from tiki_category_objects tbl1,tiki_categorized_objects tbl2 where tbl1.catObjectId=tbl2.catObjectId and categId=$categId $mid");
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    $retval["cant2"] = $cant2;
    return $retval;
  }

  function get_object_categories($type,$objId)
  {
    $objId=addslashes($objId);
    $query = "select categId from tiki_category_objects tco, tiki_categorized_objects tto
    where tco.catObjectId=tto.catObjectId and type='$type' and objId='$objId'";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["categId"];
    }
    return $ret;
  }

  function get_category_objects($categId)
  {
    // Get all the objects in a category
    $query = "select * from tiki_category_objects tbl1,tiki_categorized_objects tbl2 where tbl1.catObjectId=tbl2.catObjectId and categId=$categId";
    $result = $this->query($query);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    return $ret;
  }

  function remove_object_from_category($catObjectId, $categId)
  {
    $query = "delete from tiki_category_objects where catObjectId=$catObjectId and categId=$categId";
    $result = $this->query($query);
    // If the object is not listed in any category then remove the object
    $query = "select count(*) from tiki_category_objects where catObjectId=$catObjectId";
    $cant = $this->getOne($query);
    if(!$cant) {
      $query = "delete from tiki_categorized_objects where catObjectId=$catObjectId";
      $result = $this->query($query);
    }
  }

  // FUNCTIONS TO CATEGORIZE SPECIFIC OBJECTS ////
  function categorize_page($pageName, $categId)
  {
    // Check if we already have this object in the tiki_categorized_objects page
    $pageName_sl=addslashes($pageName);
    $catObjectId=$this->is_categorized('wiki page',$pageName_sl);
    if(!$catObjectId) {
      // The page is not cateorized
      $info = $this->get_page_info($pageName);
      $href = 'tiki-index.php?page='.urlencode($pageName);
      $catObjectId = $this->add_categorized_object('wiki page',$pageName,substr($info["description"],0,200),$pageName,$href);
    }
    $this->categorize($catObjectId,$categId);
  }

  function categorize_quiz($quizId, $categId)
  {
    // Check if we already have this object in the tiki_categorized_objects page
    $catObjectId=$this->is_categorized('quiz',$quizId);
    if(!$catObjectId) {
      // The page is not cateorized
      $info = $this->get_quiz($quizId);
      $href = 'tiki-take_quiz.php?quizId='.$quizId;
      $catObjectId = $this->add_categorized_object('quiz',$quizId,substr($info["description"],0,200),$info["name"],$href);    }
      $this->categorize($catObjectId,$categId);
  }

  function categorize_article($articleId, $categId)
  {
    // Check if we already have this object in the tiki_categorized_objects page
    $catObjectId=$this->is_categorized('article',$articleId);
    if(!$catObjectId) {
      // The page is not cateorized
      $info = $this->get_article($articleId);
      $href = 'tiki-read_article.php?articleId='.$articleId;
      $catObjectId = $this->add_categorized_object('article',$articleId,$info["heading"],$info["title"],$href);
    }
    $this->categorize($catObjectId,$categId);
  }

  function categorize_faq($faqId, $categId)
  {
    // Check if we already have this object in the tiki_categorized_objects page
    $catObjectId=$this->is_categorized('faq',$faqId);
    if(!$catObjectId) {
      // The page is not cateorized
      $info = $this->get_faq($faqId);
      $href = 'tiki-view_faq.php?faqId='.$faqId;
      $catObjectId = $this->add_categorized_object('faq',$faqId,$info["description"],$info["title"],$href);
    }
    $this->categorize($catObjectId,$categId);
  }

  function categorize_blog($blogId, $categId)
  {
    // Check if we already have this object in the tiki_categorized_objects page
    $catObjectId=$this->is_categorized('blog',$blogId);
    if(!$catObjectId) {
      // The page is not cateorized
      $info = $this->get_blog($blogId);
      $href = 'tiki-view_blog.php?blogId='.$blogId;
      $catObjectId = $this->add_categorized_object('blog',$blogId,$info["description"],$info["title"],$href);
    }
    $this->categorize($catObjectId,$categId);
  }

  function categorize_gallery($galleryId, $categId)
  {
    // Check if we already have this object in the tiki_categorized_objects page
    $catObjectId=$this->is_categorized('image gallery',$galleryId);
    if(!$catObjectId) {
      // The page is not cateorized
      $info = $this->get_gallery($galleryId);
      $href = 'tiki-browse_gallery.php?galleryId='.$galleryId;
      $catObjectId = $this->add_categorized_object('image gallery',$galleryId,$info["description"],$info["name"],$href);
    }
    $this->categorize($catObjectId,$categId);
  }

  function categorize_file_gallery($galleryId, $categId)
  {
    // Check if we already have this object in the tiki_categorized_objects page
    $catObjectId=$this->is_categorized('file gallery',$galleryId);
    if(!$catObjectId) {
      // The page is not cateorized
      $info = $this->get_file_gallery($galleryId);
      $href = 'tiki-list_file_gallery.php?galleryId='.$galleryId;
      $catObjectId = $this->add_categorized_object('file gallery',$galleryId,$info["description"],$info["name"],$href);
    }
    $this->categorize($catObjectId,$categId);
  }

  function categorize_forum($forumId, $categId)
  {
    // Check if we already have this object in the tiki_categorized_objects page
    $catObjectId=$this->is_categorized('forum',$forumId);
    if(!$catObjectId) {
      // The page is not cateorized
      $info = $this->get_forum($forumId);
      $href = 'tiki-view_forum.php?forumId='.$forumId;
      $catObjectId = $this->add_categorized_object('forum',$forumId,$info["description"],$info["name"],$href);
    }
    $this->categorize($catObjectId,$categId);
  }

  function categorize_poll($pollId, $categId)
  {
    // Check if we already have this object in the tiki_categorized_objects page
    $catObjectId=$this->is_categorized('poll',$pollId);
    if(!$catObjectId) {
      // The page is not cateorized
      $info = $this->get_poll($pollId);
      $href = 'tiki-poll_form.php?pollId='.$pollId;
      $catObjectId = $this->add_categorized_object('poll',$pollId,$info["title"],$info["title"],$href);
    }
    $this->categorize($catObjectId,$categId);
  }
  // FUNCTIONS TO CATEGORIZE SPECIFIC OBJECTS END ////
  
  function get_child_categories($categId)
  {
    $ret=Array();
    $query = "select * from tiki_categories where parentId=$categId";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $id = $res["categId"];
      $query = "select count(*) from tiki_categories where parentId=$id";
      $res["children"]=$this->getOne($query);
      $query = "select count(*) from tiki_category_objects where categId=$id";
      $res["objects"]=$this->getOne($query);
      $ret[]=$res;
    }
    return $ret;
  }
  
  function get_all_categories()
  {
    $query =" select name,categId from tiki_categories order by name";
    $result = $this->query($query);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    return $ret;
  }


  
  



  
  
  
}

$categlib= new CategLib($dbTiki);

?>
