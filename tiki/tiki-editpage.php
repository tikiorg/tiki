<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-editpage.php,v 1.51 2003-10-25 16:15:45 zaufi Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/wiki/wikilib.php');

include_once ('lib/structures/structlib.php');

if ($feature_wiki != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_view != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view this section"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["page"]) || $_REQUEST["page"] == '') {
  $smarty->assign('msg',tra("No page indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
} else {
	$page = $_REQUEST["page"];

	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}

function compare_import_versions($a1, $a2) {
	return $a1["version"] - $a2["version"];
}

if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
	require ("lib/webmail/mimeDecode.php");

	$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");
	$data = '';

	while (!feof($fp)) {
		$data .= fread($fp, 8192 * 16);
	}

	fclose ($fp);
	$name = $_FILES['userfile1']['name'];
	$params = array(
		'input' => $data,
		'crlf' => "\r\n",
		'include_bodies' => TRUE,
		'decode_headers' => TRUE,
		'decode_bodies' => TRUE
	);

	$output = Mail_mimeDecode::decode($params);
	unset ($parts);
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

			$authorid = urldecode($part["author_id"]);

			if (isset($part["hits"]))
				$hits = urldecode($part["hits"]);
			else
				$hits = 0;

			$ex = substr($part["body"], 0, 25);
			//print(strlen($part["body"]));
			$msg = '';

			if (isset($_REQUEST["save"])) {
				if ($tikilib->page_exists($pagename)) {
					$tikilib->update_page($pagename, $part["body"], tra('page imported'), $author, $authorid, $description);
				} else {
					$tikilib->create_page($pagename, $hits, $part["body"], $lastmodified, tra('created from import'), $author,
						$authorid, $description);
				}
			} else {
				$_REQUEST["edit"] = $last_part;
			}
		}
	}

	if (isset($_REQUEST["save"])) {
		unset ($_REQUEST["save"]);

		header ("location: tiki-index.php?page=$page");
		die;
	}
}

// Upload pictures here
if (($feature_wiki_pictures == 'y') && (isset($tiki_p_upload_picture)) && ($tiki_p_upload_picture == 'y')) {
	if (isset($_FILES['picfile1']) && is_uploaded_file($_FILES['picfile1']['tmp_name'])) {
		$picname = $_FILES['picfile1']['name'];

		move_uploaded_file($_FILES['picfile1']['tmp_name'], "img/wiki_up/$tikidomain" . $picname);
		$_REQUEST["edit"] = $_REQUEST["edit"] . "{picture file=img/wiki_up/$tikidomain$picname}";
	}
}
/**
 * \brief Parsed HTML tree walker (used by HTML sucker)
 *
 * This is initial implementation (don't generate any wiki syntaxes)
 *
 */
function walk_and_parse(&$c, &$src, &$stack)
{
    for ($i=0; $i <= $c["contentpos"]; $i++)
    {
        // If content type 'text' output it to destination...
        if ($c[$i]["type"] == "text")
            $src .= $c[$i]["data"];
        elseif ($c[$i]["type"] == "tag")
        {
            if ($c[$i]["data"]["type"] == "open")
            {
                // Open tag type
                switch ($c[$i]["data"]["name"])
                {
                case "title"; $src .= "\n!"; array_push($stack, array('tag' => 'title', 'string' => "\n")); break;
                case "p": $src .= "\n"; array_push($stack, array('tag' => 'p', 'string' => "\n")); break;
                case "b": $src .= '__'; array_push($stack, array('tag' => 'b', 'string' => '__')); break;
                case "i": $src .= "''"; array_push($stack, array('tag' => 'i', 'string' => "''")); break;
                case "u": $src .= "=="; array_push($stack, array('tag' => 'u', 'string' => "==")); break;
                case "center": $src .= '::'; array_push($stack, array('tag' => 'center', 'string' => '::')); break;
                case "code": $src .= '-+';  array_push($stack, array('tag' => 'code', 'string' => '+-')); break;
                case "h1": $src .= "\n!"; array_push($stack, array('tag' => 'h1', 'string' => "\n")); break;
                case "h2": $src .= "\n!!"; array_push($stack, array('tag' => 'h2', 'string' => "\n")); break;
                case "h3": $src .= "\n!!!"; array_push($stack, array('tag' => 'h3', 'string' => "\n")); break;
                case "h3": $src .= "\n!!!!"; array_push($stack, array('tag' => 'h4', 'string' => "\n")); break;
                case "h5": $src .= "\n!!!!!"; array_push($stack, array('tag' => 'h5', 'string' => "\n")); break;
                case "h6": $src .= "\n!!!!!!"; array_push($stack, array('tag' => 'h6', 'string' => "\n")); break;
                case "pre": $src .= '~pp~'; array_push($stack, array('tag' => 'pre', 'string' => '~/pp~')); break;
                case "font":
                    // If href attribute present in <a> tag
                    if (array_key_exists("pars", $c[$i]) 
                     && array_key_exists("color", $c[$i]["pars"])
                     && array_key_exists("value", $c[$i]["pars"]["color"]))
                    {
                        $src .= '~~'.$c[$i]["pars"]["color"]["value"].':';
                        array_push($stack, array('tag' => 'font', 'string' => '~~'));
                    }
                    break;
                case "img":
                    // If href attribute present in <a> tag
                    if (array_key_exists("pars", $c[$i]) 
                     && array_key_exists("src", $c[$i]["pars"])
                     && array_key_exists("value", $c[$i]["pars"]["src"]))
                        $src .= '(img src='.$c[$i]["pars"]["src"]["value"].')';
                    break;
                case "a":
                    // If href attribute present in <a> tag
                    if (array_key_exists("pars", $c[$i]) 
                     && array_key_exists("href", $c[$i]["pars"])
                     && array_key_exists("value", $c[$i]["pars"]["href"]))
                    {
                        $src .= '['.$c[$i]["pars"]["href"]["value"].'|';
                        array_push($stack, array('tag' => 'a', 'string' => ']'));
                    }
                    break;
                }
            }
            else
            {
                // This is close tag type. Is that smth we r waiting for?
                $e = end($stack);
                if ($c[$i]["data"]["name"] == $e['tag'])
                {
                    $src .= $e['string'];
                    array_pop($stack);
                }
            }
        }
        // Recursive call on tags with content...
        if (isset($c[$i]["content"]))
        {
//            if (substr($src, -1) != " ") $src .= " ";
            walk_and_parse($c[$i]["content"], $src, $stack);
        }
    }
}
// Suck another page and append to the end of current
include ('lib/htmlparser/htmlparser.inc');
$repl = isset($_REQUEST["suck_url"]) ? $_REQUEST["suck_url"] : '';
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
    $sdta = @file_get_contents($suck_url);
    if (isset($php_errormsg) && strlen($php_errormsg))
    {
        $smarty->assign('msg', tra("Can't import remote HTML page"));
        $smarty->display("styles/$style_base/error.tpl");
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
            $smarty->display("styles/$style_base/error.tpl");
            die;
        }
        $grammar = unserialize(fread($fp, filesize($grammarfile)));
        fclose($fp);
        // create parser object, insert html code and parse it
        $htmlparser = new HtmlParser($sdta, $grammar, '', 0);
        $htmlparser->Parse();
        // Should I try to convert HTML to wiki?
        $parseddata = '';
        $close_stack = array();
        walk_and_parse($htmlparser->content, $parseddata, $close_stack);
        $sdta = $parseddata;
    }
    $_REQUEST['edit'] .= $sdta;
}
//
if(strcasecmp(substr($page,0,8),"UserPage")==0) {
	$name = substr($page,8);
	if(strcasecmp($user,$name)!=0) {
		if($tiki_p_admin != 'y') {
 			$smarty->assign('msg',tra("You cannot edit this page because it is a user personal page"));
 			$smarty->display("styles/$style_base/error.tpl");
 			die;
 		}
 	}
}

if ($_REQUEST["page"] == 'SandBox' && $feature_sandbox != 'y') {
	$smarty->assign('msg', tra("The SandBox is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!isset($_REQUEST["comment"])) {
	$_REQUEST["comment"] = '';
}

/*
if(!page_exists($page)) {
  $smarty->assign('msg',tra("Page cannot be found"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}
*/
include_once ("tiki-pagesetup.php");

// Now check permissions to access this page
if ($page != 'SandBox') {
	if ($tiki_p_edit != 'y') {
		$smarty->assign('msg', tra("Permission denied you cannot edit this page"));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}
}

// Get page data
$info = $tikilib->get_page_info($page);
if(isset($info['wiki_cache']) && $info['wiki_cache']!=0) {
  $wiki_cache = $info['wiki_cache'];
  $smarty->assign('wiki_cache',$wiki_cache);
}

if ($info["flag"] == 'L') {
	$smarty->assign('msg', tra("Cannot edit page because it is locked"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($page != 'SandBox') {
	// Permissions
	// if this page has at least one permission then we apply individual group/page permissions
	// if not then generic permissions apply
	if ($tiki_p_admin != 'y') {
		if ($userlib->object_has_one_permission($page, 'wiki page')) {
			if (!$userlib->object_has_permission($user, $page, 'wiki page', 'tiki_p_edit')) {
				$smarty->assign('msg', tra("Permission denied you cannot edit this page"));

				$smarty->display("styles/$style_base/error.tpl");
				die;
			}
		} else {
			if ($tiki_p_edit != 'y') {
				$smarty->assign('msg', tra("Permission denied you cannot edit this page"));

				$smarty->display("styles/$style_base/error.tpl");
				die;
			}
		}
	}
}

if ($tiki_p_admin != 'y') {
	if ($tiki_p_use_HTML != 'y') {
		$_REQUEST["allowhtml"] = 'off';
	}
}

//$smarty->assign('allowhtml','y');

/*
if(!$user && $anonCanEdit<>'y') {

  header("location: tiki-index.php");
  die;
  //$smarty->assign('msg',tra("Anonymous users cannot edit pages"));
  //$smarty->display("styles/$style_base/error.tpl");
  //die;
}
*/
$smarty->assign_by_ref('data', $info);

$smarty->assign('footnote', '');
$smarty->assign('has_footnote', 'n');

if ($feature_wiki_footnotes == 'y') {
	if ($user) {
		$x = $wikilib->get_footnote($user, $page);

		$footnote = $wikilib->get_footnote($user, $page);
		$smarty->assign('footnote', $footnote);

		if ($footnote)
			$smarty->assign('has_footnote', 'y');

		$smarty->assign('parsed_footnote', $tikilib->parse_data($footnote));

		if (isset($_REQUEST['footnote'])) {
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

if (isset($_REQUEST["templateId"]) && $_REQUEST["templateId"] > 0) {
	$template_data = $tikilib->get_template($_REQUEST["templateId"]);

	$_REQUEST["edit"] = $template_data["content"];
	$_REQUEST["preview"] = 1;
}

if(isset($_REQUEST["edit"])) {
  
  if(isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"]=="on") {
    $edit_data = $_REQUEST["edit"];  
  } else {
	$edit_data = htmlspecialchars($_REQUEST["edit"]);
  }
  
  
} else {
	if (isset($info["data"])) {
		$edit_data = $info["data"];
	} else {
		$edit_data = '';
	}
}

if (isset($wiki_feature_copyrights) && $wiki_feature_copyrights == 'y') {
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

$smarty->assign('commentdata', '');

if (isset($_REQUEST["comment"])) {
	$smarty->assign_by_ref('commentdata', $_REQUEST["comment"]);
}

if (isset($info["description"])) {
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
if(isset($_REQUEST["allowhtml"]) and $_REQUEST["allowhtml"] == "on") {
    $smarty->assign('allowhtml','y');
} else {
	$smarty->assign('allowhtml','n');
}

$smarty->assign_by_ref('pagedata',htmldecode($edit_data));
$parsed = $tikilib->parse_data($edit_data);

/* SPELLCHECKING INITIAL ATTEMPT */
//This nice function does all the job!
if ($wiki_spellcheck == 'y') {
	if (isset($_REQUEST["spellcheck"]) && $_REQUEST["spellcheck"] == 'on') {
		$parsed = $tikilib->spellcheckreplace($edit_data, $parsed, $language, 'editwiki');

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

function htmldecode($string) {
   $string = strtr($string, array_flip(get_html_translation_table(HTML_ENTITIES)));
   $string = preg_replace("/&#([0-9]+);/me", "chr('\\1')", $string);
   return $string;
}

function parse_output(&$obj, &$parts,$i) {  
  if(!empty($obj->parts)) {    
    for($i=0; $i<count($obj->parts); $i++)      
      parse_output($obj->parts[$i], $parts,$i);  
  }else{    
    $ctype = $obj->ctype_primary.'/'.$obj->ctype_secondary;    
    switch($ctype) {    
      case 'application/x-tikiwiki':
         $aux["body"] = $obj->body;  
         $ccc=$obj->headers["content-type"];
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
if (isset($_REQUEST["save"])) {
  // Check if all Request values are delivered, and if not, set them
  // to avoid error messages. This can happen if some features are 
  // disabled
  if(!isset($_REQUEST["description"])) $_REQUEST["description"]='';
  if(!isset($_REQUEST["comment"])) $_REQUEST["comment"]='';

  if(isset($_REQUEST['wiki_cache'])) {
    $wikilib->set_page_cache($_REQUEST['page'],$_REQUEST['wiki_cache']);
  }
  include_once("lib/imagegals/imagegallib.php");
  $cat_type='wiki page';
  $cat_objid = $_REQUEST["page"];
  $cat_desc = ($feature_wiki_description == 'y') ? substr($_REQUEST["description"],0,200) : '';
  $cat_name = $_REQUEST["page"];
  $cat_href="tiki-index.php?page=".$cat_objid;
  include_once("categorize.php");

  if ((($feature_wiki_description == 'y')
    && (md5($info["description"]) != md5($_REQUEST["description"])))
    || (md5($info["data"]) != md5($_REQUEST["edit"]))) {

    $page = $_REQUEST["page"];

    if(isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"]=="on") {
      $edit = $_REQUEST["edit"];  
    } else {
//      $edit = strip_tags($_REQUEST["edit"]);
	  $edit = htmlspecialchars($_REQUEST['edit']);
    }

    // add permisions here otherwise return error!
    if(isset($wiki_feature_copyrights) && $wiki_feature_copyrights == 'y'
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
  
    // If page exists
    if(!$tikilib->page_exists($_REQUEST["page"])) {
      // Extract links and update the page

	  $links = $tikilib->get_links($_REQUEST["edit"]);
	  /*
	  $notcachedlinks = $tikilib->get_links_nocache($_REQUEST["edit"]);
	  $cachedlinks = array_diff($links, $notcachedlinks);
	  $tikilib->cache_links($cachedlinks); 
	  */
      $t = date("U");
      $tikilib->create_page($_REQUEST["page"], 0, $edit, $t, $_REQUEST["comment"],$user,$_SERVER["REMOTE_ADDR"],$description);  
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
      $tikilib->update_page($_REQUEST["page"],$edit,$_REQUEST["comment"],$user,$_SERVER["REMOTE_ADDR"],$description,$minor);
    }

    $page = urlencode($page);
    header("location: tiki-index.php?page=$page");
    die;
  } else {
    $page = urlencode($page);
    header("location: tiki-index.php?page=$page");
    die;
  }
}

if ($feature_wiki_templates == 'y' && $tiki_p_use_content_templates == 'y') {
	$templates = $tikilib->list_templates('wiki', 0, -1, 'name_asc', '');
}

$smarty->assign_by_ref('templates', $templates["data"]);

$cat_type = 'wiki page';
$cat_objid = $_REQUEST["page"];
include_once ("categorize_list.php");

if ($feature_theme_control == 'y') {
	include ('tiki-tc.php');
}

$section = 'wiki';
include_once ('tiki-section_options.php');

// 27-Jun-2003, by zaufi
// Get plugins with descriptions
global $wikilib;
$plugin_files = $wikilib->list_plugins();
$plugins = array();

// Request help string from each plugin module
foreach ($plugin_files as $pfile) {
	$pinfo["file"] = $pfile;

	$pinfo["help"] = $wikilib->get_plugin_description($pfile);
	$pinfo["name"] = strtoupper(str_replace(".php", "", str_replace("wikiplugin_", "", $pfile)));
	$plugins[] = $pinfo;
}

$smarty->assign_by_ref('plugins', $plugins);

$pageAlias = $structlib->get_page_alias($_REQUEST["page"]);
$smarty->assign('pageAlias', $pageAlias);

// Flag for 'page bar' that currently 'Edit' mode active
// so no need to show comments & attachments, but need
// to show 'wiki quick help'
$smarty->assign('edit_page', 'y');

// Set variables so the preview page will keep the newly inputted category information
if (isset($_REQUEST['cat_categorize'])) {
	if ($_REQUEST['cat_categorize'] == 'on') {
		$smarty->assign('categ_checked', 'y');
	}
}

// Display the Index Template
$smarty->assign('mid', 'tiki-editpage.tpl');
$smarty->assign('show_page_bar', 'y');
$smarty->display("styles/$style_base/tiki.tpl");

?>
