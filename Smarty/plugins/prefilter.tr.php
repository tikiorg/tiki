<?php
function smarty_prefilter_tr($source) {
  // Now replace the matched language strings with the entry in the file
//  $return = preg_replace_callback('/\{tr[^\{]*\}([^\{]+)\{\/tr\}/', '_translate_lang', $source);
// correction in order to match when a variable is inside {tr} tags. Example: {tr}The newsletter was sent to {$sent} email addresses{/tr}, and where there are parameters with {tr} 
  $return = preg_replace_callback('/(\{tr[^\}]*\})(.+?)\{\/tr\}/', '_translate_lang', $source);
  return $return;
}

function _translate_lang($key) {
  global $language;
  global $lang_use_db;
  if ($lang_use_db!='y') {
    include("lang/$language/language.php");
    if(isset($lang[$key[2]]))
      if ($key[1] == "{tr}") {
        return $lang[$key[2]];// no more possible translation in block.tr.php
      }
      else {
        return $key[1].$lang[$key[2]]."{/tr}";// perhaps variable substitution to do in block.tr.php
      }
    else {
      return $key[1].$key[2]."{/tr}";// keep the tags to be perhaps translated in block.tr.php
    }
  }
   else {
    global $tikilib;
    $content = $key[2];
    $query="select tran from tiki_language where source='".addslashes($content)."' and lang='".$language."'";
    $result=$tikilib->db->query($query);
    $res=$result->fetchRow(DB_FETCHMODE_ASSOC);
    if(DB::isError($result)) { echo $content ; return; }
    if(!isset($res["tran"])) {
      global $record_untranslated;
      if ($record_untranslated=='y') {
        $query="insert into tiki_untranslated (source,lang) values('".addslashes($content)."','".$language."')";
        //No eror checking here
        $tikilib->db->query($query);
      }
      return $key[1].$content."{/tr}";
    }
    if ($key[1] == "{tr}") {
      return $res["tran"];// no more possible translation in block.tr.php
    } else {
      return $key[1].$res["tran"]."{/tr}";// perhaps variable substituion to do in block.tr.php
		}
  }
}
?>
