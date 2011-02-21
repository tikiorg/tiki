<?php // -*- coding:utf-8 -*-
/** \brief this table associates language extension and language name in the current language and language name in the native language
* CAUTION: it is utf-8 encoding !
* PLEASE : translators, please, update this file with your language name in your own language
**/

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

$langmapping = array(
    'ar' => array ( 'العربية', tra("Arabic") ),
    'bg' => array(  'български език',      tra("Bulgarian")       ),
    'ca' => array(  'Català',      tra("Catalan")       ),
    'cn' => array(  '中文(簡体字)',      tra("Simplified Chinese")        ),
	'cs' => array(  'Česky',      tra("Czech")        ),
	'cy' => array('Cymraeg', tra('Welsh')),
    'da' => array(  'Dansk',        tra("Danish")       ),
    'de' => array(  'Deutsch',      tra("German")       ),
    'en' => array(  'English',      tra("English")      ),
    'en-uk' => array( 'English British',  tra("English British")	),
    'es' => array(  'Español',     tra("Spanish")      ),
    'el' => array(  'Greek',        tra("Greek")        ),
    'fa' => array(  'فارسي',        tra("Farsi")        ),
    'fi' => array(  'Finnish',        tra("Finnish")        ),
    'fj' => array(  'Fijian',       tra("Fijian")      ),
    'fr' => array(  'Français',    tra("French")       ),
    'fy-NL' => array(  'Frisian Netherlands',    tra("Frisian Netherlands")       ),
    'gl' => array(  'Galego',    tra("Galician")       ),
    'he' => array(  'עברית',    tra("Hebrew")       ),
    'hr' => array(  'Hrvatski',     tra("Croatian")   ),
    'id' => array(  'Indonesian',     tra("Indonesian")      ),
    'is' => array(  'Íslenska',     tra("Icelandic")      ),           
    'it' => array(  'Italiano',     tra("Italian")      ),
    'iu' => array(  'ᐃᓄᒃᑎᑐᑦ',     tra("Inuktitut")      ),
    'ja' => array(  '日本語',    tra("Japanese")     ),
    'ko' => array(  '한국어',    tra("Korean")   ),
    'hu' => array(  'Magyar',   tra("Hungarian")   ),
    'lt' => array(  'Lithuanian',   tra("Lithuanian")   ),
    'nl' => array(  'Nederlands',   tra("Dutch")        ),
    'no' => array(  'Norwegian',    tra("Norwegian")    ),
    'nn-NO' => array(  'Norwegian Nynorsk',    tra("Norwegian Nynorsk")    ),
    'pl' => array(  'Polish',       tra("Polish")       ),
    'pt' => array(  'Portuguese',       tra("Português")       ),
    'pt-br' => array(  'Português Brasileiro',  tra("Brazilian Portuguese")  ),
    'ro' => array(  'Romanian',      tra("Romanian")      ),
    'rm' => array(  'Rumantsch',      tra("Romansh")      ),
    'ru' => array(  'Русский',      tra("Russian")      ),
    'sb' => array(  'Pijin Solomon', tra("Pijin Solomon")    ),
    'si' => array(   'Sinhala',  tra("Sinhala")       ),
    'sk' => array(   'Slovenčina',  tra("Slovak")       ),
    'sl' => array(   'Slovenščina', tra('Slovene')       ),
    'sq' => array(  'Albanian', tra("Albanian")    ),
    'sr-latn' => array(   'Srpski',  tra("Serbian Latin")       ),
    'sv' => array(  'Svenska',      tra("Swedish")      ),
    'tv' => array(  'Tuvaluan',      tra("Tuvaluan")      ),
    'tr' => array(  'Turkish',      tra("Turkish")      ),
    'tw' => array(  '正體中文',          tra("Traditional Chinese")          ),
    'uk' => array( 'Українська',     tra("Ukrainian")    ),
    'vi' => array(  'Tiếng Việt',      tra("Vietnamese")      ),
);
