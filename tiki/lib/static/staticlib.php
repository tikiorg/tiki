<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/static/staticlib.php,v 1.14 2004-08-03 20:53:31 teedog Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

if (!function_exists('file_put_contents')) {
   function file_put_contents($filename, $data)
   {
       if (($h = @fopen($filename, 'w')) === false) {
           return false;
       }
       if (($bytes = @fwrite($h, $data)) === false) {
           return false;
       }
       fclose($h);
       return $bytes;
   }
} 

class StaticLib extends TikiLib {
	function StaticLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to StaticLib constructor");
		}
		$this->db = $db;
	}
	
	function create_page($pagename) {
		
		global $wikilib;
		require_once('lib/wiki/wikilib.php');
		$backlinks = $wikilib->get_backlinks($pagename);
		foreach ($backlinks as $backlink) {
			$this->update_page($backlink['fromPage']);
		}
		$this->update_page($pagename);
		return TRUE;
		
	}

	// build the static html page
	function update_page($pagename, $rebuildall = FALSE) {
		
		global $smarty, $wiki_realtime_static_path, $wiki_realtime_static_group, $wikiHomePage, $style, $style_base, $feature_categories, $feature_categorypath, $feature_categoryobjects;
		
		// variable for whether an error occured
		$display_error = FALSE;
		
		// what permissions should be used to generate static pages?
		global $userlib;
		require_once('lib/userslib.php');
		global $dbTiki;
		$userlib = new UsersLib($dbTiki);
		$static_group = $wiki_realtime_static_group;
		$user = NULL;
		$smarty->assign('user', $user);
		// reset all perms
		global $cachelib;
		include_once('lib/cache/cachelib.php');
		if(!$cachelib->isCached("allperms")) {
			$allperms = $userlib->get_permissions(0, -1, 'permName_desc', '', '');
			$cachelib->cacheItem("allperms",serialize($allperms));
		} else {
			$allperms = unserialize($cachelib->getCached("allperms"));
		}
		$allperms = $allperms["data"];
		foreach ($allperms as $perm) {
			$perm = $perm['permName'];
			$smarty->assign("$perm", 'n');
			$$perm = 'n';
		}
		// set perms from the chosen group
		$perms = $userlib->get_group_permissions($static_group);
		foreach ($perms as $perm) {
			$smarty->assign("$perm", 'y');
			$$perm = 'y';
		}
		if (empty($tiki_p_admin)) {
			$tiki_p_admin = 'n';
			$smarty->assign('tiki_p_admin', 'n');
		}
		if (empty($tiki_p_view_wiki_history)) {
			$tiki_p_view_wiki_history = 'n';
		}
		
		global $wikilib;
		require_once('lib/wiki/wikilib.php');

		$filename = $wiki_realtime_static_path . $pagename . '.html';
		
		// find the base_href for the static pages
		$static_http_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $wiki_realtime_static_path);
		$static_base_href = 'http://' . $_REQUEST['HTTP_HOST'] . $static_http_path;
		
		$tiki_p_edit = 'n';
		$page = $pagename;
		$info = $this->get_page_info($pagename);
		$pagedata = $this->parse_data($info["data"]);
		$pagedata = preg_replace("/tiki-index.php\?page=([^\'\"\$]+)/", "$static_base_href$1.html", $pagedata);
		
		$smarty->assign_by_ref('parsed',$pagedata);

		require_once('tiki-pagesetup.php');
		
		$objId = urldecode($page);
		if ($tiki_p_admin != 'y' && $feature_categories == 'y' && empty($object_has_perms)) {
			// Check to see if page is categorized
			global $categlib;
			include_once('lib/categories/categlib.php');
			$perms_array = $categlib->get_object_categories_perms($user, 'wiki page', $objId, $static_group);
		   	if (is_array($perms_array)) {
		   		$is_categorized = TRUE;
		    	foreach ($perms_array as $perm => $value) {
		    		$$perm = $value;
		    	}
		   	} else {
		   		$is_categorized = FALSE;
		   	}
			if ($is_categorized && isset($tiki_p_view_categories) && $tiki_p_view_categories != 'y') {
				$smarty->assign('msg',tra("Permission denied you cannot view this page"));
			    $display_error = TRUE;
			}
		} elseif ($feature_categories == 'y') {
			global $categlib;
			require_once('lib/categories/categlib.php');
			$is_categorized = $categlib->is_categorized('wiki page',$objId);
		} else {
			$is_categorized = FALSE;
		}


		// Now check permissions to access this page
		if($tiki_p_view != 'y') {
			$smarty->assign('msg',tra("Permission denied you cannot view this page"));
		    $display_error = TRUE;
		}
		
		// Get translated page
		global $feature_multilingual;
		if ($feature_multilingual == 'y' && $info['lang'] && $info['lang'] != "NULL") { //temporary patch
			include_once("lib/multilingual/multilinguallib.php");
			$trads = $multilinguallib->getTranslations('wiki page', $info['page_id'], $page, $info['lang']);
			$smarty->assign('trads', $trads);
		}
		
		// Get the backlinks for the page "page"
		$backlinks = $wikilib->get_backlinks($page);
		$smarty->assign_by_ref('backlinks', $backlinks);

		$smarty->assign('wiki_extras','y');
		// Display category path or not (like {catpath()})
		if ($feature_categories == 'y') {
			if (isset($is_categorized) && $is_categorized) {
				global $categlib;
				include_once('lib/categories/categlib.php');
			    $smarty->assign('is_categorized','y');
			    if(isset($feature_categorypath) and $feature_categories == 'y') {
					if ($feature_categorypath == 'y') {
					    $cats = $categlib->get_object_categories('wiki page',$objId);
					    $display_catpath = $categlib->get_categorypath($cats);
					    $smarty->assign('display_catpath',$display_catpath);
					}
			    }
			    // Display current category objects or not (like {category()})
			    if (isset($feature_categoryobjects) and $feature_categories == 'y') {
					if ($feature_categoryobjects == 'y') {
					    $catids = $categlib->get_object_categories('wiki page', $objId);
					    $display_catobjects = $categlib->get_categoryobjects($catids);
					    $smarty->assign('display_catobjects',$display_catobjects);
					}
			    }
			} else {
			    $smarty->assign('is_categorized','n');
			}
		}

		global $feature_wiki_dblclickedit, $feature_wiki_page_footer;
		$smarty->assign('feature_wiki_dblclickedit',$feature_wiki_dblclickedit);
		$smarty->assign('tiki_p_view_wiki_history',$tiki_p_view_wiki_history);
		$smarty->assign('is_a_wiki_page', 'y');

		if ($feature_wiki_page_footer == 'y') {
			$smarty->assign('feature_wiki_page_footer', 'y');
			global $wiki_page_footer_content;
			$current_url = $static_base_href . $pagename . '.html';
			$content = str_replace('{url}', $current_url, $wiki_page_footer_content);
			$smarty->assign('wiki_page_footer_content', $content);
		} else {
			$smarty->assign('feature_wiki_page_footer', 'n');
		}

		// update the module information
		global $user_assigned_modules, $modallgroups, $modseparateanon, $tikidomain, $language;
		$tikilib = $this;
		$static_mode = 'y';
		include('tiki-modules.php');

		// Display the Index Template
		global $wikilib, $feature_wiki_pageid;
		require_once('lib/wiki/wikilib.php');
		$smarty->assign('page', $pagename);
		$creator = $wikilib->get_creator($pagename);
		$smarty->assign('creator', $creator);
		$smarty->assign('lastUser',$info["user"]);
		$smarty->assign('description',$info["description"]);
		$smarty->assign('feature_wiki_pageid', $feature_wiki_pageid);
		$smarty->assign('page_id',$info['page_id']);
		$smarty->assign('mid', 'tiki-show_page.tpl');
		$smarty->assign('show_page_bar', 'y');
		$smarty->assign('print_page', 'n');
		$smarty->assign('categorypath',$feature_categorypath);
		$smarty->assign('categoryobjects',$feature_categoryobjects);
		$smarty->assign('style', $style);
		$smarty->assign('style_base', $style_base);

		// find the base_href for the dynamic tiki pages
		$path_parts = pathinfo($_SERVER['HTTP_REFERER']);
		$base_href = $path_parts['dirname'] . '/';
		$smarty->assign('base_href', $base_href);
		$smarty->assign('static_mode', 'y');
		
		if (!$display_error) {
			$smarty_result = $smarty->fetch('tiki-static.tpl');
		} else {
			$smarty_result = $smarty->fetch('error.tpl');
		}
		
		// delete file
		@unlink($filename);
		// write to file
		file_put_contents($filename, $smarty_result);
		$style_path = $wiki_realtime_static_path . "styles/" . $style;
		if(!file_exists($style_path)) {
			copy("styles/$style", $style_path);
		}
		
		return TRUE;
		
	}
	
	function rename_page($oldpagename, $newpagename) {
		
		global $wiki_realtime_static_path;
		$oldfilename = $wiki_realtime_static_path . $oldpagename . '.html';
		$newfilename = $wiki_realtime_static_path . $newpagename . '.html';
		
		// rename file
		@rename($oldfilename, $newfilename);
		
		// update new page
		$this->update_page($newpagename);
		return TRUE;
		
	}
	
	function remove_page($pagename) {

		global $wiki_realtime_static_path, $wikilib;
		require_once('lib/wiki/wikilib.php');
		$filename = $wiki_realtime_static_path . $pagename . '.html';
		
		// delete file
		@unlink($filename);

		// update pages that link to the removed page
		$backlinks = $wikilib->get_backlinks($pagename);
		foreach ($backlinks as $backlink) {
			$this->update_page($backlink['fromPage']);
		}
		return TRUE;

	}
	
	function rebuild_all_pages() {
		
		$pages = $this->list_pages();
		foreach ($pages['data'] as $page) {
			$rebuildall = TRUE;
			$this->update_page($page['pageName'], $rebuildall);
		}
		
		return TRUE;
		
	}
	
	function purge_ghost_pages() {

		global $wiki_realtime_static_path;
		$dir = $wiki_realtime_static_path;

		// supress the PHP error because that causes Tiki to crash
		$dh = @opendir($dir);
	
		if (!$dh) {
			return die;
		}

		while ($file = readdir($dh)) {
			//Don't list subdirectories
			if (!is_dir("$dir/$file")) {
				$filesarray[] = $file;
			}
		}
		closedir($dh);
		
		foreach ($filesarray as $file) {
			$pagename = str_replace('.html', '', $file);
			if (!$this->page_exists($pagename)) {
				$filename = $dir . '/' . $file;
				@unlink($filename);
			}
		}
		return TRUE;
		
	}

}

global $dbTiki;
$staticlib = new StaticLib($dbTiki);
?>
