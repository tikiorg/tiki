<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/static/staticlib.php,v 1.6 2004-07-30 19:19:19 teedog Exp $

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
		
	}

	// build the static html page
	function update_page($pagename) {
		
		global $smarty, $wiki_realtime_static_path, $wikiHomePage, $style, $tiki_p_edit;
		$filename = $wiki_realtime_static_path . $pagename . '.html';
		
		// find the base_href for the static pages
		$static_http_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $wiki_realtime_static_path);
		$static_base_href = 'http://' . $_REQUEST['HTTP_HOST'] . $static_http_path;
		
		$tiki_p_edit = 'n';
		$pageobject = $this->get_page_info($pagename);
		$pagedata = $this->parse_data($pageobject["data"]);
		$pagedata = preg_replace("/tiki-index.php\?page=([^\'\"\$]+)/", "$static_base_href$1.html", $pagedata);
		
		$smarty->assign_by_ref('parsed',$pagedata);

		// Display the Index Template
		global $wikilib, $feature_wiki_pageid, $feature_categorypath, $feature_categoryobjects;
		require_once('lib/wiki/wikilib.php');
		$smarty->assign('page', $pagename);
		$creator = $wikilib->get_creator($pagename);
		$smarty->assign('creator', $creator);
		$smarty->assign('lastUser',$pageobject["user"]);
		$smarty->assign('description',$pageobject["description"]);
		$smarty->assign('feature_wiki_pageid', $feature_wiki_pageid);
		$smarty->assign('page_id',$pageobject['page_id']);
		$smarty->assign('mid', 'tiki-show_page.tpl');
		$smarty->assign('show_page_bar', 'y');
		$smarty->assign('print_page', 'n');
		$smarty->assign('categorypath',$feature_categorypath);
		$smarty->assign('categoryobjects',$feature_categoryobjects);
		
		// find the base_href for the dynamic tiki pages
		$path_parts = pathinfo($_SERVER['HTTP_REFERER']);
		$base_href = $path_parts['dirname'] . '/';
		$smarty->assign('base_href', $base_href);
		$smarty->assign('static_mode', 'y');
		
		$smarty_result = $smarty->fetch('tiki-static.tpl');
		
		// delete file
		@unlink($filename);
		// write to file
		file_put_contents($filename, $smarty_result);
		$style_path = $wiki_realtime_static_path . "styles/" . $style;
		if(!file_exists($style_path)) {
			copy("styles/$style", $style_path);
		}

	}
	
	function rename_page($oldpagename, $newpagename) {
		
		global $wiki_realtime_static_path;
		$oldfilename = $wiki_realtime_static_path . $oldpagename . '.html';
		$newfilename = $wiki_realtime_static_path . $newpagename . '.html';
		
		// rename file
		@rename($oldfilename, $newfilename);
		
		// update new page
		$this->update_page($newpagename);
		
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

	}
	
	function rebuild_all_pages() {
		
		$pages = $this->list_pages();
		foreach ($pages['data'] as $page) {
			$this->update_page($page['pageName']);
		}
		
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
		
	}

}

global $dbTiki;
$staticlib = new StaticLib($dbTiki);
?>
