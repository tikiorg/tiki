<?php // -*- coding:utf-8 -*-
/** \brief this table associates language extension and language name in the current language and language name in the native language
* CAUTION: it is utf-8 encoding used here too
* PLEASE : translators, please, update this file with your language name in your own language
**/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

$langmapping = array(
    'br' => array(  'Português Brasileiro',  tra("Brazilian Portuguese")  ),
    'cn' => array(  '中文(簡体字)',      tra("Simplified Chinese")        ),
    'cs' => array(  'Český',      tra("Czech")        ),
    'da' => array(  'Dansk',        tra("Danish")       ),
    'de' => array(  'Deutsch',      tra("German")       ),
    'en' => array(  'English',      tra("English")      ),
    'en-uk' => array( 'English British',  tra("English British")	),
    'es' => array(  'Español',     tra("Spanish")      ),
    'el' => array(  'Greek',        tra("Greek")        ),
    'fr' => array(  'Français',    tra("French")       ),
    'he' => array(  'Hebrew',    tra("Hebrew")       ),
    'hr' => array(  'Hrvatski',     tra("Croatian")   ),
    'it' => array(  'Italiano',     tra("Italian")      ),
    'ja' => array(  '日本語',    tra("Japanese")     ),
    'nl' => array(  'Dutch',        tra("Dutch")        ),
    'no' => array(  'Norwegian',    tra("Norwegian")    ),
    'pl' => array(  'Polish',       tra("Polish")       ),
    'ru' => array(  'Russian',      tra("Russian")      ),
    'sk' => array(   'Slovenský',  tra("Slovak")       ),
    'sr' => array(   'Српски',  tra("Serbian")       ),
    'sr-latn' => array(   'Srpski',  tra("Serbian Latin")       ),
    'sv' => array(  'Svenska',      tra("Swedish")      ),
    'tw' => array(  '正體中文',          tra("Traditional Chinese")          ),
    'zh' => array(  'Chinese',      tra("Chinese")      )
);
?>
