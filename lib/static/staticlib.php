<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/static/staticlib.php,v 1.4 2004-07-29 21:38:26 teedog Exp $

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
		$backlinks = $wikilib->get_backlinks($pagename);
		foreach ($backlinks as $backlink) {
			$this->update_page($backlink['fromPage']);
		}
		$this->update_page($pagename);
		
	}

	// build the static html page
	function update_page($pagename) {
		
		global $smarty, $tikidomain, $wikiHomePage, $style, $tiki_p_edit;
		$static_path = "static";
		if ($tikidomain) {
			$static_path .= "/$tikidomain";
		}
		$filename = $static_path . '/' . $pagename . '.html';
		
		$tiki_p_edit = 'n';
		$pageobject = $this->get_page_info($pagename);
		$pagedata = $this->parse_data($pageobject["data"]);
		$pagedata = preg_replace("/tiki-index.php\?page=([^\'\"\$]+)/", "$1.html", $pagedata);
		
		$smarty->assign_by_ref('parsed',$pagedata);

		// Display the Index Template
		global $wikilib, $feature_wiki_pageid;
		$smarty->assign('page', $pagename);
		$creator = $wikilib->get_creator($pagename);
		$smarty->assign('creator', $creator);
		$smarty->assign('lastUser',$pageobject["user"]);
		$smarty->assign('description',$pageobject["description"]);
		$smarty->assign('print_page','y');
		$smarty->assign('feature_wiki_pageid', $feature_wiki_pageid);
		$smarty->assign('page_id',$pageobject['page_id']);
		$smarty->assign('mid', 'tiki-show_page.tpl');
		$smarty->assign('show_page_bar', 'n');
		$smarty->assign('print_page', 'y');
		
		$smarty_result = $smarty->fetch('tiki-static.tpl');
		
		// delete file
		@unlink($filename);
		// write to file
		file_put_contents($filename, $smarty_result);
		if(!file_exists("static/styles/$style")) {
			copy("styles/$style", "static/styles/$style");
		}

	}
	
	function rename_page($oldpagename, $newpagename) {
		
		global $tikidomain;
		$static_path = "static";
		if ($tikidomain) {
			$static_path .= "/$tikidomain";
		}
		$oldfilename = $static_path . '/' . $oldpagename . '.html';
		$newfilename = $static_path . '/' . $newpagename . '.html';
		
		// rename file
		@rename($oldfilename, $newfilename);
		
		// update new page
		$this->update_page($newpagename);
		
	}
	
	function remove_page($pagename) {

		global $tikidomain, $wikilib;
		$static_path = "static";
		if ($tikidomain) {
			$static_path .= "/$tikidomain";
		}
		$filename = $static_path . '/' . $pagename . '.html';
		
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
		foreach ($pages as $page) {
			$this->update_page($page['pageName']);
		}
		
	}
	
	function purge_ghost_pages() {

		global $tikidomain;
		$static_path = "static";
		if ($tikidomain) {
			$static_path .= "/$tikidomain";
		}
		$dir = $static_path;

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
