<?php
 
class DirLib extends TikiLib {
    
  function DirLib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
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
    $path = '<a class="link" href=tiki-directory_browse_category.php?parent="'.$info["categId"].'">'.$info["name"].'</a>';
    while($info["parent"]!=0) {
      $info = $this->dir_get_category($info["parent"]);
      $path = $path = '<a class="link" href=tiki-directory_browse_category.php?parent="'.$info["categId"].'">'.$info["name"].'</a>'.'>'.$path;
    }
    return $path;
  }
  
  // Stats functions
  // get stats (valid sites, invalid sites, categories, searches)
  function dir_stats()
  {
    $aux=Array();
    $aux["valid"] = $this->db->getOne("select count(*) from tiki_directory_sites where isValid='y'");
    $aux["invalid"] = $this->db->getOne("select count(*) from tiki_directory_sites where isValid='n'");
    $aux["categs"] = $this->db->getOne("select count(*) from tiki_directory_categories");
    $aux["searches"] = $this->db->getOne("select sum(hits) from tiki_directory_search");
    $aux["visits"] = $this->db->getOne("select sum(hits) from tiki_directory_sites");
    return $aux;
  }

  // Functions to manage categories
  
  // List
  function dir_list_categories($parent,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" and (title like '%".$find."%' or data like '%".$find."%')";  
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
  
  function dir_get_all_categories($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" and (title like '%".$find."%' or data like '%".$find."%')";  
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
      $res["path"]=$this->dir_get_path_text($res["categId"]);
      $ret[] = $res;
    }
    usort($ret,'compare_paths');
    
    return $ret;
  }
  
  
  
  // Replace
  function dir_replace_category($parent, $categId, $name, $description, $childrenType, $viewableChildren, $allowSites, $showCount, $editorGroup)
  {
    $name = addslashes($name);
    $descripton = addslashes($description);
    
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
      $query = "insert into tiki_directory_categories(parent,hits,name,description,childrenType,viewableChildren,allowSites,showCount,editorGroup)
      values($parent,0,'$name','$description','$childrenType',$viewableChildren,'$allowSites','$showCount','$editorGroup')";
      $this->query($query);
    }
  }
    
  // Get
  function dir_get_category($categId)
  {
    $query = "select * from tiki_directory_categories where categId=$categId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
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
  }
  
  // Functions to manage sites
  
  // List
  // param: valid (y,n,all)
  // param: category id (can be 0=all)
  
  // Replace
  
  // Get
  
  // Remove
  function dir_remove_site($siteId) 
  {
    $query = "delete from tiki_directory_sites where siteId=$siteId";
    $result = $this->query($query);
    // No need to cascade
  }
  
  // Functions to manage relationship between categories
  
  // Update relationship
  
  // Remove relationship
  
  // Add relationship
  
  // Functions to manage relationshipts between sites and categories
  
  // Add relation
  
  // Remove
    
  // Functions to validate sites
  
  // Validate  
   
  // Functions to add hits
  
  // Site hit
  
  // Category hit
  
  // Functions for search stats
  
  // List searched
  
  // Add search
  
}

$dirlib= new DirLib($dbTiki);


function compare_paths($p1,$p2) {
  if($p1["path"]<$p2["path"]) {
    return +1;
  } elseif($p1["path"]>$p2["path"]) {
    return -1;
  } else {
    return 0;
  }
  
}

?>