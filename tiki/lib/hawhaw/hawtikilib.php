<?php

// HAWHAW function library for TikiWiki
// Last modified: 12. July 2003

require_once("lib/hawhaw/hawhaw.inc");
require_once("lib/hawhaw/hawiki_cfg.inc");
require_once("lib/hawhaw/hawiki_parser.inc");
require_once("lib/hawhaw/hawiki.inc");

error_reporting(E_ALL & ~E_NOTICE);


function hawtra($string)
{
  // translate string with tiki-translator and do special character handling
  // e.g. Menu ==> Men&uuml; ==> Menü
  return(HAWIKI_specchar(tra($string)));
}


function HAWTIKI_date($timestamp)
{
  return(date("d/m/Y [h:i]",$timestamp));
}


function HAWTIKI_index($info)
{
  $wikiPage = new HAWIKI_page($info["data"],"tiki-index.php?mode=mobile&page=", $_REQUEST['page']);

  $wikiPage->set_navlink(tra("Wiki Home"), "tiki-index.php?mode=mobile", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  $wikiPage->set_navlink(tra("Home"), "tiki-mobile.php", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  $wikiPage->set_smiley_dir("img/smiles");
  $wikiPage->set_link_jingle("lib/hawhaw/link.wav");
  $wikiPage->set_hawimconv("lib/hawhaw/hawimconv.php");

  $wikiPage->display();

  die;
}


function HAWTIKI_view_blog_post($post_info)
{
  $page_prefix = sprintf("__%s ~np~%s~/np~__\n__%s ~np~%s~/np~__\n",
                         hawtra("posted on"), HAWTIKI_date($post_info['created']),
                         hawtra("by"), $post_info['user']);

  $blogPost = new HAWIKI_page($page_prefix . $post_info["data"],"tiki-index.php?mode=mobile&page=", $post_info["title"]);

  $blogPost->set_navlink(tra("Return to blog"), "tiki-view_blog.php?mode=mobile&blogId=" . $post_info["blogId"], HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  $blogPost->set_smiley_dir("img/smiles");
  $blogPost->set_link_jingle("lib/hawhaw/link.wav");
  $blogPost->set_hawimconv("lib/hawhaw/hawimconv.php");

  $blogPost->display();

  die;
}


function HAWTIKI_list_blogs($listpages, $tiki_p_read_blog)
{
  $blogsList = new HAW_deck(HAWIKI_TITLE, HAW_ALIGN_CENTER);

  $title = new HAW_text(hawtra("Blogs"), HAW_TEXTFORMAT_BOLD);
  $blogsList->add_text($title);
  $linkset = new HAW_linkset();

  for($i=0;$i<count($listpages['data']);$i++) {
    $blog = $listpages['data'][$i];
    // check for tiki_p_read_blog here
    if($blog['individual'] == 'n' && $tiki_p_read_blog == 'y' ||
    $blog['individual_tiki_p_read_blog'] == 'y') {
      $link = new HAW_link(HAWIKI_specchar($blog['title']),"tiki-view_blog.php?mode=mobile&blogId=".$blog['blogId']);
      $linkset->add_link($link);
    }
  }

  $blogsList->add_linkset($linkset);

  $rule = new HAW_rule();
  $blogsList->add_rule($rule);

  $home = new HAW_link(hawtra("Home"),"tiki-mobile.php");
  $blogsList->add_link($home);

  $blogsList->create_page();

  die;
}


function HAWTIKI_view_blog($listpages, $blog_data)
{
  $blogList = new HAW_deck(HAWIKI_TITLE, HAW_ALIGN_CENTER);

  $title = new HAW_text(HAWIKI_specchar($blog_data['title']), HAW_TEXTFORMAT_BOLD);
  $blogList->add_text($title);

  $linkset = new HAW_linkset();

  for($i=0;$i<count($listpages['data']);$i++)
  {
    $blog = $listpages['data'][$i];
    // check for tiki_p_read_blog here
    if (isset($blog['title']) && strlen($blog['title'])>0)
      $label = $blog['title'];
    else
      $label = HAWTIKI_date($blog['created']);

    $link = new HAW_link(HAWIKI_specchar($label),"tiki-view_blog_post.php?mode=mobile&blogId=" . $_REQUEST['blogId'] . "&postId=" . $blog['postId']);
    $linkset->add_link($link);
  }

  $blogList->add_linkset($linkset);

  $rule = new HAW_rule();
  $blogList->add_rule($rule);

  $blogs = new HAW_link(hawtra("Blogs"),"tiki-list_blogs.php?mode=mobile");
  $blogList->add_link($blogs);

  if (isset($_REQUEST['offset']) && ($_REQUEST['offset'] > 0))
  {
    // previous posts are available

    $offset = $_REQUEST['offset'] - $blog_data['maxPosts'];
    if ($offset < 0)
      $offset = 0;

    $link = new HAW_link(hawtra("prev"),"tiki-view_blog.php?mode=mobile&blogId=" . $_REQUEST['blogId'] . "&offset=" . $offset);
    $blogList->add_link($link);
  }

  if (isset($_REQUEST['offset']))
    $offset = $_REQUEST['offset'];
  else
    $offset = 0;

  if ($offset + $blog_data['maxPosts'] < $blog_data['posts'])
  {
    // next posts are available

    $offset += $blog_data['maxPosts'];
    $link = new HAW_link(hawtra("next"),"tiki-view_blog.php?mode=mobile&blogId=" . $_REQUEST['blogId'] . "&offset=" . $offset);
    $blogList->add_link($link);
  }

  $blogList->create_page();

  die;
}

?>
