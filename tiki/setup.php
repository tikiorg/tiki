<?php
session_start();
require_once("db/tiki-db.php");

error_reporting(E_ALL);

// Remove automatic quotes added to POST/COOKIE by PHP
if (get_magic_quotes_gpc ()) {
  foreach($_REQUEST as $k=>$v) {
    if(!is_array($_REQUEST[$k])) {
      $_REQUEST[$k]=stripslashes($v); 
    } 
  }
}


// Define and load Smarty components
define('SMARTY_DIR',"Smarty/");
require_once(SMARTY_DIR.'Smarty.class.php');


class Smarty_Sterling extends Smarty {
  function Smarty_Sterling() {
    $this->template_dir = "templates/";
    $this->compile_dir = "templates_c/";
    $this->config_dir = "configs/";
    $this->cache_dir = "cache/";
    $this->caching = false;
    $this->assign('app_name','Sterling');
    //$this->debugging = true;
    //$this->debug_tpl = 'debug.tpl';
  }
  
  function _smarty_include($_smarty_include_tpl_file, $_smarty_include_vars)  
  {
    global $style;
    global $style_base;

    if(isset($style)&&isset($style_base)) {
      if(file_exists("templates/styles/$style_base/$_smarty_include_tpl_file")) {
        $_smarty_include_tpl_file="styles/$style_base/$_smarty_include_tpl_file";
      } 
      
    }

    return parent::_smarty_include($_smarty_include_tpl_file, $smarty_include_vars);
  }

  function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false) {
    global $language;
    global $style;
    global $style_base;
    
    if(isset($style)&&isset($style_base)) {
      if(file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
        $_smarty_tpl_file="styles/$style_base/$_smarty_tpl_file";
      }
    }
    
    $_smarty_cache_id = $language.$_smarty_cache_id;
    $_smarty_compile_id = $language.$_smarty_compile_id;
    return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
  }
  
}

$smarty = new Smarty_Sterling();
$smarty->load_filter('pre','tr');
$smarty->load_filter('output','trimwhitespace');

// Count number of online users using:
// print($GLOBALS["PHPSESSID"]);

?>