<?php

// HAWHAW function library for TikiWiki
// Last modified: 8. November 2003

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
  return(date(HAWIKI_DATETIME_FORMAT, $timestamp));
}


function HAWTIKI_index($info)
{
  // determine title and url switch for navigation links
  if ($_REQUEST['frame'] == 'no')
  {
    $framearg = '&frame=no';
    $title = '';
  }
  else
  {
    $framearg = '';
    $title = $_REQUEST['page'];
  }

  // determine url switch for jingle playing at links
  if ($_REQUEST['jingle'] == 'no')
    $jinglearg = '&jingle=no';
  else
    $jinglearg = '';

  $wikiPage = new HAWIKI_page($info['data'],"tiki-index.php?mode=mobile$framearg$jinglearg&page=", $title);

  if ($_REQUEST['frame'] != 'no')
  {
    // create standard hawiki deck with title and navigation links
    $wikiPage->set_navlink(tra('Wiki Home'), "tiki-index.php?mode=mobile$jinglearg", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
    $wikiPage->set_navlink(tra('Home'), 'tiki-mobile.php', HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  }

  if ($_REQUEST['jingle'] != 'no')
  {
    // play standard jingle before link text is spoken
    $wikiPage->set_link_jingle("lib/hawhaw/link.wav");
  }

  $wikiPage->set_smiley_dir("img/smiles");
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
  $blogsList->enable_session();

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
  $blogList->enable_session();

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


function HAWTIKI_list_articles($listpages, $tiki_p_read_article, $offset, $maxRecords, $cant)
{
  $articleList = new HAW_deck(HAWIKI_TITLE);
  $articleList->enable_session();

  $pagetitle = new HAW_text(hawtra("Articles"), HAW_TEXTFORMAT_BOLD | HAW_TEXTFORMAT_BOXED);
  $articleList->add_text($pagetitle);
  $rule = new HAW_rule();
  $articleList->add_rule($rule);

  for($i=0;$i<count($listpages['data']);$i++)
  {
    $article = $listpages['data'][$i];

    $title = new HAW_text(HAWIKI_specchar($article['title']), HAW_TEXTFORMAT_BOLD);
    $articleList->add_text($title);

    $date = new HAW_text(HAWTIKI_date($article['created']));
    $articleList->add_text($date);

    $author = new HAW_text(hawtra("By:") . HAWIKI_specchar($article['authorName']), HAW_TEXTFORMAT_SMALL | HAW_TEXTFORMAT_ITALIC);
    $articleList->add_text($author);

    // without read permission no reading is allowed
    if($tiki_p_read_article == 'y') {
      $readlink = new HAW_link(hawtra("Read"),"tiki-read_article.php?mode=mobile&articleId=".$article['articleId']);
      $articleList->add_link($readlink);
    }

    $articleList->add_rule($rule);
  }

  if ($offset > 0)
  {
    // previous articles are available
    $prev_offset = $offset - $maxRecords;
    $prev = new HAW_link(hawtra("prev"),"tiki-list_articles.php?mode=mobile&offset=" . $prev_offset);
    $articleList->add_link($prev);
  }

  if ($cant > ($offset + $maxRecords))
  {
    // next articles are available
    $next_offset = $offset + $maxRecords;
    $next = new HAW_link(hawtra("next"),"tiki-list_articles.php?mode=mobile&offset=" . $next_offset);
    $articleList->add_link($next);
  }

  $home = new HAW_link(hawtra("Home"),"tiki-mobile.php");
  $articleList->add_link($home);

  $articleList->create_page();

  die;
}


function HAWTIKI_read_article($article_data, $pages)
{
  $prefix = sprintf("__~np~%s~/np~__\n__%s ~np~%s~/np~__\n",
                    HAWTIKI_date($article_data['created']),
                    hawtra("By:"), $article_data['authorName']);

  $heading = sprintf("\n%s\n---\n", $article_data['heading']);

  $article = new HAWIKI_page($prefix . $heading . $article_data["body"],
                             "tiki-index.php?mode=mobile&page=", $article_data["title"]);

  $article->set_navlink(tra("List articles"), "tiki-list_articles.php?mode=mobile", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);

  if (isset($_REQUEST['page']))
    $page = $_REQUEST['page'];
  else
    $page = 1;

  if ($page > 1)
  {
    $link = sprintf("tiki-read_article.php?mode=mobile&articleId=%s&page=%d", $_REQUEST['articleId'], $page-1);
    $article->set_navlink(tra("previous page"), $link, HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  }

  if ($page < $pages)
  {
    $link = sprintf("tiki-read_article.php?mode=mobile&articleId=%s&page=%d", $_REQUEST['articleId'], $page+1);
    $article->set_navlink(tra("next page"), $link, HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  }

  $article->set_smiley_dir("img/smiles");
  $article->set_link_jingle("lib/hawhaw/link.wav");
  $article->set_hawimconv("lib/hawhaw/hawimconv.php");

  $article->display();

  die;
}

?>
