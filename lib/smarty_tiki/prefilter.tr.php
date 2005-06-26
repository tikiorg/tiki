<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_prefilter_tr($source) {
  // Now replace the matched language strings with the entry in the file
//  $return = preg_replace_callback('/\{tr[^\{]*\}([^\{]+)\{\/tr\}/', '_translate_lang', $source);
// correction in order to match when a variable is inside {tr} tags. Example: {tr}The newsletter was sent to {$sent} email addresses{/tr}, and where there are parameters with {tr} 
// take away the smarty comments {* *} in case they have tr tags
  $return = preg_replace_callback('/(?s)(\{tr[^\}]*\})(.+?)\{\/tr\}/', '_translate_lang', preg_replace ('/(?s)\{\*.*?\*\}/', '', $source));
  return $return;
}

function _translate_lang($key) {
  global $language;
  global $lang;
  global $lang_use_db;
  if ($lang_use_db!='y') {
    include_once("lang/$language/language.php");
    if(isset($lang[$key[2]])) {
        if ($key[1] == "{tr}") {
          return $lang[$key[2]];// no more possible translation in block.tr.php
        }
        else {
          return $key[1].$lang[$key[2]]."{/tr}";// perhaps variable substitution to do in block.tr.php
        }
    }// not found in language.php
    elseif (strstr($key[2], "{\$")) {
         return $key[1].$key[2]."{/tr}";// keep the tags to be perhaps translated in block.tr.php
    }
    else {
         return $key[2];
    }
   }
   else {
    global $tikilib;
    $content = $key[2];
    $query="select `tran` from `tiki_language` where `source`=? and `lang`=?";
    $result=$tikilib->query($query,array($content,$language));
    $res=$result->fetchRow();
    if(isset($res["tran"])) {
	if ($key[1] == "{tr}") {
	    return $res["tran"];// no more possible translation in block.tr.php
	} else {
	    return $key[1].$res["tran"]."{/tr}";// perhaps variable substituion to do in block.tr.php
	}
    } else {
	global $record_untranslated;
	if ($record_untranslated=='y') {
	    $query="insert into `tiki_untranslated` (`source`,`lang`) values(?,?)";
	    //No eror checking here
	    $tikilib->query($query,array($content,$language),-1,-1,false);
	}
	if (strstr($key[2], "{\$"))  {
	    return $key[1].$content."{/tr}";
	} else {
	    return $key[2];
	}
    }
   }
}
?>
