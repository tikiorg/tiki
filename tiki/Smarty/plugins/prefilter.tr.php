<?php
function smarty_prefilter_tr($source) {
  // Now replace the matched language strings with the entry in the file
  $return = preg_replace_callback('/\{tr\}([^\{]+)\{\/tr\}/', '_translate_lang', $source);
  return $return;
}

function _translate_lang($key) {
  global $language;
  if ($lang_use_db!='y') {
  include("lang/$language/language.php");
  if(isset($lang[$key[1]])) {
    return $lang[$key[1]];
  } else {
    return $key[1];
  }
  } else {
    global $tikilib;
    $content = $key[1];
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
      return $content;
    }
    return $res["tran"];
  }
}
?>
