<?php
session_start();
require_once("db/tiki-db.php");
error_reporting(E_ALL);



if(count($_FILES)==0) {
// Remove automatic quotes added to POST/COOKIE by PHP
if (get_magic_quotes_gpc ()) {
  foreach($_REQUEST as $k=>$v) {
    $_REQUEST[$k]=stripslashes($v); 
  }
}
}

// Define and load Smarty components
define('SMARTY_DIR',"Smarty/");
require_once(SMARTY_DIR.'Smarty.class.php');

class Smarty_Sterling extends Smarty {
  function Smarty_Sterling() {
    $this->teplate_dir = "templates/";
    $this->compile_dir = "templates_c/";
    $this->config_dir = "configs/";
    $this->cache_dir = "cache/";
    $this->caching = false;
    $this->assign('app_name','Sterling');
    //$this->debugging = true;
    //$this->debug_tpl = 'debug.tpl';
  }
}

$smarty = new Smarty_Sterling();

// Count number of online users using:
// print($GLOBALS["PHPSESSID"]);

?>