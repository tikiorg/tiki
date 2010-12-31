<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
// [FIX] Trebly:B00624-01

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
	static $languages = array();

	if ($lg == '') {
		if( $prefs['language'] ) {
			$lang = $prefs['language'];
		} else {
			$lang = $prefs['site_language'];
		}
	} else {
		$lang = $lg;
	}

	if( ! isset( $languages[$lang] ) ) {
		$languages[ $lang ] = true;
		init_language( $lang );
	}

	$out = tra_impl( $content, $lang, $no_interactive, $args );

	record_string( $content, $out );

	return $out;
}

function init_language( $lg ) {
	global $tikidomain, $prefs;
	if( is_file("lang/$lg/language.php")) {
		global ${"lang_$lg"};

		$lang = array();
		include("lang/$lg/language.php");
		if (is_file("lang/$lg/custom.php")) {
			include_once("lang/$lg/custom.php");
		}
		if (!empty($tikidomain) && is_file("lang/$lg/$tikidomain/custom.php")) {
			include_once("lang/$lg/$tikidomain/custom.php");
		}

		if( isset( $prefs['lang_use_db'] ) && $prefs['lang_use_db'] == 'y' ) {
			global $tikilib;

			$query = "select `source`, `tran` from `tiki_language` where `lang`=?";
			$result = $tikilib->fetchAll($query, array($lg));

			foreach( $result as $row ) {
				$lang[ $row['source'] ] = $row['tran'];
			}
		}

		${"lang_$lg"} = $lang;
	}
}

function tra_impl($content, $lg='', $no_interactive = false, $args = array()) {
	global $prefs, $tikilib;

	if ($content != '') {
		global $lang;
		global ${"lang_$lg"};
		if ($lg and isset(${"lang_$lg"}[$content])) {
			return tr_replace( ${"lang_$lg"}[$content], $args );
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
					if ( isset(${"lang_$lg"}[$new_content]) ) {
						return tr_replace( ${"lang_$lg"}[$new_content].( isset(${"lang_$lg"}[$p]) ? ${"lang_$lg"}[$p] : $p ), $args );
					}
				}
			}
		}
	}

	if (isset($prefs['record_untranslated']) && $prefs['record_untranslated'] == 'y' && !empty($content) && $lg != 'en') {
		$query = 'select `id` from `tiki_untranslated` where `source`=? and `lang`=?';
		    // ### Trebly:B00624-01:added test on tikilib existence : on the first launch of tra tikilib is not yet set  
        if (isset($tikilib)) {
      		if (!$tikilib->getOne($query, array($content,$lg))) {
      			$query = "insert into `tiki_untranslated` (`source`,`lang`) values (?,?)";
      			$tikilib->query($query, array($content,$lg),-1,-1,false);
      		}
      	}	
	}

	return tr_replace( $content, $args );
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
		$interactive_collected_strings[ md5( $original . '___' . $printed ) ] = array( $original, html_entity_decode( $printed ) );
	}
}

function interactive_enabled() {
	return isset( $_SESSION['interactive_translation_mode'] ) && $_SESSION['interactive_translation_mode'] != 'off';
}

function get_collected_strings() {
	global $interactive_collected_strings;
	return $interactive_collected_strings;
}

