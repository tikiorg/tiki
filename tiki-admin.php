<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/wiki/exportlib.php');
include_once('lib/admin/adminlib.php');
include_once('lib/imagegals/imagegallib.php');
include_once ("lib/webmail/htmlMimeMail.php");
include_once ("lib/ziplib.php");

if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if(isset($_REQUEST["rmvorphimg"])) {
  $adminlib->remove_orphan_images();
}
if(isset($_REQUEST["rmvunusedpic"])) {
  $adminlib->remove_unused_pictures();	
}

if(isset($_REQUEST["layout_ss"])) {
 if(isset($_REQUEST["layout_section"]) && $_REQUEST["layout_section"]=="on") {
    $tikilib->set_preference("layout_section",'y'); 
    $smarty->assign('layout_section','y');
  } else {
    $tikilib->set_preference("layout_section",'n');
    $smarty->assign('layout_section','n');
  }
	
}

if(isset($_REQUEST["userfilesprefs"])) {
  $tikilib->set_preference("uf_use_db",$_REQUEST["uf_use_db"]);
  $tikilib->set_preference("uf_use_dir",$_REQUEST["uf_use_dir"]);
  $tikilib->set_preference("userfiles_quota",$_REQUEST["userfiles_quota"]);
  $smarty->assign('uf_use_db',$_REQUEST["uf_use_db"]);
  $smarty->assign('uf_use_dir',$_REQUEST["uf_use_dir"]);
  $smarty->assign('userfiles_quota',$_REQUEST['userfiles_quota']);
}

if(isset($_REQUEST["layout"])) {
 if(isset($_REQUEST["feature_left_column"]) && $_REQUEST["feature_left_column"]=="on") {
    $tikilib->set_preference("feature_left_column",'y'); 
    $smarty->assign('feature_left_column','y');
  } else {
    $tikilib->set_preference("feature_left_column",'n');
    $smarty->assign('feature_left_column','n');
  }
  if(isset($_REQUEST["feature_right_column"]) && $_REQUEST["feature_right_column"]=="on") {
    $tikilib->set_preference("feature_right_column",'y'); 
    $smarty->assign('feature_right_column','y');
  } else {
    $tikilib->set_preference("feature_right_column",'n');
    $smarty->assign('feature_right_column','n');
  }
  if(isset($_REQUEST["feature_top_bar"]) && $_REQUEST["feature_top_bar"]=="on") {
    $tikilib->set_preference("feature_top_bar",'y'); 
    $smarty->assign('feature_top_bar','y');
  } else {
    $tikilib->set_preference("feature_top_bar",'n');
    $smarty->assign('feature_top_bar','n');
  }
  if(isset($_REQUEST["feature_bot_bar"]) && $_REQUEST["feature_bot_bar"]=="on") {
    $tikilib->set_preference("feature_bot_bar",'y'); 
    $smarty->assign('feature_bot_bar','y');
  } else {
    $tikilib->set_preference("feature_bot_bar",'n');
    $smarty->assign('feature_bot_bar','n');
  }
}


if(isset($_REQUEST["maxrss"])) {
  
}

if(isset($_REQUEST["rss"])) {
  $tikilib->set_preference('max_rss_articles',$_REQUEST["max_rss_blogs"]);
  $smarty->assign("max_rss_blogs",$_REQUEST["max_rss_blogs"]);
  $tikilib->set_preference('max_rss_image_galleries',$_REQUEST["max_rss_image_galleries"]);
  $smarty->assign("max_rss_image_galleries",$_REQUEST["max_rss_image_galleries"]);
  $tikilib->set_preference('max_rss_file_galleries',$_REQUEST["max_rss_file_galleries"]);
  $smarty->assign("max_rss_file_galleries",$_REQUEST["max_rss_file_galleries"]);
  $tikilib->set_preference('max_rss_image_gallery',$_REQUEST["max_rss_image_gallery"]);
  $smarty->assign("max_rss_image_gallerys",$_REQUEST["max_rss_image_gallery"]);
  $tikilib->set_preference('max_rss_file_gallery',$_REQUEST["max_rss_file_gallery"]);
  $smarty->assign("max_rss_file_gallery",$_REQUEST["max_rss_file_gallery"]);
  $tikilib->set_preference('max_rss_wiki',$_REQUEST["max_rss_wiki"]);
  $smarty->assign("max_rss_wiki",$_REQUEST["max_rss_wiki"]);
  $tikilib->set_preference('max_rss_blogs',$_REQUEST["max_rss_blogs"]);
  $smarty->assign("max_rss_blogs",$_REQUEST["max_rss_blogs"]);
  $tikilib->set_preference('max_rss_blog',$_REQUEST["max_rss_blog"]);
  $smarty->assign("max_rss_blog",$_REQUEST["max_rss_blog"]);
  $tikilib->set_preference('max_rss_forum',$_REQUEST["max_rss_forum"]);
  $smarty->assign("max_rss_forum",$_REQUEST["max_rss_forum"]);
  $tikilib->set_preference('max_rss_forums',$_REQUEST["max_rss_forums"]);
  $smarty->assign("max_rss_forums",$_REQUEST["max_rss_forums"]);
 if(isset($_REQUEST["rss_articles"]) && $_REQUEST["rss_articles"]=="on") {
    $tikilib->set_preference("rss_articles",'y'); 
    $smarty->assign('rss_articles','y');
  } else {
    $tikilib->set_preference("rss_articles",'n');
    $smarty->assign('rss_articles','n');
  }
  if(isset($_REQUEST["rss_blogs"]) && $_REQUEST["rss_blogs"]=="on") {
    $tikilib->set_preference("rss_blogs",'y'); 
    $smarty->assign('rss_blogs','y');
  } else {
    $tikilib->set_preference("rss_blogs",'n');
    $smarty->assign('rss_blogs','n');
  }
  if(isset($_REQUEST["rss_image_galleries"]) && $_REQUEST["rss_image_galleries"]=="on") {
    $tikilib->set_preference("rss_image_galleries",'y'); 
    $smarty->assign('rss_image_galleries','y');
  } else {
    $tikilib->set_preference("rss_image_galleries",'n');
    $smarty->assign('rss_image_galleries','n');
  }
  if(isset($_REQUEST["rss_file_galleries"]) && $_REQUEST["rss_file_galleries"]=="on") {
    $tikilib->set_preference("rss_file_galleries",'y'); 
    $smarty->assign('rss_file_galleries','y');
  } else {
    $tikilib->set_preference("rss_file_galleries",'n');
    $smarty->assign('rss_file_galleries','n');
  }
  if(isset($_REQUEST["rss_wiki"]) && $_REQUEST["rss_wiki"]=="on") {
    $tikilib->set_preference("rss_wiki",'y'); 
    $smarty->assign('rss_wiki','y');
  } else {
    $tikilib->set_preference("rss_wiki",'n');
    $smarty->assign('rss_wiki','n');
  }
  if(isset($_REQUEST["rss_forum"]) && $_REQUEST["rss_forum"]=="on") {
    $tikilib->set_preference("rss_forum",'y'); 
    $smarty->assign('rss_forum','y');
  } else {
    $tikilib->set_preference("rss_forum",'n');
    $smarty->assign('rss_forum','n');
  }
  if(isset($_REQUEST["rss_forums"]) && $_REQUEST["rss_forums"]=="on") {
    $tikilib->set_preference("rss_forums",'y'); 
    $smarty->assign('rss_forums','y');
  } else {
    $tikilib->set_preference("rss_forums",'n');
    $smarty->assign('rss_forums','n');
  }
  if(isset($_REQUEST["rss_blog"]) && $_REQUEST["rss_blog"]=="on") {
    $tikilib->set_preference("rss_blog",'y'); 
    $smarty->assign('rss_blog','y');
  } else {
    $tikilib->set_preference("rss_blog",'n');
    $smarty->assign('rss_blog','n');
  }
  if(isset($_REQUEST["rss_image_gallery"]) && $_REQUEST["rss_image_gallery"]=="on") {
    $tikilib->set_preference("rss_image_gallery",'y'); 
    $smarty->assign('rss_image_gallery','y');
  } else {
    $tikilib->set_preference("rss_image_gallery",'n');
    $smarty->assign('rss_image_gallery','n');
  }
  if(isset($_REQUEST["rss_file_gallery"]) && $_REQUEST["rss_file_gallery"]=="on") {
    $tikilib->set_preference("rss_file_gallery",'y'); 
    $smarty->assign('rss_file_gallery','y');
  } else {
    $tikilib->set_preference("rss_file_gallery",'n');
    $smarty->assign('rss_file_gallery','n');
  }
} else {
  $smarty->assign("max_rss_articles", $tikilib->get_preference("max_rss_articles",10));
  $smarty->assign("max_rss_wiki", $tikilib->get_preference("max_rss_wiki",10));
  $smarty->assign("max_rss_blog", $tikilib->get_preference("max_rss_blog",10));
  $smarty->assign("max_rss_blogs", $tikilib->get_preference("max_rss_blogs",10));
  $smarty->assign("max_rss_forums", $tikilib->get_preference("max_rss_forums",10));
  $smarty->assign("max_rss_forum", $tikilib->get_preference("max_rss_forum",10));
  $smarty->assign("max_rss_file_galleries", $tikilib->get_preference("max_rss_file_galleries",10));
  $smarty->assign("max_rss_image_galleries", $tikilib->get_preference("max_rss_image_galleries",10));
  $smarty->assign("max_rss_file_gallery", $tikilib->get_preference("max_rss_file_gallery",10));
  $smarty->assign("max_rss_image_gallery", $tikilib->get_preference("max_rss_image_gallery",10));
}


// Change preferences
if(isset($_REQUEST["prefs"])) {
  
  if(isset($_REQUEST['contact_user'])) {
    $tikilib->set_preference('contact_user',$_REQUEST['contact_user']);
    $smarty->assign('contact_user',$_REQUEST['contact_user']);
  }
  
  if(isset($_REQUEST["tikiIndex"])) {
    $tikilib->set_preference("tikiIndex",$_REQUEST["tikiIndex"]); 
    $smarty->assign_by_ref('tikiIndex',$_REQUEST["tikiIndex"]);
  }
  
  $tikilib->set_preference('system_os',$_REQUEST['system_os']);
  $smarty->assign('system_os',$_REQUEST['system_os']);
  
  if(!empty($_REQUEST["urlIndex"])&&isset($_REQUEST["useUrlIndex"])&&$_REQUEST["useUrlIndex"]=='on') {
    $_REQUEST["tikiIndex"]=$_REQUEST["urlIndex"];	
    $tikilib->set_preference("tikiIndex",$_REQUEST["tikiIndex"]); 
    $smarty->assign_by_ref('tikiIndex',$_REQUEST["tikiIndex"]);	
  }

  if(isset($_REQUEST["style_site"])) {
    $tikilib->set_preference("style",$_REQUEST["style_site"]); 
    $smarty->assign_by_ref('style_site',$_REQUEST["style_site"]);
  }
  
  if(isset($_REQUEST["slide_style"])) {
    $tikilib->set_preference("slide_style",$_REQUEST["slide_style"]); 
    $smarty->assign_by_ref('slide_style',$_REQUEST["slide_style"]);
  }
  
  if(isset($_REQUEST["siteTitle"])) {
    $tikilib->set_preference("siteTitle",$_REQUEST["siteTitle"]); 
    $smarty->assign_by_ref('siteTitle',$_REQUEST["siteTitle"]);
  }
  $tikilib->set_preference('feature_server_name',$_REQUEST["feature_server_name"]);
  $smarty->assign('feature_server_name',$_REQUEST["feature_server_name"]);
  
  if(isset($_REQUEST["tmpDir"])) {
    $tikilib->set_preference("tmpDir",$_REQUEST["tmpDir"]);
    $smarty->assign_by_ref('tmpDir',$_REQUEST["tmpDir"]);
  } else {
    if (substr(PHP_OS, 0, 3) == 'WIN') $tdir='C:/temp' ; else $tdir='/tmp'; // untested. Dont have windows
    $tikilib->set_preference("tmpDir",$tdir);
    $smarty->assign('tmpDir',$tdir);
  }

  if(isset($_REQUEST["language"])) {
    $tikilib->set_preference("language",$_REQUEST["language"]); 
    $smarty->assign_by_ref('site_language',$_REQUEST["language"]);
  }

  if(isset($_REQUEST["lang_use_db"]) && $_REQUEST["lang_use_db"]=="on") {
    $tikilib->set_preference("lang_use_db",'y');
    $smarty->assign('lang_use_db','y');
  } else {
    $tikilib->set_preference("lang_use_db",'n');
    $smarty->assign('lang_use_db','n');
  }

  if(isset($_REQUEST["record_untranslated"]) && $_REQUEST["record_untranslated"]=="on") {
    $tikilib->set_preference("record_untranslated",'y');
    $smarty->assign('record_untranslated','y');
  } else {
    $tikilib->set_preference("record_untranslated",'n');
    $smarty->assign('record_untranslated','n');
  }


  if(isset($_REQUEST['display_timezone'])) {
    $tikilib->set_preference('display_timezone',$_REQUEST['display_timezone']); 
    $smarty->assign_by_ref('display_timezone',$_REQUEST['display_timezone']);
  }

  if(isset($_REQUEST['long_date_format'])) {
    $tikilib->set_preference('long_date_format',$_REQUEST['long_date_format']); 
    $smarty->assign_by_ref('long_date_format',$_REQUEST['long_date_format']);
  }

  if(isset($_REQUEST['short_date_format'])) {
    $tikilib->set_preference('short_date_format',$_REQUEST['short_date_format']); 
    $smarty->assign_by_ref('short_date_format',$_REQUEST['short_date_format']);
  }

  if(isset($_REQUEST['long_time_format'])) {
    $tikilib->set_preference('long_time_format',$_REQUEST['long_time_format']); 
    $smarty->assign_by_ref('long_time_format',$_REQUEST['long_time_format']);
  }

  if(isset($_REQUEST['short_time_format'])) {
    $tikilib->set_preference('short_time_format',$_REQUEST['short_time_format']); 
    $smarty->assign_by_ref('short_time_format',$_REQUEST['short_time_format']);
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
  
  if(isset($_REQUEST["feature_obzip"]) && $_REQUEST["feature_obzip"]=="on") {
    $tikilib->set_preference("feature_obzip",'y'); 
    $smarty->assign('feature_obzip','y');
  } else {
    $tikilib->set_preference("feature_obzip",'n');
    $smarty->assign('feature_obzip','n');
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
  
  if(isset($_REQUEST["direct_pagination"]) && $_REQUEST["direct_pagination"]=="on") {
    $tikilib->set_preference("direct_pagination",'y'); 
    $smarty->assign('direct_pagination','y');
  } else {
    $tikilib->set_preference("direct_pagination",'n');
    $smarty->assign('direct_pagination','n');
  }
  
  if(isset($_REQUEST["count_admin_pvs"]) && $_REQUEST["count_admin_pvs"]=="on") {
    $tikilib->set_preference("count_admin_pvs",'y'); 
    $smarty->assign('count_admin_pvs','y');
  } else {
    $tikilib->set_preference("count_admin_pvs",'n');
    $smarty->assign('count_admin_pvs','n');
  }
  
  if(isset($_REQUEST["popupLinks"]) && $_REQUEST["popupLinks"]=="on") {
    $tikilib->set_preference("popupLinks",'y'); 
  } else {
    $tikilib->set_preference("popupLinks",'n');
  }
  
  if(isset($_REQUEST["useUrlIndex"]) && $_REQUEST["useUrlIndex"]=="on") {
    $tikilib->set_preference("useUrlIndex",'y'); 
  } else {
    $tikilib->set_preference("useUrlIndex",'n');
  }
  $tikilib->set_preference("urlIndex",$_REQUEST["urlIndex"]);
 
  
  if(isset($_REQUEST["maxRecords"])) {
    $tikilib->set_preference("maxRecords",$_REQUEST["maxRecords"]);
  }
  
  header('location: tiki-admin.php#general');  
  
}

if(isset($_REQUEST["loginprefs"])) {
  if(isset($_REQUEST["change_theme"]) && $_REQUEST["change_theme"]=="on") {
    $tikilib->set_preference("change_theme",'y'); 
    $smarty->assign('change_theme','y');
  } else {
    $tikilib->set_preference("change_theme",'n');
    $smarty->assign('change_theme','n');
  }
  
  if(isset($_REQUEST["change_language"]) && $_REQUEST["change_language"]=="on") {
    $tikilib->set_preference("change_language",'y'); 
    $smarty->assign('change_language','y');
  } else {
    $tikilib->set_preference("change_language",'n');
    $smarty->assign('change_language','n');
  }

  if(isset($_REQUEST["allowRegister"]) && $_REQUEST["allowRegister"]=="on") {
    $tikilib->set_preference("allowRegister",'y'); 
    $smarty->assign('allowRegister','y');
  } else {
    $tikilib->set_preference("allowRegister",'n');
    $smarty->assign('allowRegister','n');
  }

  if(isset($_REQUEST["useRegisterPasscode"]) && $_REQUEST["useRegisterPasscode"]=="on") {
    $tikilib->set_preference("useRegisterPasscode",'y'); 
    $smarty->assign('useRegisterPasscode','y');
  } else {
    $tikilib->set_preference("useRegisterPasscode",'n');
    $smarty->assign('useRegisterPasscode','n');
  }
  
  $tikilib->set_preference("registerPasscode",$_REQUEST["registerPasscode"]);
  $smarty->assign('registerPasscode',$_REQUEST["registerPasscode"]);

  $tikilib->set_preference("min_pass_length",$_REQUEST["min_pass_length"]);
  $smarty->assign('min_pass_length',$_REQUEST["min_pass_length"]);
  
  $tikilib->set_preference("pass_due",$_REQUEST["pass_due"]);
  $smarty->assign('pass_due',$_REQUEST["pass_due"]);

  if(isset($_REQUEST["validateUsers"]) && $_REQUEST["validateUsers"]=="on") {
    $tikilib->set_preference("validateUsers",'y'); 
    $smarty->assign('validateUsers','y');
  } else {
    $tikilib->set_preference("validateUsers",'n');
    $smarty->assign('validateUsers','n');
  }
  
  if(isset($_REQUEST["pass_chr_num"]) && $_REQUEST["pass_chr_num"]=="on") {
    $tikilib->set_preference("pass_chr_num",'y'); 
    $smarty->assign('pass_chr_num','y');
  } else {
    $tikilib->set_preference("pass_chr_num",'n');
    $smarty->assign('pass_chr_num','n');
  }
  
  if(isset($_REQUEST["feature_challenge"]) && $_REQUEST["feature_challenge"]=="on") {
    $tikilib->set_preference("feature_challenge",'y'); 
    $smarty->assign('feature_challenge','y');
  } else {
    $tikilib->set_preference("feature_challenge",'n');
    $smarty->assign('feature_challenge','n');
  }
  
  if(isset($_REQUEST["feature_clear_passwords"]) && $_REQUEST["feature_clear_passwords"]=="on") {
    $tikilib->set_preference("feature_clear_passwords",'y'); 
    $smarty->assign('feature_clear_passwords','y');
  } else {
    $tikilib->set_preference("feature_clear_passwords",'n');
    $smarty->assign('feature_clear_passwords','n');
  }  
  
  if(isset($_REQUEST["forgotPass"]) && $_REQUEST["forgotPass"]=="on") {
    $tikilib->set_preference("forgotPass",'y'); 
    $smarty->assign('forgotPass','y');
  } else {
    $tikilib->set_preference("forgotPass",'n');
    $smarty->assign('forgotPass','n');
  }

/* # not implemented
  $b = isset($_REQUEST['http_basic_auth']) && $_REQUEST['http_basic_auth'] == 'on';
  $tikilib->set_preference('http_basic_auth', $b); 
  $smarty->assign('http_basic_auth', $b);
*/

  $b = (isset($_REQUEST['https_login']) && $_REQUEST['https_login'] == 'on') ? 'y' : 'n';
  $tikilib->set_preference('https_login', $b); 
  $smarty->assign('https_login', $b);

  $b = (isset($_REQUEST['https_login_required']) && $_REQUEST['https_login_required'] == 'on') ? 'y' : 'n';
  $tikilib->set_preference('https_login_required', $b); 
  $smarty->assign('https_login_required', $b);

  $v = isset($_REQUEST['http_domain']) ? $_REQUEST['http_domain'] : '';
  $tikilib->set_preference('http_domain', $v); 
  $smarty->assign('http_domain', $v);

  $v = isset($_REQUEST['http_port']) ? $_REQUEST['http_port'] : 80;
  $tikilib->set_preference('http_port', $v); 
  $smarty->assign('http_port', $v);

  $v = isset($_REQUEST['http_prefix']) ? $_REQUEST['http_prefix'] : '/';
  $tikilib->set_preference('http_prefix', $v); 
  $smarty->assign('http_prefix', $v);

  $v = isset($_REQUEST['https_domain']) ? $_REQUEST['https_domain'] : '';
  $tikilib->set_preference('https_domain', $v); 
  $smarty->assign('https_domain', $v);

  $v = isset($_REQUEST['https_port']) ? $_REQUEST['https_port'] : 443;
  $tikilib->set_preference('https_port', $v); 
  $smarty->assign('https_port', $v);

  $v = isset($_REQUEST['https_prefix']) ? $_REQUEST['https_prefix'] : '/';
  $tikilib->set_preference('https_prefix', $v); 
  $smarty->assign('https_prefix', $v);
  $tikilib->set_preference('rememberme',$_REQUEST['rememberme']);
  $tikilib->set_preference('remembertime',$_REQUEST['remembertime']);
  $smarty->assign('rememberme',$_REQUEST['rememberme']);
  $smarty->assign('remembertime',$_REQUEST['remembertime']);

  if(isset($_REQUEST["auth_method"])) {
    $tikilib->set_preference('auth_method',$_REQUEST['auth_method']);
    $smarty->assign('auth_method',$_REQUEST['auth_method']);
  }
}

if(isset($_REQUEST["cmsprefs"])) {
  if(isset($_REQUEST["maxArticles"])) {
    $tikilib->set_preference("maxArticles",$_REQUEST["maxArticles"]);
    $smarty->assign('maxArticles',$_REQUEST["maxArticles"]);
  }
  
}

if(isset($_REQUEST["wikiprefs"])) {
  if(isset($_REQUEST["wiki_comments_per_page"])) {
    $tikilib->set_preference("wiki_comments_per_page",$_REQUEST["wiki_comments_per_page"]);
    $smarty->assign('wiki_comments_per_page',$_REQUEST["wiki_comments_per_page"]);
  }
  if(isset($_REQUEST["wiki_comments_default_ordering"])) {
    $tikilib->set_preference("wiki_comments_default_ordering",$_REQUEST["wiki_comments_default_ordering"]);
    $smarty->assign('wiki_comments_default_ordering',$_REQUEST["wiki_comments_default_ordering"]);
  }
}

if(isset($_REQUEST["wikidiscussprefs"])) {
  if(isset($_REQUEST["feature_wiki_discuss"])) {
    $tikilib->set_preference('feature_wiki_discuss','y');
    $smarty->assign('feature_wiki_discuss','y');
  } else {
    $tikilib->set_preference("feature_wiki_discuss",'n');
    $smarty->assign('feature_wiki_discuss','n');
  }

  if(isset($_REQUEST["wiki_forum"])) {
    $tikilib->set_preference('wiki_forum',$_REQUEST["wiki_forum"]);
    $smarty->assign('wiki_forum',$_REQUEST["wiki_forum"]);
    $wiki_forum_id=$tikilib->getOne("select forumID from tiki_forums where name='".$_REQUEST["wiki_forum"]."'");
    $tikilib->set_preference('wiki_forum_id',$wiki_forum_id);
    $smarty->assign('wiki_forum_id',$wiki_forum_id);
  }
}

if(isset($_REQUEST["wikiexport"])) {
  //$cosa=$tikilib->export_wiki_page('HomePage',1);
  $exportlib->MakeWikiZip();
  die;
  //print(nl2br($cosa));
}

if(isset($_REQUEST["wikiattprefs"])) {
  $tikilib->set_preference('w_use_db',$_REQUEST["w_use_db"]);
  $tikilib->set_preference('w_use_dir',$_REQUEST["w_use_dir"]);
  $smarty->assign('w_use_db',$_REQUEST["w_use_db"]);
  $smarty->assign('w_use_dir',$_REQUEST["w_use_dir"]);
  if(isset($_REQUEST["feature_wiki_attachments"]) && $_REQUEST["feature_wiki_attachments"]=="on") {
    $tikilib->set_preference("feature_wiki_attachments",'y'); 
    $smarty->assign('feature_wiki_attachments','y');
  } else {
    $tikilib->set_preference("feature_wiki_attachments",'n');
    $smarty->assign('feature_wiki_attachments','n');
  }
}

if(isset($_REQUEST["trkset"])) {
  $tikilib->set_preference('t_use_db',$_REQUEST["t_use_db"]);
  $tikilib->set_preference('t_use_dir',$_REQUEST["t_use_dir"]);
  $smarty->assign('t_use_db',$_REQUEST["t_use_db"]);
  $smarty->assign('t_use_dir',$_REQUEST["t_use_dir"]);
}

if(isset($_REQUEST["directory"])) {
  if(isset($_REQUEST["directory_validate_urls"]) && $_REQUEST["directory_validate_urls"]=="on") {
    $tikilib->set_preference('directory_validate_urls','y');
    $smarty->assign('directory_validate_urls','y');
  } else {
    $tikilib->set_preference('directory_validate_urls','n');
    $smarty->assign('directory_validate_urls','n');
  }
  $tikilib->set_preference('directory_columns',$_REQUEST["directory_columns"]);
  $tikilib->set_preference('directory_links_per_page',$_REQUEST["directory_links_per_page"]);
  $tikilib->set_preference('directory_open_links',$_REQUEST["directory_open_links"]);
  $smarty->assign('directory_columns',$_REQUEST['directory_columns']);
  $smarty->assign('directory_links_per_page',$_REQUEST['directory_links_per_page']);
  $smarty->assign('directory_open_links',$_REQUEST['directory_open_links']);
}

if(isset($_REQUEST["webmail"])) {
  if(isset($_REQUEST["webmail_view_html"]) && $_REQUEST["webmail_view_html"]=="on") {
    $tikilib->set_preference("webmail_view_html",'y'); 
    $smarty->assign('webmail_view_html','y');
  } else {
    $tikilib->set_preference("webmail_view_html",'n');
    $smarty->assign('webmail_view_html','n');
  }  
  $tikilib->set_preference('webmail_max_attachment',$_REQUEST["webmail_max_attachment"]);
  $smarty->assign('webmail_max_attachment',$_REQUEST["webmail_max_attachment"]);
}



if(isset($_REQUEST["homeforumprefs"])&&isset($_REQUEST["homeForum"])) {
  $tikilib->set_preference("home_forum",$_REQUEST["homeForum"]);
  $smarty->assign('home_forum',$_REQUEST["homeForum"]);
}  

if(isset($_REQUEST['imagegallistprefs'])) {
  if(isset($_REQUEST["gal_list_name"]) && $_REQUEST["gal_list_name"]=="on") {
    $tikilib->set_preference("gal_list_name",'y'); 
    $smarty->assign("gal_list_name",'y');
  } else {
    $tikilib->set_preference("gal_list_name",'n');
    $smarty->assign("gal_list_name",'n');
  }  
  if(isset($_REQUEST["gal_list_description"]) && $_REQUEST["gal_list_description"]=="on") {
    $tikilib->set_preference("gal_list_description",'y'); 
    $smarty->assign("gal_list_description",'y');
  } else {
    $tikilib->set_preference("gal_list_description",'n');
    $smarty->assign("gal_list_description",'n');
  }  
  if(isset($_REQUEST["gal_list_created"]) && $_REQUEST["gal_list_created"]=="on") {
    $tikilib->set_preference("gal_list_created",'y'); 
    $smarty->assign("gal_list_created",'y');
  } else {
    $tikilib->set_preference("gal_list_created",'n');
    $smarty->assign("gal_list_created",'n');
  }  
  if(isset($_REQUEST["gal_list_lastmodif"]) && $_REQUEST["gal_list_lastmodif"]=="on") {
    $tikilib->set_preference("gal_list_lastmodif",'y'); 
    $smarty->assign("gal_list_lastmodif",'y');
  } else {
    $tikilib->set_preference("gal_list_lastmodif",'n');
    $smarty->assign("gal_list_lastmodif",'n');
  }  
  if(isset($_REQUEST["gal_list_user"]) && $_REQUEST["gal_list_user"]=="on") {
    $tikilib->set_preference("gal_list_user",'y'); 
    $smarty->assign("gal_list_user",'y');
  } else {
    $tikilib->set_preference("gal_list_user",'n');
    $smarty->assign("gal_list_user",'n');
  }  
  if(isset($_REQUEST["gal_list_imgs"]) && $_REQUEST["gal_list_imgs"]=="on") {
    $tikilib->set_preference("gal_list_imgs",'y'); 
    $smarty->assign("gal_list_imgs",'y');
  } else {
    $tikilib->set_preference("gal_list_imgs",'n');
    $smarty->assign("gal_list_imgs",'n');
  }  
  if(isset($_REQUEST["gal_list_visits"]) && $_REQUEST["gal_list_visits"]=="on") {
    $tikilib->set_preference("gal_list_visits",'y'); 
    $smarty->assign("gal_list_visits",'y');
  } else {
    $tikilib->set_preference("gal_list_visits",'n');
    $smarty->assign("gal_list_visits",'n');
  }  

}


if(isset($_REQUEST["forumlistprefs"])) {
  if(isset($_REQUEST["forum_list_topics"]) && $_REQUEST["forum_list_topics"]=="on") {
    $tikilib->set_preference("forum_list_topics",'y'); 
    $smarty->assign("forum_list_topics",'y');
  } else {
    $tikilib->set_preference("forum_list_topics",'n');
    $smarty->assign("forum_list_topics",'n');
  }  
  if(isset($_REQUEST["forum_list_posts"]) && $_REQUEST["forum_list_posts"]=="on") {
    $tikilib->set_preference("forum_list_posts",'y'); 
    $smarty->assign("forum_list_posts",'y');
  } else {
    $tikilib->set_preference("forum_list_posts",'n');
    $smarty->assign("forum_list_posts",'n');
  }  
  if(isset($_REQUEST["forum_list_ppd"]) && $_REQUEST["forum_list_ppd"]=="on") {
    $tikilib->set_preference("forum_list_ppd",'y'); 
    $smarty->assign("forum_list_ppd",'y');
  } else {
    $tikilib->set_preference("forum_list_ppd",'n');
    $smarty->assign("forum_list_ppd",'n');
  }  
  if(isset($_REQUEST["forum_list_lastpost"]) && $_REQUEST["forum_list_lastpost"]=="on") {
    $tikilib->set_preference("forum_list_lastpost",'y'); 
    $smarty->assign("forum_list_lastpost",'y');
  } else {
    $tikilib->set_preference("forum_list_lastpost",'n');
    $smarty->assign("forum_list_lastpost",'n');
  }  
  if(isset($_REQUEST["forum_list_visits"]) && $_REQUEST["forum_list_visits"]=="on") {
    $tikilib->set_preference("forum_list_visits",'y'); 
    $smarty->assign("forum_list_visits",'y');
  } else {
    $tikilib->set_preference("forum_list_visits",'n');
    $smarty->assign("forum_list_visits",'n');
  }  
  if(isset($_REQUEST["forum_list_desc"]) && $_REQUEST["forum_list_desc"]=="on") {
    $tikilib->set_preference("forum_list_desc",'y'); 
    $smarty->assign("forum_list_desc",'y');
  } else {
    $tikilib->set_preference("forum_list_desc",'n');
    $smarty->assign("forum_list_desc",'n');
  }  
  
}

if(isset($_REQUEST["forumprefs"])) {
  if(isset($_REQUEST["feature_forum_rankings"]) && $_REQUEST["feature_forum_rankings"]=="on") {
    $tikilib->set_preference("feature_forum_rankings",'y'); 
    $smarty->assign("feature_forum_rankings",'y');
  } else {
    $tikilib->set_preference("feature_forum_rankings",'n');
    $smarty->assign("feature_forum_rankings",'n');
  }  
  if(isset($_REQUEST["feature_forum_parse"]) && $_REQUEST["feature_forum_parse"]=="on") {
    $tikilib->set_preference("feature_forum_parse",'y'); 
    $smarty->assign("feature_forum_parse",'y');
  } else {
    $tikilib->set_preference("feature_forum_parse",'n');
    $smarty->assign("feature_forum_parse",'n');
  }  
  if(isset($_REQUEST["feature_forum_quickjump"]) && $_REQUEST["feature_forum_quickjump"]=="on") {
    $tikilib->set_preference("feature_forum_quickjump",'y'); 
    $smarty->assign("feature_forum_quickjump",'y');
  } else {
    $tikilib->set_preference("feature_forum_quickjump",'n');
    $smarty->assign("feature_forum_quickjump",'n');
  }  

  if(isset($_REQUEST["feature_forum_topicd"]) && $_REQUEST["feature_forum_topicd"]=="on") {
    $tikilib->set_preference("feature_forum_topicd",'y'); 
    $smarty->assign("feature_forum_topicd",'y');
  } else {
    $tikilib->set_preference("feature_forum_topicd",'n');
    $smarty->assign("feature_forum_topicd",'n');
  }  


  if(isset($_REQUEST["forums_ordering"])) {
    $tikilib->set_preference("forums_ordering",$_REQUEST["forums_ordering"]);
    $smarty->assign('forums_ordering',$_REQUEST["forums_ordering"]);
  }
}


if(isset($_REQUEST["wikisetprefs"])) {
  if(isset($_REQUEST["maxVersions"])) {
    $tikilib->set_preference("maxVersions",$_REQUEST["maxVersions"]);	
  }
  if(isset($_REQUEST["keep_versions"])) {
    $tikilib->set_preference("keep_versions",$_REQUEST["keep_versions"]);	
    $smarty->assign('keep_versions',$_REQUEST["keep_versions"]);
  }
}


if(isset($_REQUEST["pollprefs"])) {
  if(isset($_REQUEST["poll_comments_per_page"])) {
    $tikilib->set_preference("poll_comments_per_page",$_REQUEST["poll_comments_per_page"]);
    $smarty->assign('poll_comments_per_page',$_REQUEST["poll_comments_per_page"]);
  }
  if(isset($_REQUEST["poll_comments_default_ordering"])) {
    $tikilib->set_preference("poll_comments_default_ordering",$_REQUEST["poll_comments_default_ordering"]);
    $smarty->assign('poll_comments_default_ordering',$_REQUEST["poll_comments_default_ordering"]);
  }
  if(isset($_REQUEST["feature_poll_comments"]) && $_REQUEST["feature_poll_comments"]=="on") {
    $tikilib->set_preference("feature_poll_comments",'y'); 
    $smarty->assign("feature_poll_comments",'y');
  } else {
    $tikilib->set_preference("feature_poll_comments",'n');
    $smarty->assign("feature_poll_comments",'n');
  }
}


if(isset($_REQUEST["blogcomprefs"])) {
  if(isset($_REQUEST["blog_comments_per_page"])) {
    $tikilib->set_preference("blog_comments_per_page",$_REQUEST["blog_comments_per_page"]);
    $smarty->assign('blog_comments_per_page',$_REQUEST["blog_comments_per_page"]);
  }
  if(isset($_REQUEST["blog_comments_default_ordering"])) {
    $tikilib->set_preference("blog_comments_default_ordering",$_REQUEST["blog_comments_default_ordering"]);
    $smarty->assign('blog_comments_default_ordering',$_REQUEST["blog_comments_default_ordering"]);
  }
}

if(isset($_REQUEST["faqcomprefs"])) {
  if(isset($_REQUEST["faq_comments_per_page"])) {
    $tikilib->set_preference("faq_comments_per_page",$_REQUEST["faq_comments_per_page"]);
    $smarty->assign('faq_comments_per_page',$_REQUEST["faq_comments_per_page"]);
  }
  if(isset($_REQUEST["faq_comments_default_ordering"])) {
    $tikilib->set_preference("faq_comments_default_ordering",$_REQUEST["faq_comments_default_ordering"]);
    $smarty->assign('faq_comments_default_ordering',$_REQUEST["faq_comments_default_ordering"]);
  }
  if(isset($_REQUEST["feature_faq_comments"]) && $_REQUEST["feature_faq_comments"]=="on") {
    $tikilib->set_preference("feature_faq_comments",'y'); 
    $smarty->assign("feature_faq_comments",'y');
  } else {
    $tikilib->set_preference("feature_faq_comments",'n');
    $smarty->assign("feature_faq_comments",'n');
  }
}



if(isset($_REQUEST["imagegalcomprefs"])) {
  if(isset($_REQUEST["image_galleries_comments_per_page"])) {
    $tikilib->set_preference("image_galleries_comments_per_page",$_REQUEST["image_galleries_comments_per_page"]);
    $smarty->assign('image_galleries_comments_per_page',$_REQUEST["image_galleries_comments_per_page"]);
  }
  if(isset($_REQUEST["image_galleries_comments_default_ordering"])) {
    $tikilib->set_preference("image_galleries_comments_default_ordering",$_REQUEST["image_galleries_comments_default_ordering"]);
    $smarty->assign('image_galleries_comments_default_ordering',$_REQUEST["image_galleries_comments_default_ordering"]);
  }
}

if(isset($_REQUEST["filegalcomprefs"])) {
  if(isset($_REQUEST["file_galleries_comments_per_page"])) {
    $tikilib->set_preference("file_galleries_comments_per_page",$_REQUEST["file_galleries_comments_per_page"]);
    $smarty->assign('file_galleries_comments_per_page',$_REQUEST["file_galleries_comments_per_page"]);
  }
  if(isset($_REQUEST["file_galleries_comments_default_ordering"])) {
    $tikilib->set_preference("file_galleries_comments_default_ordering",$_REQUEST["file_galleries_comments_default_ordering"]);
    $smarty->assign('file_galleries_comments_default_ordering',$_REQUEST["file_galleries_comments_default_ordering"]);
  }
}

if(isset($_REQUEST["articlecomprefs"])) {
  if(isset($_REQUEST["article_comments_per_page"])) {
    $tikilib->set_preference("article_comments_per_page",$_REQUEST["article_comments_per_page"]);
    $smarty->assign('article_comments_per_page',$_REQUEST["article_comments_per_page"]);
  }
  if(isset($_REQUEST["article_comments_default_ordering"])) {
    $tikilib->set_preference("article_comments_default_ordering",$_REQUEST["article_comments_default_ordering"]);
    $smarty->assign('article_comments_default_ordering',$_REQUEST["article_comments_default_ordering"]);
  }
}

if(isset($_REQUEST["auth_pear"])) {

  if(isset($_REQUEST["auth_create_user_tiki"]) && $_REQUEST["auth_create_user_tiki"]=="on") {
    $tikilib->set_preference("auth_create_user_tiki",'y');
    $smarty->assign("auth_create_user_tiki",'y');
  } else {
    $tikilib->set_preference("auth_create_user_tiki",'n');
    $smarty->assign("auth_create_user_tiki",'n');
  }

  if(isset($_REQUEST["auth_create_user_auth"]) && $_REQUEST["auth_create_user_auth"]=="on") {
    $tikilib->set_preference("auth_create_user_auth",'y');
    $smarty->assign("auth_create_user_auth",'y');
  } else {
    $tikilib->set_preference("auth_create_user_auth",'n');
    $smarty->assign("auth_create_user_auth",'n');
  }

  if(isset($_REQUEST["auth_skip_admin"]) && $_REQUEST["auth_skip_admin"]=="on") {
    $tikilib->set_preference("auth_skip_admin",'y');
    $smarty->assign("auth_skip_admin",'y');
  } else {
    $tikilib->set_preference("auth_skip_admin",'n');
    $smarty->assign("auth_skip_admin",'n');
  }

  if(isset($_REQUEST["auth_ldap_host"])) {
    $tikilib->set_preference("auth_ldap_host",$_REQUEST["auth_ldap_host"]);
    $smarty->assign('auth_ldap_host',$_REQUEST["auth_ldap_host"]);
  }

  if(isset($_REQUEST["auth_ldap_port"])) {
    $tikilib->set_preference("auth_ldap_port",$_REQUEST["auth_ldap_port"]);
    $smarty->assign('auth_ldap_port',$_REQUEST["auth_ldap_port"]);
  }

  if(isset($_REQUEST["auth_ldap_scope"])) {
    $tikilib->set_preference("auth_ldap_scope",$_REQUEST["auth_ldap_scope"]);
    $smarty->assign('auth_ldap_scope',$_REQUEST["auth_ldap_scope"]);
  }

  if(isset($_REQUEST["auth_ldap_basedn"])) {
    $tikilib->set_preference("auth_ldap_basedn",$_REQUEST["auth_ldap_basedn"]);
    $smarty->assign('auth_ldap_basedn',$_REQUEST["auth_ldap_basedn"]);
  }

  if(isset($_REQUEST["auth_ldap_userdn"])) {
    $tikilib->set_preference("auth_ldap_userdn",$_REQUEST["auth_ldap_userdn"]);
    $smarty->assign('auth_ldap_userdn',$_REQUEST["auth_ldap_userdn"]);
  }

  if(isset($_REQUEST["auth_ldap_userattr"])) {
    $tikilib->set_preference("auth_ldap_userattr",$_REQUEST["auth_ldap_userattr"]);
    $smarty->assign('auth_ldap_userattr',$_REQUEST["auth_ldap_userattr"]);
  }

  if(isset($_REQUEST["auth_ldap_useroc"])) {
    $tikilib->set_preference("auth_ldap_useroc",$_REQUEST["auth_ldap_useroc"]);
    $smarty->assign('auth_ldap_useroc',$_REQUEST["auth_ldap_useroc"]);
  }
  if(isset($_REQUEST["auth_ldap_groupdn"])) {
    $tikilib->set_preference("auth_ldap_groupdn",$_REQUEST["auth_ldap_groupdn"]);
    $smarty->assign('auth_ldap_groupdn',$_REQUEST["auth_ldap_groupdn"]);
  }

  if(isset($_REQUEST["auth_ldap_groupattr"])) {
    $tikilib->set_preference("auth_ldap_groupattr",$_REQUEST["auth_ldap_groupattr"]);
    $smarty->assign('auth_ldap_groupattr',$_REQUEST["auth_ldap_groupattr"]);
  }

  if(isset($_REQUEST["auth_ldap_groupoc"])) {
    $tikilib->set_preference("auth_ldap_groupoc",$_REQUEST["auth_ldap_groupoc"]);
    $smarty->assign('auth_ldap_groupoc',$_REQUEST["auth_ldap_groupoc"]);
  }

  if(isset($_REQUEST["auth_ldap_memberattr"])) {
    $tikilib->set_preference("auth_ldap_memberattr",$_REQUEST["auth_ldap_memberattr"]);
    $smarty->assign('auth_ldap_ldap_memberattr',$_REQUEST["auth_ldap_memberattr"]);
  }

  if(isset($_REQUEST["auth_ldap_memberisdn"]) && $_REQUEST["auth_ldap_memberisdn"]=="on") {
    $tikilib->set_preference("auth_ldap_memberisdn",'y');
    $smarty->assign("auth_ldap_memberisdn",'y');
  } else {
    $tikilib->set_preference("auth_ldap_memberisdn",'n');
    $smarty->assign("auth_ldap_memberisdn",'n');
  }

  if(isset($_REQUEST["auth_ldap_adminuser"])) {
    $tikilib->set_preference("auth_ldap_adminuser",$_REQUEST["auth_ldap_adminuser"]);
    $smarty->assign('auth_ldap_adminuser',$_REQUEST["auth_ldap_adminuser"]);
  }

  if(isset($_REQUEST["auth_ldap_adminpass"])) {
    $tikilib->set_preference("auth_ldap_adminpass",$_REQUEST["auth_ldap_adminpass"]);
    $smarty->assign('auth_ldap_adminpass',$_REQUEST["auth_ldap_adminpass"]);
  }
}


if(isset($_REQUEST["setwikihome"])) {
  $tikilib->set_preference('wikiHomePage',$_REQUEST["wikiHomePage"]);
  $smarty->assign('wikiHomePage',$_REQUEST["wikiHomePage"]);
}

if(isset($_REQUEST["setwikiregex"])) {
  $tikilib->set_preference('wiki_page_regex',$_REQUEST["wiki_page_regex"]);
  $smarty->assign('wiki_page_regex',$_REQUEST["wiki_page_regex"]);	
}

if(isset($_REQUEST["wikifeatures"])) {
  if(isset($_REQUEST["feature_lastChanges"]) && $_REQUEST["feature_lastChanges"]=="on") {
    $tikilib->set_preference("feature_lastChanges",'y'); 
    $smarty->assign("feature_lastChanges",'y');
  } else {
    $tikilib->set_preference("feature_lastChanges",'n');
    $smarty->assign("feature_lastChanges",'n');
  }

  if(isset($_REQUEST["feature_wiki_comments"]) && $_REQUEST["feature_wiki_comments"]=="on") {
    $tikilib->set_preference("feature_wiki_comments",'y'); 
    $smarty->assign("feature_wiki_comments",'y');
  } else {
    $tikilib->set_preference("feature_wiki_comments",'n');
    $smarty->assign("feature_wiki_comments",'n');
  }
  
  
  
  if(isset($_REQUEST["feature_wiki_pictures"]) && $_REQUEST["feature_wiki_pictures"]=="on") {
    $tikilib->set_preference("feature_wiki_pictures",'y'); 
    $smarty->assign("feature_wiki_pictures",'y');
  } else {
    $tikilib->set_preference("feature_wiki_pictures",'n');
    $smarty->assign("feature_wiki_pictures",'n');
  }
  
  if(isset($_REQUEST["feature_wiki_description"]) && $_REQUEST["feature_wiki_description"]=="on") {
    $tikilib->set_preference("feature_wiki_description",'y'); 
    $smarty->assign("feature_wiki_description",'y');
  } else {
    $tikilib->set_preference("feature_wiki_description",'n');
    $smarty->assign("feature_wiki_description",'n');
  }
      
  if(isset($_REQUEST["wiki_spellcheck"]) && $_REQUEST["wiki_spellcheck"]=="on") {
    $tikilib->set_preference("wiki_spellcheck",'y'); 
    $smarty->assign("wiki_spellcheck",'y');
  } else {
    $tikilib->set_preference("wiki_spellcheck",'n');
    $smarty->assign("wiki_spellcheck",'n');
  }

  if(isset($_REQUEST["feature_warn_on_edit"]) && $_REQUEST["feature_warn_on_edit"]=="on") {
    $tikilib->set_preference("feature_warn_on_edit",'y'); 
    $smarty->assign("feature_warn_on_edit",'y');
  } else {
    $tikilib->set_preference("feature_warn_on_edit",'n');
    $smarty->assign("feature_warn_on_edit",'n');
  }
  
  if(isset($_REQUEST["feature_page_title"]) && $_REQUEST["feature_page_title"]=="on") {
    $tikilib->set_preference("feature_page_title",'y'); 
    $smarty->assign("feature_page_title",'y');
  } else {
    $tikilib->set_preference("feature_page_title",'n');
    $smarty->assign("feature_page_title",'n');
  }
  
  $tikilib->set_preference('wiki_cache',$_REQUEST["wiki_cache"]);
  $smarty->assign('wiki_cache',$_REQUEST["wiki_cache"]);
  
  $tikilib->set_preference("warn_on_edit_time",$_REQUEST["warn_on_edit_time"]);
  $smarty->assign('warn_on_edit_time',$_REQUEST["warn_on_edit_time"]);
  
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
  
  if(isset($_REQUEST["feature_wiki_footnotes"]) && $_REQUEST["feature_wiki_footnotes"]=="on") {
    $tikilib->set_preference("feature_wiki_footnotes",'y'); 
    $smarty->assign("feature_wiki_footnotes",'y');
  } else {
    $tikilib->set_preference("feature_wiki_footnotes",'n');
    $smarty->assign("feature_wiki_footnotes",'n');
  }
/*  
  if(isset($_REQUEST["feature_wiki_notepad"]) && $_REQUEST["feature_wiki_notepad"]=="on") {
    $tikilib->set_preference("feature_wiki_notepad",'y'); 
    $smarty->assign("feature_wiki_notepad",'y');
  } else {
    $tikilib->set_preference("feature_wiki_notepad",'n');
    $smarty->assign("feature_wiki_notepad",'n');
  }
*/
  if(isset($_REQUEST["feature_wiki_usrlock"]) && $_REQUEST["feature_wiki_usrlock"]=="on") {
    $tikilib->set_preference("feature_wiki_usrlock",'y'); 
    $smarty->assign("feature_wiki_usrlock",'y');
  } else {
    $tikilib->set_preference("feature_wiki_usrlock",'n');
    $smarty->assign("feature_wiki_usrlock",'n');
  }

  
  $tikilib->set_preference('feature_wiki_tables',$_REQUEST['feature_wiki_tables']);
  $smarty->assign('feature_wiki_tables',$_REQUEST['feature_wiki_tables']);
  
  
  if(isset($_REQUEST["feature_wiki_undo"]) && $_REQUEST["feature_wiki_undo"]=="on") {
    $tikilib->set_preference("feature_wiki_undo",'y'); 
    $smarty->assign("feature_wiki_undo",'y');
  } else {
    $tikilib->set_preference("feature_wiki_undo",'n');
    $smarty->assign("feature_wiki_undo",'n');
  }
  
  if(isset($_REQUEST["feature_wiki_templates"]) && $_REQUEST["feature_wiki_templates"]=="on") {
    $tikilib->set_preference("feature_wiki_templates",'y'); 
    $smarty->assign("feature_wiki_templates",'y');
  } else {
    $tikilib->set_preference("feature_wiki_templates",'n');
    $smarty->assign("feature_wiki_templates",'n');
  }


  if(isset($_REQUEST["feature_wiki_multiprint"]) && $_REQUEST["feature_wiki_multiprint"]=="on") {
    $tikilib->set_preference("feature_wiki_multiprint",'y'); 
    $smarty->assign("feature_wiki_multiprint",'y');
  } else {
    $tikilib->set_preference("feature_wiki_multiprint",'n');
    $smarty->assign("feature_wiki_multiprint",'n');
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
  
  $tikilib->set_preference("gal_use_db",$_REQUEST["gal_use_db"]);
  $smarty->assign('gal_use_db',$_REQUEST["gal_use_db"]);
  $tikilib->set_preference("gal_use_lib",$_REQUEST["gal_use_lib"]);
  $smarty->assign('gal_use_lib',$_REQUEST["gal_use_lib"]);
  $tikilib->set_preference("gal_use_dir",$_REQUEST["gal_use_dir"]);
  $smarty->assign('gal_use_dir',$_REQUEST["gal_use_dir"]);
  
  $tikilib->set_preference("gal_match_regex",$_REQUEST["gal_match_regex"]);
  $smarty->assign('gal_match_regex',$_REQUEST["gal_match_regex"]);
  $tikilib->set_preference("gal_nmatch_regex",$_REQUEST["gal_nmatch_regex"]);
  $smarty->assign('gal_nmatch_regex',$_REQUEST["gal_nmatch_regex"]);
  
  if(isset($_REQUEST["feature_image_galleries_comments"]) && $_REQUEST["feature_image_galleries_comments"]=="on") {
    $tikilib->set_preference("feature_image_galleries_comments",'y'); 
    $smarty->assign("feature_image_galleries_comments",'y');
  } else {
    $tikilib->set_preference("feature_image_galleries_comments",'n');
    $smarty->assign("feature_image_galleries_comments",'n');
  }
}

if(isset($_REQUEST["filegalfeatures"])) {
      
  if(isset($_REQUEST["feature_file_galleries_rankings"]) && $_REQUEST["feature_file_galleries_rankings"]=="on") {
    $tikilib->set_preference("feature_file_galleries_rankings",'y'); 
    $smarty->assign("feature_file_galleries_rankings",'y');
  } else {
    $tikilib->set_preference("feature_file_galleries_rankings",'n');
    $smarty->assign("feature_file_galleries_rankings",'n');
  }

  $tikilib->set_preference("fgal_match_regex",$_REQUEST["fgal_match_regex"]);
  $smarty->assign('fgal_match_regex',$_REQUEST["fgal_match_regex"]);
  $tikilib->set_preference("fgal_nmatch_regex",$_REQUEST["fgal_nmatch_regex"]);
  $smarty->assign('fgal_nmatch_regex',$_REQUEST["fgal_nmatch_regex"]);
  
  $tikilib->set_preference("fgal_use_db",$_REQUEST["fgal_use_db"]);
  $smarty->assign('fgal_use_db',$_REQUEST["fgal_use_db"]);
  $tikilib->set_preference("fgal_use_dir",$_REQUEST["fgal_use_dir"]);
  $smarty->assign('fgal_use_dir',$_REQUEST["fgal_use_dir"]);
  
  if(isset($_REQUEST["feature_file_galleries_comments"]) && $_REQUEST["feature_file_galleries_comments"]=="on") {
    $tikilib->set_preference("feature_file_galleries_comments",'y'); 
    $smarty->assign("feature_file_galleries_comments",'y');
  } else {
    $tikilib->set_preference("feature_file_galleries_comments",'n');
    $smarty->assign("feature_file_galleries_comments",'n');
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
  
  if(isset($_REQUEST["cms_spellcheck"]) && $_REQUEST["cms_spellcheck"]=="on") {
    $tikilib->set_preference("cms_spellcheck",'y'); 
    $smarty->assign("cms_spellcheck",'y');
  } else {
    $tikilib->set_preference("cms_spellcheck",'n');
    $smarty->assign("cms_spellcheck",'n');
  }
  
  if(isset($_REQUEST["feature_article_comments"]) && $_REQUEST["feature_article_comments"]=="on") {
    $tikilib->set_preference("feature_article_comments",'y'); 
    $smarty->assign("feature_article_comments",'y');
  } else {
    $tikilib->set_preference("feature_article_comments",'n');
    $smarty->assign("feature_article_comments",'n');
  }
  
  if(isset($_REQUEST["feature_cms_templates"]) && $_REQUEST["feature_cms_templates"]=="on") {
    $tikilib->set_preference("feature_cms_templates",'y'); 
    $smarty->assign("feature_cms_templates",'y');
  } else {
    $tikilib->set_preference("feature_cms_templates",'n');
    $smarty->assign("feature_cms_templates",'n');
  }
}

if(isset($_REQUEST['bloglistconf'])) {
  if(isset($_REQUEST["blog_list_title"]) && $_REQUEST["blog_list_title"]=="on") {
    $tikilib->set_preference("blog_list_title",'y'); 
    $smarty->assign("blog_list_title",'y');
  } else {
    $tikilib->set_preference("blog_list_title",'n');
    $smarty->assign("blog_list_title",'n');
  }

  if(isset($_REQUEST["blog_list_description"]) && $_REQUEST["blog_list_description"]=="on") {
    $tikilib->set_preference("blog_list_description",'y'); 
    $smarty->assign("blog_list_description",'y');
  } else {
    $tikilib->set_preference("blog_list_description",'n');
    $smarty->assign("blog_list_description",'n');
  }

  if(isset($_REQUEST["blog_list_activity"]) && $_REQUEST["blog_list_activity"]=="on") {
    $tikilib->set_preference("blog_list_activity",'y'); 
    $smarty->assign("blog_list_activity",'y');
  } else {
    $tikilib->set_preference("blog_list_activity",'n');
    $smarty->assign("blog_list_activity",'n');
  }


  if(isset($_REQUEST["blog_list_visits"]) && $_REQUEST["blog_list_visits"]=="on") {
    $tikilib->set_preference("blog_list_visits",'y'); 
    $smarty->assign("blog_list_visits",'y');
  } else {
    $tikilib->set_preference("blog_list_visits",'n');
    $smarty->assign("blog_list_visits",'n');
  }


  if(isset($_REQUEST["blog_list_posts"]) && $_REQUEST["blog_list_posts"]=="on") {
    $tikilib->set_preference("blog_list_posts",'y'); 
    $smarty->assign("blog_list_posts",'y');
  } else {
    $tikilib->set_preference("blog_list_posts",'n');
    $smarty->assign("blog_list_posts",'n');
  }


  if(isset($_REQUEST["blog_list_lastmodif"]) && $_REQUEST["blog_list_lastmodif"]=="on") {
    $tikilib->set_preference("blog_list_lastmodif",'y'); 
    $smarty->assign("blog_list_lastmodif",'y');
  } else {
    $tikilib->set_preference("blog_list_lastmodif",'n');
    $smarty->assign("blog_list_lastmodif",'n');
  }

  if(isset($_REQUEST["blog_list_user"]) && $_REQUEST["blog_list_user"]=="on") {
    $tikilib->set_preference("blog_list_user",'y'); 
    $smarty->assign("blog_list_user",'y');
  } else {
    $tikilib->set_preference("blog_list_user",'n');
    $smarty->assign("blog_list_user",'n');
  }


  if(isset($_REQUEST["blog_list_created"]) && $_REQUEST["blog_list_created"]=="on") {
    $tikilib->set_preference("blog_list_created",'y'); 
    $smarty->assign("blog_list_created",'y');
  } else {
    $tikilib->set_preference("blog_list_created",'n');
    $smarty->assign("blog_list_created",'n');
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
  
  if(isset($_REQUEST["blog_spellcheck"]) && $_REQUEST["blog_spellcheck"]=="on") {
    $tikilib->set_preference("blog_spellcheck",'y'); 
    $smarty->assign("blog_spellcheck",'y');
  } else {
    $tikilib->set_preference("blog_spellcheck",'n');
    $smarty->assign("blog_spellcheck",'n');
  }
  
  if(isset($_REQUEST["feature_blog_comments"]) && $_REQUEST["feature_blog_comments"]=="on") {
    $tikilib->set_preference("feature_blog_comments",'y'); 
    $smarty->assign("feature_blog_comments",'y');
  } else {
    $tikilib->set_preference("feature_blog_comments",'n');
    $smarty->assign("feature_blog_comments",'n');
  }
  
  if(isset($_REQUEST["feature_blogposts_comments"]) && $_REQUEST["feature_blogposts_comments"]=="on") {
    $tikilib->set_preference("feature_blogposts_comments",'y'); 
    $smarty->assign("feature_blogposts_comments",'y');
  } else {
    $tikilib->set_preference("feature_blogposts_comments",'n');
    $smarty->assign("feature_blogposts_comments",'n');
  }
  
  $tikilib->set_preference("blog_list_order",$_REQUEST["blog_list_order"]);
  $tikilib->set_preference("blog_list_user",$_REQUEST["blog_list_user"]);
  $smarty->assign('blog_list_order',$_REQUEST["blog_list_order"]);
  $smarty->assign('blog_list_user',$_REQUEST['blog_list_user']);
}

if(isset($_REQUEST["blogset"])&&isset($_REQUEST["homeBlog"])) {
  $tikilib->set_preference("home_blog",$_REQUEST["homeBlog"]);
  $smarty->assign('home_blog',$_REQUEST["homeBlog"]);
}

if(isset($_REQUEST["galset"])&&isset($_REQUEST["homeGallery"])) {
  $tikilib->set_preference("home_gallery",$_REQUEST["homeGallery"]);
  $smarty->assign('home_gallery',$_REQUEST["homeGallery"]);
}

if(isset($_REQUEST["filegalset"])) {
  $tikilib->set_preference("home_file_gallery",$_REQUEST["homeFileGallery"]);
  $smarty->assign('home_file_gallery',$_REQUEST["homeFileGallery"]);
}


if(isset($_REQUEST["features"])) {
  if(isset($_REQUEST["feature_wiki"]) && $_REQUEST["feature_wiki"]=="on") {
    $tikilib->set_preference("feature_wiki",'y'); 
    $smarty->assign("feature_wiki",'y');
  } else {
    $tikilib->set_preference("feature_wiki",'n');
    $smarty->assign("feature_wiki",'n');
  }
  
  if(isset($_REQUEST["feature_chat"]) && $_REQUEST["feature_chat"]=="on") {
    $tikilib->set_preference("feature_chat",'y'); 
    $smarty->assign("feature_chat",'y');
  } else {
    $tikilib->set_preference("feature_chat",'n');
    $smarty->assign("feature_chat",'n');
  }
  
  if(isset($_REQUEST["feature_polls"]) && $_REQUEST["feature_polls"]=="on") {
    $tikilib->set_preference("feature_polls",'y'); 
    $smarty->assign("feature_polls",'y');
  } else {
    $tikilib->set_preference("feature_polls",'n');
    $smarty->assign("feature_polls",'n');
  }
  
  if(isset($_REQUEST["feature_custom_home"]) && $_REQUEST["feature_custom_home"]=="on") {
    $tikilib->set_preference("feature_custom_home",'y'); 
    $smarty->assign("feature_custom_home",'y');
  } else {
    $tikilib->set_preference("feature_custom_home",'n');
    $smarty->assign("feature_custom_home",'n');
  }
  
  if(isset($_REQUEST["feature_forums"]) && $_REQUEST["feature_forums"]=="on") {
    $tikilib->set_preference("feature_forums",'y'); 
    $smarty->assign("feature_forums",'y');
  } else {
    $tikilib->set_preference("feature_forums",'n');
    $smarty->assign("feature_forums",'n');
  }
  
  if(isset($_REQUEST["feature_file_galleries"]) && $_REQUEST["feature_file_galleries"]=="on") {
    $tikilib->set_preference("feature_file_galleries",'y'); 
    $smarty->assign("feature_file_galleries",'y');
  } else {
    $tikilib->set_preference("feature_file_galleries",'n');
    $smarty->assign("feature_file_galleries",'n');
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
  
  if(isset($_REQUEST["feature_trackers"]) && $_REQUEST["feature_trackers"]=="on") {
    $tikilib->set_preference("feature_trackers",'y'); 
    $smarty->assign("feature_trackers",'y');
  } else {
    $tikilib->set_preference("feature_trackers",'n');
    $smarty->assign("feature_trackers",'n');
  }
  
  if(isset($_REQUEST["feature_directory"]) && $_REQUEST["feature_directory"]=="on") {
    $tikilib->set_preference("feature_directory",'y'); 
    $smarty->assign("feature_directory",'y');
  } else {
    $tikilib->set_preference("feature_directory",'n');
    $smarty->assign("feature_directory",'n');
  }
  
  if(isset($_REQUEST["feature_newsreader"]) && $_REQUEST["feature_newsreader"]=="on") {
    $tikilib->set_preference("feature_newsreader",'y'); 
    $smarty->assign("feature_newsreader",'y');
  } else {
    $tikilib->set_preference("feature_newsreader",'n');
    $smarty->assign("feature_newsreader",'n');
  }
  
  if(isset($_REQUEST["feature_notepad"]) && $_REQUEST["feature_notepad"]=="on") {
    $tikilib->set_preference("feature_notepad",'y'); 
    $smarty->assign("feature_notepad",'y');
  } else {
    $tikilib->set_preference("feature_notepad",'n');
    $smarty->assign("feature_notepad",'n');
  }

  if(isset($_REQUEST["feature_userfiles"]) && $_REQUEST["feature_userfiles"]=="on") {
    $tikilib->set_preference("feature_userfiles",'y'); 
    $smarty->assign("feature_userfiles",'y');
  } else {
    $tikilib->set_preference("feature_userfiles",'n');
    $smarty->assign("feature_userfiles",'n');
  }

  if(isset($_REQUEST["feature_usermenu"]) && $_REQUEST["feature_usermenu"]=="on") {
    $tikilib->set_preference("feature_usermenu",'y'); 
    $smarty->assign("feature_usermenu",'y');
  } else {
    $tikilib->set_preference("feature_usermenu",'n');
    $smarty->assign("feature_usermenu",'n');
  }
  
  if(isset($_REQUEST["feature_minical"]) && $_REQUEST["feature_minical"]=="on") {
    $tikilib->set_preference("feature_minical",'y'); 
    $smarty->assign("feature_minical",'y');
  } else {
    $tikilib->set_preference("feature_minical",'n');
    $smarty->assign("feature_minical",'n');
  }



  if(isset($_REQUEST["feature_theme_control"]) && $_REQUEST["feature_theme_control"]=="on") {
    $tikilib->set_preference("feature_theme_control",'y'); 
    $smarty->assign("feature_theme_control",'y');
  } else {
    $tikilib->set_preference("feature_theme_control",'n');
    $smarty->assign("feature_theme_control",'n');
  }
  
  if(isset($_REQUEST["feature_workflow"]) && $_REQUEST["feature_workflow"]=="on") {
    $tikilib->set_preference("feature_workflow",'y'); 
    $smarty->assign("feature_workflow",'y');
  } else {
    $tikilib->set_preference("feature_workflow",'n');
    $smarty->assign("feature_workflow",'n');
  }

  if(isset($_REQUEST["feature_user_watches"]) && $_REQUEST["feature_user_watches"]=="on") {
    $tikilib->set_preference("feature_user_watches",'y'); 
    $smarty->assign("feature_user_watches",'y');
  } else {
    $tikilib->set_preference("feature_user_watches",'n');
    $smarty->assign("feature_user_watches",'n');
  }


  if(isset($_REQUEST["feature_charts"]) && $_REQUEST["feature_charts"]=="on") {
    $tikilib->set_preference("feature_charts",'y'); 
    $smarty->assign("feature_charts",'y');
  } else {
    $tikilib->set_preference("feature_charts",'n');
    $smarty->assign("feature_charts",'n');
  }


  if(isset($_REQUEST["feature_phpopentracker"]) && $_REQUEST["feature_phpopentracker"]=="on") {
    $tikilib->set_preference("feature_phpopentracker",'y'); 
    $smarty->assign("feature_phpopentracker",'y');
  } else {
    $tikilib->set_preference("feature_phpopentracker",'n');
    $smarty->assign("feature_phpopentracker",'n');
  }

  
  if(isset($_REQUEST["feature_eph"]) && $_REQUEST["feature_eph"]=="on") {
    $tikilib->set_preference("feature_eph",'y'); 
    $smarty->assign("feature_eph",'y');
  } else {
    $tikilib->set_preference("feature_eph",'n');
    $smarty->assign("feature_eph",'n');
  }

  if(isset($_REQUEST["feature_live_support"]) && $_REQUEST["feature_live_support"]=="on") {
    $tikilib->set_preference("feature_live_support",'y'); 
    $smarty->assign("feature_live_support",'y');
  } else {
    $tikilib->set_preference("feature_live_support",'n');
    $smarty->assign("feature_live_support",'n');
  }
  
  if(isset($_REQUEST["feature_contact"]) && $_REQUEST["feature_contact"]=="on") {
    $tikilib->set_preference("feature_contact",'y'); 
    $smarty->assign("feature_contact",'y');
  } else {
    $tikilib->set_preference("feature_contact",'n');
    $smarty->assign("feature_contact",'n');
  }
  
  if(isset($_REQUEST["feature_messages"]) && $_REQUEST["feature_messages"]=="on") {
    $tikilib->set_preference("feature_messages",'y'); 
    $smarty->assign("feature_messages",'y');
  } else {
    $tikilib->set_preference("feature_messages",'n');
    $smarty->assign("feature_messages",'n');
  }
  
  if(isset($_REQUEST["feature_tasks"]) && $_REQUEST["feature_tasks"]=="on") {
    $tikilib->set_preference("feature_tasks",'y'); 
    $smarty->assign("feature_tasks",'y');
  } else {
    $tikilib->set_preference("feature_tasks",'n');
    $smarty->assign("feature_tasks",'n');
  }
  
  if(isset($_REQUEST["feature_webmail"]) && $_REQUEST["feature_webmail"]=="on") {
    $tikilib->set_preference("feature_webmail",'y'); 
    $smarty->assign("feature_webmail",'y');
  } else {
    $tikilib->set_preference("feature_webmail",'n');
    $smarty->assign("feature_webmail",'n');
  }
  
  if(isset($_REQUEST["feature_surveys"]) && $_REQUEST["feature_surveys"]=="on") {
    $tikilib->set_preference("feature_surveys",'y'); 
    $smarty->assign("feature_surveys",'y');
  } else {
    $tikilib->set_preference("feature_surveys",'n');
    $smarty->assign("feature_surveys",'n');
  }
  
  if(isset($_REQUEST["feature_newsletters"]) && $_REQUEST["feature_newsletters"]=="on") {
    $tikilib->set_preference("feature_newsletters",'y'); 
    $smarty->assign("feature_newsletters",'y');
  } else {
    $tikilib->set_preference("feature_newsletters",'n');
    $smarty->assign("feature_newsletters",'n');
  }
  
  if(isset($_REQUEST["feature_drawings"]) && $_REQUEST["feature_drawings"]=="on") {
    $tikilib->set_preference("feature_drawings",'y'); 
    $smarty->assign("feature_drawings",'y');
  } else {
    $tikilib->set_preference("feature_drawings",'n');
    $smarty->assign("feature_drawings",'n');
  }
  
  if(isset($_REQUEST["feature_html_pages"]) && $_REQUEST["feature_html_pages"]=="on") {
    $tikilib->set_preference("feature_html_pages",'y'); 
    $smarty->assign("feature_html_pages",'y');
  } else {
    $tikilib->set_preference("feature_html_pages",'n');
    $smarty->assign("feature_html_pages",'n');
  }
  
  if(isset($_REQUEST["feature_search_stats"]) && $_REQUEST["feature_search_stats"]=="on") {
    $tikilib->set_preference("feature_search_stats",'y'); 
    $smarty->assign("feature_search_stats",'y');
  } else {
    $tikilib->set_preference("feature_search_stats",'n');
    $smarty->assign("feature_search_stats",'n');
  }
  
  if(isset($_REQUEST["feature_referer_stats"]) && $_REQUEST["feature_referer_stats"]=="on") {
    $tikilib->set_preference("feature_referer_stats",'y'); 
    $smarty->assign("feature_referer_stats",'y');
  } else {
    $tikilib->set_preference("feature_referer_stats",'n');
    $smarty->assign("feature_referer_stats",'n');
  }
  
  if(isset($_REQUEST["feature_categories"]) && $_REQUEST["feature_categories"]=="on") {
    $tikilib->set_preference("feature_categories",'y'); 
    $smarty->assign("feature_categories",'y');
  } else {
    $tikilib->set_preference("feature_categories",'n');
    $smarty->assign("feature_categories",'n');
  }
  
  if(isset($_REQUEST["feature_faqs"]) && $_REQUEST["feature_faqs"]=="on") {
    $tikilib->set_preference("feature_faqs",'y'); 
    $smarty->assign("feature_faqs",'y');
  } else {
    $tikilib->set_preference("feature_faqs",'n');
    $smarty->assign("feature_faqs",'n');
  }
  
  if(isset($_REQUEST["feature_shoutbox"]) && $_REQUEST["feature_shoutbox"]=="on") {
    $tikilib->set_preference("feature_shoutbox",'y'); 
    $smarty->assign("feature_shoutbox",'y');
  } else {
    $tikilib->set_preference("feature_shoutbox",'n');
    $smarty->assign("feature_shoutbox",'n');
  }
  
  if(isset($_REQUEST["feature_quizzes"]) && $_REQUEST["feature_quizzes"]=="on") {
    $tikilib->set_preference("feature_quizzes",'y'); 
    $smarty->assign("feature_quizzes",'y');
  } else {
    $tikilib->set_preference("feature_quizzes",'n');
    $smarty->assign("feature_quizzes",'n');
  }
  
  if(isset($_REQUEST["feature_smileys"]) && $_REQUEST["feature_smileys"]=="on") {
    $tikilib->set_preference("feature_smileys",'y'); 
    $smarty->assign("feature_smileys",'y');
  } else {
    $tikilib->set_preference("feature_smileys",'n');
    $smarty->assign("feature_smileys",'n');
  }
  
  if(isset($_REQUEST["feature_stats"]) && $_REQUEST["feature_stats"]=="on") {
    $tikilib->set_preference("feature_stats",'y'); 
    $smarty->assign("feature_stats",'y');
  } else {
    $tikilib->set_preference("feature_stats",'n');
    $smarty->assign("feature_stats",'n');
  }
  
  if(isset($_REQUEST["feature_games"]) && $_REQUEST["feature_games"]=="on") {
    $tikilib->set_preference("feature_games",'y'); 
    $smarty->assign("feature_games",'y');
  } else {
    $tikilib->set_preference("feature_games",'n');
    $smarty->assign("feature_games",'n');
  }
  
  if(isset($_REQUEST["user_assigned_modules"]) && $_REQUEST["user_assigned_modules"]=="on") {
    $tikilib->set_preference("user_assigned_modules",'y'); 
    $smarty->assign("user_assigned_modules",'y');
  } else {
    $tikilib->set_preference("user_assigned_modules",'n');
    $smarty->assign("user_assigned_modules",'n');
  }
  
  if(isset($_REQUEST["feature_user_bookmarks"]) && $_REQUEST["feature_user_bookmarks"]=="on") {
    $tikilib->set_preference("feature_user_bookmarks",'y'); 
    $smarty->assign("feature_user_bookmarks",'y');
  } else {
    $tikilib->set_preference("feature_user_bookmarks",'n');
    $smarty->assign("feature_user_bookmarks",'n');
  }
  
  if(isset($_REQUEST["feature_comm"]) && $_REQUEST["feature_comm"]=="on") {
    $tikilib->set_preference("feature_comm",'y'); 
    $smarty->assign("feature_comm",'y');
  } else {
    $tikilib->set_preference("feature_comm",'n');
    $smarty->assign("feature_comm",'n');
  }

  if(isset($_REQUEST["feature_search"]) && $_REQUEST["feature_search"]=="on") {
    $tikilib->set_preference("feature_search",'y'); 
    $smarty->assign("feature_search",'y');
  } else {
    $tikilib->set_preference("feature_search",'n');
    $smarty->assign("feature_search",'n');
  }

  $b = (isset($_REQUEST["feature_search_fulltext"]) && $_REQUEST["feature_search_fulltext"]=="on") ? 'y' : 'n';
  $tikilib->set_preference("feature_search_fulltext", $b); 
  $smarty->assign("feature_search_fulltext", $b);

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
  
  if(isset($_REQUEST["feature_hotwords_nw"]) && $_REQUEST["feature_hotwords_nw"]=="on") {
    $tikilib->set_preference("feature_hotwords_nw",'y'); 
    $smarty->assign("feature_hotwords_nw",'y');
  } else {
    $tikilib->set_preference("feature_hotwords_nw",'n');
    $smarty->assign("feature_hotwords_nw",'n');
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
      $smarty->display("styles/$style_base/error.tpl");
      die;  
  }
  $tikilib->create_tag($_REQUEST["tagname"]);  
}
if(isset($_REQUEST["restoretag"])) {
  // Check existance
  if(!$tikilib->tag_exists($_REQUEST["tagname"])) {
      $smarty->assign('msg',tra("Tag not found"));
      $smarty->display("styles/$style_base/error.tpl");
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
     $smarty->display("styles/$style_base/error.tpl");
     die;    
  }
  $userlib->change_user_password('admin',$_REQUEST["adminpass"]);
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

$slide_styles=Array();
$h=opendir("styles/slideshows");
while($file=readdir($h)) {
  if(strstr($file,"css")) {
    $slide_styles[]=$file;
  }
}
closedir($h);
$smarty->assign_by_ref('slide_styles',$slide_styles);

$languages=Array();
$h=opendir("lang/");
while($file=readdir($h)) {
  if($file!='.' && $file!='..' && is_dir('lang/'.$file) && strlen($file)==2) {
    $languages[]=$file;
  }
}
closedir($h);
$smarty->assign_by_ref('languages',$languages);
$smarty->assign('site_language',$tikilib->get_preference("language",'en'));

$smarty->assign('lang_use_db',$tikilib->get_preference("lang_use_db",'n'));

$timezone_options = $tikilib->get_timezone_list(false);
$smarty->assign_by_ref('timezone_options',$timezone_options);

$server_time = new Date();
$display_timezone = $tikilib->get_preference('display_timezone', $server_time->tz->getID());
$smarty->assign_by_ref('display_timezone',$display_timezone);

$timezone_server = $timezone_options[$server_time->tz->getID()];
$smarty->assign_by_ref('timezone_server',$timezone_server);

$long_date_format = $tikilib->get_preference('long_date_format', '%A %d ' . tra('of') . ' %B, %Y');
$short_date_format = $tikilib->get_preference('short_date_format', '%a %d ' . tra('of') . ' %b, %Y');
$long_time_format = $tikilib->get_preference('long_time_format', '%H:%M:%S %Z');
$short_time_format = $tikilib->get_preference('short_time_format', '%H:%M %Z');
$smarty->assign_by_ref('short_date_format',$short_date_format);
$smarty->assign_by_ref('long_date_format',$long_date_format);
$smarty->assign_by_ref('short_time_format',$short_time_format);
$smarty->assign_by_ref('long_time_format',$long_time_format);



$smarty->assign('rememberme',$tikilib->get_preference('rememberme','disabled'));
$smarty->assign('remembertime',$tikilib->get_preference('remembertime',7200));


include_once("lib/commentslib.php");
$commentslib = new Comments($dbTiki);
$forums = $commentslib->list_forums(0,-1,'name_desc','');
$smarty->assign_by_ref('forums',$forums["data"]);
$blogs=$tikilib->list_blogs(0,-1,'created_desc','');
$smarty->assign_by_ref('blogs',$blogs["data"]);
$galleries = $tikilib->list_visible_galleries(0, -1, 'name_desc', 'admin','');
$file_galleries = $tikilib->list_visible_file_galleries(0, -1, 'name_desc', 'admin','');
$smarty->assign_by_ref('galleries',$galleries["data"]);
$smarty->assign_by_ref('file_galleries',$file_galleries["data"]);

$tags = $tikilib->get_tags();
$smarty->assign_by_ref("tags",$tags);

// Preferences to load
// anonCanEdit
// maxVersions


$home_blog = $tikilib->get_preference("home_blog",0);
$home_forum = $tikilib->get_preference("home_forum",0);
$home_gallery = $tikilib->get_preference("home_gallery",0);
$home_file_gallery = $tikilib->get_preference("home_file_gallery",0);
$smarty->assign('home_forum_url','tiki-view_forum.php?forumId='.$home_forum);
$smarty->assign('home_blog_url','tiki-view_blog.php?blogId='.$home_blog);
$smarty->assign('home_gallery_url','tiki-browse_gallery.php?galleryId='.$home_gallery);
$smarty->assign('home_file_gallery_url','tiki-list_file_gallery.php?galleryId='.$home_file_gallery);
$smarty->assign('home_blog_name','');
$smarty->assign('home_gal_name','');
$smarty->assign('home_forum_name','');
$smarty->assign('home_fil_name','');
if($home_forum) {
  $hforuminfo = $commentslib->get_forum($home_forum);
  $smarty->assign('home_forum_name',substr($hforuminfo["name"],0,20));
}
if($home_blog) {
  $hbloginfo = $tikilib->get_blog($home_blog);
  $smarty->assign('home_blog_name',substr($hbloginfo["title"],0,20));
}
if($home_gallery) {
  $hgalinfo = $imagegallib->get_gallery($home_gallery);
  $smarty->assign('home_gal_name',substr($hgalinfo["name"],0,20));
}
if($home_file_gallery) {
  $hgalinfo = $imagegallib->get_gallery($home_file_gallery);
  $smarty->assign('home_fil_name',substr($hgalinfo["name"],0,20));
}
$anonCanEdit = $tikilib->get_preference("anonCanEdit",'n');
$allowRegister = $tikilib->get_preference("allowRegister",'n');
$useRegisterPasscode = $tikilib->get_preference("useRegisterPasscode",'n');
$registerPasscode = $tikilib->get_preference("registerPasscode",'');
$useUrlIndex = $tikilib->get_preference("useUrlIndex",'n');
$urlIndex = $tikilib->get_preference("urlIndex",'');

$validateUsers = $tikilib->get_preference("validateUsers",'n');
$forgotPass = $tikilib->get_preference("forgotPass",'n');
$maxVersions = $tikilib->get_preference("maxVersions", 0);
$keep_versions = $tikilib->get_preference("keep_versions", 1);
$maxRecords = $tikilib->get_preference("maxRecords",10);
$title = $tikilib->get_preference("title","");
$popupLinks = $tikilib->get_preference("popupLinks",'n');
$gal_match_regex = $tikilib->get_preference('gal_match_regex','');
$smarty->assign('gal_match_regex',$gal_match_regex);
$smarty->assign_by_ref('popupLinks',$popupLinks);
$smarty->assign_by_ref('anonCanEdit',$anonCanEdit);
$smarty->assign_by_ref('allowRegister',$allowRegister);
$smarty->assign_by_ref('useRegisterPasscode',$useRegisterPasscode);
$smarty->assign_by_ref('registerPasscode',$registerPasscode);
$smarty->assign_by_ref('urlIndex',$urlIndex);
$smarty->assign_by_ref('useUrlIndex',$useUrlIndex);
$smarty->assign_by_ref('forgotPass',$forgotPass);
$smarty->assign_by_ref('validateUsers',$validateUsers);
$smarty->assign_by_ref('maxVersions',$maxVersions);
$smarty->assign_by_ref('keep_versions',$keep_versions);
$smarty->assign_by_ref('title',$title);
$smarty->assign_by_ref('maxRecords',$maxRecords);

// The style here is the style for the site not for the theme
$style_site = $tikilib->get_preference("style",'default.css'); 
$smarty->assign_by_ref('style_site',$style_site);


// Display the template
$smarty->assign('mid','tiki-admin.tpl');
$smarty->display("styles/$style_base/tiki.tpl");


?>
