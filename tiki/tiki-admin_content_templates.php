<?php
// Initialization
require_once('tiki-setup.php');

if($tiki_p_edit_content_templates != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}

if(!isset($_REQUEST["templateId"])) {
  $_REQUEST["templateId"] = 0;
}
$smarty->assign('templateId',$_REQUEST["templateId"]);

if($_REQUEST["templateId"]) {
  $info = $tikilib->get_template($_REQUEST["templateId"]);
  if($tikilib->template_is_in_section($_REQUEST["templateId"],'html')) {
    $info["section_html"]='y';
  } else {
    $info["section_html"]='n';
  }
  if($tikilib->template_is_in_section($_REQUEST["templateId"],'wiki')) {
    $info["section_wiki"]='y';
  } else {
    $info["section_wiki"]='n';
  }
  if($tikilib->template_is_in_section($_REQUEST["templateId"],'cms')) {
    $info["section_cms"]='y';
  } else {
    $info["section_cms"]='n';
  }
} else {
  $info = Array();
  $info["name"]='';
  $info["content"]='';
  $info["section_cms"]='n';
  $info["section_html"]='n';
  $info["section_wiki"]='n';
}
$smarty->assign('info',$info);


if(isset($_REQUEST["remove"])) {
  $tikilib->remove_template($_REQUEST["remove"]);
}

if(isset($_REQUEST["removesection"])) {
  $tikilib->remove_template_from_section($_REQUEST["rtemplateId"],$_REQUEST["removesection"]);
}



$smarty->assign('preview','n');
if(isset($_REQUEST["preview"])) {
  $smarty->assign('preview','y');
  if(isset($_REQUEST["section_html"])&&$_REQUEST["section_html"]=='on') {
     $info["section_html"]='y';
     $parsed = nl2br($_REQUEST["content"]);
  }  else {
     $info["section_html"]='n';
     $parsed = $tikilib->parse_data($_REQUEST["content"]);
  }
  $smarty->assign('parsed',$parsed);
  if(isset($_REQUEST["section_wiki"])&&$_REQUEST["section_wiki"]=='on') {
     $info["section_wiki"]='y';
  }  else {
     $info["section_wiki"]='n';
  }
  if(isset($_REQUEST["section_cms"])&&$_REQUEST["section_cms"]=='on') {
    $info["section_cms"]='y';
  }  else {
    $info["section_cms"]='n';
  }
  $info["content"]=$_REQUEST["content"];
  $info["name"]=$_REQUEST["name"];
  $smarty->assign('info',$info);
}

if(isset($_REQUEST["save"])) {
  $tid = $tikilib->replace_template($_REQUEST["templateId"], $_REQUEST["name"],$_REQUEST["content"]);

  $smarty->assign("templateId",'0');
  $info["name"]='';
  $info["content"]='';
  $info["section_cms"]='n';
  $info["section_wiki"]='n';
  $info["section_html"]='n';
  if(isset($_REQUEST["section_html"])&&$_REQUEST["section_html"]=='on') {
     $tikilib->add_template_to_section($tid,'html');
  }  else {
     $tikilib->remove_template_from_section($tid,'html');
  }

  if(isset($_REQUEST["section_wiki"])&&$_REQUEST["section_wiki"]=='on') {
    $tikilib->add_template_to_section($tid,'wiki');
  }  else {
    $tikilib->remove_template_from_section($tid,'wiki');
  }
  if(isset($_REQUEST["section_cms"])&&$_REQUEST["section_cms"]=='on') {
    $tikilib->add_template_to_section($tid,'cms');
  }  else {
    $tikilib->remove_template_from_section($tid,'cms');
  }
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'created_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 

if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

$smarty->assign_by_ref('sort_mode',$sort_mode);
$channels = $tikilib->list_all_templates($offset,$maxRecords,$sort_mode,$find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($channels["cant"] > ($offset+$maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);  
} else {
  $smarty->assign('prev_offset',-1); 
}

$smarty->assign_by_ref('channels',$channels["data"]);


// Display the template
$smarty->assign('mid','tiki-admin_content_templates.tpl');
$smarty->display('tiki.tpl');
?>