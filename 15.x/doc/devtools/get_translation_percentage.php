<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * This script was created to get the translation percentage for each language.php file.
 * 
 * Before calculating the percentage, it will run get_strings.php to make sure all language.php
 * files are up to date.
 * 
 * The output is in wiki syntax and if a page name is provided as the second parameter,
 * it will be updated.
 */

die("REMOVE THIS LINE TO USE THE SCRIPT.\n");

if (!isset($argv[1])) {
	echo "\nUsage: php get_translation_percentage.php pathToTikiRootDir wikiPageName\n";
	echo "Example: php get_translation_percentage.php /home/user/public_html/tiki i18nStats\n";
	echo "The second parameter is optional\n\n";
	die;
}

$tikiPath = $argv[1];

if (substr($tikiPath, -1) != '/') {
	$tikiPath .= '/';
}

if (isset($argv[2])) {
	$wikiPage = $argv[2];
}

if (!file_exists($tikiPath)) {
	die("\nERROR: $tikiPath doesn't exist\n\n");
} else if (!file_exists($tikiPath . 'db/local.php')) {
	die("\nERROR: $tikiPath doesn't seem to be a valid Tiki installation\n\n");
}

chdir($tikiPath);
require_once('tiki-setup.php');
require_once('lang/langmapping.php');
require_once('lib/language/File.php');

if (isset($wikiPage) && !$tikilib->page_exists($wikiPage)) {
	die("\nERROR: $wikiPage doesn't exist\n\n");
}

// update all language.php files by calling get_strings.php
$output = array();
$return_var = null;

exec('php get_strings.php', $output, $return_var);

if ($return_var == 1) {
	die("\nCouln't execute get_strings.php\n\n");
}

// calculate the percentage for each language.php
$outputData = array();
$globalStats = array();

// $langmapping is set on lang/langmapping.php
foreach ($langmapping as $lang => $null) {
	$filePath = "lang/$lang/language.php";
	if (file_exists($filePath) && $lang != 'en') {
		$parseFile = new Language_File($filePath);
		$stats = $parseFile->getStats();
		
		$outputData[$lang] = array(
			'total' => $stats['total'],
			'untranslated' => $stats['untranslated'],
			'translated' => $stats['translated'],
			'percentage' => $stats['percentage'],
		);
		
		if ($stats['percentage'] >= 70) {
			$globalStats['70+']++; 
		} else if ($stats['percentage'] >= 30) {
			$globalStats['30+']++;
		} else if ($stats['percentage'] < 30) {
			$globalStats['0+']++;
		}
	}
}


// output translation percentage to terminal or to a wiki page
$output = "! Status of Tiki translations\n";
$output .= "Page last modified on " . $tikilib->date_format($prefs['long_date_format']) . "\n\n";
$output .= "This page is generated automatically. Please do not change it.\n\n";
$output .= "The total number of strings is different for each language due to unused translations present in the language.php files.\n\n";
$output .= "__Global stats:__\n* {$globalStats['70+']} languages with more than 70% translated\n* {$globalStats['30+']} languages with more than 30% translated\n* {$globalStats['0+']} languages with less than 30% translated\n\n";
$output .= "{FANCYTABLE(head=\"Language code (ISO)|English name|Native Name|Completion|Percentage|Number of strings\" sortable=\"y\")}\n";

foreach ($outputData as $lang => $data) {
	$output .= "$lang | {$langmapping[$lang][1]} | {$langmapping[$lang][0]} | {gauge value=\"{$data['percentage']}\" size=\"100\" showvalue=\"false\"} | ";
	$output .= "{$data['percentage']}% | Total: {$data['total']} %%% Translated: {$data['translated']} %%% Untranslated: {$data['untranslated']} \n";
}

$output .= '{FANCYTABLE}';

if (isset($wikiPage)) {
	$tikilib->update_page($wikiPage, $output, 'Updating translation stats', 'i18nbot', '127.0.0.1');
} else {
	echo $output;
}
