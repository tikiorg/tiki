<?php

// $Header: /cvsroot/tikiwiki/tiki/setup_smarty.php,v 1.25 2004-05-06 00:18:14 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== FALSE) {
  header("location: index.php");
	die();
}

// uncomment and adapt the following line if you use smarty external to tiki
// define('SMARTY_DIR', 'lib/smarty/');

require_once ( 'lib/smarty/libs/Smarty.class.php');

class Smarty_TikiWiki extends Smarty {
	
	function Smarty_TikiWiki($tikidomain = "") {
		if ($tikidomain) { $tikidomain.= '/'; }
		$this->template_dir = 'templates/';
		$this->compile_dir = "templates_c/$tikidomain";
		$this->config_dir = "configs/";
		$this->cache_dir = "templates_c/$tikidomain";
		$this->caching = 0;
		$this->assign('app_name', 'TikiWiki');
		$this->plugins_dir = array(	// the directory order must be like this to overload a plugin
			dirname(dirname(SMARTY_DIR))."/smarty_tiki",
			SMARTY_DIR."plugins"
		);
	}

	function _smarty_include($params) {
		global $style, $style_base, $tikidomain;

		if (isset($style) && isset($style_base)) {
			if ($tikidomain and file_exists("templates/$tikidomain/styles/$style_base/".$params['smarty_include_tpl_file'])) {
				$params['smarty_include_tpl_file'] = "$tikidomain/styles/$style_base/".$params['smarty_include_tpl_file'];
			} elseif ($tikidomain and file_exists("templates/$tikidomain/".$params['smarty_include_tpl_file'])) {
				$params['smarty_include_tpl_file'] = "$tikidomain/".$params['smarty_include_tpl_file'];
			} elseif (file_exists("templates/styles/$style_base/".$params['smarty_include_tpl_file'])) {
				$params['smarty_include_tpl_file'] = "styles/$style_base/".$params['smarty_include_tpl_file'];
			}
		}
		return parent::_smarty_include($params);
	}

	function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false) {
		global $language, $style, $style_base, $tikidomain;

		if (isset($style) && isset($style_base)) {
			if ($tikidomain and file_exists("templates/$tikidomain/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/styles/$style_base/$_smarty_tpl_file";
			} elseif ($tikidomain and file_exists("templates/$tikidomain/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/$_smarty_tpl_file";
			} elseif (file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";
			}
		}
		$_smarty_cache_id = $language . $_smarty_cache_id;
		$_smarty_compile_id = $language . $_smarty_compile_id;
		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
	}
	/* fetch in a specific language  without theme consideration */
	function fetchLang($lg, $_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false)  {
		global $language;
		global $lang;

		$_smarty_cache_id = $lg . $_smarty_cache_id;
		$_smarty_compile_id = $lg . $_smarty_compile_id;
		$isCompiled = $this->_is_compiled($_smarty_tpl_file, $this->_get_compile_path($_smarty_tpl_file));
		if (!$isCompiled) {
			$lgSave = $language;
			$language = $lg;
			include("lang/$language/language.php");
				// the language file needs to be included again:
				// the file could have been included before: prefilter.tr using include_once will not reload the file
				// but the $lang can be from another language
		}
		$res = parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
		if (!$isCompiled) {
			$language = $lgSave;
			include ("lang/$language/language.php");
		}
		return ereg_replace("^[ \t]*", "", $res);
	}
	function is_cached($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null) {
		global $language, $style, $style_base, $tikidomain;

		if (isset($style) && isset($style_base)) {
			if ($tikidomain and file_exists("templates/$tikidomain/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/styles/$style_base/$_smarty_tpl_file";
			} elseif ($tikidomain and file_exists("templates/$tikidomain/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/$_smarty_tpl_file";
			} elseif (file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";
			}
		}
		$_smarty_cache_id = $language . $_smarty_cache_id;
		$_smarty_compile_id = $language . $_smarty_compile_id;
		return parent::is_cached($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id);
	}
	function clear_cache($_smarty_tpl_file = null, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_exp_time=null) {
		global $language, $style, $style_base;

		if (isset($style) && isset($style_base) && isset($_smarty_tpl_file)) {
			if ($tikidomain and file_exists("templates/$tikidomain/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/styles/$style_base/$_smarty_tpl_file";
			} elseif ($tikidomain and file_exists("templates/$tikidomain/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/$_smarty_tpl_file";
			} elseif (file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";
			}
		}
		$_smarty_cache_id = $language . $_smarty_cache_id;
		$_smarty_compile_id = $language . $_smarty_compile_id;
		return parent::clear_cache($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_exp_time);
	}
}

$smarty = new Smarty_TikiWiki($tikidomain);
$smarty->load_filter('pre', 'tr');
// $smarty->load_filter('output','trimwhitespace');
?>
