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
    'ca' 	=> array('Catalan_Countries'),
    'cs' 	=> array('Czech_Republic'),
    'da' 	=> array('Denmark'),
    'de' 	=> array('Germany'),
    'en' 	=> array('United_States'),
    'en-uk'	=> array('United_Kingdom'),
    'es' 	=> array('Spain'),
    'el' 	=> array('Greece'),
    'fi' 	=> array('Finland'),
    'fj' 	=> array('Fiji'),
    'fr' 	=> array('France'),
    'he' 	=> array('Israel'),
    'hr' 	=> array('Croatia'),
    'id' 	=> array('Indonesia'),
    'is'	=> array('Iceland'),  
    'it' 	=> array('Italy'),
    'ja' 	=> array('Japan'),
    'lt' 	=> array('Lithuania'),
    'hu' 	=> array('Hungary'),
    'nl' 	=> array('Netherlands'),
    'no' 	=> array('Norway'),
    'pl' 	=> array('Poland'),
    'pt' 	=> array('Portugal'),
    'pt-br'	=> array('Brazil'),
    'ro' 	=> array('Romania'),
    'ru' 	=> array('Russian_Federation'),
    'sb' 	=> array('Solomon_Islands'),
    'sk' 	=> array('Slovakia'),
    'sl' 	=> array('Slovenia'),
    'sr-latn'	=> array('Serbia'),
    'sq' 	=> array('Albania'),
    'sv' 	=> array('Sweden'),
    'tr' 	=> array('Turkey'),
    'tv' 	=> array('Tuvalu'),
    'uk' 	=> array('Ukraine'),
    'cy'	=> array('Wales'),
    'vi' 	=> array('Viet_Nam'),
);
