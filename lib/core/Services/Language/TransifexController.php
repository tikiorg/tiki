<?php
// (c) Copyright 2002-today by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
// TransifexContoller extends the language controller with Transifex (www.transifex.com) related functionalities

class Services_Language_TransifexController extends Services_Language_Controller
{

	function __construct()
	{
		$this->utilities = new Services_Language_Utilities;
	}

	function setUp()
	{
		global $prefs;

		if ($prefs['feature_transifex'] = 'n') { //TODO: change it to != y
			throw new Services_Exception(tr('Feature Disabled'), 403);
		}
	}

	/**
	 * Process a transifex language.php file
	 *
	 * @param $file Complete path to the file including file name
	 *
	 * @return array
	 */
	private function processTransifexLanguagePhp($file)
	{
		//check if file is available
		if (! is_file($file)) {
			throw new Services_Exception_Denied(tr('Invalid file parameter supplied'));
		}

		//language.php file is big, set time limit to prevent timeout
		set_time_limit(0);

		//read the file into an array
		$fileContent = file($file);

		//preg_match array values to get translation strings from the language.php file
		//$captureFullString |(^\/\/\s*?".+"\s*=>\s*".*".*)|
		//$captureSource |^\/\/\s*?".+"\s*=>\s*"(.*)".*|
		//$captureTranslation |^\/\/\s*?"(.+)"\s*=>\s*".*".*|
		foreach ($fileContent as $line) {
			if (preg_match('|^\/\/\s*?"(.+)"\s*=>\s*"(.*)".*|', $line, $matches)) {
				//assuming that untranslated lines are those, that have the same key and value OR where value is empty
				if (($matches[1] === $matches[2]) || ($matches[2] === '')) {
					$untranslated[$matches[1]] = $matches[2];
					//lets keep the original lines as they were so that untranslated strings remain commented out
					$lang[] = $matches[0];
				} //assuming translated lines are those, where key and value are different
				else {
					$translated[$matches[1]] = $matches[2];
					//remove slashes and space to activate translated lines
					$lang[] = substr($matches[0], 3);
					//$lang[] = Language::removePhpSlashes($matches[0]);
				}
			}
		}
		return [
			'lang' => $lang,
			'translated' => $translated,
			'untranslated' => $untranslated,
		];
	}
}
