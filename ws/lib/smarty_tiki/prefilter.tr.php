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
$return=$source;
if ((!empty($_SESSION['interactive_translation_mode'])&&($_SESSION['interactive_translation_mode']=='on'))){	
	for ($i=0;$i<3;$i++){
		$return=preg_replace("/alt=(.*)\{tr\}([^\{]*)\{\/tr\}(.*)([\"\'])/U","alt=$1$2$3$4",$return);
		$return=preg_replace("/title=(.*)\{tr\}([^\{]*)\{\/tr\}(.*)([\"\'])/U","title=$1$2$3$4",$return);
		$return=preg_replace("/value=(.*)\{tr\}([^\{]*)\{\/tr\}([^\"\']*)([\"\'])/U","value=$1$2$3$4",$return);
	}
	$return=str_replace("{tr}Error{/tr}","Error",$return);
}
  $return = preg_replace_callback('/(?s)(\{tr\})(.+?)\{\/tr\}/', '_translate_lang', preg_replace ('/(?s)\{\*.*?\*\}/', '', $return));
  return $return;
}

function _translate_lang($key) {
	$s = tra($key[2]);
	if ( $s == $key[2] && strstr($key[2], '{$') ) {
		return $key[1].$key[2].'{/tr}';// keep the tags to be perhaps translated in block.tr.php
	} else {
		return $s;
    }
}
?>
