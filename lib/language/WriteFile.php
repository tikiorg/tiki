<?php

require_once('lib/core/TikiDb.php');
require_once('Language.php');

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
	 * @return null
	 */
	public function writeStringsToFile(array $strings, $filePath)
	{
		if (empty($strings)) {
			return false;
		}
		
		if (!file_exists($filePath)) {
			throw new Language_Exception("File $filePath does not exist.");
		}
		
		$this->filePath = $filePath;
		
		$this->translations = $this->getCurrentTranslations();
		$entries = $this->mergeStringsWithTranslations($strings, $this->translations);

		$handle = fopen($this->filePath, 'w');
		
		if ($handle) {
			fwrite($handle, "<?php\n");
			fwrite($handle, "\$lang = array(\n");
			
			foreach ($entries as $entry) {
				fwrite($handle, $this->formatString($entry));
			}
			
			fwrite($handle, ");\n");
		}
		
		fclose($handle);
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
		$entries = array();
		
		foreach ($strings as $string) {
			if (isset($translations[$string->name])) {
				$string->translation = $translations[$string->name];
				$entries[$string->name] = $string;
			} else {
				$entries[$string->name] = $string;
			} 
		}
		
		return $entries;
	}
	
	/**
	 * Format a pair source and translation as
	 * a string to be written to a language.php file
	 * 
	 * @param stdClass $entry an object with the English source string and the translation if any 
	 * @return string
	 */
	protected function formatString(stdClass $entry)
	{
		$source = Language::addPhpSlashes($entry->name);
		
		if (isset($entry->translation)) {
			$trans = Language::addPhpSlashes($entry->translation);
			// make sure translation is unset since each $entry is a object
			// passed by reference for every call of $this->writeStringsToFile()
			unset($entry->translation);
			return "\"$source\" => \"$trans\",\n";
		} else {
			return "// \"$source\" => \"$source\",\n";
		}
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