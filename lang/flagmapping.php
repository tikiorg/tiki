<?php // -*- coding:utf-8 -*-
/** \brief This table associates language country (flag name) and language name in the current language
* PLEASE : Translators, update this file with your flag for your own language (if applicable)
**/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$flagmapping = array(
    'ar' 	=> array(  ''),
    'ca' 	=> array(  'Spain'),
    'cn' 	=> array(  'China'),
    'zh' 	=> array(  'China'),
    'cs' 	=> array(  'Czech_Republic'),
    'da' 	=> array(  'Denmark'),
    'de' 	=> array(  'Germany'),
    'en' 	=> array(  'United_States'),
    'en-uk' => array(  'United_Kingdom'),
    'es' 	=> array(  'Spain'),
    'el' 	=> array(  'Greece'),
    'fa' 	=> array(  ''),
    'fi' 	=> array(  'Finland'),
    'fj' 	=> array(  'Fiji'),
    'fr' 	=> array(  'France'),
    'gl' 	=> array(  ''),
    'he' 	=> array(  'Israel'),
    'hr' 	=> array(  'Croatia'),
    'is'     => array(  'Iceland'),  
    'it' 	=> array(  'Italy'),
    'ja' 	=> array(  'Japan'),
    'ko' 	=> array(  ''),
    'hu' 	=> array(  'Hungary'),
    'nl' 	=> array(  'Netherlands'),
    'no' 	=> array(  'Norway'),
    'pl' 	=> array(  'Poland'),
    'pt' 	=> array(  'Portugal'),
    'pt-br' => array(  'Brazil'),
    'ru' 	=> array(  'Russian_Federation'),
    'sb' 	=> array(  'Solomon_Islands'),
    'sk' 	=> array(  'Slovakia'),
    'sr' 	=> array(  'Serbia'),
    'sr-latn' => array('Serbia'),
    'sv' 	=> array(  'Sweden'),
    'tv' 	=> array(  'Tuvalu'),
    'tw' 	=> array(  'Taiwan'),
    'uk' 	=> array(  'Ukraine')
);
