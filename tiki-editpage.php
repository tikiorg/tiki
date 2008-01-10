<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-editpage.php,v 1.181.2.22 2008-01-10 15:17:16 lphuberdeau Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
//var_dump($_REQUEST);die;
// Initialization
$section = "wiki page";
require_once ('tiki-setup.php');
include_once ('lib/wiki/wikilib.php');
include_once ('lib/structures/structlib.php');
include_once ('lib/notifications/notificationlib.php');
require_once ("lib/ajax/ajaxlib.php");
require_once ("lib/wiki/wiki-ajax.php");
if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').': feature_wiki');
	$smarty->display('error.tpl');
	die;
}
// Anti-bot feature: if enabled, anon user must type in a code displayed in an image
if (isset($_REQUEST['save']) && (!$user || $user == 'anonymous') && $prefs['feature_antibot'] == 'y') {
	if((!isset($_SESSION['random_number']) || $_SESSION['random_number'] != $_REQUEST['antibotcode'])) {
		$smarty->assign('msg',tra("You have mistyped the anti-bot verification code; please try again."));
		$smarty->display("error.tpl");
		die;
	}
}
// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"]) || $_REQUEST["page"] == '') { 
	$_REQUEST['page'] = $wikilib->get_default_wiki_page();
}
$page = $_REQUEST["page"];
$smarty->assign_by_ref('page', $_REQUEST["page"]);

// Permissions
$info = $tikilib->get_page_info($page);
$tikilib->get_perm_object($page, 'wiki page', $info, true);
if ($tiki_p_edit != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot edit this page"));
	$smarty->display("error.tpl");
	die;
}

$page_ref_id = '';
if (isset($_REQUEST["page_ref_id"])) {
  $page_ref_id = $_REQUEST["page_ref_id"];
}
$smarty->assign('page_ref_id',$page_ref_id);
//Is new page to be inserted into structure?
if (isset($_REQUEST["current_page_id"])) {
	if (empty($_REQUEST['page'])) {
		$smarty->assign('msg', tra("You must specify a page name, it will be created if it doesn't exist."));
		$smarty->display("error.tpl");
		die;
	}
    if ($tikilib->page_exists($_REQUEST['page'])) {
		$smarty->assign('msg', $_REQUEST['page'] . " " . tra("page not added (Exists)"));
		$smarty->display("error.tpl");
		die;
	}
	$structure_info = $structlib->s_get_structure_info($_REQUEST['current_page_id']);
	if ($tiki_p_edit_structures  != 'y' || !$tikilib->user_has_perm_on_object($user,$structure_info["pageName"],'wiki page','tiki_p_edit')) {
		$smarty->assign('msg', tra("Permission denied you cannot edit this page"));
		$smarty->display("error.tpl");
		die;
	}
  $smarty->assign('current_page_id',$_REQUEST["current_page_id"]);
  if (isset($_REQUEST["add_child"])) {
    $smarty->assign('add_child', "true");
  }
} else {
  $smarty->assign('current_page_id',0);
	$smarty->assign('add_child', false);
}
function compare_import_versions($a1, $a2) {
  return $a1["version"] - $a2["version"];
}
if (isset($_REQUEST['cancel_edit'])) {
	if ($prefs['feature_wikiapproval'] == 'y' && substr($page, 0, strlen($prefs['wikiapproval_prefix'])) == $prefs['wikiapproval_prefix'] && !$tikilib->page_exists($page)) {
		$approvedPageName = substr($page, strlen($prefs['wikiapproval_prefix']));
		$page = $approvedPageName;  
	}
	$tikilib->semaphore_unset($page, $_SESSION["edit_lock_$page"]);
	$url = "location: tiki-index.php?page=" . urlencode($page);
	if (!empty($_REQUEST['page_ref_id'])) {
		$url .= '&page_ref_id='.$_REQUEST['page_ref_id'];
	}	
    header($url);
    die;
}
if (isset($_REQUEST['minor'])) {
	$_REQUEST['isminor'] = 'on';
	$_REQUEST['save'] = true;
}
// We set empty wiki page name as default here if not set (before including Tiki modules)
if ($prefs['feature_warn_on_edit'] == 'y') {
	$editpageconflict = 'n';
	$beingEdited = 'n';
	$semUser = '';
	$u = $user? $user: 'anonymous';
	if (!empty($page) && ($page != 'sandbox' || $page == 'sandbox' && $tiki_p_admin == 'y')) {
		if (!isset($_REQUEST['save'])) {
			if ($tikilib->semaphore_is_set($page, $prefs['warn_on_edit_time'] * 60) && $tikilib->get_semaphore_user($page) != $u) {
				$editpageconflict = 'y';
			} elseif ($tiki_p_edit == 'y') {
				$_SESSION["edit_lock_$page"] = $tikilib->semaphore_set($page);
			}
			$semUser = $tikilib->get_semaphore_user($page);
			$beingedited = 'y';
		} else {
			if (!empty($_SESSION["edit_lock_$page"])) {
				$tikilib->semaphore_unset($page, $_SESSION["edit_lock_$page"]);
			}
		}
	}
	if ($editpageconflict == 'y' && !isset($_REQUEST["conflictoverride"]) ) {
		include_once('lib/smarty_tiki/modifier.userlink.php');
		$msg = tra("This page is being edited by ") .
			smarty_modifier_userlink($semUser) . ". " . 
			tra("Please check with the user before editing the page,
			otherwise the changes will be stored as two separate versions in the history and
			you will have to manually merge them later. ") ;
		$msg .= '<br /><br /><a href="tiki-editpage.php?page=';
		$msg .= urlencode($page);
		$msg .= '&conflictoverride=y">' . tra('Override lock and carry on with edit') . '</a>';
		$smarty->assign('msg',$msg);
		$smarty->assign('errortitle',tra('Page is currently being edited'));
		$smarty->display("error.tpl");
		die;
	}
}
$category_needed = false;
$contribution_needed = false;
if (isset($_REQUEST['lock_it']) && $_REQUEST['lock_it'] =='on') {
	$lock_it = 'y';
} else {
	$lock_it = 'n';
}
$hash = array();
$hash['lock_it'] = $lock_it;
if (!empty($_REQUEST['contributions'])) {
	$hash['contributions'] = $_REQUEST['contributions'];
}
if (!empty($_REQUEST['contributors'])) {
	$hash['contributors'] = $_REQUEST['contributors'];
}
if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
  check_ticket('edit-page');
  require ("lib/mail/mimelib.php");
  $fp = fopen($_FILES['userfile1']['tmp_name'], "rb");
  $data = '';
  while (!feof($fp)) {
    $data .= fread($fp, 8192 * 16);
  }
  fclose ($fp);
  $name = $_FILES['userfile1']['name'];
  $output = mime::decode($data);
  $parts = array();
  parse_output($output, $parts, 0);
  $last_part = '';
  $last_part_ver = 0;
  usort($parts, 'compare_import_versions');
  foreach ($parts as $part) {
    if ($part["version"] > $last_part_ver) {
      $last_part_ver = $part["version"];
      $last_part = $part["body"];
    }
    if (isset($part["pagename"])) {
      $pagename = urldecode($part["pagename"]);
      $version = urldecode($part["version"]);
      $author = urldecode($part["author"]);
      $lastmodified = $part["lastmodified"];
      if (isset($part["description"])) {
        $description = $part["description"];
      } else {
        $description = '';
      }
      $pageLang = isset($part["lang"])? $part["lang"]: "";
      $authorid = urldecode($part["author_id"]);
      if (isset($part["hits"]))
        $hits = urldecode($part["hits"]);
      else
        $hits = 0;
      $ex = substr($part["body"], 0, 25);
      //print(strlen($part["body"]));
      $msg = '';
	if (isset($_REQUEST['save']) && $prefs['feature_contribution'] == 'y' && $prefs['feature_contribution_mandatory'] == 'y' && (empty($_REQUEST['contributions']) || count($_REQUEST['contributions']) <= 0)) {
		$contribution_needed = true;
		$smarty->assign('contribution_needed', 'y');
	} else {
		$contribution_needed = false;
	}
 	if (isset($_REQUEST['save']) && $prefs['feature_categories'] == 'y' && $prefs['feature_wiki_mandatory_category'] >=0 && (empty($_REQUEST['cat_categories']) || count($_REQUEST['cat_categories']) <= 0)) {
		$category_needed = true;
		$smarty->assign('category_needed', 'y');
	} else {
		$category_needed = false;
	}
	if (isset($_REQUEST["save"]) && !$category_needed && !$contribution_needed) {
        if (strtolower($pagename) != 'sandbox' || $tiki_p_admin == 'y') {
        	make_clean($description);
        	if ($tikilib->page_exists($pagename)) {
			if ($prefs['feature_multilingual'] == 'y') {
				$info = $tikilib->get_page_info($pagename);
				if ($info['lang'] != $pageLang) {
					include_once("lib/multilingual/multilinguallib.php");
				 	if ($multilinguallib->updatePageLang('wiki page', $info['page_id'], $pageLang, true)){
						$pageLang = $info['lang'];
						$smarty->assign('msg', tra("The language can't be changed as its set of translations has already this language"));
						$smarty->display("error.tpl");
						die;
					}
				}
  			}
          	$tikilib->update_page($pagename, $part["body"], tra('page imported'), $author, $authorid, $description, null, $pageLang, false, $hash);
        	} else {
          	$tikilib->create_page($pagename, $hits, $part["body"], $lastmodified, tra('created from import'), $author, $authorid, $description, $pageLang, false, $hash);
        	}
        }
      } else {
        $_REQUEST["edit"] = $last_part;
      }
    }
  }
  if (isset($_REQUEST["save"])) {
    unset ($_REQUEST["save"]);
    if ($page_ref_id) {
      header ("location: tiki-index.php?page_ref_id=$page_ref_id");
    } else {
      header ("location: tiki-index.php?page=$page");
    }
    die;
  }
}
$smarty->assign('category_needed',$category_needed);
$smarty->assign('contribution_needed',$contribution_needed);
$wiki_up = "img/wiki_up";
if ($tikidomain) { $wiki_up.= "/$tikidomain"; }
// Upload pictures here
if (($prefs['feature_wiki_pictures'] == 'y') && (isset($tiki_p_upload_picture)) && ($tiki_p_upload_picture == 'y')) {
	$i = 1;
	while ( isset($_FILES['picfile'.$i]) ) {
		if ( is_uploaded_file($_FILES['picfile'.$i]['tmp_name']) ) {
			$picname = $_FILES['picfile'.$i]['name'];
			if ( preg_match('/\.(gif|png|jpe?g)$/i',$picname) ) {
				if (@getimagesize($_FILES['picfile'.$i]['tmp_name'])) {
					move_uploaded_file($_FILES['picfile'.$i]['tmp_name'], "$wiki_up/$picname");
					chmod("$wiki_up/$picname", 0644); // seems necessary on some system (see move_uploaded_file doc on php.net)
				}
			}
		}
		$i++;
	}
}
if ($prefs['feature_wiki_attachments'] == 'y' && isset($_REQUEST["attach"]) && ($tiki_p_wiki_attach_files == 'y' || $tiki_p_wiki_admin_attachments == 'y')) {
	if (isset($_FILES['userfile2']) && is_uploaded_file($_FILES['userfile2']['tmp_name'])) {
		$ret = $tikilib->attach_file($_FILES['userfile2']['name'], $_FILES['userfile2']['tmp_name'], $prefs['w_use_db'] == 'y'? 'db': 'dir');
		if ($ret['ok']) {
			$wikilib->wiki_attach_file($page, $_FILES['userfile2']['name'], $_FILES['userfile2']['type'], $_FILES['userfile2']['size'], ($prefs['w_use_db'] == 'dir')?'': $ret['data'], $_REQUEST["attach_comment"], $user, $ret['fhash']);
		} else {
				$smarty->assign('msg', $ret['error']);
				$smarty->display("error.tpl");
				die();
		}
	}
}
/**
 * \brief Parsed HTML tree walker (used by HTML sucker)
 *
 * This is initial implementation (stupid... w/o any intellegence (almost :))
 * It is rapidly designed version... just for test: 'can this feature be useful'.
 * Later it should be replaced by well designed one :) don't bash me now :)
 *
 * \param &$c array -- parsed HTML
 * \param &$src string -- output string
 * \param &$p array -- ['stack'] = closing strings stack,
                       ['listack'] = stack of list types currently opened
                       ['first_td'] = flag: 'is <tr> was just before this <td>'
 */
function walk_and_parse(&$c, &$src, &$p, $head_url )
{
    // If no string
    if( ! $c )
    {
	return;
    }
    for ($i=0; $i <= $c["contentpos"]; $i++)
    {
        // If content type 'text' output it to destination...
        if ($c[$i]["type"] == "text")
	{
	    if( ! preg_match( '/^\s*$/s', $c[$i]["data"] ) )
	    {
		$src .= preg_replace( '/^\s+/s', ' ', $c[$i]["data"] );
	    }
	}
        elseif ($c[$i]["type"] == "comment")
        {
		$src .= preg_replace( '/<!--/', "\n~hc~", 
			preg_replace( '/-->/', "~/hc~\n", $c[$i]["data"] )
			);
	}
        elseif ($c[$i]["type"] == "tag")
        {
            if ($c[$i]["data"]["type"] == "open")
	    {
                // Open tag type
                switch ($c[$i]["data"]["name"])
                {
		// Tags we don't want at all.
		case "meta": 
		    $c[$i]["content"] = '';
		break;
		case "br": $src .= '%%%'; break;
                case "title": $src .= "\n!"; $p['stack'][] = array('tag' => 'title', 'string' => "\n"); break;
                case "p": $src .= "\n"; $p['stack'][] = array('tag' => 'p', 'string' => "\n"); break;
                case "b": $src .= '__'; $p['stack'][] = array('tag' => 'b', 'string' => '__'); break;
                case "i": $src .= "''"; $p['stack'][] = array('tag' => 'i', 'string' => "''"); break;
                case "em": $src .= "''"; $p['stack'][] = array('tag' => 'em', 'string' => "''"); break;
                case "strong": $src .= '__'; $p['stack'][] = array('tag' => 'strong', 'string' => '__'); break;
                case "u": $src .= "=="; $p['stack'][] = array('tag' => 'u', 'string' => "=="); break;
                case "center": $src .= '::'; $p['stack'][] = array('tag' => 'center', 'string' => '::'); break;
                case "code": $src .= '-+';  $p['stack'][] = array('tag' => 'code', 'string' => '+-'); break;
                case "dd": $src .= ':';  $p['stack'][] = array('tag' => 'dd', 'string' => "\n"); break;
                case "dt": $src .= ';';  $p['stack'][] = array('tag' => 'dt', 'string' => ''); break;
                // headers detection looks like real suxx code...
                // but possible it run faster :) I don't know where is profiler in PHP...
                case "h1": $src .= "\n!"; $p['stack'][] = array('tag' => 'h1', 'string' => "\n"); break;
                case "h2": $src .= "\n!!"; $p['stack'][] = array('tag' => 'h2', 'string' => "\n"); break;
                case "h3": $src .= "\n!!!"; $p['stack'][] = array('tag' => 'h3', 'string' => "\n"); break;
                case "h4": $src .= "\n!!!!"; $p['stack'][] = array('tag' => 'h4', 'string' => "\n"); break;
                case "h5": $src .= "\n!!!!!"; $p['stack'][] = array('tag' => 'h5', 'string' => "\n"); break;
                case "h6": $src .= "\n!!!!!!"; $p['stack'][] = array('tag' => 'h6', 'string' => "\n"); break;
                case "pre": $src .= "~pre~\n"; $p['stack'][] = array('tag' => 'pre', 'string' => "~/pre~\n"); break;
                case "sub": $src .= "{SUB()}"; $p['stack'][] = array('tag' => 'sub', 'string' => "{SUB}"); break;
                // Table parser
                case "table": $src .= '||'; $p['stack'][] = array('tag' => 'table', 'string' => '||'); break;
                case "tr": $p['first_td'] = true; break;
                case "td": $src .= $p['first_td'] ? '' : '|'; $p['first_td'] = false; break;
                // Lists parser
                case "ul": $p['listack'][] = '*'; break;
                case "ol": $p['listack'][] = '#'; break;
                case "li":
                    // Generate wiki list item according to current list depth.
                    // (ensure '*/#' starts from begining of line)
                    $temp_max = count($p['listack']);
                    for ($l = ''; strlen($l) < $temp_max; $l .= end($p['listack']));
                    $src .= "\n$l ";
                    break;
                case "font":
                    // If color attribute present in <font> tag
                    if (isset($c[$i]["pars"]["color"]["value"]))
                    {
                        $src .= '~~'.$c[$i]["pars"]["color"]["value"].':';
                        $p['stack'][] = array('tag' => 'font', 'string' => '~~');
                    }
                    break;
                case "img":
                    // If src attribute present in <img> tag
                    if (isset($c[$i]["pars"]["src"]["value"]))
                        // Note what it produce (img) not {img}! Will fix this below...
		        if( strstr( $c[$i]["pars"]["src"]["value"], "http:" ) )
			{
			    $src .= '(img src='.$c[$i]["pars"]["src"]["value"].')';
			} else {
			    $src .= '(img src='.$head_url.$c[$i]["pars"]["src"]["value"].')';
			}
                    break;
                case "a":
                    // If href attribute present in <a> tag
                    if (isset($c[$i]["pars"]["href"]["value"]))
		    {
		        if( strstr( $c[$i]["pars"]["href"]["value"], "http:" ) )
			{
			    $src .= '['.$c[$i]["pars"]["href"]["value"].'|';
			} else {
			    $src .= '['.$head_url.$c[$i]["pars"]["href"]["value"].'|';
			}
                        $p['stack'][] = array('tag' => 'a', 'string' => ']');
                    }
                    if( isset($c[$i]["pars"]["name"]["value"]) )
		    {
		    	$src .= '{ANAME()}'.$c[$i]["pars"]["name"]["value"].'{ANAME}';
		    }
                    break;
                }
            }
            else
            {
                // This is close tag type. Is that smth we r waiting for?
                switch ($c[$i]["data"]["name"])
                {
                case "ul":
                    if (end($p['listack']) == '*') array_pop($p['listack']);
                    break;
                case "ol":
                    if (end($p['listack']) == '#') array_pop($p['listack']);
                    break;
                default:
                    $e = end($p['stack']);
                    if ($c[$i]["data"]["name"] == $e['tag'])
                    {
                        $src .= $e['string'];
                        array_pop($p['stack']);
                    }
                    break;
                }
            }
        }
        // Recursive call on tags with content...
        if (isset($c[$i]["content"]))
        {
//            if (substr($src, -1) != " ") $src .= " ";
            walk_and_parse($c[$i]["content"], $src, $p, $head_url );
        }
    }
}
// Suck another page and append to the end of current
include ('lib/htmlparser/htmlparser.inc');
$suck_url = isset($_REQUEST["suck_url"]) ? $_REQUEST["suck_url"] : '';
$parsehtml = isset ($_REQUEST["parsehtml"]) ? ($_REQUEST["parsehtml"] == 'on' ? 'y' : 'n')  : 'n';
if (isset($_REQUEST['do_suck']) && strlen($suck_url) > 0)
{
    // \note by zaufi
    //   This is ugly implementation of wiki HTML import.
    //   I think it should be plugable import/export converters with ability
    //   to choose from edit form what converter to use for operation.
    //   In case of import converter, it can try to guess what source
    //   file is (using mime type from remote server response).
    //   Of couse converters may have itsown configuration panel what should be
    //   pluged into wiki page edit form too... (like HTML importer may have
    //   flags 'strip HTML tags' and 'try to convert HTML to wiki' :)
    //   At least one export filter for wiki already coded :) -- PDF exporter...
    $sdta = $tikilib->httprequest($suck_url);
    if (isset($php_errormsg) && strlen($php_errormsg))
    {
        $smarty->assign('msg', tra("Can't import remote HTML page"));
        $smarty->display("error.tpl");
        die;
    }
    // Need to parse HTML?
    if ($parsehtml == 'y')
    {
        // Read compiled (serialized) grammar
        $grammarfile = 'lib/htmlparser/htmlgrammar.cmp';
        if (!$fp = @fopen($grammarfile,'r'))
        {
            $smarty->assign('msg', tra("Can't parse remote HTML page"));
            $smarty->display("error.tpl");
            die;
        }
        $grammar = unserialize(fread($fp, filesize($grammarfile)));
        fclose($fp);
        // create parser object, insert html code and parse it
        $htmlparser = new HtmlParser($sdta, $grammar, '', 0);
        $htmlparser->Parse();
        // Should I try to convert HTML to wiki?
        $parseddata = '';
        $p =  array('stack' => array(), 'listack' => array(), 'first_td' => false);
	$head_url = preg_replace( ';[^/]*$;', '', $_REQUEST["suck_url"] );
        walk_and_parse( $htmlparser->content, $parseddata, $p, $head_url );
        // Is some tags still opened? (It can be if HTML not valid, but this is not reason
        // to produce invalid wiki :)
        while (count($p['stack']))
        {
            $e = end($p['stack']);
            $sdta .= $e['string'];
            array_pop($p['stack']);
        }
        // Unclosed lists r ignored... wiki have no special start/end lists syntax....
        // OK. Things remains to do:
        // 1) fix linked images
        $parseddata = preg_replace(',\[(.*)\|\(img src=(.*)\)\],mU','{img src=$2 link=$1}', $parseddata);
        // 2) fix remains images (not in links)
        $parseddata = preg_replace(',\(img src=(.*)\),mU','{img src=$1}', $parseddata);
        // 3) remove empty lines
        $parseddata = preg_replace(",[\n]+,mU","\n", $parseddata);
        // Reassign previous data
        $sdta = $parseddata;
    }
    $_REQUEST['edit'] .= $sdta;
}
// if "UserPage" complete with the user name
if ($prefs['feature_wiki_userpage'] == 'y' && $tiki_p_admin != 'y' && $page == $prefs['feature_wiki_userpage_prefix']) {
	$page .= $user;
	$_REQUEST['page'] = $page;
}
if (strtolower($_REQUEST["page"]) == 'sandbox' && $prefs['feature_sandbox'] != 'y') {
  $smarty->assign('msg', tra("The SandBox is disabled"));
  $smarty->display("error.tpl");
  die;
}
if (!isset($_REQUEST["comment"])) {
  $_REQUEST["comment"] = '';
}
// Get page data
if(isset($info['wiki_cache'])) {
  $prefs['wiki_cache'] = $info['wiki_cache'];
  $smarty->assign('wiki_cache',$prefs['wiki_cache']);
}
if ($info["flag"] == 'L' && !$wikilib->is_editable($page, $user, $info)) {
  $smarty->assign('msg', tra("Cannot edit page because it is locked"));
  $smarty->display("error.tpl");
  die;
}
$smarty->assign('editable','y');
$smarty->assign('show_page','n');
$smarty->assign('comments_show','n');

// wysiwyg decision
include 'tiki-parsemode_setup.php';
$smarty->assign_by_ref('data', $info);
$smarty->assign('footnote', '');
$smarty->assign('has_footnote', 'n');
if ($prefs['feature_wiki_footnotes'] == 'y') {
  if ($user) {
    $x = $wikilib->get_footnote($user, $page);
    $footnote = $wikilib->get_footnote($user, $page);
    $smarty->assign('footnote', $footnote);
    if ($footnote)
      $smarty->assign('has_footnote', 'y');
    $smarty->assign('parsed_footnote', $tikilib->parse_data($footnote));
    if (isset($_REQUEST['footnote'])) {
      check_ticket('edit-page');
      $smarty->assign('parsed_footnote', $tikilib->parse_data($_REQUEST['footnote']));
      $smarty->assign('footnote', $_REQUEST['footnote']);
      $smarty->assign('has_footnote', 'y');
      if (empty($_REQUEST['footnote'])) {
        $wikilib->remove_footnote($user, $page);
      } else {
        $wikilib->replace_footnote($user, $page, $_REQUEST['footnote']);
      }
    }
  }
}
if (isset($_REQUEST["templateId"]) && $_REQUEST["templateId"] > 0 && !isset($_REQUEST['preview']) && !isset($_REQUEST['save'])) {
  $template_data = $tikilib->get_template($_REQUEST["templateId"]);
  $_REQUEST["edit"] = $template_data["content"]."\n".$_REQUEST["edit"];
  $_REQUEST["preview"] = 1;
  $smarty->assign("templateId", $_REQUEST["templateId"]);
}
if (isset($_REQUEST["categId"]) && $_REQUEST["categId"] > 0) {
	$categs = split("\+",$_REQUEST["categId"]);
	$smarty->assign('categIds',$categs);
	$smarty->assign('categIdstr',$_REQUEST["categId"]);
} else {
	$smarty->assign('categIds',array());
	$smarty->assign('categIdstr',0);
}
if (isset($_REQUEST["ratingId"]) && $_REQUEST["ratingId"] > 0) {
	$smarty->assign("poll_template",$_REQUEST["ratingId"]);
} else {
	$smarty->assign("poll_template",0);
}
if(isset($_REQUEST["edit"])) {
    $edit_data = $_REQUEST["edit"];  
} else {
    if (isset($info['draft'])) {
	$edit_data = $info['draft']['data'];
    } elseif (isset($info["data"])) {
	$edit_data = $info["data"];
	} elseif ($prefs['feature_wikiapproval'] == 'y' && substr($page, 0, strlen($prefs['wikiapproval_prefix'])) == $prefs['wikiapproval_prefix'] && !$tikilib->page_exists($page)) {
	// Handle first creation of staging copy 
	$oldpage = substr($page, strlen($prefs['wikiapproval_prefix']));	
	// Get page data
		if ($tikilib->page_exists($oldpage)) {
			$oldinfo = $tikilib->get_page_info($oldpage);
			$edit_data = $oldinfo["data"];
		} else {
			$edit_data = '';
		}
    } else {
	$edit_data = '';
    }
}
$likepages = '';
$smarty->assign_by_ref('likepages', $likepages);
if ($prefs['feature_likePages'] == 'y' and $edit_data == '' && !$tikilib->page_exists($page)) {
	$likepages = $wikilib->get_like_pages($page);
}
	
if (isset($prefs['wiki_feature_copyrights']) && $prefs['wiki_feature_copyrights'] == 'y') {
  if (isset($_REQUEST['copyrightTitle'])) {
    $smarty->assign('copyrightTitle', $_REQUEST["copyrightTitle"]);
  }
  if (isset($_REQUEST['copyrightYear'])) {
    $smarty->assign('copyrightYear', $_REQUEST["copyrightYear"]);
  }
  if (isset($_REQUEST['copyrightAuthors'])) {
    $smarty->assign('copyrightAuthors', $_REQUEST["copyrightAuthors"]);
  }
}
if (isset($_REQUEST["comment"])) {
  $smarty->assign_by_ref('commentdata', $_REQUEST["comment"]);
} elseif (isset($info['draft'])) {
    $smarty->assign_by_ref('commentdata',$info['draft']['data']);
} else {
    $smarty->assign('commentdata', '');
}
if (isset($info["description"])) {
    if (isset($info['draft'])) {
	$info['description'] = $info['draft']['description'];
    }
    $smarty->assign('description', $info["description"]);
    $description = $info["description"];
} else {
  $smarty->assign('description', '');
  $description = '';
}
if(isset($_REQUEST["description"])) {
  $smarty->assign_by_ref('description',$_REQUEST["description"]);
  $description = $_REQUEST["description"];
}
if($is_html) {
    $smarty->assign('allowhtml','y');
} else {
  $smarty->assign('allowhtml','n');
}
if (empty($_REQUEST['lock_it']) && !empty($info['flag']) && $info['flag'] == 'L') {
	$lock_it = 'y';
}
$smarty->assign_by_ref('lock_it', $lock_it);
if (isset($_REQUEST["lang"])) {
  if ($prefs['feature_multilingual'] == 'y' && isset($info["lang"]) && $info['lang'] != $_REQUEST["lang"]) {
	include_once("lib/multilingual/multilinguallib.php");
	if ($multilinguallib->updatePageLang('wiki page', $info['page_id'], $_REQUEST["lang"], true)) {
		$pageLang = $info['lang'];
		$smarty->assign('msg', tra("The language can't be changed as its set of translations has already this language"));
		$smarty->display("error.tpl");
		die;
  	}
   }
	$pageLang = $_REQUEST["lang"];
} elseif (isset($info["lang"])) {
  $pageLang = $info["lang"];
} else {
  $pageLang = "";
}
$smarty->assign('lang', $pageLang);
if ( ! isset($_REQUEST['edit']) && ! $is_html ) {
	// When we get data from database (i.e. we are not in preview mode) and if we don't allow HTML,
	//   then we need to convert database's HTML entities into their "normal chars" equivalents
	$smarty->assign('pagedata', TikiLib::htmldecode($edit_data));
} else {
	$smarty->assign('pagedata', $edit_data);
}
if ( isset($_REQUEST['edit']) && ! $is_html ) {
	// When we are in preview mode (i.e. data doesn't come from database) and if we don't allow HTML,
	//   then we need to convert HTML special chars into their HTML entities equivalent;
	$parsed = htmlspecialchars($edit_data);
} else {
	$parsed = $edit_data;
}
// apply the optional post edit filters before preview
if(isset($_REQUEST["preview"]) || ($prefs['wiki_spellcheck'] == 'y' && isset($_REQUEST["spellcheck"]) && $_REQUEST["spellcheck"] == 'on')) {
  $parsed = $tikilib->apply_postedit_handlers($parsed);
  $parsed = $tikilib->parse_data($parsed,$is_html);
} else {
  $parsed = "";
}
/* SPELLCHECKING INITIAL ATTEMPT */
//This nice function does all the job!
if ($prefs['wiki_spellcheck'] == 'y') {
  if (isset($_REQUEST["spellcheck"]) && $_REQUEST["spellcheck"] == 'on') {
    $parsed = $tikilib->spellcheckreplace($edit_data, $parsed, $prefs['language'], 'editwiki');
    $smarty->assign('spellcheck', 'y');
  } else {
    $smarty->assign('spellcheck', 'n');
  }
}
$smarty->assign_by_ref('parsed', $parsed);
$smarty->assign('preview',0);
// If we are in preview mode then preview it!
if(isset($_REQUEST["preview"])) {
  $smarty->assign('preview',1);
}

function parse_output(&$obj, &$parts,$i) {
  if(!empty($obj['parts'])) {
    for($i=0; $i<count($obj['parts']); $i++)
      parse_output($obj['parts'][$i], $parts,$i);
  }else{
    switch($obj['type']) {
      case 'application/x-tikiwiki':
         $aux["body"] = $obj['body'];
         $ccc=$obj['header']["content-type"];
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
// Check if the page has changed
$pageAlias = '';
$cat_type='wiki page';
$cat_objid = $_REQUEST["page"];
if (isset($_REQUEST['save']) && $prefs['feature_contribution'] == 'y' && $prefs['feature_contribution_mandatory'] == 'y' && (empty($_REQUEST['contributions']) || count($_REQUEST['contributions']) <= 0)) {
	$contribution_needed = true;
	$smarty->assign('contribution_needed', 'y');
} else {
	$contribution_needed = false;
}
if (isset($_REQUEST['save']) && $prefs['feature_categories'] == 'y' && $prefs['feature_wiki_mandatory_category'] >=0 && (empty($_REQUEST['cat_categories']) || count($_REQUEST['cat_categories']) <= 0)) {
	$category_needed = true;
	$smarty->assign('category_needed', 'y');
} else {
	$category_needed = false;
}	
if (isset($_REQUEST["save"]) && (strtolower($_REQUEST['page']) != 'sandbox' || $tiki_p_admin == 'y') && !$category_needed && !$contribution_needed) {
  check_ticket('edit-page');
  // Check if all Request values are delivered, and if not, set them
  // to avoid error messages. This can happen if some features are
  // disabled
  if(!isset($_REQUEST["description"])) $_REQUEST["description"]='';
  if(!isset($_REQUEST["comment"])) $_REQUEST["comment"]='';
  if(!isset($_REQUEST["lang"])) $_REQUEST["lang"]='';
  if(isset($_REQUEST['wiki_cache'])) {
    $wikilib->set_page_cache($_REQUEST['page'],$_REQUEST['wiki_cache']);
  }
  include_once("lib/imagegals/imagegallib.php");
  $cat_desc = ($prefs['feature_wiki_description'] == 'y') ? substr($_REQUEST["description"],0,200) : '';
  $cat_name = $_REQUEST["page"];
  $cat_href="tiki-index.php?page=".urlencode($cat_objid);
  include_once("categorize.php");
  include_once("poll_categorize.php");
  include_once("freetag_apply.php");
    $page = $_REQUEST["page"];
    if($is_html) {
      $edit = $_REQUEST["edit"];
    } else {
      $edit = htmlspecialchars($_REQUEST['edit']);
    }
    // add permisions here otherwise return error!
    if(isset($prefs['wiki_feature_copyrights']) && $prefs['wiki_feature_copyrights'] == 'y'
      && isset($_REQUEST['copyrightTitle'])
      && isset($_REQUEST['copyrightYear'])
      && isset($_REQUEST['copyrightAuthors'])
      && !empty($_REQUEST['copyrightYear'])
      && !empty($_REQUEST['copyrightTitle'])
    ) {
      include_once("lib/copyrights/copyrightslib.php");
      $copyrightslib = new CopyrightsLib($dbTiki);
      $copyrightYear = $_REQUEST['copyrightYear'];
      $copyrightTitle = $_REQUEST['copyrightTitle'];
      $copyrightAuthors = $_REQUEST['copyrightAuthors'];
      $copyrightslib->add_copyright($page,$copyrightTitle,$copyrightYear,$copyrightAuthors,$user);
    }
    // Parse $edit and eliminate image references to external URIs (make them internal)
    $edit = $imagegallib->capture_images($edit);
    // apply the optional page edit filters before data storage
    $edit = $tikilib->apply_postedit_handlers($edit);
    // If page exists
    if(!$tikilib->page_exists($_REQUEST["page"])) {
      // Extract links and update the page
      $links = $tikilib->get_links($_REQUEST["edit"]);
      /*
      $notcachedlinks = $tikilib->get_links_nocache($_REQUEST["edit"]);
      $cachedlinks = array_diff($links, $notcachedlinks);
      $tikilib->cache_links($cachedlinks);
      */
      $tikilib->create_page($_REQUEST["page"], 0, $edit, $tikilib->now, $_REQUEST["comment"],$user,$_SERVER["REMOTE_ADDR"],$description, $pageLang, $is_html, $hash);
      if ($prefs['wiki_watch_author'] == 'y') {
        $tikilib->add_user_watch($user,"wiki_page_changed",$_REQUEST["page"],'wiki page',$page,"tiki-index.php?page=$page");
      }

		if( $prefs['feature_multilingual'] == 'y'
		 && isset( $_REQUEST['translationOf']  )
		 && ! empty( $_REQUEST['translationOf'] )
		 && ! empty( $pageLang ) )
		{
			include_once("lib/multilingual/multilinguallib.php");
			$infoSource = $tikilib->get_page_info($_REQUEST['translationOf']);
			$infoCurrent = $tikilib->get_page_info($_REQUEST['page']);
			if ($multilinguallib->insertTranslation('wiki page', $infoSource['page_id'], $infoSource['lang'], $infoCurrent['page_id'], $pageLang)){
				$pageLang = $info['lang'];
				$smarty->assign('msg', tra("The language can't be changed as its set of translations has already this language"));
				$smarty->display("error.tpl");
				die;
			}
		}
    } else {
      $links = $tikilib->get_links($edit);
      /*
      $tikilib->cache_links($links);
      */
      if(isset($_REQUEST['isminor'])&&$_REQUEST['isminor']=='on') {
        $minor=true;
      } else {
        $minor=false;
      }
      $tikilib->update_page($_REQUEST["page"],$edit,$_REQUEST["comment"],$user,$_SERVER["REMOTE_ADDR"],$description,$minor,$pageLang, $is_html, $hash);
    }
  //Page may have been inserted from a structure page view
  if (isset($_REQUEST['current_page_id']) ) {
    $page_info = $structlib->s_get_page_info($_REQUEST['current_page_id']);
		$pageAlias = $page_info['page_alias'];
    if (isset($_REQUEST["add_child"]) ) {
      //Insert page after last child of current page
      $subpages = $structlib->s_get_pages($_REQUEST["current_page_id"]);
      $max = count($subpages);
      $last_child_ref_id = null;
      if ($max != 0) {
        $last_child = $subpages[$max - 1];
        $last_child_ref_id = $last_child["page_ref_id"];
      }
    $page_ref_id = $structlib->s_create_page($_REQUEST['current_page_id'], $last_child_ref_id, $_REQUEST["page"], '');
    }
    else {
      //Insert page after current page
      $page_ref_id = $structlib->s_create_page($page_info["parent_id"], $_REQUEST['current_page_id'], $_REQUEST["page"], '');
    }
    //Criss Holman added the if containing this code of which I don't know the use, but a check before the permissions copy
    //is definitely needed in case someone has tiki_p_edit/tiki_p_admin_wiki in a page belonging to a structure. chealer
    if ($tikilib->user_has_perm_on_object($user, $_REQUEST["page"],'wiki page', 'tiki_p_admin_wiki', 'tiki_p_admin_categories'))
    $userlib->copy_object_permissions($page_info["pageName"], $_REQUEST["page"],'wiki page');
  } 
    
  $page = urlencode($page);
  if ($page_ref_id) {
    header("location: tiki-index.php?page_ref_id=$page_ref_id");
  } else {
    header("location: tiki-index.php?page=$page");
  }
  die;
} //save
$smarty->assign('pageAlias',$pageAlias);
if ($prefs['feature_wiki_templates'] == 'y' && $tiki_p_use_content_templates == 'y') {
  $templates = $tikilib->list_templates('wiki', 0, -1, 'name_asc', '');
  $smarty->assign_by_ref('templates', $templates["data"]);
}
if ($prefs['feature_polls'] =='y' and $prefs['feature_wiki_ratings'] == 'y' && $tiki_p_wiki_admin_ratings == 'y') {
	function pollnameclean($s) { global $page; if (isset($s['title'])) $s['title'] = substr($s['title'],strlen($page)+2); return $s; }
	if (!isset($polllib) or !is_object($polllib)) include("lib/polls/polllib_shared.php");
	if (!isset($categlib) or !is_object($categlib)) include("lib/categories/categlib.php");
	if (isset($_REQUEST['removepoll'])) {
		$catObjectId = $categlib->is_categorized($cat_type,$cat_objid);
		$polllib->remove_object_poll($cat_type,$cat_objid);
	}
	$polls_templates = $polllib->get_polls('t');
	$smarty->assign('polls_templates',$polls_templates['data']);
	$poll_rated = $polllib->get_rating($cat_type,$cat_objid);
	if (isset($poll_rated['title'])) {
		$poll_rated  = array_map('pollnameclean',$poll_rated);
	}
	$smarty->assign('poll_rated',$poll_rated);
	if (isset($_REQUEST['poll_title'])) {
		$smarty->assign('poll_title',$_REQUEST['poll_title']);
	}
	if (isset($_REQUEST['poll_template'])) {
		$smarty->assign('poll_template',$_REQUEST['poll_template']);
	}
	$listpolls = $polllib->get_polls('o',"$page: ");
	/*	if ($listpolls['data']) {
		$listpolls['data'] = array_map('pollnameclean',$listpolls['data']);
	}
*/
	$smarty->assign('listpolls',$listpolls['data']);
}
if ($prefs['feature_multilingual'] == 'y') {
	$languages = array();
	$languages = $tikilib->list_languages();
	$smarty->assign_by_ref('languages', $languages);

	if( isset( $_REQUEST['translationOf'] ) ) {
		$smarty->assign( 'translationOf', $_REQUEST['translationOf'] );
	}
}
$cat_type = 'wiki page';
$cat_objid = $_REQUEST["page"];
$smarty->assign('section',$section);
include_once ('tiki-section_options.php');
if ($prefs['feature_freetags'] == 'y') {
	include_once ("freetag_list.php");
	//If in preview mode get the tags from the form and not from database
	if (isset($_REQUEST["preview"])) {
	    $smarty->assign('taglist',$_REQUEST["freetag_string"]);
	}
}
if ($prefs['feature_categories'] == 'y') {
	include_once ("categorize_list.php");
	
	if (isset($_REQUEST["current_page_id"]) && $prefs['feature_wiki_categorize_structure'] == 'y' && $categlib->is_categorized('wiki page', $structure_info["pageName"])) {
		$categIds = $categlib->get_object_categories('wiki page', $structure_info["pageName"]);
		$smarty->assign('categIds',$categIds);
	}
	if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], 'tiki-index.php') && !$tikilib->page_exists($_REQUEST["page"])) { // default the categs the page you come from for a new page
		if (preg_match('/page=([^\&]+)/', $_SERVER['HTTP_REFERER'], $ms))
			$p = $ms[1];
		else
			$p = $wikilib->get_default_wiki_page();
		$cs = $categlib->get_object_categories('wiki page', $p);
		for ($i = count($categories) - 1; $i >= 0; --$i) {
			if (in_array($categories[$i]['categId'], $cs))
				$categories[$i]['incat'] = 'y';
		}
	}
}
$plugins = $wikilib->list_plugins(true);
$smarty->assign_by_ref('plugins', $plugins);
$smarty->assign('showstructs', array());
if ($structlib->page_is_in_structure($_REQUEST["page"])) {
  $structs = $structlib->get_page_structures($_REQUEST["page"]);
}
// Flag for 'page bar' that currently 'Edit' mode active
// so no need to show comments & attachments, but need
// to show 'wiki quick help'
$smarty->assign('edit_page', 'y');
$smarty->assign('categ_checked', 'n');
// Set variables so the preview page will keep the newly inputted category information
if (isset($_REQUEST['cat_categorize'])) {
  if ($_REQUEST['cat_categorize'] == 'on') {
    $smarty->assign('categ_checked', 'y');
	}
}
if ($prefs['wiki_feature_copyrights'] == 'y' && $tiki_p_edit_copyrights == 'y') {
	include_once ('lib/copyrights/copyrightslib.php');
	$copyrightslib = new CopyrightsLib($dbTiki);
	$copyrights = $copyrightslib->list_copyrights($_REQUEST["page"]);
	if ($copyrights['cant'])
	$smarty->assign_by_ref('copyrights', $copyrights['data']);
}
$defaultRows = $prefs['default_rows_textarea_wiki'];
include_once("textareasize.php");
include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','','wiki');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);
$smarty->assign('quicktagscant', $quicktags["cant"]);
if (!$user or $user == 'anonymous') {
	$smarty->assign('anon_user', 'y');
}
if ($prefs['feature_contribution'] == 'y') {
	include_once('contribution.php');
}
if ($prefs['feature_wikiapproval'] == 'y') {	
	if (substr($page, 0, strlen($prefs['wikiapproval_prefix'])) == $prefs['wikiapproval_prefix']) {
		$approvedPageName = substr($page, strlen($prefs['wikiapproval_prefix']));	
		$smarty->assign('beingStaged', 'y');
		$smarty->assign('approvedPageName', $approvedPageName);
	} elseif ($prefs['wikiapproval_approved_category'] > 0 && in_array($prefs['wikiapproval_approved_category'], $cats)) {		
		$stagingPageName = $prefs['wikiapproval_prefix'] . $page;
		if ($prefs['wikiapproval_block_editapproved'] == 'y') {
			header("location: tiki-editpage.php?page=$stagingPageName");
		}
		$smarty->assign('needsStaging', 'y');
		$smarty->assign('stagingPageName', $stagingPageName);		
	}
	if ($prefs['wikiapproval_outofsync_category'] > 0 && in_array($prefs['wikiapproval_outofsync_category'], $cats)) {
		$smarty->assign('outOfSync', 'y');
		if (!isset($_REQUEST['preview'])) {
			$smarty->assign('preview',1);
			$parsed = $tikilib->parse_data($edit_data,$is_html);
			$smarty->assign('parsed', $parsed);
			$smarty->assign('staging_preview', 'y');
		}
		if (isset($approvedPageName)) {
			include_once('lib/wiki/histlib.php');
			$approvedPageInfo = $histlib->get_page_from_history($approvedPageName, 0);
			if ($info['lastModif'] > $approvedPageInfo['lastModif']) {
				$lastSyncVersion = $histlib->get_version_by_time($page, $approvedPageInfo['lastModif']);
				// get very first version if unable to get last sync version.
				if ($lastSyncVersion == 0) $lastSyncVersion = $histlib->get_version_by_time($page, 0, 'after');
				// if really not possible, just give up.
				if ($lastSyncVersion > 0) $smarty->assign('lastSyncVersion', $lastSyncVersion );
			}
		}		
	}
}
ask_ticket('edit-page');
$ajaxlib->registerTemplate('tiki-editpage.tpl');
$ajaxlib->processRequests();
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the Index Template
$smarty->assign('mid', 'tiki-editpage.tpl');
$smarty->assign('showtags', 'n');
$smarty->assign('qtnum', '1');
$smarty->assign('qtcycle', '');
$smarty->display("tiki.tpl");
?>
