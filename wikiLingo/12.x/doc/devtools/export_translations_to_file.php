<?php

/**
 * Script to export translations from the database
 * to language.php files.
 */

die("REMOVE THIS LINE TO USE THE SCRIPT.\n");

if (!isset($argv[1])) {
    echo "\nUsage: php export_translations_to_file.php langCode\n";
    echo "Example: php export_translations_to_file.php de\n";
    die;
}

require_once('tiki-setup.php');
require_once('lang/langmapping.php');
require_once('lib/language/Language.php');
require_once('lib/language/LanguageTranslations.php');

$langCode = $argv[1];

if (!array_key_exists($langCode, $langmapping)) {
    die("Invalid language code.\n");
}

$language = new LanguageTranslations($langCode);

try {
    $stats = $language->writeLanguageFile();
} catch (Exception $e) {
    die("{$e->getMessage()}\n");
}

echo sprintf("Wrote %d new strings and updated %d to lang/%s/language.php\n", $stats['new'], $stats['modif'], $language->lang);
