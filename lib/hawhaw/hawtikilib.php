<?php

// HAWHAW function library for TikiWiki
// Last modified: 9. July 2003

require_once("lib/hawhaw/hawhaw.inc");
require_once("lib/hawhaw/hawiki_cfg.inc");
require_once("lib/hawhaw/hawiki_parser.inc");
require_once("lib/hawhaw/hawiki.inc");

error_reporting(E_ALL & ~E_NOTICE);


function HAWTIKI_index($info)
{
  $wikiPage = new HAWIKI_page($info["data"],"tiki-index.php?mode=mobile&page=");

  $wikiPage->set_navlink(tra("Home Page"), "tiki-index.php?mode=mobile", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  $wikiPage->set_navlink(tra("Menu"), "tiki-mobile.php", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  $wikiPage->set_smiley_dir("img/smiles");
  $wikiPage->set_link_jingle("lib/hawhaw/link.wav");
  $wikiPage->set_hawimconv("lib/hawhaw/hawimconv.php");

  $wikiPage->display();

  die;
}


function HAWTIKI_view_blog_post($post_info)
{
  $blogPost = new HAWIKI_page($post_info["data"],"tiki-index.php?mode=mobile&page=");

  $blogPost->set_navlink(tra("Blogs"), "tiki-list_blogs.php?mode=mobile", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  $blogPost->set_navlink(tra("Menu"), "tiki-mobile.php", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  $blogPost->set_smiley_dir("img/smiles");
  $blogPost->set_link_jingle("lib/hawhaw/link.wav");
  $blogPost->set_hawimconv("lib/hawhaw/hawimconv.php");

  $blogPost->display();

  die;
}


function HAWTIKI_list_blogs($listpages, $tiki_p_read_blog)
{
  $blogsList = new HAW_deck("Tiki", HAW_ALIGN_CENTER);

  $title = new HAW_text("Tiki Blogs", HAW_TEXTFORMAT_BOLD);
  $blogsList->add_text($title);
  $linkset = new HAW_linkset();

  for($i=0;$i<count($listpages['data']);$i++) {
    $blog = $listpages['data'][$i];
    // check for tiki_p_read_blog here
    if($blog['individual'] == 'n' && $tiki_p_read_blog == 'y' ||
    $blog['individual_tiki_p_read_blog'] == 'y') {
      $link = new HAW_link($blog['title'],"tiki-view_blog.php?mode=mobile&blogId=".$blog['blogId']);
      $linkset->add_link($link);
    }
  }

  $blogsList->add_linkset($linkset);

  $blogsList->create_page();

  die;
}


function HAWTIKI_view_blog($listpages, $blogId)
{
  $blogList = new HAW_deck("Tiki", HAW_ALIGN_CENTER);

  $title = new HAW_text("Tiki Blogs", HAW_TEXTFORMAT_BOLD);
  $blogList->add_text($title);
  $linkset = new HAW_linkset();

  for($i=0;$i<count($listpages['data']);$i++) {
    $blog = $listpages['data'][$i];
    // check for tiki_p_read_blog here
    if (isset($blog['title']) && strlen($blog['title'])>0)
      $label = $blog['title'];
    else
      $label = date("d/m/Y [h:i]",$blog['created']);

    $link = new HAW_link($label,"tiki-view_blog_post.php?mode=mobile&blogId=" . $blogId . "&postId=" . $blog['postId']);
    $linkset->add_link($link);
  }

  $blogList->add_linkset($linkset);

  $blogList->create_page();

  die;
}

?>