<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
if (isset($_REQUEST['loginprefs'])) {
	check_ticket('admin-inc-login');

	if (empty($_REQUEST['registration_choices'])) {
		$_REQUEST['registration_choices'] = array();
	}
	$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
	$in = array();
	$out = array();
	foreach ($listgroups['data'] as $gr) {
		if ($gr['groupName'] == 'Anonymous') {
			continue;
		}
		if ($gr['registrationChoice'] == 'y' && !in_array($gr['groupName'], $_REQUEST['registration_choices'])) {
			// deselect
			$out[] = $gr['groupName'];
		} elseif ($gr['registrationChoice'] != 'y' && in_array($gr['groupName'], $_REQUEST['registration_choices'])) {
			//select
			$in[] = $gr['groupName'];
		}
	}
	if (count($in)) {
		$userlib->set_registrationChoice($in, 'y');
	}
	if (count($out)) {
		$userlib->set_registrationChoice($out, null);
	}
}
if (!empty($_REQUEST['refresh_email_group'])) {
	$nb = $userlib->refresh_set_email_group();
	$smarty->assign('feedback', tra(sprintf(tra("%d user-group assignments"), $nb)));
}

$smarty->assign('gd_lib_found', function_exists('gd_info') ? 'y' : 'n');

$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
$smarty->assign("listgroups", $listgroups['data']);
ask_ticket('admin-inc-login');

/**
 *
 * A Group of functions related to Password Blacklist & Password Index Handling
 *
 * Class blacklist
 */

class blacklist extends TikiLib
{


    /**
     * @var int the maximum length of the password
     */
    public $length;

    /**
     * @var bool  if the password requires numbers and letters
     */
    public $charnum;
    /**
     * @var bool if the password requries special characters
     */
    public $special;
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
        $this->length = $GLOBALS['prefs']['min_pass_length']; // the maximum length of the password
        $this->charnum = $GLOBALS['prefs']['pass_chr_num']; // if the password requires numbers and letters
        $this->special = $GLOBALS['prefs']['pass_chr_special']; // if the password reburies special characters
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
     * Given a filename, it will load the pass index database with its contents. Files should be word lists seperated by new lines.
     *
     * @param $filename
     */
    public function loadPassIndex($filename)
    {

        $query = "LOAD DATA INFILE '" . $filename . "' IGNORE INTO TABLE `tiki_password_index` LINES TERMINATED BY '\n' (`password`);";
        $this->query($query, array());

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

        $query = 'SELECT password FROM tiki_password_index WHERE length >= ?';
        if ($this->special) $query .= ' && special';
        if ($this->charnum) $query .= ' && numchar';
        $query .= ' ORDER BY id ASC LIMIT ' . $this->limit;

        $result = $this->query($query, array($this->length));
        $this->actual = $result->NumRows();

        if ($toDisk){
            $filename = $this->generateBlacklistName();
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

        $filename = '';
        if ($asFile) $filename = 'lib/pass_blacklists/'; // directory
        $filename .= $this->charnum;
        $filename .= '-' . $this->special;
        $filename .= '-' . $this->length;
        $filename .= '-1-'; // indicates user created file
        $filename .= $this->actual;
        if (!$asFile) return $filename;
        $filename .= '.txt';

        return $filename;
    }

    public function whatFileUsing()
    {
        if ($GLOBALS['prefs']['pass_blacklist'] == 'n' || !isset($GLOBALS['prefs']['pass_blacklist'])) return 'Disabled';
        else if ($GLOBALS['prefs']['pass_blacklist'] == 'auto') return readableBlackName(explode('-',$GLOBALS['prefs']['pass_auto_blacklist'])).' - Auto Selected';
        else return readableBlackName(explode('-',$GLOBALS['prefs']['pass_blacklist']));

    }
}

$blackL = new blacklist();

if (isset($_POST['uploadIndex'])){
    if ($_FILES['passwordlist']['error'] === 4) Feedback::error(tr('You need to select a file to upload.'));
    else if ($_FILES['passwordlist']['error']) Feedback::error(tr('File Upload Error: ' . $_FILES['passwordlist']['error']));
    else{  // if file has been uploaded, and there are no errors, then index the file in the databse.
        $blackL->deletePassIndex();
        $blackL->createPassIndex();
        $blackL->loadPassIndex($_FILES['passwordlist']['tmp_name']);
        $smarty->assign('sucess_message', 'Uploaded file has been populated into database and indexed. Ready to generate password lists.');
    }
}else if (isset($_POST['saveblacklist']) || isset($_POST['viewblacklist'])) {

    if (isset($_POST['charnum'])) $blackL->charnum = 1;
    else $blackL->charnum = 0;

    if (isset($_POST['special'])) $blackL->special = 1;
    else $blackL->special = 0;

    $blackL->length = $_POST['length'];
    $blackL->limit = $_POST['limit'];
    if (isset($_POST['viewblacklist'])) {  // if viewing the password list, enter plain text mode, spit out passwords, then exit.

        header('Content-type: text/plain');
        $blackL->generatePassList(false);
        exit;
    }
    // else if save blacklist chosen
    if ($blackL->generatePassList(true)) {
        $filename = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$blackL->generateBlacklistName();
        $smarty->assign('sucess_message', 'Passwod Blacklist Saved to Disk');
        $blackL->set_preference('pass_blacklist', $blackL->generateBlacklistName(false));
        loadBlacklist($filename);
    }else Feedback::error(tr('Unable to Write Password File to Disk'));

}else if (isset($_POST['deleteIndex'])){

    $blackL->deletePassIndex();
}

$smarty->assign('file_using',$blackL->whatFileUsing());
$smarty->assign('length',$blackL->length);
$smarty->assign('charnum',$blackL->charnum);
$smarty->assign('special',$blackL->special);
$smarty->assign('limit',$blackL->limit);

$smarty->assign('num_indexed',$blackL->passIndexNum());
