<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/core/TikiDb.php');
require_once('Language.php');

/**
 * @package   Tiki
 * @subpackage    Language
 * Class to update language.php file with new
 * collected strings.
 */
class Language_WriteFile
{
	/**
	 * Path to a language.php file
	 * @var string
	 */
	protected $filePath;
	
	/**
	 * Current language translations.
	 * @var array
	 */
	protected $translations;
	
	/**
	 * Update language.php file with new strings.
	 * 
	 * @param array $strings English strings collected from source files
	 * @param string path to language.php file
	 * @param bool $outputFiles whether file paths were string was found should be included or not in the output
	 * @return null
	 */
	public function writeStringsToFile(array $strings, $filePath, $outputFiles = false)
	{
		if (empty($strings)) {
			return false;
		}
		
		if (!file_exists($filePath)) {
			throw new Language_Exception("File $filePath does not exist.");
		}
		
		if (!is_writable($filePath)) {
			throw new Language_Exception("Can't write to file $filePath.");
		}
		
		$this->filePath = $filePath;
		$tmpFilePath = $this->filePath . '.tmp';
		
		// backup original language file
		copy($filePath, $filePath . '.old');
		
		$this->translations = $this->getCurrentTranslations();
		$entries = $this->mergeStringsWithTranslations($strings, $this->translations);
		
		$handle = fopen($tmpFilePath, 'w');
		
		if ($handle) {
			fwrite($handle, "<?php\n");
			fwrite($handle, $this->fileHeader());
			fwrite($handle, "\$lang = array(\n");
			
			foreach ($entries as $entry) {
				fwrite($handle, $this->formatString($entry, $outputFiles));
			}
			
			fwrite($handle, ");\n");
			fclose($handle);
		}
		
		rename($tmpFilePath, $this->filePath);	
	}

	/**
	 * Return the text used for language.php header
	 * @return string
	 */
	protected function fileHeader()
	{
		$header = <<<TXT
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Note for translators about translation of text ending with punctuation
// 
// The current list of concerned punctuation can be found in 'lib/init/tra.php'
// On 2009-03-02, it is: (':', '!', ';', '.', ',', '?')
// For clarity, we explain here only for colons: ':' but it is the same for the rest
// 
// Short version: it is not a problem that string "Login:" has no translation. Only "Login" needs to be translated.
// 
// Technical justification:
// If a string ending with colon needs translating (like "{tr}Login:{/tr}")
// then Tiki tries to translate 'Login' and ':' separately.
// This allows to have only one translation for "{tr}Login{/tr}" and "{tr}Login:{/tr}"
// and it still allows to translate ":" as " :" for languages that
// need it (like French)
// Note: the difference is invisible but " :" has an UTF-8 non-breaking-space, not a regular space, but the UTF-8 equivalent of the HTML &nbsp;.
// This allows correctly displaying emails and JavaScript messages, not only web pages as would happen with &nbsp;.


TXT;
		
		return $header;
	}
	
	/**
	 * Merge collected strings from source files with translations from
	 * language.php ignoring translations for strings that are not present
	 * anymore in the source files.
	 * 
	 * @param array $strings English strings collected from source files 
	 * @param array $translations Translations from language.php file
	 * @return array
	 */
	protected function mergeStringsWithTranslations(array $strings, array $translations)
	{
		$punctuations = array(':', '!', ';', '.', ',', '?'); // Modify lib/init/tra.php accordingly
		
		$entries = array();
		
		foreach ($strings as $string) {
			if (isset($translations[$string['name']])) {
				$string['translation'] = $translations[$string['name']];
			} else {
				// Handle punctuations at the end of the string (cf. comments in lib/init/tra.php)
				// For example, if $string->name == 'Login:', we don't keep it if we also have a string 'Log In'
				// (except if we already have an explicit translation for 'Log In:')
				$stringLength = strlen($string['name']);
				$stringLastChar = $string['name'][$stringLength - 1];
				
				if (in_array($stringLastChar, $punctuations) ) {
					$trimmedString = substr($string['name'], 0, $stringLength - 1);
					$string['name'] = $trimmedString;
					if (isset($translations[$trimmedString])) {
						$string['translation'] = $translations[$trimmedString]; 
					}
				}
			}
			
			$entries[$string['name']] = $string;
		}
		
		return $entries;
	}
	
	/**
	 * Format a pair source and translation as
	 * a string to be written to a language.php file
	 * 
	 * @param array $entry an array with the English source string and the translation if any 
	 * @param bool $outputFiles whether file paths were string was found should be included or not in the output
	 * @return string
	 */
	protected function formatString(array $entry, $outputFiles = false)
	{
		// final formated string
		$string = '';
		
		if ($outputFiles && (isset($entry['files']) && !empty($entry['files']))) {
			$string .= '/* ' . join(', ', $entry['files']) . " */\n";
		}
		
		$source = Language::addPhpSlashes($entry['name']);
		
		if (isset($entry['translation'])) {
			$trans = Language::addPhpSlashes($entry['translation']);			
			$string .= "\"$source\" => \"$trans\",\n";
		} else {
			$string .= "// \"$source\" => \"$source\",\n";
		}
		
		return $string;
	}
	
	/**
	 * Return an array with available translations for
	 * current language.
	 * 
	 * @return array language translations
	 */
	protected function getCurrentTranslations()
	{
		require($this->filePath);

		if (isset($lang) && !empty($lang)) {
			return $lang;
		} else {
			return array();
		}
	}
}
