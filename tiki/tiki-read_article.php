<?php
// Initialization
require_once('tiki-setup.php');

if(!isset($_REQUEST["articleId"])) {
  $smarty->assign('msg',tra("No article indicated"));
  $smarty->display('error.tpl');
  die;  

}

if(isset($_REQUEST["articleId"])) {
  $tikilib->add_article_hit($_REQUEST["articleId"]);
  $smarty->assign('articleId',$_REQUEST["articleId"]);
  $article_data = $tikilib->get_article($_REQUEST["articleId"]);
  if(!$article_data) {
    $smarty->assign('msg',tra("Article not found"));
    $smarty->display('error.tpl');
    die;  
  }
  if( ($article_data["publishDate"]>date("U")) && ($tiki_p_admin != 'y') ){
    $smarty->assign('msg',tra("Article is not published yet"));
    $smarty->display('error.tpl');
    die;  
  }
  $smarty->assign('title',$article_data["title"]);
  $smarty->assign('authorName',$article_data["authorName"]);
  $smarty->assign('topicId',$article_data["topicId"]);
  $smarty->assign('useImage',$article_data["useImage"]);
  $smarty->assign('image_name',$article_data["image_name"]);
  $smarty->assign('image_type',$article_data["image_type"]);
  $smarty->assign('image_size',$article_data["image_size"]);
  $smarty->assign('image_x',$article_data["image_x"]);
  $smarty->assign('image_x',$article_data["image_y"]);
  $smarty->assign('image_data',urlencode($article_data["image_data"]));
  $smarty->assign('reads',$article_data["reads"]);
  $smarty->assign('size',$article_data["size"]);
  if(strlen($article_data["image_data"])>0) {
    $smarty->assign('hasImage','y');
    $hasImage='y';
  }
  $smarty->assign('heading',$article_data["heading"]);
  $smarty->assign('body',$article_data["body"]);
  $smarty->assign('publishDate',$article_data["publishDate"]);
  $smarty->assign('edit_data','y');
  
  $body = $article_data["body"];
  $heading = $article_data["heading"]; 
  $smarty->assign('parsed_body',$tikilib->parse_data($body));
  $smarty->assign('parsed_heading',$tikilib->parse_data($heading));
}




// Display the Index Template
$smarty->assign('mid','tiki-read_article.tpl');
$smarty->assign('show_page_bar','n');
$smarty->display('tiki.tpl');
?>