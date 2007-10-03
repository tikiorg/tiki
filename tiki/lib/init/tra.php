<?php
/** translate a English string
 * @param $content - English string
 * @param $lg - language - if not specify = global current language
 */
// BUG; will not work with a language other than the default ($lang will be or not redefined)
function tra($content, $lg='') {
	global $lang_use_db;
	global $language;
	if ($content) {
		if ($lang_use_db != 'y') {
			global $lang;
			if ($lg != "") {
				if (is_file("lang/$lg/language.php")) {
					$l = $lg;
				} else {
					$l = $language;
				}
			} elseif (is_file("lang/$language/language.php")) {
				$l = $language;
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
				$lang = &${"lang_$l"};
			}
			if (isset($lang[$content])) {
				return $lang[$content];
			} else {
				return $content;
			}
		} else {
			global $tikilib,$multilinguallib;
			$query = "select `tran` from `tiki_language` where `source`=? and `lang`=?";
			$result = $tikilib->query($query, array($content,$lg == ""? $language: $lg));
			$res = $result->fetchRow();
			if (!$res) {
				return $content;
			}
			if (!isset($res["tran"])) {
				global $record_untranslated;
				if ($record_untranslated == 'y') {
					$query = "insert into `tiki_untranslated` (`source`,`lang`) values (?,?)";
					$tikilib->query($query, array($content,$language),-1,-1,false);
				}
				return $content;
			}
			$res["tran"] = preg_replace("~&lt;br(\s*/)&gt;~","<br$1>",$res["tran"]);
			return $res["tran"];
		}
	}
}
?>
