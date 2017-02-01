#!/usr/bin/php
<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: prefsdoc.php 60335 2016-11-20 19:23:21Z drsassafras $


// can only be run from the command line
if (php_sapi_name() !== 'cli'){
	echo 'can only be run from the command line';
	die;
}

$baseDir = realpath(__DIR__ .'/../../../').'/';

require_once ($baseDir.'tiki-setup.php');

/**
 *
 * Used for automatically generating preference documentation
 * for use on doc.tiki.org. It takes the currently installed preferences
 * and generates files in wiki-syntax to be pasted into doc.tiki.org
 *
 * Running this will overwrite any previously generated pref doc files
 *
 *
 * limitations
 *
 * Currently a single file is generated for each preference page, however the
 * documentation on doc.tiki.org is split into tabs.
 *
 * Help links dont alwyas link to doc.tiki.org (only a few exceptions) so
 * this script should deal with that, or the help links should be changed
 *
 *
 * Class PrefsDoc
 *
 */

class PrefsDoc extends TWVersion{

	public $baseDir;
	private $PrefVars;
	public $fileCount;
	public $prefCount;
	public $prefDefault;
	public $prefDescription;
	public $prefName;
	public $docTable;

	public function getPrefs(){

		$prefs = array();
		$docFiles = scandir($this->baseDir.'lib/prefs'); // grab all the files that house prefs
		foreach ($docFiles as $fileName) {
			if ($fileName !== 'index.php' && substr($fileName, -4) === '.php') {  // filter out any file thats not a pref file
				require ($this->baseDir.'lib/prefs/'.$fileName);
				$callVar = 'prefs_'.substr($fileName,0,-4).'_list';
				$prefs = array_merge($prefs,$callVar());			// create one big var with all the pref info

			}
		}
		$this->PrefVars = $prefs;
	}



	/**
	 *
	 * This generates a list of prefs in the order that they appear on the admin panel.
	 *
	 * @param string $fileName Name of file to scan
	 *
	 * @return array  array of pref names
	 *
	 */
	public function getAdminUIPrefs($fileName){
			$file = file_get_contents($this->baseDir . 'templates/admin/' . $fileName);
			$file = str_replace(array('\'', '"'), '', $file);
			preg_match_all('{preference name=(\w*)(.*)}', $file, $out, PREG_PATTERN_ORDER);  // return array of every pref name in file
			return $out[1];
	}


	/**
	 *
	 * This sets an vars of an indivigual prefrence
	 *
	 * @param $param
	 */
	public function setParams($param)
	{

		// set default
		if ($this->PrefVars[$param]['default'] == 'n') {
			$this->prefDefault = 'Disabled';
		} else if ($this->PrefVars[$param]['default'] == 'y') {
			$this->prefDefault = 'Enabled';            // Change default codes to human readable format
		} else if (is_array($this->PrefVars[$param]['default'])) {
			$this->prefDefault = implode(', ', $this->PrefVars[$param]['default']);
		} else {
			$this->prefDefault = $this->PrefVars[$param]['default'];
		}
		// end first processing the below should be applied to the above.... not a continuation (eg. empty array)
		$this->prefDefault = trim($this->prefDefault);
		if (!$this->prefDefault) {
			$this->prefDefault = '~~gray:None~~';
		} else if (!preg_match('\W', $this->prefDefault)) {                // if Pref is a singe word
			$this->prefDefault = ucfirst($this->prefDefault);            // then caps the first letter.
		} else{
			if (strlen($this->prefDefault) > 30) {
				$this->prefDefault = substr($this->prefDefault, 0, 27) . '...';
			}
			$this->prefDefault = $this->wikiConvert($this->prefDefault,true);
		}

		// set name
		if ($this->PrefVars[$param]['help']) {
			$this->prefName = '((' . $this->PrefVars[$param]['help'] . '|' . $this->PrefVars[$param]['name'] . '))';
		}else {
			$this->prefName = $this->PrefVars[$param]['name'];
		}
		$this->prefName = $this->wikiConvert($this->prefName);

		// set description
		$this->prefDescription = $this->PrefVars[$param]['description'];
		if ($this->PrefVars[$param]['hint'])
			$this->prefDescription .= " ''Hint: ".$this->PrefVars[$param]['hint']."''";
		foreach ($this->PrefVars[$param]['tags'] as $tag)
			if ($tag === 'experimental')
				$this->prefDescription .= ' (experimental)';
		$this->prefDescription = $this->wikiConvert($this->prefDescription);
	}


	/**
	 * Preps a string from tiki prefs for insertion into tiki-syntax land
	 *
	 * @param $string string to be parsed
	 *
	 * @param $escape bool if $string should be enclosed in tiki no-parse tags
	 *
	 *
	 * @return string parsed string sutable for wiki insertion
	 *
	 */
	private function wikiConvert($string, $escape = false){
		$escapedString = '';
		if ($string) {
			if ($escape) {
				$escapedString = ' ~np~';
			}
			$escapedString .= str_replace("\n", ' ', $string);
			$escapedString = trim($escapedString);
			if ($escape) {
				$escapedString .= '~/np~';
			}
		}

		return $escapedString;

	}

	/**
	 *
	 * Writes a generated pref wiki formatted file to disk
	 *
	 * @param string $fileName  The file name to write to disk.
	 *
	 */
	public function writeFile($fileName){

		$begining = '~hc~ -- Table generated with prefsdoc.php for tiki '.$this->getVersion()." -- ~/hc~\n";
		$begining .= '{FANCYTABLE(head="Option | Description | Default" sortable="n")}'."\n";

		$end = "{FANCYTABLE}\n/////\n";

		$docName = substr(substr($fileName, 8), 0, -4) . ".txt";				// Name of file to be written

		if (!is_dir($this->baseDir.'storage/prefsdoc')) {
			if (!mkdir($this->baseDir . 'storage/prefsdoc')) ;
			{            // create subdir for housing generated files, if it does not exist
				die("Cant create storage/prefsdoc directory\n");
			}
		}if (is_file($this->baseDir . 'storage/prefsdoc/' . $docName)) {
			if (!unlink($this->baseDir . 'storage/prefsdoc/' . $docName)){
				die("Cant overwrite existing $docName\n");
				}
			}

		if (!file_put_contents($this->baseDir . 'storage/prefsdoc/' . $docName, $begining.$this->docTable.$end))		// write one file for each pref page on control panel
			die("Unable to write $docName\n");
	}
}

$Doc = new PrefsDoc();
$Doc->baseDir = $baseDir;
$Doc->fileCount = 0;
$Doc->prefCount = 0;
$Doc->getPrefs();

$docFiles = scandir($baseDir.'templates/admin'); // grab all the files that house prefs
foreach ($docFiles as $fileName) {
	if (substr($fileName, 0, 8) === 'include_') {  // filter out any file thats not a pref file
		$FilePrefs = $Doc->getAdminUIPrefs($fileName);
		$Doc->fileCount ++;
		$Doc->docTable = '';
		foreach ($FilePrefs as $param) {
			$Doc->setParams($param);
			$Doc->prefCount++;
			$Doc->docTable .= $Doc->prefName . '~|~' . $Doc->prefDescription . '~|~' . $Doc->prefDefault . "\n";
		}
		$Doc->writeFile($fileName);
	}
}

echo "\033[33m".$Doc->fileCount. ' pref files written to storage/prefsdoc/ with a total of '.$Doc->prefCount." prefs.\033[0m\n";
