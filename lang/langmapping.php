<?php // -*- coding:utf-8 -*-
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** \brief this table associates language extension and language name in the current language and language name in the native language
* CAUTION: it is utf-8 encoding !
* PLEASE : translators, please, update this file with your language name in your own language
**/

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

$langmapping = array(
	'ar' => array('العربية', tra("Arabic")),
	'bg' => array('български език', tra("Bulgarian")),
	'ca' => array('Català', tra("Catalan")),
	'cn' => array('简体中文', tra("Simplified Chinese")),
	'cs' => array('Česky', tra("Czech")),
	'cy' => array('Cymraeg', tra('Welsh')),
	'da' => array('Dansk', tra("Danish")),
	'de' => array('Deutsch', tra("German")),
	'en' => array('English', tra("English")),
	'en-uk' => array('British English', tra("English British")),
	'es' => array('Español', tra("Spanish")),
	'el' => array('Ελληνικά', tra("Greek")),
	'fa' => array('فارسي', tra("Farsi")),
	'fi' => array('Suomi', tra("Finnish")),
	'fj' => array('Fijian', tra("Fijian")),
	'fr' => array('Français', tra("French")),
	'fy-NL' => array('Frysk Nederlâns', tra("Frisian Netherlands")),
	'gl' => array('Galego', tra("Galician")),
	'he' => array('עברית', tra("Hebrew")),
	'hr' => array('Hrvatski', tra("Croatian")),
	'id' => array('Bahasa Indonesia', tra("Indonesian")),
	'is' => array('Íslenska', tra("Icelandic")),
	'it' => array('Italiano', tra("Italian")),
	'iu' => array('ᐃᓄᒃᑎᑐᑦ', tra("Inuktitut")),
	'iu-ro' => array('Inuktitut', tra("Inuktitut (Roman)")),
	'iu-iq' => array('Inuinnaqtun', tra("Iniunnaqtun")),
	'ja' => array('日本語', tra("Japanese")),
	'ko' => array('한국어', tra("Korean")),
	'hu' => array('Magyar', tra("Hungarian")),
	'lt' => array('Lietuvių', tra("Lithuanian")),
	'nds' => array('Plattdüütsch', tra("Low German")),
	'nl' => array('Nederlands', tra("Dutch")),
	'no' => array('Norsk', tra("Norwegian")),
	'pl' => array('Polszczyzna', tra("Polish")),
	'pt' => array('Português', tra("Portuguese")),
	'pt-br' => array('Português Brasileiro', tra("Brazilian Portuguese")),
	'ro' => array('Română', tra("Romanian")),
	'rm' => array('Rumantsch', tra("Romansh")),
	'ru' => array('Русский', tra("Russian")),
	'sb' => array('Pijin Solomon', tra("Pijin Solomon")),
	'si' => array('Sinhala', tra("Sinhala")),
	'sk' => array('Slovenčina', tra("Slovak")),
	'sl' => array('Slovenščina', tra('Slovene')),
	'sq' => array('Shqip', tra("Albanian")),
	'sr-latn' => array('Srpski', tra("Serbian Latin")),
	'sv' => array('Svenska', tra("Swedish")),
	'th' => array('ภาษาไทย', tra("Thai")),
	'tv' => array('Tuvaluan', tra("Tuvaluan")),
	'tr' => array('Türkçe', tra("Turkish")),
	'tw' => array('正體中文', tra("Traditional Chinese")),
	'uk' => array('Українська', tra("Ukrainian")),
	'vi' => array('Tiếng Việt', tra("Vietnamese")),
);
