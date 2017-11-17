<?php // -*- coding:utf-8 -*-
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

$langmapping = [
	'ar' => ['العربية', tra("Arabic")],
	'bg' => ['български език', tra("Bulgarian")],
	'ca' => ['Català', tra("Catalan")],
	'cn' => ['简体中文', tra("Simplified Chinese")],
	'cs' => ['Česky', tra("Czech")],
	'cy' => ['Cymraeg', tra('Welsh')],
	'da' => ['Dansk', tra("Danish")],
	'de' => ['Deutsch', tra("German")],
	'en' => ['English', tra("English")],
	'en-uk' => ['British English', tra("English British")],
	'es' => ['Español', tra("Spanish")],
	'el' => ['Ελληνικά', tra("Greek")],
	'fa' => ['فارسي', tra("Farsi")],
	'fi' => ['Suomi', tra("Finnish")],
	'fj' => ['Fijian', tra("Fijian")],
	'fr' => ['Français', tra("French")],
	'fy-NL' => ['Frysk Nederlâns', tra("Frisian Netherlands")],
	'gl' => ['Galego', tra("Galician")],
	'he' => ['עברית', tra("Hebrew")],
	'hr' => ['Hrvatski', tra("Croatian")],
	'id' => ['Bahasa Indonesia', tra("Indonesian")],
	'is' => ['Íslenska', tra("Icelandic")],
	'it' => ['Italiano', tra("Italian")],
	'iu' => ['ᐃᓄᒃᑎᑐᑦ', tra("Inuktitut")],
	'iu-ro' => ['Inuktitut', tra("Inuktitut (Roman)")],
	'iu-iq' => ['Inuinnaqtun', tra("Iniunnaqtun")],
	'ja' => ['日本語', tra("Japanese")],
	'ko' => ['한국어', tra("Korean")],
	'hu' => ['Magyar', tra("Hungarian")],
	'lt' => ['Lietuvių', tra("Lithuanian")],
	'nds' => ['Plattdüütsch', tra("Low German")],
	'nl' => ['Nederlands', tra("Dutch")],
	'no' => ['Norsk', tra("Norwegian")],
	'pl' => ['Polszczyzna', tra("Polish")],
	'pt' => ['Português', tra("Portuguese")],
	'pt-br' => ['Português Brasileiro', tra("Brazilian Portuguese")],
	'ro' => ['Română', tra("Romanian")],
	'rm' => ['Rumantsch', tra("Romansh")],
	'ru' => ['Русский', tra("Russian")],
	'sb' => ['Pijin Solomon', tra("Pijin Solomon")],
	'si' => ['Sinhala', tra("Sinhala")],
	'sk' => ['Slovenčina', tra("Slovak")],
	'sl' => ['Slovenščina', tra('Slovene')],
	'sq' => ['Shqip', tra("Albanian")],
	'sr-latn' => ['Srpski', tra("Serbian Latin")],
	'sv' => ['Svenska', tra("Swedish")],
	'th' => ['ภาษาไทย', tra("Thai")],
	'tv' => ['Tuvaluan', tra("Tuvaluan")],
	'tr' => ['Türkçe', tra("Turkish")],
	'tw' => ['正體中文', tra("Traditional Chinese")],
	'uk' => ['Українська', tra("Ukrainian")],
	 'ur' => ['اردو', tra("Urdu")],

	'vi' => ['Tiếng Việt', tra("Vietnamese")],
];
