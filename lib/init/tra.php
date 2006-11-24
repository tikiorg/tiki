<?php
/** translate a English string
 * @param $content - English string
 * @param $lg - language - if not specify = global current language
 */
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
					include_once("lang/$l/language.php");
					if (is_file("lang/$l/custom.php")) {
						include_once("lang/$l/custom.php");
					}
				}
			if (isset($lang[$content])) {
				return $lang[$content];
			} else {
				return $content;
			}
		} else {
			global $tikilib,$multilinguallib;
			$tag=isset($multilinguallib)?$multilinguallib->getInteractiveTag($content):"";
			$query = "select `tran` from `tiki_language` where `source`=? and `lang`=?";
			$result = $tikilib->query($query, array($content,$lg == ""? $language: $lg));
			$res = $result->fetchRow();
			if (!$res) {
				return $content.$tag;
			}
			if (!isset($res["tran"])) {
				global $record_untranslated;
				if ($record_untranslated == 'y') {
					$query = "insert into `tiki_untranslated` (`source`,`lang`) values (?,?)";
					$tikilib->query($query, array($content,$language),-1,-1,false);
				}
				return $content.$tag;
			}
			$res["tran"] = preg_replace("~&lt;br(\s*/)&gt;~","<br$1>",$res["tran"]);
			return $res["tran"].$tag;
		}
	}
}
?>
