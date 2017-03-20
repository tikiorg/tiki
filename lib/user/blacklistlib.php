<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: monitorlib.php 57962 2016-03-17 20:02:39Z jonnybradley $


/**
 *
 * A Group of functions related to Password Blacklist & Password Index Handling
 *
 * Class blacklist
 */

class blacklistLib extends TikiLib
{
	/**
	 * @var int the number of passwords to generate (limit) or actual number, after the fact.
	 */
	public $limit;
	/**
	 * @var int the actual number of passwords generated.
	 */
	public $actual;


	/**
	 * Set default values
	 *
	 * blacklist constructor.
	 */
	public function __construct()
	{
		$this->limit = 1000; // the number of passwords to generate (limit)
	}

	/**
	 * removes the password index databse, if it exists.
	 */
	public function deletePassIndex()
	{
		$query = 'DROP TABLE IF EXISTS tiki_password_index;';

		$this->query($query, array());

	}

	/**
	 * Creates the word index database table.
	 */
	public function createPassIndex()
	{
		$query = 'CREATE TABLE `tiki_password_index` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`password` VARCHAR(30) NOT NULL , UNIQUE (`password`) ,
`length` TINYINT(30) NULL DEFAULT NULL ,
`numchar` BOOLEAN NULL DEFAULT NULL ,
`special` BOOLEAN NULL , PRIMARY KEY (`id`), UNIQUE (`password`)) ENGINE = InnoDB;';

		$this->query($query, array());
	}

	/**
	 *
	 * Given a filename, it will load the pass index database with its contents.
	 * Files should be word lists separated by new lines.
	 *
	 * @param $filename string
	 * @param $load bool        Specifies if LOAD DATA INFILE is used. One needs to
	 *                          be running mysql locally and have permission to use it
	 *                          however it can handle much larger sets of data.
	 */
	public function loadPassIndex($filename,$load=false)
	{

		if ($load) {
			$query = "LOAD DATA INFILE '" . $filename . "' IGNORE INTO TABLE `tiki_password_index` LINES TERMINATED BY '\n' (`password`);";
			$this->query($query, array());
		}else {
			$passwords = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$passwords = array_map('strtolower', $passwords);
			$passwords = array_map('trim', $passwords);
			$passwords = array_unique($passwords);
			$passwords = array_map('addslashes', $passwords);
			$passwords = "('" . implode("'),('", $passwords) . "')";
			$query = "INSERT INTO tiki_password_index (password) VALUES $passwords";
			$this->query($query);
		}

		unlink($filename); // delete used temp file.

		$query = 'UPDATE tiki_password_index SET password = LOWER(password), length = CHAR_LENGTH(password), numchar = IF(password REGEXP \'[a-z]\' && password REGEXP \'[0-9]\',1,0), special = password REGEXP \'[!@#$%^&*()=+?><\\,.`;:{}~\\\'/"]\'';
// the above indexes the password list with length, if the pasword contains both a letter and number, and if it contains special charactes (except for [], casue i couldnt figure it out!

		$this->query($query, array());

	}

	/**
	 * Find the number of indexed passwords currently stored
	 *@$result TikiDb_Pdo_Result
	 *
	 * @return int
	 */
	public function passIndexNum()
	{

// first check if table exists, and return 0 if it does not.
		$query = 'SELECT 1 FROM information_schema.COLUMNS
WHERE table_name = \'tiki_password_index\'
LIMIT 1;';
		$result = $this->query($query, array());
		$tableExists = $result->fetchRow();
		if ($tableExists[1] != 1) return 0;

// if table does exits, find number of results and return.
		$query = 'SELECT MAX(id) FROM tiki_password_index';
		$result = $this->query($query, array());
		$num_rows = $result->fetchRow();

		return $num_rows['MAX(id)'];
	}

	/**
	 *
	 * Generates a formatted list of passwords, with new lines separating each password
	 *
	 * @param $toDisk bool if the file is witten to disk or to screen.
	 *
	 * @return bool true on success and false on failure.
	 */
	public function generatePassList($toDisk)
	{
		global $prefs;

		$query = 'SELECT password FROM tiki_password_index WHERE length >= ?';
		if ($prefs['pass_chr_special']) $query .= ' && special';
		if ($prefs['pass_chr_num']) $query .= ' && numchar';
		$query .= ' ORDER BY id ASC LIMIT ' . $this->limit;

		$result = $this->query($query, array($prefs['min_pass_length']));
		$this->actual = $result->NumRows();

		if ($toDisk){
			$filename = $this->generateBlacklistName();
			if (!is_dir(dirname($filename)))
				if (!mkdir(dirname($filename))) return false; // if the directory isnt there create it, return false on failure.
			if (file_exists($filename))
				if (!unlink($filename)) return false; // if the file already exists, then delete, return false on failure.
			$pointer = @fopen($filename,'x');
			if (!$pointer) return false;
			while ($foo = $result->fetchrow()) {
				if (!fwrite($pointer, $foo['password'] . PHP_EOL)) return false;
			}
			fclose($pointer);
		}else{
			while ($foo = $result->fetchrow()) {
				echo $foo['password'] . PHP_EOL;
			}
		}
		return true;
	}

	/**
	 * Generates the name for a password file
	 *
	 * @param bool $asFile should the directory be returned as a file name with directory, if false only the name without extension
	 *
	 * @return string
	 *
	 */
	public function generateBlacklistName($asFile = true)
	{
		global $prefs;

		$filename = '';
		if ($asFile) $filename = 'storage/pass_blacklists/'; // directory
		$filename .= $prefs['pass_chr_num'];
		$filename .= '-' . $prefs['pass_chr_special'];
		$filename .= '-' . $prefs['min_pass_length'];
		$filename .= '-1-'; // indicates user created file
		$filename .= $this->actual;
		if (!$asFile) return $filename;
		$filename .= '.txt';

		return $filename;
	}

	public function whatFileUsing()
	{
		if ($GLOBALS['prefs']['pass_blacklist'] == 'n' || !isset($GLOBALS['prefs']['pass_blacklist'])) return 'Disabled';
		else if ($GLOBALS['prefs']['pass_blacklist_file'] == 'auto') return $this->readableBlackName(explode('-',$GLOBALS['prefs']['pass_auto_blacklist'])).' - Auto Selected';
		else return $this->readableBlackName(explode('-',$GLOBALS['prefs']['pass_blacklist_file']));

	}

	/**
	 * Formatts a blacklist file name into a human readable description.
	 *
	 * @param $NameArray array of blacklist file specifycations
	 *
	 * @return string
	 *
	 */

	private function readableBlackName($NameArray){

		$readable = 'Num & Let: ' . $NameArray['0'];
		$readable .= ', Special: ' . $NameArray['1'];
		$readable .= ', Min Len: ' . $NameArray['2'];
		$readable .= ', Custom: ' . $NameArray['3'];
		$readable .= ', Word Count: ' . $NameArray['4'];
		return $readable;

	}

	/**
	 *
	 * populates the password blacklist database table with the contents of a file of passwords.
	 *
	 * @param $filename string the name & path of the saved password
	 */
	public function loadBlacklist($filename){
		if (is_readable($filename)){
			$passwords = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$passwords = array_map('strtolower', $passwords);
			$passwords = array_map('trim', $passwords);
			$passwords = array_unique($passwords);
			$passwords = array_map('addslashes', $passwords);
			$passwords = "('".implode("'),('",$passwords)."')";

			$tikiDb = new TikiDb_Bridge();
			$query = 'DROP TABLE IF EXISTS tiki_password_blacklist;';
			$tikiDb->query($query, array());
			$query = 'CREATE TABLE `tiki_password_blacklist` ( `password` VARCHAR(30) NOT NULL , PRIMARY KEY (`password`) USING HASH)';
			$tikiDb->query($query, array());

			$query = "INSERT INTO tiki_password_blacklist (password) VALUES $passwords";
			$tikiDb->query($query);
		}else Feedback::error(tr('Unable to Populate Blacklist: File dose not exist or is not readable.'));
	}

	/**
	 * Obtains blacklists available, and returns one according to which one is best suited to current settings.
	 * This function may only be called when values being updated, as it relies on the $_POST vars differing from
	 * saved settings
	 *
	 * @var $file[0] bool chracter & number
	 * @var $file[1] bool special character
	 * @var $file[2] int  minimum number of characters
	 * @var $file[3] bool is user generated
	 * @var $file[4] int  number of passwords (limit)
	 *
	 * @param $pass_chr_num string the post var being updated
	 * @param $pass_chr_num string the post var beign updated
	 * @param $length string length value being updated
	 *
	 *
	 * @return array|bool the file name (without extension) that is best suited to govern the blacklist, or false on no suitable files.
	 */
	public function selectBestBlacklist($pass_chr_num,$pass_chr_special,$length){
		$fileIndex = $this->genIndexedBlacks(false);
		$bestFile = false;
		$chrnum = false;
		$special = false;
		if ($pass_chr_num == 'on') $chrnum = true;
		if ($pass_chr_special == 'on') $special = true;

		foreach ($fileIndex as $file){
			if ($file[0] == $chrnum &&       // first qualify the options
				$file[1] == $special &&
				$file[2] <= $length ){
				$count = 2;
				while ($count < 5) {         // then pick the best option
					if ($file[$count] >= $bestFile[$count]) {
						if ($file[$count] > $bestFile[$count]) $bestFile = $file;
						$count++;
					} else $count = 5;
				}
			}
		}

		return $bestFile;
	}

		/**
		 * reads available password list files from disk and returns a sorted array of files
		 *
		 * @param $returnFormatted bool if false, will return a human readable array, if false, will return the same array with only numbers.
		 *
		 * @return array
		 */

	public function genIndexedBlacks($returnFormatted = true){

		$blacklist_options = array_diff(scandir(__DIR__ .'/../pass_blacklists'), array('..', '.', 'index.php', '.htaccess', '.svn', '.DS_Store', 'readme.txt'));
		if (is_dir('storage/pass_blacklists')) {
			$blacklist_options = array_merge($blacklist_options,array_diff(scandir(__DIR__ .'/../../storage/pass_blacklists'), array('..', '.', 'index.php', '.htaccess', '.svn', '.DS_Store', 'readme.txt')));
		}
		sort($blacklist_options);

		$fileindex = array();
		foreach ($blacklist_options as $blacklist_file) {
			$blacklist_file = substr($blacklist_file, 0, -4);
			$fileindex[$blacklist_file] = explode('-', $blacklist_file);
			if ($returnFormatted) $fileindex[$blacklist_file] =$this->readableBlackName($fileindex[$blacklist_file]);
		}
		return $fileindex;
	}

}