<?php
 
class DirLib extends TikiLib {
    
  function DirLib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to DirLib constructor");  
    }
    $this->db = $db;  
  }

  // Path functions
  function dir_get_category_path_admin($categId)
  {
    
    $info = $this->dir_get_category($categId);
    $path = '<a class="link" href="tiki-directory_admin_categories.php?parent='.$info["categId"].'">'.$info["name"].'</a>';
    while($info["parent"]!=0) {
      $info = $this->dir_get_category($info["parent"]);
      $path = '<a class="link" href="tiki-directory_admin_categories.php?parent='.$info["categId"].'">'.$info["name"].'</a>'.'>'.$path;
    }
    return $path;
  }
  
  function dir_get_path_text($categId)
  {
    $info = $this->dir_get_category($categId);
    $path = $info["name"];
    while($info["parent"]!=0) {
      $info = $this->dir_get_category($info["parent"]);
      $path = $info["name"].'>>'.$path;
    }
    return $path;
  }

  function dir_get_category_path_browse($categId)
  {
    $path = '';
    $info = $this->dir_get_category($categId);
    $path = '<a class="dirlink" href=tiki-directory_browse.php?parent='.$info["categId"].'>'.$info["name"].'</a>';
    while($info["parent"]!=0) {
      $info = $this->dir_get_category($info["parent"]);
      $path = $path = '<a class="dirlink" href=tiki-directory_browse.php?parent='.$info["categId"].'>'.$info["name"].'</a>'.'>'.$path;
    }
    return $path;
  }
  
    
  // Stats functions
  // get stats (valid sites, invalid sites, categories, searches)
  

  // Functions to manage categories
  
  function get_random_subcats($parent,$cant)
  {
    //Return an array of 'cant' random subcategories
    $count = $this->db->getOne("select count(*) from tiki_directory_categories where parent=$parent");
    if($count<$cant) $cant=$count;
    $ret = Array();
    while(count($ret)<$cant) {
      $x = rand(0,$count);
      if(!in_array($x,$ret)) {
        $ret[]=$x;
      }
    }
    $ret=Array();
    foreach($ret as $r) {
      $query = "select * from tiki_directory_categories limit $r,1";
      $result = $this->query($query);
      $ret[] = $result->fetchRow(DB_FETCHMODE_ASSOC);
    }
    return $ret;
  }
  
  // List
  function dir_list_categories($parent,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" and (name like $findesc or description like $findesc)";  
    } else {
      $mid=""; 
    }
    $query = "select * from tiki_directory_categories where parent=$parent $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_directory_categories where parent=$parent $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["sites"]=$this->db->getOne("select count(*) from tiki_category_sites where categId=".$res["categId"]);
      //$res["path"]=$this->dir_get_path_text($res["categId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  // List all categories
  function dir_list_all_categories($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid="where (name like $findesc or description like $findesc)";  
    } else {
      $mid=""; 
    }
    $query = "select * from tiki_directory_categories $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_directory_categories $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["sites"]=$this->db->getOne("select count(*) from tiki_category_sites where categId=".$res["categId"]);
      //$res["path"]=$this->dir_get_path_text($res["categId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function dir_list_sites($parent,$offset,$maxRecords,$sort_mode,$find,$isValid)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" and (name like $findesc or description like $findesc)";  
    } else {
      $mid=""; 
    }
    if($isValid) {
      $mid.= " and isValid='$isValid' ";
    }
    $query = "select * from tiki_directory_sites tds, tiki_category_sites tcs where tds.siteId=tcs.siteId and tcs.categId=$parent $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_directory_sites tds, tiki_category_sites tcs where tds.siteId=tcs.siteId and tcs.categId=$parent $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["cats"]=$this->dir_get_site_categories($res["siteId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function dir_list_invalid_sites($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" and (name like $findesc or description like $findesc)";  
    } else {
      $mid=""; 
    }
    
    $query = "select * from tiki_directory_sites where isValid='n' $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_directory_sites where isValid='n' $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["cats"]=$this->dir_get_site_categories($res["siteId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function dir_get_site_categories($siteId)
  {
    $query = "select tdc.name,tcs.categId from tiki_category_sites tcs,tiki_directory_categories tdc where tcs.siteId=$siteId and tcs.categId=tdc.categId";
    $result = $this->query($query);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["path"]=$this->dir_get_path_text($res["categId"]);
      $ret[]=$res;
    }
    return $ret;
  }
  
  function dir_list_all_sites($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" and (name like $findesc or description like $findesc)";  
    } else {
      $mid=""; 
    }
    
    $query = "select * from tiki_directory_sites $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_directory_sites $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["cats"]=$this->dir_get_site_categories($res["siteId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function dir_list_all_valid_sites($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where isValid='y' and (name like $findesc or description like $findesc)";  
    } else {
      $mid=" where isValid='y' "; 
    }
    
    $query = "select * from tiki_directory_sites $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_directory_sites $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["cats"]=$this->dir_get_site_categories($res["siteId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  
  
  function dir_get_all_categories($offset,$maxRecords,$sort_mode,$find,$siteId=0)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where (title like $findesc or data like $findesc)";  
    } else {
      $mid=""; 
    }
    $query = "select * from tiki_directory_categories $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_directory_categories $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["path"]=$this->dir_get_path_text($res["categId"]);
      $res["belongs"]='n';
      if($siteId) {
        $belongs = $this->db->getOne("select count(*) from tiki_category_sites where siteId=$siteId and categId=".$res["categId"]);
        if($belongs) {
          $res["belongs"]='y';
        } 
      }
      $ret[] = $res;
    }
    usort($ret,'compare_paths');
    return $ret;
  }
  
  function dir_get_all_categories_np($offset,$maxRecords,$sort_mode,$find,$parent)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" and (title like $findesc or data like $findesc)";  
    } else {
      $mid=""; 
    }
    $query = "select * from tiki_directory_categories where categId<>$parent $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_directory_categories where categId<>$parent $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["path"]=$this->dir_get_path_text($res["categId"]);
      $ret[] = $res;
    }
    usort($ret,'compare_paths');
    return $ret;
  }
  
  function dir_get_all_categories_accept_sites($offset,$maxRecords,$sort_mode,$find,$siteId=0)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" and (title like $findesc or data like $findesc)";  
    } else {
      $mid=""; 
    }
    $query = "select * from tiki_directory_categories where allowSites='y' $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_directory_categories $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["sites"]=$this->db->getOne("select count(*) from tiki_category_sites where categId=".$res["categId"]);
      $res["path"]=$this->dir_get_path_text($res["categId"]);
      $res["belongs"]='n';
      if($siteId) {
        $belongs = $this->db->getOne("select count(*) from tiki_category_sites where siteId=$siteId and categId=".$res["categId"]);
        if($belongs) {
          $res["belongs"]='y';
        } 
      }
      $ret[] = $res;
    }
    usort($ret,'compare_paths');
    
    return $ret;
  }
  
  function dir_validate_site($siteId)
  {
    $query = "update tiki_directory_sites set isValid='y' where siteId=$siteId";
    $this->query($query);
  }
  
  
  function dir_replace_site($siteId,$name,$description,$url,$country,$isValid)
  {
    global $cachepages;
    $name = addslashes($name);
    $description = addslashes($description);
    $now=date("U");
    if($siteId) {
      $query ="update tiki_directory_sites set
      name='$name',
      description='$description',
      url='$url',
      country='$country',
      isValid='$isValid',
      lastModif=$now
      where siteId=$siteId";
      $this->query($query);
      return $siteId;        
    } else {
      $query = "insert into tiki_directory_sites(name,description,url,country,isValid,hits,created,lastModif)
      values('$name','$description','$url','$country','$isValid',0,$now,$now)";
      $this->query($query);        
      $siteId=$this->db->getOne("select max(siteId) from tiki_directory_sites where created=$now and name='$name'");
      if($cachepages == 'y') {
        $this->cache_url($url);
      }
      return $siteId;
    }
    // Now try to cache the site

  }
  
  
  
    
  // Replace
  function dir_replace_category($parent, $categId, $name, $description, $childrenType, $viewableChildren, $allowSites, $showCount, $editorGroup)
  {
    $name = addslashes($name);
    $description = addslashes($description);
    if($categId) {
      $query = "update tiki_directory_categories set
        name = '$name',
        parent = $parent,
        description = '$description',
        childrenType = '$childrenType',
        viewableChildren = $viewableChildren,
        allowSites = '$allowSites',
        showCount = '$showCount',
        editorGroup = '$editorGroup'
        where categId=$categId";
      $this->query($query);        
    } else {
      $query = "insert into tiki_directory_categories(parent,hits,name,description,childrenType,viewableChildren,allowSites,showCount,editorGroup,sites)
      values($parent,0,'$name','$description','$childrenType',$viewableChildren,'$allowSites','$showCount','$editorGroup',0)";
      $this->query($query);
      $categId=$this->getOne("select max(categId) from tiki_directory_categories where name='$name'");
    }
    return $categId;
  }
    
  // Get
  function dir_get_site($siteId)
  {
    $query = "select * from tiki_directory_sites where siteId=$siteId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function dir_get_category($categId)
  {
    $query = "select * from tiki_directory_categories where categId=$categId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function dir_remove_site($siteId) 
  {
    $query = "delete from tiki_directory_sites where siteId=$siteId";
    $this->query($query);
    $query = "delete from tiki_category_sites where siteId=$siteId";
    $this->query($query);
  }
  
  function dir_add_site_to_category($siteId,$categId)
  {
   $query = "replace into tiki_category_sites(siteId,categId) values($siteId,$categId)";
   $this->query($query);
  }
  
  function remove_site_from_categories($siteId)
  {
    $query = "delete from tiki_category_sites where siteId=$siteId";
    $this->query($query);
  }
  
  function remove_site_from_category($siteId,$categId)
  {
    $query = "delete from tiki_category_sites where siteId=$siteId and categId=$categId";
    $this->query($query);
  }
  
  // Remove
  function dir_remove_category($categId)
  {
    $query = "select * from tiki_directory_categories where parent=$categId";
    $result = $this->query($query);
    // Get each children category
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $categId=$res["categId"];
      $this->dir_remove_category($res["categId"]);
    }
    // Remove sites from this category
    $query = "select * from tiki_category_sites where categId=$categId";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $siteId=$res["siteId"];
      $query2 = "delete from tiki_category_sites where categId=$categId and siteId=$siteId";
      $result2 = $this->query($query2);
      $cant= $this->db->getOne("select count(*) from tiki_category_sites where siteId=$siteId");
      if(!$cant) {
        $this->dir_remove_site($siteId);
      }
    }
    // Remove relationshipts involving this category
    $query = "delete from tiki_related_categories where categId=$categId or relatedTo=$categId";
    $result = $this->query($query);
    // Remove the category
    $query = "delete from tiki_directory_categories where categId=$categId";
    $result = $this->query($query);
    $query = "delete from tiki_category_sites where categId=$categId";
    $result = $this->query($query);
  }
  
  function dir_remove_related($parent,$related)
  {
    $query = "delete from tiki_related_categories where categId=$parent and relatedTo=$related";
    $this->query($query);
  }
  
  // Functions to manage sites
  
  // List
  // param: valid (y,n,all)
  // param: category id (can be 0=all)
  
  // Replace
  
  // Get
  
  // Remove
  //function dir_remove_site($siteId) 
  //{
//    $query = "delete from tiki_directory_sites where siteId=$siteId";
    //$result = $this->query($query);
    // No need to cascade
  //}
  
  // Functions to manage relationship between categories
  function dir_list_related_categories($parent,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
       
    // Armar query con info de las categories
    $query = "select * from tiki_related_categories where categId=$parent limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_related_categories where categId=$parent";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["path"]=$this->dir_get_path_text($res["relatedTo"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function dir_add_categ_rel($parent,$categ)
  {
    $query = "replace into tiki_related_categories(categId,relatedTo) values('$parent','$categ')";
    $this->query($query);
  } 
  
  function dir_url_exists($url)
  {
    $cant = $this->db->getOne("select count(*) from tiki_directory_sites where url='$url'");
    return $cant;
  }
      
  // Functions to validate sites
  
  
  // Validate  
   
  // Functions to add hits
  
  // Site hit
  function dir_add_site_hit($siteId)
  {
  	global $count_admin_pvs;
  	global $user;
    if($count_admin_pvs == 'y' || $user!='admin') {
      $query = "update tiki_directory_sites set hits=hits+1 where siteId=$siteId";
      $this->query($query);
    }
  }  
  
  // Category hit
  function dir_add_category_hit($categId)
  {
  	global $count_admin_pvs;
  	global $user;
    if($count_admin_pvs == 'y' || $user!='admin') {
      $query = "update tiki_directory_categories set hits=hits+1 where categId=$categId";
      $this->query($query);
    }
  }
   
  // Search
  
  function dir_search($words, $how='or', $offset=0,$maxRecords=-1,$sort_mode='hits_desc')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    // First of all split the words by whitespaces building the query string
    // we'll search by name, url, description and cache
    // the relevance will be calculated using hits
    $words = split(' ',$words);
    for($i=0;$i<count($words);$i++) {
      $words[$i]=trim($words[$i]);
      $word = $words[$i];
      if(!empty($word)) {
        // Check if the term is in the stats then add it or increment it
        if($this->db->getOne("select count(*) from tiki_directory_search where term='$word'")) {
          $query = "update tiki_directory_search set hits=hits+1 where term='$word'";
          $this->query($query);
        } else {
          $query = "insert into tiki_directory_search(term,hits) values('$word',1)";
          $this->query($query);
        }
      }
      // Now build the query
      $words[$i] = " ((name like '%$word%') or (description like '%$word%') or (url like '%$word%') or (cache like '%$word%')) ";
    }
    $words = implode($how,$words);
    $query = "select * from tiki_directory_sites where isValid='y' and $words  order by $sort_mode limit $offset,$maxRecords";
    $cant = $this->db->getOne("select count(*) from tiki_directory_sites where isValid='y' and $words");
    $result = $this->query($query);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["cats"]=$this->dir_get_site_categories($res["siteId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  

  function dir_search_cat($parent,$words, $how='or', $offset=0,$maxRecords=-1,$sort_mode='hits_desc')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    // First of all split the words by whitespaces building the query string
    // we'll search by name, url, description and cache
    // the relevance will be calculated using hits
    $words = split(' ',$words);
    for($i=0;$i<count($words);$i++) {
      $words[$i]=trim($words[$i]);
      $word = $words[$i];
      // Check if the term is in the stats then add it or increment it
      if($this->db->getOne("select count(*) from tiki_directory_search where term='$word'")) {
        $query = "update tiki_directory_search set hits=hits+1 where term='$word'";
        $this->query($query);
      } else {
        $query = "insert into tiki_directory_search(term,hits) values('$word',1)";
        $this->query($query);
      }
      // Now build the query
      $words[$i] = " ((tds.name like '%$word%') or (tds.description like '%$word%') or (tds.url like '%$word%') or (cache like '%$word%')) ";
    }
    $words = implode($how,$words);
    $query = "select distinct tds.name,tds.siteId,tds.description,tds.url,tds.country,tds.hits,tds.created,tds.lastModif from tiki_directory_sites tds,tiki_category_sites tcs,tiki_directory_categories tdc where tds.siteId=tcs.siteId and tcs.categId=tdc.categId and isValid='y' and tdc.categId=$parent and $words  order by $sort_mode limit $offset,$maxRecords";
    $cant = $this->db->getOne("select count(*) from tiki_directory_sites tds,tiki_category_sites tcs,tiki_directory_categories tdc where tds.siteId=tcs.siteId and tcs.categId=tdc.categId and isValid = 'y' and tdc.categId=$parent and $words");
    $result = $this->query($query);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["cats"]=$this->dir_get_site_categories($res["siteId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  
  // Functions for search stats
  
  // List searched
  
  // Add search
  
}

$dirlib= new DirLib($dbTiki);


function compare_paths($p1,$p2) {
  if($p1["path"]<$p2["path"]) {
    return -1;
  } elseif($p1["path"]>$p2["path"]) {
    return +1;
  } else {
    return 0;
  }
  
}

?>
