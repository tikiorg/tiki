<?php
 
class StructLib extends TikiLib {

  function StructLib($db) 
  {
  	# this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to StructLib constructor");  
    }
    $this->db = $db;  
  }
  
  
  function s_export_structure($structure)
  {
	global $exportlib;
	global $dbTiki;
  	include_once('lib/wiki/exportlib.php');
  	$zipname         = "$structure.zip";
    include_once("lib/tar.class.php");
    $tar = new tar();
  	$pages = $this->s_get_structure_pages($structure);
    foreach($pages as $page) {	
    	$data = $exportlib->export_wiki_page($page,0);
    	$tar->addData($page,$data,date("U"));
    }	
	$tar->toTar("dump/$structure.tar",FALSE);
    header("location: dump/$structure.tar");
    return '';    
  }
  
  function s_get_structure_pages($structure)
  {
	$ret = Array($structure); 	
  	$structure = addslashes($structure);
  	$query = "select page from tiki_structures where parent='$structure' order by pos asc";
	$result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {	
    	$page = $res['page'];
		$ret[]=$page;
    	$ret2 = $this->s_get_structure_pages($page);
    	if(count($ret2) > 0) {
    		$ret = array_merge($ret,$ret2);
    	}
    }
    return $ret;
  }
  
  function s_export_structure_tree($structure,$level=0)
  {
  	$structure = addslashes($structure);
  	$query = "select page from tiki_structures where parent='$structure' order by pos asc";
	$result = $this->query($query);
	if($level==0) {
		print($structure);print("\n");
		$this->s_export_structure_tree($structure,$level+1);	
	} else { 
	    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {	
	   		for($i=0;$i<$level;$i++) {
	   			print(" ");
	   		}
	    	$page = $res['page'];
	    	print($page);print("\n");
	    	$this->s_export_structure_tree($page,$level+1);
	    }
	}
  }
  
  
  
  
  function s_remove_page($page,$delete)
  {
    // Now recursively remove
    $page_sl=addslashes($page);
    $query = "select page from tiki_structures where parent='$page_sl'";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $this->s_remove_page($res["page"],$delete);	
    }
    $query = "delete from tiki_structures where page='$page_sl'";
    $result = $this->query($query);
    if($delete) {
      $this->remove_all_versions($page);	
    }
    return true;	
  }
  
  function s_create_page($parent,$after,$name)
  {
    $parent_sl=addslashes($parent);
    $after_sl=addslashes($after);
    $name_sl=addslashes($name);
    
    if(!$this->page_exists($name)) {
      $now=date("U");
      $this->create_page($name, 0, '', $now, 'created from stucture', 'system', '0.0.0.0','');
    }    
    if($after) {
      $max = $this->getOne("select pos from tiki_structures where page='$after_sl'");	
    } else {
      $max =0;	
    }
    if($max>0) {
     //If max is 5 then we are inserting after position 5 so we'll insert 5 and move all
     // the others
     $query = "update tiki_structures set pos=pos+1 where pos>$max and parent='$parent_sl'";
     $result = $this->query($query);
    }
    $cant = $this->getOne("select count(*) from tiki_structures where page='$name_sl'");
    if($cant) return false;
    $max++;
    $query = "insert into tiki_structures(parent,page,pos) values('$parent_sl','$name_sl',$max)";
    
    $result = $this->query($query);
    // If the page doesn't exist then create the page!
  }
 
  function get_subtree($structure,$page,&$html,$level='')
  {
    $page_sl=addslashes($page);
    $ret = Array();
    $first=true;
    //$level++;
    $sublevel=0;
    $query = "select page from tiki_structures where parent='$page_sl' order by pos asc";
    $result = $this->query($query);
    $subs=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    	if($first) {
    	  $html.='<ul>';
    	  $first=false;	
    	}
    	$sublevel++;
	$upage=urlencode($res["page"]);
    	if($level) {$plevel=$level.'.'.$sublevel;} else {$plevel=$sublevel;}
    	$html.="<li style='list-style:disc outside;'><a class='link' href='tiki-edit_structure.php?structure=$structure&amp;page=$upage'>$plevel&nbsp;".$res["page"]."</a>&nbsp;[<a class='link' href='tiki-edit_structure.php?structure=$structure&amp;remove=$upage'>x</a>]";
    	$html.="&nbsp;[<a class='link' href='tiki-index.php?page=$upage'>view</a>|<a  class='link' href='tiki-editpage.php?page=$upage'>edit</a>]";
    	//$prev = $this->get_prev_page($res["page"]);
    	//$next = $this->get_next_page($res["page"]);
    	//$html.=" prev: $prev next: $next ";
    	$html.="</li>";
    	
    	$subs[]=$this->get_subtree($structure,$res["page"],$html,$plevel);
    } 	
    if(!$first) {
      $html.='</ul>';
    }
    $aux["name"]=$page;
    $aux["cant"]=count($subs);
    $aux["pages"]=$subs;
    $ret[]=$aux;
    return $ret;
  }
  
  function get_structure($page)
  {
    $page_sl=addslashes($page);
    $parent = $this->getOne("select parent from tiki_structures where page='$page_sl'");
    if(!$parent) return $page;
    return $this->get_structure($parent);
  }
  
  function get_subtree_toc($structure,$page,&$html,$level='')
  {
    $page_sl=addslashes($page);
    $ret = Array();
    $first=true;
    //$level++;
    $sublevel=0;
    $query = "select page from tiki_structures where parent='$page_sl' order by pos asc";
    $result = $this->query($query);
    $subs=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    	if($first) {
    	  $html.='<ul>';
    	  $first=false;	
    	}
    	$sublevel++;
    	if($level) {$plevel=$level.'.'.$sublevel;} else {$plevel=$sublevel;}
	$upage=urlencode($res["page"]);
    	$html.="<li style='list-style:disc outside;'><a class='link' href='tiki-index.php?page=$upage'>$plevel&nbsp;".$res["page"]."</a>";
    	//$html.="&nbsp;[<a class='link' href='tiki-index.php?page=${res["page"]}'>view</a>|<a  class='link' href='tiki-editpage.php?page=${res["page"]}'>edit</a>]";
    	$html.="</li>";
    	
    	$subs[]=$this->get_subtree_toc($structure,$res["page"],$html,$plevel);
    } 	
    if(!$first) {
      $html.='</ul>';
    }
    $aux["name"]=$page;
    $aux["cant"]=count($subs);
    $aux["pages"]=$subs;
    $ret[]=$aux;
    return $ret;
  }
  
  function get_subtree_toc_slide($structure,$page,&$html,$level='')
  {
    $page_sl=addslashes($page);
    $ret = Array();
    $first=true;
    //$level++;
    $sublevel=0;
    $query = "select page from tiki_structures where parent='$page_sl' order by pos asc";
    $result = $this->query($query);
    $subs=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    	if($first) {
    	  $html.='<ul>';
    	  $first=false;	
    	}
    	$sublevel++;
    	if($level) {$plevel=$level.'.'.$sublevel;} else {$plevel=$sublevel;}
	$upage=urlencode($res["page"]);
    	$html.="<li style='list-style:disc outside;'><a class='link' href='tiki-slideshow2.php?page=$upage'>$plevel&nbsp;".$res["page"]."</a>";
    	//$html.="&nbsp;[<a class='link' href='tiki-index.php?page=${res["page"]}'>view</a>|<a  class='link' href='tiki-editpage.php?page=${res["page"]}'>edit</a>]";
    	$html.="</li>";
    	
    	$subs[]=$this->get_subtree_toc($structure,$res["page"],$html,$plevel);
    } 	
    if(!$first) {
      $html.='</ul>';
    }
    $aux["name"]=$page;
    $aux["cant"]=count($subs);
    $aux["pages"]=$subs;
    $ret[]=$aux;
    return $ret;
  }
  
  function page_is_in_structure($page)
  {
    $page=addslashes($page);
    $cant = $this->getOne("select count(*) from tiki_structures where page='$page'");
    return $cant;
  }
  
  function get_next_page($page,$deep=1)
  {
    $page=addslashes($page);
    // If we have children then get the first children
    if($deep) {
    $query = "select page from tiki_structures where parent='$page' order by pos asc";
    $result = $this->query($query);
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      return $res["page"];
    }
    }
    
    // Try to get the next page with the same parent as this
    $parent = $this->getOne("select parent from tiki_structures where page='$page'");
    $pos = $this->getOne("select pos from tiki_structures where page='$page'");
    if(!$parent) return '';
    $query = "select page from tiki_structures where parent='".addslashes($parent)."' and pos>$pos order by pos asc";
    $result = $this->query($query);
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      return $res["page"];
    } else {
      return $this->get_next_page($parent,0);
    }
    
  }
  
  function get_prev_page($page)
  {
    // Try to get the next page with the same parent as this
    $page=addslashes($page);
    $parent = $this->getOne("select parent from tiki_structures where page='$page'");
    $pos = $this->getOne("select pos from tiki_structures where page='$page'");
    if(!$parent) return '';
    $query = "select page from tiki_structures where parent='".addslashes($parent)."' and pos<$pos order by pos desc";
    $result = $this->query($query);
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      return $res["page"];
    } else {
      return $parent;
    }
  }
 
  // Return an array of subpages
  function get_pages($page)
  {
    $page=addslashes($page);
    $ret = Array();
    $query = "select page from tiki_structures where parent='$page' order by pos desc";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res["page"];
    }	
    return $ret;
  }
  
  function get_max_children($page)
  {
    $page=addslashes($page);
    $query = "select page from tiki_structures where parent='$page'";	
    $result = $this->query($query);
    if(!$result->numRows()) {
	return '';
    }
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  // Return all the pages belonging to the structure in an array
  function get_structure_pages($page)
  {
    $ret = Array($page);
    //print("page: $page<br/>");
    $page=addslashes($page);
    $query = "select page from tiki_structures where parent='$page'";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res["page"];
      $ret2 = $this->get_structure_pages($res["page"]);
      $ret=array_unique(array_merge($ret,$ret2));	
    }
    
    $ret=array_unique($ret);
    //print_r($ret);print("<br/>");   
    return $ret;
  }
  
  function list_structures($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where parent='' and (page like '%".$find."%' or parent like '%".$find."%')";  
    } else {
      $mid=" where parent=''"; 
    }
    $query = "select * from tiki_structures $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_structures $mid";
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
   
  
  
}

$structlib= new StructLib($dbTiki);
?>
