<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.translate.php
 * Type:     block
 * Name:     translate
 * Purpose:  translate a block of text
 * -------------------------------------------------------------
 */
 
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

//global $lang;
//include_once('lang/language.php');

function smarty_block_tr($params, $content, &$smarty)
{
  global $lang_use_db;
  if ($lang_use_db!='y') {
    if ($content) {
    global $language;
    global $lang;
    include_once("lang/$language/language.php");
      if(isset($lang[$content])) {
        echo $lang[$content];  
      } else {
        echo $content;        
      }
    }
  } else {
    global $tikilib;
    global $language;
    $query="select `tran` from `tiki_language` where `source`=? and `lang`=?";
    $result=$tikilib->query($query,array($content,$language));
    $res=$result->fetchRow();
    if(!$res) { echo $content ; return; }
    if(!isset($res["tran"])) {
      global $record_untranslated;
      if ($record_untranslated=='y') {
        $query="insert into `tiki_untranslated` (`source`,`lang`) values(?,?)";
        //No eror checking here
        $tikilib->query($query,array($content,$language),-1,-1,false);
        }
      echo $content;
    }
    echo $res["tran"];
  }
}
?>
