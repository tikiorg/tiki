<?php
/** translate a English string
 * @param $content - English string
 * @param $lg - language - if not specify = global current language
 */

global $interactive_collected_strings;
$interactive_collected_strings = array();

function tr($content) {
	$args = func_get_args();
	return tra( $content, '', false, array_slice( $args, 1 ) );
}

function tra($content, $lg='', $no_interactive = false, $args = array()) {
	global $prefs;

	if ($lg == '') {
		if( $prefs['language'] ) {
			$lang = $prefs['language'];
		} else {
			$lang = $prefs['site_language'];
		}
	} else {
		$lang = $lg;
	}

	$out = tra_impl( $content, $lang, $no_interactive, $args );

	if( empty( $lg ) || $lg == $lang ) {
		record_string( $content, $out );
	}

	return $out;
}

function tra_impl($content, $lg='', $no_interactive = false, $args = array()) {
	global $prefs;

	if ($content != '') {
		if ($prefs['lang_use_db'] != 'y') {
			global $lang, $tikidomain;
			if (is_file("lang/$lg/language.php")) {
				$l = $lg;
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
				  if (!empty($tikidomain) && is_file("lang/$l/$tikidomain/custom.php")) {
					include_once("lang/$l/$tikidomain/custom.php");
				  }
				  ${"lang_$l"} = $lang;
				  unset($lang);
				}
			}
			if ($l and isset(${"lang_$l"}[$content])) {
				return tr_replace( ${"lang_$l"}[$content], $args );
			} else {
				// If no translation has been found and if the string ends with a punctuation,
				//   try to translate punctuation separately (e.g. if the content is 'Login:' or 'Login :',
				//   then it will try to translate 'Login' and ':' separately).
				// This should avoid duplicated strings like 'Login' and 'Login:' that were needed before
				//   (because there is no space before ':' in english, but there is one in others like french)
				$punctuations = array(':', '!', ';', '.', ',', '?'); // Modify get_strings.php accordingly
				$content_length = strlen($content);
				foreach ( $punctuations as $p ) {
					if ( $content[$content_length - 1] == $p ) {
						$new_content = substr($content, 0, $content_length - 1);
						if ( isset(${"lang_$l"}[$new_content]) ) {
							return tr_replace( ${"lang_$l"}[$new_content].( isset(${"lang_$l"}[$p]) ? ${"lang_$l"}[$p] : $p ), $args );
						} else {
							return tr_replace( $content, $args );
						}
					}
				}

				return tr_replace( $content, $args );
			}
		} else {
			global $tikilib,$multilinguallib;
			// things that don't work for interactive translation need to be filtered out
			$query = "select `tran` from `tiki_language` where `source`=? and `lang`=?";
			// set language to site default if no lang specified or for user
			$result = $tikilib->query($query, array($content,$lg));
			$res = $result->fetchRow();

			if (!$res || !isset($res["tran"])) {
				if ($prefs['record_untranslated'] == 'y') {
					$query = "insert into `tiki_untranslated` (`source`,`lang`) values (?,?)";
					$tikilib->query($query, array($content,$lg),-1,-1,false);
				}
				return tr_replace( $content, $args );
			}
			$res["tran"] = preg_replace("~&lt;br(\s*/)&gt;~","<br$1>",$res["tran"]);
			return tr_replace( $res["tran"], $args );
		}
	}
}

function tr_replace( $content, $args ) {
	if( ! count( $args ) ) {
		$out = $content;
	} else {
		$needles = array();
		$replacements = $args;

		foreach( array_keys( $args ) as $num )
			$needles[] = "%$num";
		
		$out = str_replace( $needles, $replacements, $content );
	}

	return $out;
}

function record_string( $original, $printed ) {
	global $interactive_collected_strings;
	if( interactive_enabled() ) {
		$interactive_collected_strings[ md5( $original . '___' . $printed ) ] = array( $original, $printed );
	}
}

function interactive_enabled() {
	return isset( $_SESSION['interactive_translation_mode'] ) && $_SESSION['interactive_translation_mode'] != 'off';
}

function get_collected_strings() {
	global $interactive_collected_strings;
	return $interactive_collected_strings;
}

