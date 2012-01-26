<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: MergeFiles.php 37798 2011-09-29 19:59:41Z changi67 $

/**
 * Class to update a language.php file with the translations
 * done in another language.php. Tipically used to keep language
 * files synchronized in different Tiki branches.
 */
class Language_MergeFiles
{
	/**
	 * File used to collect new translations.
	 * @var Language_ParseFile
	 */
	private $sourceFile;
	
	/**
	 * File that will be updated with new translations
	 * done on $this->sourceFile.
	 * @var Language_ParseFile
	 */
	private $targetFile;
	
	public function __construct(Language_ParseFile $sourceFile, Language_ParseFile $targetFile)
	{
		$this->sourceFile = $sourceFile;
		$this->targetFile = $targetFile;
	}
	
	public function merge()
	{
		$sourceStrings = $this->sourceFile->parse();
		$targetStrings = $this->targetFile->parse();
		$toUpdate = array();
		
		foreach ($targetStrings as $key => $string) {
			if (isset($sourceStrings[$key]) && $sourceStrings[$key]['translated'] === true
				&& (!isset($string['translation']) || $sourceStrings[$key]['translation'] != $string['translation']))
			{
				$toUpdate[$key] = $sourceStrings[$key]['translation'];
			}
		}
		
		// move the code that actually writes to the language.php to another class
		// that would handle every ways to write to a language.php file.
		$tmpFilePath = $this->targetFile->filePath . '.tmp';
		$handle = fopen($tmpFilePath, 'w');
		$lines = file($this->targetFile->filePath);

		if ($handle) {
			// foreach each line in the target file check decide whether to keep the
			// current translation or use the translation from the source file if one exists 
			foreach ($lines as $line) {
				$matches = array();
				
				if (preg_match('|^/?/?\s*\"(.*)\"\s*\=\>\s*\"(.*)\"\s*\,\s*$|', $line, $matches)
					&& isset($toUpdate[$matches[1]]))
				{
					fwrite($handle, "\"{$matches[1]}\" => \"{$toUpdate[$matches[1]]}\",\n");
				} else {
					fwrite($handle, $line);
				}
			}
			
			rename($tmpFilePath, $this->targetFile->filePath);
		}
	}
}