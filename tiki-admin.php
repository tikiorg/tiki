<?php
// Initialization
require_once('tiki-setup.php');

if(isset($_REQUEST["rmvorphimg"])) {
  $tikilib->remove_orphan_images();
}

// Change preferences
if(isset($_REQUEST["prefs"])) {
  if(isset($_REQUEST["tikiIndex"])) {
    $tikilib->set_preference("tikiIndex",$_REQUEST["tikiIndex"]); 
    $smarty->assign_by_ref('tikiIndex',$_REQUEST["tikiIndex"]);
  }

  if(isset($_REQUEST["style"])) {
    $tikilib->set_preference("style",$_REQUEST["style"]); 
    $smarty->assign_by_ref('style',$_REQUEST["style"]);
  }
  if(isset($_REQUEST["language"])) {
    $tikilib->set_preference("language",$_REQUEST["language"]); 
    $smarty->assign_by_ref('language',$_REQUEST["language"]);
  }
  if(isset($_REQUEST["anonCanEdit"]) && $_REQUEST["anonCanEdit"]=="on") {
    $tikilib->set_preference("anonCanEdit",'y'); 
    $smarty->assign('anonCanEdit','y');
  } else {
    $tikilib->set_preference("anonCanEdit",'n');
    $smarty->assign('anonCanEdit','n');
  }
  
  if(isset($_REQUEST["modallgroups"]) && $_REQUEST["modallgroups"]=="on") {
    $tikilib->set_preference("modallgroups",'y'); 
    $smarty->assign('modallgroups','y');
  } else {
    $tikilib->set_preference("modallgroups",'n');
    $smarty->assign('modallgroups','n');
  }

  if(isset($_REQUEST["cachepages"]) && $_REQUEST["cachepages"]=="on") {
    $tikilib->set_preference("cachepages",'y'); 
    $smarty->assign('cachepages','y');
  } else {
    $tikilib->set_preference("cachepages",'n');
    $smarty->assign('cachepages','n');
  }
  
  if(isset($_REQUEST["cacheimages"]) && $_REQUEST["cacheimages"]=="on") {
    $tikilib->set_preference("cacheimages",'y'); 
    $smarty->assign('cacheimages','y');
  } else {
    $tikilib->set_preference("cacheimages",'n');
    $smarty->assign('cacheimages','n');
  }

  if(isset($_REQUEST["popupLinks"]) && $_REQUEST["popupLinks"]=="on") {
    $tikilib->set_preference("popupLinks",'y'); 
  } else {
    $tikilib->set_preference("popupLinks",'n');
  }
  if(isset($_REQUEST["allowRegister"]) && $_REQUEST["allowRegister"]=="on") {
    $tikilib->set_preference("allowRegister",'y'); 
  } else {
    $tikilib->set_preference("allowRegister",'n');
  }
  if(isset($_REQUEST["maxRecords"])) {
    $tikilib->set_preference("maxRecords",$_REQUEST["maxRecords"]);
  }
  if(isset($_REQUEST["maxVersions"])) {
    $tikilib->set_preference("maxVersions",$_REQUEST["maxVersions"]);	
  }
  
  
}

if(isset($_REQUEST["cmsprefs"])) {
  if(isset($_REQUEST["maxArticles"])) {
    $tikilib->set_preference("maxArticles",$_REQUEST["maxArticles"]);
  }
}

if(isset($_REQUEST["wikifeatures"])) {
  if(isset($_REQUEST["feature_lastChanges"]) && $_REQUEST["feature_lastChanges"]=="on") {
    $tikilib->set_preference("feature_lastChanges",'y'); 
    $smarty->assign("feature_lastChanges",'y');
  } else {
    $tikilib->set_preference("feature_lastChanges",'n');
    $smarty->assign("feature_lastChanges",'n');
  }
  
  if(isset($_REQUEST["feature_dump"]) && $_REQUEST["feature_dump"]=="on") {
    $tikilib->set_preference("feature_dump",'y'); 
    $smarty->assign("feature_dump",'y');
  } else {
    $tikilib->set_preference("feature_dump",'n');
    $smarty->assign("feature_dump",'n');
  }

  if(isset($_REQUEST["feature_wiki_rankings"]) && $_REQUEST["feature_wiki_rankings"]=="on") {
    $tikilib->set_preference("feature_wiki_rankings",'y'); 
    $smarty->assign("feature_wiki_rankings",'y');
  } else {
    $tikilib->set_preference("feature_wiki_rankings",'n');
    $smarty->assign("feature_wiki_rankings",'n');
  }

  
  if(isset($_REQUEST["feature_ranking"]) && $_REQUEST["feature_ranking"]=="on") {
    $tikilib->set_preference("feature_ranking",'y'); 
    $smarty->assign("feature_ranking",'y');
  } else {
    $tikilib->set_preference("feature_ranking",'n');
    $smarty->assign("feature_ranking",'n');
  }
  
  if(isset($_REQUEST["feature_listPages"]) && $_REQUEST["feature_listPages"]=="on") {
    $tikilib->set_preference("feature_listPages",'y'); 
    $smarty->assign("feature_listPages",'y');
  } else {
    $tikilib->set_preference("feature_listPages",'n');
    $smarty->assign("feature_listPages",'n');
  }
  
  if(isset($_REQUEST["feature_history"]) && $_REQUEST["feature_history"]=="on") {
    $tikilib->set_preference("feature_history",'y'); 
    $smarty->assign("feature_history",'y');
  } else {
    $tikilib->set_preference("feature_history",'n');
    $smarty->assign("feature_history",'n');
  }
  
  if(isset($_REQUEST["feature_sandbox"]) && $_REQUEST["feature_sandbox"]=="on") {
    $tikilib->set_preference("feature_sandbox",'y'); 
    $smarty->assign("feature_sandbox",'y');
  } else {
    $tikilib->set_preference("feature_sandbox",'n');
    $smarty->assign("feature_sandbox",'n');
  }
  
  if(isset($_REQUEST["feature_backlinks"]) && $_REQUEST["feature_backlinks"]=="on") {
    $tikilib->set_preference("feature_backlinks",'y'); 
    $smarty->assign("feature_backlinks",'y');
  } else {
    $tikilib->set_preference("feature_backlinks",'n');
    $smarty->assign("feature_backlinks",'n');
  }
  
  if(isset($_REQUEST["feature_likePages"]) && $_REQUEST["feature_likePages"]=="on") {
    $tikilib->set_preference("feature_likePages",'y'); 
    $smarty->assign("feature_likePages",'y');
  } else {
    $tikilib->set_preference("feature_likePages",'n');
    $smarty->assign("feature_likePages",'n');
  }
    
  if(isset($_REQUEST["feature_userVersions"]) && $_REQUEST["feature_userVersions"]=="on") {
    $tikilib->set_preference("feature_userVersions",'y'); 
    $smarty->assign("feature_userVersions",'y');
  } else {
    $tikilib->set_preference("feature_userVersions",'n');
    $smarty->assign("feature_userVersions",'n');
  }
}


if(isset($_REQUEST["galfeatures"])) {
      
  if(isset($_REQUEST["feature_gal_rankings"]) && $_REQUEST["feature_gal_rankings"]=="on") {
    $tikilib->set_preference("feature_gal_rankings",'y'); 
    $smarty->assign("feature_gal_rankings",'y');
  } else {
    $tikilib->set_preference("feature_gal_rankings",'n');
    $smarty->assign("feature_gal_rankings",'n');
  }
}

if(isset($_REQUEST["cmsfeatures"])) {
      
  if(isset($_REQUEST["feature_cms_rankings"]) && $_REQUEST["feature_cms_rankings"]=="on") {
    $tikilib->set_preference("feature_cms_rankings",'y'); 
    $smarty->assign("feature_cms_rankings",'y');
  } else {
    $tikilib->set_preference("feature_cms_rankings",'n');
    $smarty->assign("feature_cms_rankings",'n');
  }
}

if(isset($_REQUEST["blogfeatures"])) {
  if(isset($_REQUEST["feature_blog_rankings"]) && $_REQUEST["feature_blog_rankings"]=="on") {
    $tikilib->set_preference("feature_blog_rankings",'y'); 
    $smarty->assign("feature_blog_rankings",'y');
  } else {
    $tikilib->set_preference("feature_blog_rankings",'n');
    $smarty->assign("feature_blog_rankings",'n');
  }
}

if(isset($_REQUEST["blogset"])) {
  $tikilib->set_preference("home_blog",$_REQUEST["homeBlog"]);
}

if(isset($_REQUEST["galset"])) {
  $tikilib->set_preference("home_gallery",$_REQUEST["homeGallery"]);
}


if(isset($_REQUEST["features"])) {
  if(isset($_REQUEST["feature_wiki"]) && $_REQUEST["feature_wiki"]=="on") {
    $tikilib->set_preference("feature_wiki",'y'); 
    $smarty->assign("feature_wiki",'y');
  } else {
    $tikilib->set_preference("feature_wiki",'n');
    $smarty->assign("feature_wiki",'n');
  }
  if(isset($_REQUEST["feature_banners"]) && $_REQUEST["feature_banners"]=="on") {
    $tikilib->set_preference("feature_banners",'y'); 
    $smarty->assign("feature_banners",'y');
  } else {
    $tikilib->set_preference("feature_banners",'n');
    $smarty->assign("feature_banners",'n');
  }
  
  if(isset($_REQUEST["feature_xmlrpc"]) && $_REQUEST["feature_xmlrpc"]=="on") {
    $tikilib->set_preference("feature_xmlrpc",'y'); 
    $smarty->assign("feature_xmlrpc",'y');
  } else {
    $tikilib->set_preference("feature_xmlrpc",'n');
    $smarty->assign("feature_xmlrpc",'n');
  }

  if(isset($_REQUEST["feature_search"]) && $_REQUEST["feature_search"]=="on") {
    $tikilib->set_preference("feature_search",'y'); 
    $smarty->assign("feature_search",'y');
  } else {
    $tikilib->set_preference("feature_search",'n');
    $smarty->assign("feature_search",'n');
  }

  if(isset($_REQUEST["feature_edit_templates"]) && $_REQUEST["feature_edit_templates"]=="on") {
    $tikilib->set_preference("feature_edit_templates",'y'); 
    $smarty->assign("feature_edit_templates",'y');
  } else {
    $tikilib->set_preference("feature_edit_templates",'n');
    $smarty->assign("feature_edit_templates",'n');
  }

  if(isset($_REQUEST["feature_dynamic_content"]) && $_REQUEST["feature_dynamic_content"]=="on") {
    $tikilib->set_preference("feature_dynamic_content",'y'); 
    $smarty->assign("feature_dynamic_content",'y');
  } else {
    $tikilib->set_preference("feature_dynamic_content",'n');
    $smarty->assign("feature_dynamic_content",'n');
  }

  if(isset($_REQUEST["feature_articles"]) && $_REQUEST["feature_articles"]=="on") {
    $tikilib->set_preference("feature_articles",'y'); 
    $smarty->assign("feature_articles",'y');
  } else {
    $tikilib->set_preference("feature_articles",'n');
    $smarty->assign("feature_articles",'n');
  }
  
  if(isset($_REQUEST["feature_submissions"]) && $_REQUEST["feature_submissions"]=="on") {
    $tikilib->set_preference("feature_submissions",'y'); 
    $smarty->assign("feature_submissions",'y');
  } else {
    $tikilib->set_preference("feature_submissions",'n');
    $smarty->assign("feature_submissions",'n');
  }
  
  if(isset($_REQUEST["feature_blogs"]) && $_REQUEST["feature_blogs"]=="on") {
    $tikilib->set_preference("feature_blogs",'y'); 
    $smarty->assign("feature_blogs",'y');
  } else {
    $tikilib->set_preference("feature_blogs",'n');
    $smarty->assign("feature_blogs",'n');
  }




  if(isset($_REQUEST["feature_hotwords"]) && $_REQUEST["feature_hotwords"]=="on") {
    $tikilib->set_preference("feature_hotwords",'y'); 
    $smarty->assign("feature_hotwords",'y');
  } else {
    $tikilib->set_preference("feature_hotwords",'n');
    $smarty->assign("feature_hotwords",'n');
  }
  
  if(isset($_REQUEST["feature_userPreferences"]) && $_REQUEST["feature_userPreferences"]=="on") {
    $tikilib->set_preference("feature_userPreferences",'y'); 
    $smarty->assign("feature_userPreferences",'y');
  } else {
    $tikilib->set_preference("feature_userPreferences",'n');
    $smarty->assign("feature_userPreferences",'n');
  }


 if(isset($_REQUEST["feature_featuredLinks"]) && $_REQUEST["feature_featuredLinks"]=="on") {
    $tikilib->set_preference("feature_featuredLinks",'y'); 
    $smarty->assign("feature_featuredLinks",'y');
  } else {
    $tikilib->set_preference("feature_featuredLinks",'n');
    $smarty->assign("feature_featuredLinks",'n');
  }


  if(isset($_REQUEST["feature_galleries"]) && $_REQUEST["feature_galleries"]=="on") {
    $tikilib->set_preference("feature_galleries",'y'); 
    $smarty->assign("feature_galleries",'y');
  } else {
    $tikilib->set_preference("feature_galleries",'n');
    $smarty->assign("feature_galleries",'n');
  }

  
}


   

if(isset($_REQUEST["createtag"])) {
  // Check existance
  if($tikilib->tag_exists($_REQUEST["tagname"])) {
      $smarty->assign('msg',tra("Tag already exists"));
      $smarty->display('error.tpl');
      die;  
  }
  $tikilib->create_tag($_REQUEST["tagname"]);  
}
if(isset($_REQUEST["restoretag"])) {
  // Check existance
  if(!$tikilib->tag_exists($_REQUEST["tagname"])) {
      $smarty->assign('msg',tra("Tag not found"));
      $smarty->display('error.tpl');
      die;    
  }
  $tikilib->restore_tag($_REQUEST["tagname"]);  
}
if(isset($_REQUEST["removetag"])) {
  // Check existance
  $tikilib->remove_tag($_REQUEST["tagname"]);  
}


if(isset($_REQUEST["newadminpass"])) {
  if($_REQUEST["adminpass"] <> $_REQUEST["again"]) {
     $smarty->assign('msg',tra("The passwords dont match"));
     $smarty->display('error.tpl');
     die;    
  }
  $userlib->set_admin_pass($_REQUEST["adminpass"]);
}

if(isset($_REQUEST["dump"])) {
  include("lib/tar.class.php");
  error_reporting(E_ERROR|E_WARNING);
  $tikilib->dump(); 
}

$styles=Array();
$h=opendir("styles/");
while($file=readdir($h)) {
  if(strstr($file,"css")) {
    $styles[]=$file;
  }
}
closedir($h);
$smarty->assign_by_ref('styles',$styles);

$languages=Array();
$h=opendir("lang/");
while($file=readdir($h)) {
  if($file!='.' && $file!='..' && is_dir('lang/'.$file) && strlen($file)==2) {
    $languages[]=$file;
  }
}
closedir($h);
$smarty->assign_by_ref('languages',$languages);

$blogs=$tikilib->list_blogs(0,-1,'created_desc','');
$smarty->assign_by_ref('blogs',$blogs["data"]);
$galleries = $tikilib->list_galleries(0, -1, 'name_desc', 'admin','');
$smarty->assign_by_ref('galleries',$galleries["data"]);

$tags = $tikilib->get_tags();
$smarty->assign_by_ref("tags",$tags);

// Preferences to load
// anonCanEdit
// maxVersions
$home_blog = $tikilib->get_preference("home_blog",0);
$home_gallery = $tikilib->get_preference("home_gallery",0);
$smarty->assign('home_blog_url','tiki-view_blog.php?blogId='.$home_blog);
$smarty->assign('home_gallery_url','tiki-browse_gallery.php?galleryId='.$home_gallery);
$smarty->assign('home_blog_name','');
$smarty->assign('home_gal_name','');
if($home_blog) {
  $hbloginfo = $tikilib->get_blog($home_blog);
  $smarty->assign('home_blog_name',substr($hbloginfo["title"],0,20));
}
if($home_gallery) {
  $hgalinfo = $tikilib->get_gallery($home_gallery);
  $smarty->assign('home_gal_name',substr($hgalinfo["name"],0,20));
}

$anonCanEdit = $tikilib->get_preference("anonCanEdit",'n');
$allowRegister = $tikilib->get_preference("allowRegister",'n');
$maxVersions = $tikilib->get_preference("maxVersions", 20);
$maxRecords = $tikilib->get_preference("maxRecords",10);
$title = $tikilib->get_preference("title","");
$popupLinks = $tikilib->get_preference("popupLinks",'n');
$smarty->assign_by_ref('popupLinks',$popupLinks);
$smarty->assign_by_ref('anonCanEdit',$anonCanEdit);
$smarty->assign_by_ref('allowRegister',$allowRegister);
$smarty->assign_by_ref('maxVersions',$maxVersions);
$smarty->assign_by_ref('title',$title);
$smarty->assign_by_ref('maxRecords',$maxRecords);
// Display the template
$smarty->assign('mid','tiki-admin.tpl');
$smarty->display('tiki.tpl');
?>