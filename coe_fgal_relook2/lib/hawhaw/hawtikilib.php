<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// $Id$

// HAWHAW function library for Tiki

require_once("lib/hawhaw/hawhaw.inc");
require_once("lib/hawhaw/hawiki_cfg.inc");
require_once("lib/hawhaw/hawiki.inc");

error_reporting(E_ALL & ~E_NOTICE);


/**
  generic class for articles lists, forum lists etc.
*/
class HAWTIKI_list
{
  var $title;
  var $offset;
  var $maxRecords;
  var $cant;      //list size
  var $backlink;
  var $items;
  var $use_separators = false;
  var $offset_parm_name = "offset"; // default name (for article and forum lists)

  function HAWTIKI_list($title, $offset, $maxRecords, $nav_url, $cant)
  {
    $this->title = $title;
    $this->offset = $offset;
    $this->maxRecords = $maxRecords;
    $this->nav_url = $nav_url;
    $this->cant = $cant;
    $this->items = array();
    $this->query_parms = array();
  }

  function set_backlink($label, $url)
  {
    $this->backlink = new HAW_link($label, $url);
  }

  function set_offset_parm_name($name)
  {
    $this->offset_parm_name = $name; // modify default name (e.g. for forum thread lists)
  }
 
  function set_query_parameter($query_parameter)
  {
    $this->query_parms[] = $query_parameter;
  }

  function add_listitem($item)
  {
    $this->items[] = $item; // push item on top of array

    if ($item->get_size() > 1)
      $this->use_separators = true;
  }

  function create()
  {
    $list = new HAW_deck(HAWIKI_TITLE);
    HAWTIKI_deck_init($list);

    $listtitle = new HAW_text($this->title, HAW_TEXTFORMAT_BOLD | HAW_TEXTFORMAT_BOXED);
    $list->add_text($listtitle);

    if (isset($this->backlink))
      $list->add_link($this->backlink);

    if ($this->offset > 0) {
      // previous list items are available
      $prev_offset = $this->offset - $this->maxRecords;
      $prev_url = $this->nav_url . "?mode=mobile&" . $this->offset_parm_name . "=" . $prev_offset;
      while (list($key, $val) = each($this->query_parms))
        $prev_url .= "&" . $val; // add query parameters
      $prev = new HAW_link(hawtra("previous page"), $prev_url);
      $list->add_link($prev);
    }

    if ($this->cant > ($this->offset + $this->maxRecords)) {
      // next list items are available
      $next_offset = $this->offset + $this->maxRecords;
      $next_url = $this->nav_url . "?mode=mobile&" . $this->offset_parm_name . "=" . $next_offset;
      while (list($key, $val) = each($this->query_parms))
        $next_url .= "&" . $val; // add query parameters
      $next = new HAW_link(hawtra("next page"), $next_url);
      $list->add_link($next);
    }

    $rule = new HAW_rule();
    $list->add_rule($rule);

    // show all list items
    while (list($key, $val) = each($this->items)) {
      $val->render($list);

      if ($this->use_separators)
        $list->add_rule($rule);
    }

    if (!$this->use_separators)
      $list->add_rule($rule);  // show at least one separator here ...

    // repeat navigation links
    if (isset($this->backlink))
      $list->add_link($this->backlink);
    if (isset($prev))
      $list->add_link($prev);
    if (isset($next))
      $list->add_link($next);

    $list->create_page();
    die;
  }
};


/**
  listitem used in HAWTIKI_list class
*/
class HAWTIKI_listitem
{
  var $arr_text;
  var $arr_link;

  function HAWTIKI_listitem()
  {
    $this->arr_text = array();
    $this->arr_link = array();
  }

  function add_text($hawtext)
  {
    $this->arr_text[] = $hawtext; // push HAW_text object on top of text array
  }

  function add_link($hawlink)
  {
    $this->arr_link[] = $hawlink; // push HAW_link object on top of link array
  }

  function get_size()
  {
    return (count($this->arr_text) + count($this->arr_link));
  }

  function render(&$deck)
  {
    while (list($key, $val) = each($this->arr_text))
      $deck->add_text($val);

    while (list($key, $val) = each($this->arr_link))
      $deck->add_text($val);
  }
};


function hawtra($string)
{
  // translate string with tiki-translator and do special character handling
  // e.g. Menu ==> Men&uuml; ==> Men�
  return(HAWIKI_specchar(tra($string)));
}


function HAWTIKI_index($info)
{
  // determine title and url switch for navigation links
  if (isset($_REQUEST['frame']) && $_REQUEST['frame'] == 'no') {
    $framearg = '&frame=no';
    $title = '';
  }
  else
  {
    $framearg = '';
    $title = $_REQUEST['page'];
  }

  // determine url switch for jingle playing at links
  if (isset($_REQUEST['jingle']) && $_REQUEST['jingle'] == 'no')
    $jinglearg = '&jingle=no';
  else
    $jinglearg = '';

  // try to determine calling party number in case of voice browser request
  if (!isset($_SESSION['calling_party_number']) &&
      preg_match('/session.callerid=([^&]*)/', $_SERVER['REQUEST_URI'], $matches))
  {
    // request from Voxeo voice browser
    // ==> store calling party number in session
    $_SESSION["calling_party_number"] = "(+) " . $matches[1];
  }

  $wikiPage = new HAWIKI_page($info['data'],"tiki-index.php?mode=mobile$framearg$jinglearg&page=",
                              $title, $info['lang']);

  if (!isset($_REQUEST['frame']) || $_REQUEST['frame'] != 'no') {
    // create standard hawiki deck with title and navigation links
    $wikiPage->set_navlink(tra('Wiki Home'), "tiki-index.php?mode=mobile$jinglearg", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
    $wikiPage->set_navlink(tra('Home'), 'tiki-mobile.php', HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  }

  if (!isset($_REQUEST['jingle']) || $_REQUEST['jingle'] != 'no') {
    // play standard jingle before link text is spoken
    $wikiPage->set_link_jingle("lib/hawhaw/link.wav");
  }

  $wikiPage->set_smiley_dir("img/smiles");
  $wikiPage->set_hawimconv("lib/hawhaw/hawimconv.php");

  $wikiPage->display();

  die;
}


function HAWTIKI_list_blogs($listpages, $tiki_p_read_blog)
{
  $blogsList = new HAW_deck(HAWIKI_TITLE, HAW_ALIGN_CENTER);
  HAWTIKI_deck_init($blogsList);

  $title = new HAW_text(hawtra("Blogs"), HAW_TEXTFORMAT_BOLD);
  $blogsList->add_text($title);
  $linkset = new HAW_linkset();

  for ($i=0, $icount_listpages = count($listpages['data']); $i < $icount_listpages; $i++) {
    $blog = $listpages['data'][$i];
    // check for tiki_p_read_blog here
    if ($blog['individual'] == 'n' && $tiki_p_read_blog == 'y' ||
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
  // determine title and url switch for navigation links
  if (isset($_REQUEST['frame']) && $_REQUEST['frame'] == 'no') {
    $framearg = '&frame=no';
    $title = '';
  }
  else
  {
    $framearg = '';
    $title = $blog_data['title'];
  }

  // determine url switch for jingle playing at links
  if (isset($_REQUEST['jingle']) && $_REQUEST['jingle'] == 'no')
    $jinglearg = '&jingle=no';
  else
    $jinglearg = '';

  $browser_detector = new HAW_deck(); // this deck is used for browser detection only!
  if ($browser_detector->ml == HAW_WML) {
    // display only one posting on WAP browsers
    $max_posts = 1;
  } else {
    // display as much postings as administered by tiki
    $max_posts = $blog_data['maxPosts'];
  }

  $nonparsed_text = "";

  for ($i=0, $icount_listpages = count($listpages['data']); ($i < $max_posts) && ($i < $icount_listpages); $i++) {
    // separate postings by horizontal bar
    if ($i > 0)
      $nonparsed_text .= "\n---\n";

    // post title
    if ($listpages['data'][$i]['title'])
      $nonparsed_text .= "!" . $listpages['data'][$i]['title'] . "\n";

    // post header
    $nonparsed_text .= sprintf("__%s ~np~%s~/np~__\n__%s ~np~%s~/np~__\n",
                               hawtra("posted on"), date(HAWIKI_DATETIME_LONG, $listpages['data'][$i]['created']),
                               hawtra("by"), $listpages['data'][$i]['user']);

    // post body
    $nonparsed_text .= $listpages['data'][$i]['data'];
  }

  if (isset($_REQUEST['offset']))
    $offset = $_REQUEST['offset'];
  else
    $offset = 0;

  if (($offset + $max_posts) < $blog_data['posts']) {
    // next posts are available ==> create link to continue
    $offset += $max_posts;

    $nonparsed_text .= sprintf("\n---\n[%s|%s]", "tiki-view_blog.php?mode=mobile$framearg$jinglearg&blogId=" . $_REQUEST['blogId'] . "&offset=" . $offset,
                               hawtra("Continue"));
  }

  $blog = new HAWIKI_page($nonparsed_text, "tiki-index.php?mode=mobile$framearg$jinglearg&page=", $title, "");

  if (!isset($_REQUEST['frame']) || $_REQUEST['frame'] != 'no') {
    // create standard hawiki deck with title and navigation links
    $blog->set_navlink(tra('Blogs'), "tiki-list_blogs.php?mode=mobile", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  }

  if (!isset($_REQUEST['jingle']) || $_REQUEST['jingle'] != 'no') {
    // play standard jingle before link text is spoken
    $blog->set_link_jingle("lib/hawhaw/link.wav");
  }

  $blog->set_smiley_dir("img/smiles");
  $blog->set_hawimconv("lib/hawhaw/hawimconv.php");
  $blog->display();

  die;
}

function HAWTIKI_view_blog_post($blog_post_data)
{
  // determine title and url switch for navigation links
  if (isset($_REQUEST['frame']) && $_REQUEST['frame'] == 'no') {
    $framearg = '&frame=no';
    $title = '';
  } else {
    $framearg = '';
    $title = $blog_post_data['title'];
  }

  // determine url switch for jingle playing at links
  if (isset($_REQUEST['jingle']) && $_REQUEST['jingle'] == 'no')
    $jinglearg = '&jingle=no';
  else
    $jinglearg = '';

  $nonparsed_text = "";

  // post title
  if ($blog_post_data['title'])
    $nonparsed_text .= "!" . $blog_post_data['title'] . "\n";

  // post header
  $nonparsed_text .= sprintf("__%s ~np~%s~/np~__\n__%s ~np~%s~/np~__\n",
    hawtra("posted on"), date(HAWIKI_DATETIME_LONG, $blog_post_data['created']),
    hawtra("by"), $blog_post_data['user']);

  // post body
  $nonparsed_text .= $blog_post_data['data'];

  $blogpost = new HAWIKI_page($nonparsed_text, "tiki-index.php?mode=mobile$framearg$jinglearg&page=", $title, "");

  if (!isset($_REQUEST['frame']) || $_REQUEST['frame'] != 'no') {
    // create standard hawiki deck with title and navigation links
    $blogpost->set_navlink(tra('Blog'), "tiki-view_blog.php?blogId=".$blog_post_data['blogId']."&mode=mobile", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  }

  if (!isset($_REQUEST['jingle']) || $_REQUEST['jingle'] != 'no') {
    // play standard jingle before link text is spoken
    $blogpost->set_link_jingle("lib/hawhaw/link.wav");
  }

  $blogpost->set_smiley_dir("img/smiles");
  $blogpost->set_hawimconv("lib/hawhaw/hawimconv.php");
  $blogpost->display();

  die;
}


function HAWTIKI_list_articles($listpages, $tiki_p_read_article, $offset, $maxRecords, $cant)
{
  if ($tiki_p_read_article != 'y') die;

  $article_list = new HAWTIKI_list(hawtra("Articles"), $offset, $maxRecords, "tiki-list_articles.php", $cant);
  $article_list->set_backlink(hawtra("Home"), "tiki-mobile.php");

  for ( $i = 0, $icount_listpages = count($listpages['data']); $i < $icount_listpages; $i++) {
    $article = $listpages['data'][$i];
    $listitem = new HAWTIKI_listitem();

    $title = new HAW_text(HAWIKI_specchar($article['title']), HAW_TEXTFORMAT_BOLD);
    $listitem->add_text($title);

    $date = new HAW_text(date(HAWIKI_DATETIME_SHORT, $article['publishDate']));
    $listitem->add_text($date);

    $author = new HAW_text(hawtra("By:") . HAWIKI_specchar($article['authorName']), HAW_TEXTFORMAT_SMALL | HAW_TEXTFORMAT_ITALIC);
    $listitem->add_text($author);

    $readlink = new HAW_link(hawtra("Read"),"tiki-read_article.php?mode=mobile&articleId=".$article['articleId']);
    $listitem->add_link($readlink);

    $article_list->add_listitem($listitem);
  }

  $article_list->create();
  die;
}


function HAWTIKI_read_article($article_data, $pages)
{
  $prefix = sprintf("__~np~%s~/np~__\n__%s ~np~%s~/np~__\n",
                    date(HAWIKI_DATETIME_SHORT, $article_data['publishDate']),
                    hawtra("By:"), $article_data['authorName']);

  $heading = sprintf("\n%s\n---\n", $article_data['heading']);

  $article = new HAWIKI_page($prefix . $heading . $article_data["body"],
                             "tiki-index.php?mode=mobile&page=", $article_data["title"], $article_data["lang"]);

  $article->set_navlink(tra("List articles"), "tiki-list_articles.php?mode=mobile", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);

  if (isset($_REQUEST['page']))
    $page = $_REQUEST['page'];
  else
    $page = 1;

  if ($page > 1) {
    $link = sprintf("tiki-read_article.php?mode=mobile&articleId=%s&page=%d", $_REQUEST['articleId'], $page-1);
    $article->set_navlink(tra("previous page"), $link, HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  }

  if ($page < $pages) {
    $link = sprintf("tiki-read_article.php?mode=mobile&articleId=%s&page=%d", $_REQUEST['articleId'], $page+1);
    $article->set_navlink(tra("next page"), $link, HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  }

  $article->set_smiley_dir("img/smiles");
  $article->set_link_jingle("lib/hawhaw/link.wav");
  $article->set_hawimconv("lib/hawhaw/hawimconv.php");

  $article->display();

  die;
}


function HAWTIKI_forums($data, $tiki_p_forum_read, $offset, $maxRecords, $cant)
{
  if ($tiki_p_forum_read != 'y') die;

  $forum_list = new HAWTIKI_list(hawtra("Forums"), $offset, $maxRecords, "tiki-forums.php", $cant);
  $forum_list->set_backlink(hawtra("Home"), "tiki-mobile.php");

  for ($i = 0, $icount_data = count($data); $i < $icount_data; $i++) {
    $listitem = new HAWTIKI_listitem();
    $link = new HAW_link($data[$i]['name'], "tiki-view_forum.php?mode=mobile&forumId=" . $data[$i]['forumId'] . "&comments_sort_mode=lastPost_desc");
    $listitem->add_link($link);
    $forum_list->add_listitem($listitem);
  }

  $forum_list->create();

  die;
}


function HAWTIKI_view_forum($forum_name, $threads, $tiki_p_forum_read, $offset, $maxRecords, $cant)
{
  if ($tiki_p_forum_read != 'y') die;

  $thread_list = new HAWTIKI_list($forum_name, $offset, $maxRecords, "tiki-view_forum.php", $cant);
  $thread_list->set_offset_parm_name("comments_offset");
  $thread_list->set_query_parameter("forumId=" . $_REQUEST['forumId']);
  $thread_list->set_backlink(hawtra("Forums"), "tiki-forums.php?mode=mobile");

  while (list($key, $val) = each($threads))
  {
    $listitem = new HAWTIKI_listitem();

    $title = new HAW_text(HAWIKI_specchar($val['title']), HAW_TEXTFORMAT_BOLD);
    $listitem->add_text($title);

    if (isset($val['lastPostData'])) {
      // there's a reply available - show data of last post
      $date = new HAW_text(date(HAWIKI_DATETIME_SHORT, $val['lastPostData']['commentDate']));
      $author = new HAW_text(hawtra("By:") . HAWIKI_specchar($val['lastPostData']['userName']), HAW_TEXTFORMAT_SMALL | HAW_TEXTFORMAT_ITALIC);
      $threadId = $val['lastPostData']['threadId'];
    } else {
      // no reply at all - show data of original posting
      $date = new HAW_text(date(HAWIKI_DATETIME_SHORT, $val['commentDate']));
      $author = new HAW_text(hawtra("By:") . HAWIKI_specchar($val['userName']), HAW_TEXTFORMAT_SMALL | HAW_TEXTFORMAT_ITALIC);
      $threadId = $val['threadId'];
    }

    $listitem->add_text($date);
    $listitem->add_text($author);

    $readlink = new HAW_link(hawtra("Read"),"tiki-view_forum_thread.php?mode=mobile&comments_parentId=" . $threadId . "&forumId=" . $_REQUEST['forumId']);
    $listitem->add_link($readlink);

    $thread_list->add_listitem($listitem);
  }

  $thread_list->create();

  die;
}


function HAWTIKI_view_forum_thread($forum_name, $thread_info, $tiki_p_forum_read)
{
  if ($tiki_p_forum_read != 'y') die;

  $prefix = sprintf("__%s:__\n__~np~%s~/np~__\n__%s ~np~%s~/np~__\n---\n",
                    hawtra("Last post"),
                    date(HAWIKI_DATETIME_SHORT, $thread_info['commentDate']),
                    hawtra("By:"), $thread_info['userName']);

  $thread = new HAWIKI_page($prefix . $thread_info['data'],
                            "tiki-index.php?mode=mobile&page=", $thread_info['title'], "");

  $thread->set_navlink($forum_name, "tiki-view_forum.php?mode=mobile&comments_sort_mode=lastPost_desc&forumId=" . $_REQUEST['forumId'], HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);

  $thread->set_smiley_dir("img/smiles");
  $thread->set_link_jingle("lib/hawhaw/link.wav");
  $thread->set_hawimconv("lib/hawhaw/hawimconv.php");

  $thread->display();

  die;
}


function HAWTIKI_deck_init(&$deck)
{
  // init tiki deck

  $deck->enable_session();

  if (isset($_REQUEST['skin'])) {
    if ($_REQUEST['skin'] == "none")
      unset($_SESSION['haw_skin']);
    else
      $_SESSION['haw_skin'] = $_REQUEST['skin'];
  }

  if (isset($_SESSION['haw_skin']))
    $deck->use_simulator("lib/hawhaw/skin/" . $_SESSION['haw_skin'] . "/skin.css");

  $banner = new HAW_banner("img/poweredbyhawhaw.gif","http://mobile.tiki.org/HawHaw", "MobileTiki is powered by HAWHAW");

  $deck->add_banner($banner);
  
  $deck->set_width(HAWIKI_DISP_WIDTH);
  $deck->set_height(HAWIKI_DISP_HEIGHT);
  $deck->set_disp_bgcolor(HAWIKI_DISP_BGCOLOR);
}
