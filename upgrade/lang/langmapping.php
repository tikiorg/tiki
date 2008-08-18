<?php // -*- coding:utf-8 -*-
/** \brief this table associates language extension and language name in the current language and language name in the native language
* CAUTION: it is utf-8 encoding used here too
* PLEASE : translators, please, update this file with your language name in your own language
**/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$langmapping = array(
    'ar' => array ( 'العربية', tra("Arabic") ),
    'ca' => array(  'Català',      tra("Catalan")       ),
    'cn' => array(  '中文(簡体字)',      tra("Simplified Chinese")        ),
    'zh' => array(  'Chinese',      tra("Chinese")      ),
    'cs' => array(  'Česky',      tra("Czech")        ),
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
    'gl' => array(  'Galego',    tra("Galician")       ),
    'he' => array(  'עברית',    tra("Hebrew")       ),
    'hr' => array(  'Hrvatski',     tra("Croatian")   ),
    'it' => array(  'Italiano',     tra("Italian")      ),
    'ja' => array(  '日本語',    tra("Japanese")     ),
    'ko' => array(  '한국말',    tra("Korean")     ),
    'hu' => array(  'Magyar',   tra("Hungarian")   ),
    'nl' => array(  'Nederlands',   tra("Dutch")        ),
    'no' => array(  'Norwegian',    tra("Norwegian")    ),
    'pl' => array(  'Polish',       tra("Polish")       ),
    'pt' => array(  'Portuguese',       tra("Português")       ),
    'pt-br' => array(  'Português Brasileiro',  tra("Brazilian Portuguese")  ),
    'ru' => array(  'Русский',      tra("Russian")      ),
    'sb' => array(  'Pijin Solomon', tra("Pijin Solomon")    ),
    'sk' => array(   'Slovenský',  tra("Slovak")       ),
    'sr' => array(   'Српски',  tra("Serbian")       ),
    'sr-latn' => array(   'Srpski',  tra("Serbian Latin")       ),
    'sv' => array(  'Svenska',      tra("Swedish")      ),
    'tv' => array(  'Tuvaluan',      tra("Tuvaluan")      ),
    'tw' => array(  '正體中文',          tra("Traditional Chinese")          ),
    'uk' => array( 'Українська',     tra("Ukrainian")    )
);
?>
