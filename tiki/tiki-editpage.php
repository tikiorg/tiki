<?php
// Initialization
require_once('tiki-setup.php');


if($feature_wiki != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["page"])) {
  $smarty->assign('msg',tra("No page indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
} else {
  $page = $_REQUEST["page"];
  $smarty->assign_by_ref('page',$_REQUEST["page"]); 
}

function compare_import_versions($a1,$a2) {
  return $a1["version"]-$a2["version"];
}


if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
  require ("lib/webmail/mimeDecode.php");
  $fp = fopen($_FILES['userfile1']['tmp_name'],"rb");
  $data = '';
  while(!feof($fp)) {
    $data .= fread($fp,8192*16);
  }
  fclose($fp);
  $name = $_FILES['userfile1']['name'];
  $params = array('input' => $data,
                  'crlf'  => "\r\n", 
                  'include_bodies' => TRUE,
                  'decode_headers' => TRUE, 
                  'decode_bodies'  => TRUE
                  );  
  $output = Mail_mimeDecode::decode($params);    
  unset($parts);
  parse_output($output, $parts,0);  
  $last_part='';
  $last_part_ver=0;
  usort($parts,'compare_import_versions');
  foreach($parts as $part) {
    if($part["version"]>$last_part_ver) {
      $last_part_ver=$part["version"];
      $last_part=$part["body"];
    }
    if(isset($part["pagename"])) {
      $pagename=urldecode($part["pagename"]);
      $version=urldecode($part["version"]);
      $author=urldecode($part["author"]);
      $lastmodified=$part["lastmodified"];
      if(isset($part["description"])) {
          $description = $part["description"];
        } else {
          $description = '';
        }
      $authorid=urldecode($part["author_id"]);
      if(isset($part["hits"])) $hits=urldecode($part["hits"]); else $hits=0;
      $ex=substr($part["body"],0,25);
      //print(strlen($part["body"]));
      $msg='';
      if(isset($_REQUEST["save"])) {
        if($tikilib->page_exists($pagename)) {
          $tikilib->update_page($pagename,$part["body"],tra('page imported'),$author,$authorid,$description);
        } else {
  
          $tikilib->create_page($pagename,$hits,$part["body"],$lastmodified,tra('created from import'),$author,$authorid,$description);
        }
      } else {
        $_REQUEST["edit"]=$last_part;
      }
    }
  }
  if(isset($_REQUEST["save"])) {
    unset($_REQUEST["save"]);
    header("location: tiki-index.php?page=$page");
    die;
  }
} 
      
// Upload pictures here
if(($feature_wiki_pictures == 'y') && (isset($tiki_p_upload_picture)) && ($tiki_p_upload_picture == 'y')) {
  if(isset($_FILES['picfile1'])&&is_uploaded_file($_FILES['picfile1']['tmp_name'])) {
    $picname = $_FILES['picfile1']['name'];
    move_uploaded_file($_FILES['picfile1']['tmp_name'],'img/wiki_up/'.$picname);
    $_REQUEST["edit"]=$_REQUEST["edit"]."{picture file=img/wiki_up/$picname}";
  }	
}



if(substr($page,0,8)=="UserPage") {
  $name = substr($page,8);
  if($user != $name) {
    if($tiki_p_admin != 'y') { 
      $smarty->assign('msg',tra("You cannot edit this page because it is a user personal page"));
      $smarty->display("styles/$style_base/error.tpl");
      die;
    }
  }
}

if($_REQUEST["page"]=='SandBox' && $feature_sandbox!='y') {
  $smarty->assign('msg',tra("The SandBox is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;	
}

if(!isset($_REQUEST["comment"])) {
  $_REQUEST["comment"]='';
}

/*
if(!page_exists($page)) {
  $smarty->assign('msg',tra("Page cannot be found"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}
*/

include_once("tiki-pagesetup.php");


// Now check permissions to access this page
if($page != 'SandBox') {
if($tiki_p_edit != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot edit this page"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
}


// Get page data
$info = $tikilib->get_page_info($page);

if($info["flag"]=='L') {
  $smarty->assign('msg',tra("Cannot edit page because it is locked"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

if($page != 'SandBox') {
// Permissions
// if this page has at least one permission then we apply individual group/page permissions
// if not then generic permissions apply
if($tiki_p_admin != 'y') {
  if($userlib->object_has_one_permission($page,'wiki page')) {
    if(!$userlib->object_has_permission($user,$page,'wiki page','tiki_p_edit')) {
      $smarty->assign('msg',tra("Permission denied you cannot edit this page"));
      $smarty->display("styles/$style_base/error.tpl");
      die;  
    }
  } else {
    if($tiki_p_edit != 'y')  {
      $smarty->assign('msg',tra("Permission denied you cannot edit this page"));
      $smarty->display("styles/$style_base/error.tpl");
      die;  
    }
  }
}
}

if($tiki_p_admin != 'y') {
  if($tiki_p_use_HTML != 'y') {
    $_REQUEST["allowhtml"] = 'off';
  }
}

$smarty->assign('allowhtml','y');

/*
if(!$user && $anonCanEdit<>'y') {
  
  header("location: tiki-index.php");
  die;
  //$smarty->assign('msg',tra("Anonymous users cannot edit pages"));
  //$smarty->display("styles/$style_base/error.tpl");
  //die;
}
*/

$smarty->assign_by_ref('data',$info);

if(isset($_REQUEST["templateId"])&&$_REQUEST["templateId"]>0) {
  $template_data = $tikilib->get_template($_REQUEST["templateId"]);
  $_REQUEST["edit"]=$template_data["content"];
  $_REQUEST["preview"]=1;
}

if(isset($_REQUEST["edit"])) {
  
  if(isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"]=="on") {
    $edit_data = $_REQUEST["edit"];  
  } else {
    $edit_data = strip_tags($_REQUEST["edit"]);
  }
  
  
  
} else {
  if(isset($info["data"])) {
    $edit_data = $info["data"];
  } else {
    $edit_data = ''; 
  }
}
$smarty->assign('commentdata','');
if(isset($_REQUEST["comment"])) {
  $smarty->assign_by_ref('commentdata',$_REQUEST["comment"]); 
}
if(isset($info["description"])) {
  $smarty->assign('description',$info["description"]);
  $description=$info["description"];
} else {
  $smarty->assign('description','');
  $description = '';
}
if(isset($_REQUEST["description"])) {
  $smarty->assign_by_ref('description',$_REQUEST["description"]);
  $description = $_REQUEST["description"];
}
if(isset($_REQUEST["allowhtml"])) {
  if($_REQUEST["allowhtml"] == "on") {
    $smarty->assign('allowhtml','y');
  }
}
$smarty->assign_by_ref('pagedata',$edit_data);
$parsed = $tikilib->parse_data($edit_data);

/* SPELLCHECKING INITIAL ATTEMPT */
//This nice function does all the job!
if($wiki_spellcheck == 'y') {
if(isset($_REQUEST["spellcheck"])&&$_REQUEST["spellcheck"]=='on') {
  $parsed = $tikilib->spellcheckreplace($edit_data,$parsed,$language,'editwiki');
  $smarty->assign('spellcheck','y');
} else {
  $smarty->assign('spellcheck','n');
}
}

$smarty->assign_by_ref('parsed',$parsed);

$smarty->assign('preview',0);
// If we are in preview mode then preview it!
if(isset($_REQUEST["preview"])) {
  $smarty->assign('preview',1); 
} 

function parse_output(&$obj, &$parts,$i) {  
  if(!empty($obj->parts)) {    
    for($i=0; $i<count($obj->parts); $i++)      
      parse_output($obj->parts[$i], $parts,$i);  
  }else{    
    $ctype = $obj->ctype_primary.'/'.$obj->ctype_secondary;    
    switch($ctype) {    
      case 'application/x-tikiwiki':
         $aux["body"] = $obj->body;  
         $ccc=$obj->headers["content-type"];
         $items = split(';',$ccc);
         foreach($items as $item) {
           $portions = split('=',$item);
           if(isset($portions[0])&&isset($portions[1])) {
             $aux[trim($portions[0])]=trim($portions[1]);
           }
         }
         
         
         $parts[]=$aux;
         
    }  
  }
}



// Pro
if(isset($_REQUEST["save"])) {
  
  if(isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"]=="on") {
    $edit = $_REQUEST["edit"];  
  } else {
    $edit = strip_tags($_REQUEST["edit"]);
  }

  // Parse $edit and eliminate image references to external URIs (make them internal)
  $edit = $tikilib->capture_images($edit);
  
  // If page exists
  if(!$tikilib->page_exists($_REQUEST["page"])) {
    // Extract links and update the page
    $links = $tikilib->get_links($_REQUEST["edit"]);
    $tikilib->cache_links($links);
    $t = date("U");
    $tikilib->create_page($_REQUEST["page"], 0, $edit, $t, $_REQUEST["comment"],$user,$_SERVER["REMOTE_ADDR"],$description);  
  } else {
    $links = $tikilib->get_links($edit);
    $tikilib->cache_links($links);
    $tikilib->update_page($_REQUEST["page"],$edit,$_REQUEST["comment"],$user,$_SERVER["REMOTE_ADDR"],$description);
  }
  
  $cat_type='wiki page';
  $cat_objid = $_REQUEST["page"];
  $cat_desc = substr($_REQUEST["edit"],0,200);
  $cat_name = $_REQUEST["page"];
  $cat_href="tiki-index.php?page=".$cat_objid;
  include_once("categorize.php");
  
  header("location: tiki-index.php?page=$page");
  die;
}

if($feature_wiki_templates == 'y' && $tiki_p_use_content_templates == 'y') {
  $templates = $tikilib->list_templates('wiki',0,-1,'name_asc','');
}
$smarty->assign_by_ref('templates',$templates["data"]);

$cat_type='wiki page';
$cat_objid = $_REQUEST["page"];
include_once("categorize_list.php");

$section='wiki';
include_once('tiki-section_options.php');

// Display the Index Template
$smarty->assign('mid','tiki-editpage.tpl');
$smarty->assign('show_page_bar','y');
$smarty->display("styles/$style_base/tiki.tpl");

?>