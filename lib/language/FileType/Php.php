<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Define properties to collect translatable
 * strings from PHP files. 
 */
class Language_FileType_Php extends Language_FileType
{
	protected $regexes = array(
		'|\Wtra?\s*\(\s*\'(.+?)\'\s*[\),]|s',
		'|\Wtra?\s*\(\s*"(.+?)"\s*[\),]|s'
		// The regex below probably can replace the two regexes above
		//'|\Wtra?\s*\(\s*["\'](.+?)["\']\s*[\),]|s'
	);
	
	protected $extensions = array('.php');
	
	protected $cleanupRegexes = array(
		"!/\*.*?\*/!s" => '',  // C comments
		"!^\s*//get_strings(.*)\$!m" => '$1', // the "unused strings" - the strings that will be translated later through a variable are marked with //get_strings tra("string")
		"!^\s*//.*\$!m" => '', // C++ comments
		"!^\s*\#.*\$!m" => '', // shell comments
	);
}