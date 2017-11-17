<?php // -*- coding:utf-8 -*-
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** \brief This table associates language country (flag name) and language name in the current language
* PLEASE : Translators, update this file with your flag for your own language (if applicable)
**/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

$flagmapping = [
	'ca' 	=> ['Catalan_Countries'],
	'cs' 	=> ['Czech_Republic'],
	'da' 	=> ['Denmark'],
	'de' 	=> ['Germany'],
	'en' 	=> ['United_States'],
	'en-uk'	=> ['United_Kingdom'],
	'es' 	=> ['Spain'],
	'el' 	=> ['Greece'],
	'fi' 	=> ['Finland'],
	'fj' 	=> ['Fiji'],
	'fr' 	=> ['France'],
	'he' 	=> ['Israel'],
	'hr' 	=> ['Croatia'],
	'id' 	=> ['Indonesia'],
	'is'	=> ['Iceland'],
	'it' 	=> ['Italy'],
	'ja' 	=> ['Japan'],
	'lt' 	=> ['Lithuania'],
	'hu' 	=> ['Hungary'],
//        'nds' 	=> array('Low_Germany'),
		'nds' 	=> ['Hansa_HH'],
//        'nds'     => array('Hansa_HL'),
	'nl' 	=> ['Netherlands'],
	'no' 	=> ['Norway'],
	'pl' 	=> ['Poland'],
	'pt' 	=> ['Portugal'],
	'pt-br'	=> ['Brazil'],
	'ro' 	=> ['Romania'],
	'ru' 	=> ['Russian_Federation'],
	'sb' 	=> ['Solomon_Islands'],
	'sk' 	=> ['Slovakia'],
	'sl' 	=> ['Slovenia'],
	'sr-latn'	=> ['Serbia'],
	'sq' 	=> ['Albania'],
	'sv' 	=> ['Sweden'],
	'tr' 	=> ['Turkey'],
	'tv' 	=> ['Tuvalu'],
	'uk' 	=> ['Ukraine'],
	'cy'	=> ['Wales'],
	'vi' 	=> ['Viet_Nam'],
];
