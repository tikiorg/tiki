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
//global $lang;
//include_once('lang/language.php');
function smarty_block_tr($params, $content, &$smarty)
{
  global $lang_use_db;
  if ($lang_use_db!='y') {
    global $lang;
    if ($content) {
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
