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
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
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
    global $language,$multilinguallib;
    $tag=isset($multilinguallib)?$multilinguallib->getInteractiveTag($content):"";
    $query="select `tran` from `tiki_language` where `source`=? and `lang`=?";
    $result=$tikilib->query($query,array($content,$language));
    $res=$result->fetchRow();
    if(!$res) { echo $content.$tag ; return; }
    if(!isset($res["tran"])) {
      global $record_untranslated;
      if ($record_untranslated=='y') {
        $query="insert into `tiki_untranslated` (`sourcfile:///home/tiki_head/tikiwiki/lib/smarty_tiki/block.tr.phpe`,`lang`) values(?,?)";
        //No eror checking here
        $tikilib->query($query,array($content,$language),-1,-1,false);
        }
      echo $content.$tag;
    }else{ 
	//To allow multiline translation
	$res["tran"]=ereg_replace("&lt;br&gt;","<br>",$res["tran"]);
    	echo $res["tran"].$tag;
	}
  }
}
?>
