<?php
/**
 * \file
 * $Header: /cvsroot/tikiwiki/tiki/tests/core/init.scripts/40-smarty.php,v 1.1 2003-08-22 19:04:40 zaufi Exp $
 *
 * \brief Instantiate Smarty
 *
 */
 

// Define and load Smarty components
define('SMARTY_DIR', $tiki_root_dir . 'Smarty/');
require_once(SMARTY_DIR . 'Smarty.class.php');

class Smarty_TikiWiki extends Smarty
{
	function Smarty_TikiWiki($tikidomain = "")
    {
		$this->template_dir = "templates/";
		$this->compile_dir  = "templates_c/$tikidomain";
		$this->config_dir   = "configs/";
		$this->cache_dir    = "cache/$tikidomain";
		$this->caching      = false;
		$this->assign('app_name', 'TikiWiki');
	}

	function _smarty_include($_smarty_include_tpl_file, $_smarty_include_vars)
    {
		global $style;
		global $style_base;

		if (isset($style)
         && isset($style_base)
         && file_exists("templates/styles/$style_base/$_smarty_include_tpl_file"))
    		$_smarty_include_tpl_file = "styles/$style_base/$_smarty_include_tpl_file";

		return parent::_smarty_include($_smarty_include_tpl_file, $_smarty_include_vars);
	}

	function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false)
    {
		global $language;
		global $style;
		global $style_base;

		if (isset($style)
         && isset($style_base)
         && file_exists("templates/styles/$style_base/$_smarty_tpl_file"))
			$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";

		$_smarty_cache_id = $language . $_smarty_cache_id;
		$_smarty_compile_id = $language . $_smarty_compile_id;
		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
	}
}

$smarty = new Smarty_TikiWiki($tikidomain);
$smarty->load_filter('pre', 'tr');

?>
