<?php

// $Header: /cvsroot/tikiwiki/tiki/setup_smarty.php,v 1.17 2004-03-18 17:02:13 sylvieg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

if (isset($_SERVER["REQUEST_URI"])) {
  ini_set('session.cookie_path', str_replace( "\\", "/", dirname($_SERVER["REQUEST_URI"])));
}

require_once ("db/tiki-db.php");
require_once("lib/tikiticketlib.php");

error_reporting (E_ALL);
// Set the separator for PHP generated tags to be &amp; instead of &
// This is necessary for XHTML compliance
ini_set("arg_separator.output","&amp;");

//ini_set('register_globals','off');

// Remove automatic quotes added to POST/COOKIE by PHP
if (get_magic_quotes_gpc()) {
	foreach ($_REQUEST as $k => $v) {
		if (!is_array($_REQUEST[$k])) $_REQUEST[$k] = stripslashes($v);
	}
}

// Define and load Smarty components

// let smarty define SMARTY_DIR so it's an absolute path :
// define('SMARTY_DIR', 'lib/smarty/');

require_once ( 'lib/smarty/libs/Smarty.class.php');

class Smarty_TikiWiki extends Smarty {
	
	function Smarty_TikiWiki($tikidomain = "") {
		$this->template_dir = 'templates/';
		$this->compile_dir = "templates_c/$tikidomain";
		$this->config_dir = "configs/";
		$this->cache_dir = "cache/$tikidomain";
		$this->caching = 0;
		$this->assign('app_name', 'TikiWiki');
		$this->plugins_dir = array(	// the directory order must be like this to overload a plugin
			dirname(dirname(SMARTY_DIR))."/smarty_tiki",
			SMARTY_DIR."plugins"
		);
	}

	function _smarty_include($params) {
		global $style, $style_base;

		if (isset($style) && isset($style_base)) {
			if (file_exists("templates/styles/$style_base/".$params['smarty_include_tpl_file'])) {
				$params['smarty_include_tpl_file'] = "styles/$style_base/".$params['smarty_include_tpl_file'];
			}
		}
		return parent::_smarty_include($params);
	}

	function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false) {
		global $language, $style, $style_base;

		if (isset($style) && isset($style_base)) {
			if (file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";
			}
		}
		$_smarty_cache_id = $language . $_smarty_cache_id;
		$_smarty_compile_id = $language . $_smarty_compile_id;
		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
	}
	/* fetch in a specific language  without theme consideration */
	function fetchLang($lg, $_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false)  {
		global $language, $style, $style_base;

		$lgSave = $language;
		$language = $lg; // the global var needs to be changed in case the tpl needs to be compiled

		$_smarty_cache_id = $language . $_smarty_cache_id;
		$_smarty_compile_id = $language . $_smarty_compile_id;
		$res = parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
		$language = $lgSave;
		return ereg_replace("^[ \t]*", "", $res);
	}
}

if (!isset($tikidomain))
	$tikidomain = "";

$smarty = new Smarty_TikiWiki($tikidomain);
$smarty->load_filter('pre', 'tr');
//$smarty->load_filter('output','trimwhitespace');

if (isset($_REQUEST['highlight'])) {
	$smarty->load_filter('output','highlight');
}

// Count number of online users using:
// print($GLOBALS["PHPSESSID"]);

?>
