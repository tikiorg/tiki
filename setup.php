<?php

// $Header: /cvsroot/tikiwiki/tiki/setup.php,v 1.24 2003-09-18 19:09:08 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ("db/tiki-db.php");

error_reporting (E_ALL);

// Remove automatic quotes added to POST/COOKIE by PHP
if (get_magic_quotes_gpc()) {
	foreach ($_REQUEST as $k => $v) {
		if (!is_array($_REQUEST[$k])) {
			$_REQUEST[$k] = stripslashes($v);
		}
	}
}

// Define and load Smarty components
define('SMARTY_DIR', 'Smarty/');
require_once (SMARTY_DIR . 'Smarty.class.php');

class Smarty_Sterling extends Smarty {
	function Smarty_Sterling($tikidomain = "") {
		$this->template_dir = "templates/";

		$this->compile_dir = "templates_c/$tikidomain";
		$this->config_dir = "configs/";
		$this->cache_dir = "cache/$tikidomain";
		$this->caching = false;
		$this->assign('app_name', 'Sterling');
		// do we need this?
		// $this->request_use_auto_globals = true;
	//$this->debugging = true;
	//$this->debug_tpl = 'debug.tpl';
	}

	function _smarty_include($_smarty_include_tpl_file,$_smarty_include_vars) {
		global $style;

		global $style_base;

		if (isset($style) && isset($style_base)) {
			if (file_exists("templates/styles/$style_base/$_smarty_include_tpl_file")) {
				$_smarty_include_tpl_file = "styles/$style_base/$_smarty_include_tpl_file";
			}
		}

		return parent::_smarty_include($_smarty_include_tpl_file,$_smarty_include_vars);
	}

	function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false) {
		global $language;

		global $style;
		global $style_base;

		if (isset($style) && isset($style_base)) {
			if (file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";
			}
		}

		$_smarty_cache_id = $language . $_smarty_cache_id;
		$_smarty_compile_id = $language . $_smarty_compile_id;
		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
	}
}

if (!isset($tikidomain))
	$tikidomain = "";

$smarty = new Smarty_Sterling($tikidomain);
$smarty->load_filter('pre', 'tr');
//$smarty->load_filter('output','trimwhitespace');

if (isset($_REQUEST['highlight'])) {
	$smarty->load_filter('output','highlight');
}
// Count number of online users using:
// print($GLOBALS["PHPSESSID"]);

?>
