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
		
		$translations = $this->getCurrentTranslations();
		$strings = array_combine($strings, $strings);
		$entries = $this->mergeStringsWithTranslations($strings, $translations);
		
		$handle = fopen($this->filePath, 'w');
		
		if ($handle) {
			fwrite($handle, "<?php\n");
			fwrite($handle, "\$lang = array(\n");
			
			foreach ($entries as $source => $trans) {
				fwrite($handle, $this->formatString($source, $trans));
			}
			
			fwrite($handle, ");\n");
		}
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
			if (isset($translations[$string])) {
				$entries[$string] = $translations[$string];
			} else {
				$entries[$string] = $string;
			} 
		}
		
		return $entries;
	}
	
	/**
	 * Format a pair source and translation as
	 * a string to be written to a language.php file
	 * 
	 * @param string $source the source string in English
	 * @param string $trans the translation
	 * @return string
	 */
	protected function formatString($source, $trans)
	{
		$source = Language::addPhpSlashes($source);
		$trans = Language::addPhpSlashes($trans);

		if ($source == $trans) {
			return "// \"$source\" => \"$trans\",\n";
		} else {
			return "\"$source\" => \"$trans\",\n";
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