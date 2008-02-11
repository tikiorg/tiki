<?php
/** translate a English string
 * @param $content - English string
 * @param $lg - language - if not specify = global current language
 */

function tra($content, $lg='', $no_interactive = false) {
	global $prefs;

	if ($content) {
		if ($prefs['lang_use_db'] != 'y') {
			global $lang;
			if ($lg != "") {
				if (is_file("lang/$lg/language.php")) {
					$l = $lg;
				} else {
					$l = $prefs['language'];
				}
			} elseif (is_file('lang/'.$prefs['language'].'/language.php')) {
				$l = $prefs['language'];
			} else {
				$l = false;
			}
			if ($l) {
				global ${"lang_$l"};
				if (!isset(${"lang_$l"})) {
				  include_once("lang/$l/language.php");
				  if (is_file("lang/$l/custom.php")) {
					include_once("lang/$l/custom.php");
				  }
				  ${"lang_$l"} = $lang;
				  unset($lang);
				}
				$lang = &${"lang_".$prefs['language']};
			}
			if ($l and isset(${"lang_$l"}[$content])) {
				return ${"lang_$l"}[$content];
			} else {
				return $content;
			}
		} else {
			global $tikilib,$multilinguallib;
			// things that don't work for interactive translation need to be filtered out
			if ($no_interactive == true) {
				$tag = "";
			} elseif (strpos($content,'<span ') !== FALSE) {
				// span tags cannot be nested 
				$tag="";
			} elseif (isset($multilinguallib)) {
				$tag=$multilinguallib->getInteractiveTag($content);
			} else {
				$tag="";
			}
			$query = "select `tran` from `tiki_language` where `source`=? and `lang`=?";
			$result = $tikilib->query($query, array($content,$lg == ""? $prefs['language'] : $lg));
			$res = $result->fetchRow();
			if (!$res) {
				return $content.$tag;
			}
			if (!isset($res["tran"])) {
				if ($prefs['record_untranslated'] == 'y') {
					$query = "insert into `tiki_untranslated` (`source`,`lang`) values (?,?)";
					$tikilib->query($query, array($content,$prefs['language']),-1,-1,false);
				}
			return $content.$tag;
			}
			$res["tran"] = preg_replace("~&lt;br(\s*/)&gt;~","<br$1>",$res["tran"]);
			return $res["tran"].$tag;
		}
	}
}
?>
