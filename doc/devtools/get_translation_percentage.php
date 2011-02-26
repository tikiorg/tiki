<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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

if (!$tikilib->page_exists($wikiPage)) {
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

// $langmapping is set on lang/langmapping.php
foreach ($langmapping as $lang => $null) {
	if (file_exists("lang/$lang/language.php") && $lang != 'en') {
		$lines = file("lang/$lang/language.php");
		$total = 0;
		$translated = 0;
		$untranslated = 0;
		
		foreach ($lines as $line) {
			$matches = array();
			
			if (preg_match('|^(/?/?)\s*?".+"\s*=>\s*".+".*|', $line, $matches)) {
				$total++;
				
				if (!empty($matches[1])) {
					$untranslated++;
				} else {
					$translated++;
				}
			}
		}
		
		$outputData[$lang] = array(
			'total' => $total,
			'untranslated' => $untranslated,
			'translated' => $translated,
			'percentage' => round($translated / $total, 4) * 100,
		);
	}
}


// output translation percentage to terminal or to a wiki page
$output = "! Status of Tiki translations\n";
$output .= "Page last modified on " . $tikilib->date_format($prefs['long_date_format']) . "\n\n";
$output .= "This page is generated automatically. Please do not change it.\n\n";
$output .= "The total number of strings is different for each language due to unused translations present in the language.php files.\n\n";
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
