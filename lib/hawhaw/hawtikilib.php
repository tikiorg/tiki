<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

// $Header: /cvsroot/tikiwiki/tiki/lib/hawhaw/hawtikilib.php,v 1.11 2004-03-29 21:26:34 mose Exp $

// HAWHAW function library for TikiWiki
// Last modified: 13. December 2003

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

  // try to determine calling party number in case of voice browser request
  if (!isset($_SESSION['calling_party_number']) &&
      ereg("session.callerid=([^&]*)", $_SERVER['REQUEST_URI'], $matches))
  {
    // request from Voxeo voice browser
    // ==> store calling party number in session
    $_SESSION["calling_party_number"] = "(+) " . $matches[1];
  }

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
  // determine title and url switch for navigation links
  if ($_REQUEST['frame'] == 'no')
  {
    $framearg = '&frame=no';
    $title = '';
  }
  else
  {
    $framearg = '';
    $title = $blog_data['title'];
  }

  // determine url switch for jingle playing at links
  if ($_REQUEST['jingle'] == 'no')
    $jinglearg = '&jingle=no';
  else
    $jinglearg = '';

  $browser_detector = new HAW_deck(); // this deck is used for browser detection only!
  if ($browser_detector->ml == HAW_WML)
  {
    // display only one posting on WAP browsers
    $max_posts = 1;
  }
  else
  {
    // display as much postings as administered by tiki
    $max_posts = $blog_data['maxPosts'];
  }

  $nonparsed_text = "";

  for ($i=0; ($i < $max_posts) && ($i < count($listpages['data'])); $i++)
  {
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

  if (($offset + $max_posts) < $blog_data['posts'])
  {
    // next posts are available ==> create link to continue
    $offset += $max_posts;

    $nonparsed_text .= sprintf("\n---\n[%s|%s]", "tiki-view_blog.php?mode=mobile$framearg$jinglearg&blogId=" . $_REQUEST['blogId'] . "&offset=" . $offset,
                               hawtra("Continue"));
  }

  $blog = new HAWIKI_page($nonparsed_text, "tiki-index.php?mode=mobile$framearg$jinglearg&page=", $title);

  if ($_REQUEST['frame'] != 'no')
  {
    // create standard hawiki deck with title and navigation links
    $blog->set_navlink(tra('Blogs'), "tiki-list_blogs.php?mode=mobile", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
  }

  if ($_REQUEST['jingle'] != 'no')
  {
    // play standard jingle before link text is spoken
    $blog->set_link_jingle("lib/hawhaw/link.wav");
  }

  $blog->set_smiley_dir("img/smiles");
  $blog->set_hawimconv("lib/hawhaw/hawimconv.php");
  $blog->display();

  die;
}


function HAWTIKI_list_articles($listpages, $tiki_p_read_article, $offset, $maxRecords, $cant)
{
  $articleList = new HAW_deck(HAWIKI_TITLE);
  $articleList->enable_session();

  $pagetitle = new HAW_text(hawtra("Articles"), HAW_TEXTFORMAT_BOLD | HAW_TEXTFORMAT_BOXED);
  $articleList->add_text($pagetitle);

  $home = new HAW_link(hawtra("Home"),"tiki-mobile.php");
  $articleList->add_link($home);

  if ($offset > 0)
  {
    // previous articles are available
    $prev_offset = $offset - $maxRecords;
    $prev = new HAW_link(hawtra("previous page"),"tiki-list_articles.php?mode=mobile&offset=" . $prev_offset);
    $articleList->add_link($prev);
  }

  if ($cant > ($offset + $maxRecords))
  {
    // next articles are available
    $next_offset = $offset + $maxRecords;
    $next = new HAW_link(hawtra("next page"),"tiki-list_articles.php?mode=mobile&offset=" . $next_offset);
    $articleList->add_link($next);
  }

  $rule = new HAW_rule();
  $articleList->add_rule($rule);

  for($i=0;$i<count($listpages['data']);$i++)
  {
    $article = $listpages['data'][$i];

    $title = new HAW_text(HAWIKI_specchar($article['title']), HAW_TEXTFORMAT_BOLD);
    $articleList->add_text($title);

    $date = new HAW_text(date(HAWIKI_DATETIME_SHORT, $article['created']));
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

  // repeat navigation links from above
  $articleList->add_link($home);
  if (isset($prev))
    $articleList->add_link($prev);
  if (isset($next))
    $articleList->add_link($next);

  $articleList->create_page();

  die;
}


function HAWTIKI_read_article($article_data, $pages)
{
  $prefix = sprintf("__~np~%s~/np~__\n__%s ~np~%s~/np~__\n",
                    date(HAWIKI_DATETIME_SHORT, $article_data['created']),
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
