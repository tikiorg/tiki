<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// ** This is the main script to release Tiki **
//
// To get the Tiki release HOWTO, try:
//    php doc/devtools/release.php --howto
//
// You can also get a detailed help on this script with:
//    php doc/devtools/release.php --help
//

define('TOOLS', dirname(__FILE__));
define('ROOT', realpath(TOOLS . '/../..'));
define('TEMP_DIR', 'temp');

define('CHANGELOG_FILENAME', 'changelog.txt');
define('CHANGELOG', ROOT . '/' . CHANGELOG_FILENAME);
define('COPYRIGHTS_FILENAME', 'copyright.txt');
define('COPYRIGHTS', ROOT . '/' . COPYRIGHTS_FILENAME);
define('SF_TW_MEMBERS_URL', 'http://sourceforge.net/p/tikiwiki/_members');
define('DEV_TW_MEMBERS_URL', 'http://dev.tiki.org/getTikiUser.php');
define('README_FILENAME', 'README');
define('README', ROOT . '/' . README_FILENAME);
define('LICENSE_FILENAME', 'license.txt');

// Display all errors and warnings, including strict level
define('ERROR_REPORTING_LEVEL', E_ALL | E_STRICT);
error_reporting(ERROR_REPORTING_LEVEL);

chdir(ROOT .'/');

require_once ROOT . '/lib/setup/third_party.php';
require_once TOOLS . '/svntools.php';

if (version_compare(PHP_VERSION, '5.0.0', '<')) {
	error("You need PHP version 5 or more to run this script\n");
}
$phpCommand = isset($_SERVER['_']) ? $_SERVER['_'] : 'php';
$phpCommandArguments = implode(' ', $_SERVER['argv']);

if (! ($options = get_options()) || $options['help']) {
	display_usage();
}

if ($options['howto']) {
	display_howto();
}

if (! check_svn_version()) {
	error("You need the subversion 'svn' program at least at version " . SVN_MIN_VERSION . "\n");
}

if (! $options['no-check-svn'] && has_uncommited_changes('.')) {
	error("Uncommited changes exist in the working folder.\n");
}

include_once('lib/setup/twversion.class.php');
$TWV = new TWVersion();

if ($options['only-secdb']) {
	updateSecdb($TWV->version);
	exit;
}

$script = $_SERVER['argv'][0];
$version = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
$subrelease = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : '';

if (! preg_match("/^\d+\.\d+$/", $version)) {
	error("Version number should be in X.X format.\n");
}

$isPre = strpos($subrelease, 'pre') === 0;
if ($isPre) {
	$subrelease = substr($subrelease, 3);
	$pre = 'pre';
} else {
	$pre = '';
}
$splitedversion = explode('.', $version);
$mainversion = $splitedversion[0];

$check_version = $version.$subrelease;
if ($TWV->version != $check_version) {
	error("The version in the code ".strtolower($TWV->version)." differs from the version provided to the script $check_version.\nThe version should be modified in lib/setup/twversion.class.php to match the released version.");
}

echo color("\nTiki release process started for version '$version" . ($subrelease ? " $subrelease" : '') . "'\n", 'cyan');
if ($isPre) {
	echo color("The script is running in 'pre-release' mode, which means that no subversion tag will be created.\n", 'yellow');
}

if (! $options['no-first-update'] && important_step('Update working copy to the last revision')) {
	echo "Update in progress...";
	update_working_copy('.');

	if (! $options['no-check-svn'] && has_uncommited_changes('.')) {
		error("\rUncommited changes exist in the working folder.\n");
	}
	$revision = (int) get_info('.')->entry->commit['revision'];
	info("\r>> Checkout updated to revision $revision.");
}

if (empty($subrelease)) {
	$branch = "branches/$mainversion.x";
	$tag = "tags/$version";
	$packageVersion = $version;
	if (! empty($pre)) {
		$packageVersion .= ".$pre";
	}
	$secdbVersion = $version;
} else {
	$branch = "branches/$mainversion.x";
	$tag = "tags/$version$subrelease";
	$packageVersion = "$version.$pre$subrelease";
	$secdbVersion = "$version$subrelease";
}

if (! $options['no-readme-update'] && important_step("Update '" . README_FILENAME . "' file")) {
	update_readme_file($secdbVersion, $version);
	info('>> ' . README_FILENAME . ' file updated.');
	important_step('Commit updated ' . README_FILENAME . ' file', true, "[REL] Update " . README_FILENAME . " file for $secdbVersion");
}

if (! $options['no-lang-update'] && important_step("Update language files")) {
	passthru("$phpCommand get_strings.php");
	$removeFiles = glob('lang/*/language.php.old');
	foreach ($removeFiles as $rf) {
		unlink($rf);
	}
	unset($removeFiles);
	info('>> Language files updated and temporary files removed.');
	important_step('Commit updated language files', true, "[REL] Update language.php files for $secdbVersion");
}

if (! $options['no-changelog-update'] && important_step("Update '" . CHANGELOG_FILENAME . "' file (using final version number '$version')")) {
	if ($ucf = update_changelog_file($version)) {
		if ($ucf['nbCommits'] == 0) {
			info('>> Changelog updated (last commits were already inside)');
		} else {
			if ($ucf['sameFinalVersion']) {
				info(">> There were already some commits for the same final version number in the changelog. Merging them with the new ones.");
			}
			info(">> Changelog updated with {$ucf['nbCommits']} new commits (revision {$ucf['firstRevision']} to {$ucf['lastRevision']}), excluding duplicates, merges and release-related commits.");
		}
		important_step("Commit new " . CHANGELOG_FILENAME, true, "[REL] Update " . CHANGELOG_FILENAME . " for $secdbVersion");
	} else {
		error('Changelog update failed.');
	}
	unset($ucf);
}

$nbCommiters = 0;
if (! $options['no-copyright-update'] && important_step("Update '" . COPYRIGHTS_FILENAME . "' file (using final version number '$version')")) {
	if ($ucf = update_copyright_file($mainversion . '.0')) {
		info(
			"\r>> Copyrights updated: "
			. ($ucf['newContributors'] == 0 ? 'No new contributor, ' : "+{$ucf['newContributors']} contributor(s), ")
			. ($ucf['newCommits'] == 0 ? 'No new commit' : "+{$ucf['newCommits']} commit(s)")
		);
		important_step("Commit new " . COPYRIGHTS_FILENAME, true, "[REL] Update " . COPYRIGHTS_FILENAME . " for $secdbVersion");
	} else {
		error('Copyrights update failed.');
	}
}

if (! $options['no-check-php'] && important_step("Check syntax of all PHP files")) {
	$error_msg = '';
	$dir = '.';
	check_php_syntax($dir, $error_msg, $options['no-check-php-warnings']) or error($error_msg);
	info('>> Current PHP code successfully passed the syntax check.');
}

if (! $options['no-check-smarty'] && important_step("Check syntax of all Smarty templates")) {
	$error_msg = '';
	check_smarty_syntax($error_msg);
	info('>> Current Smarty code successfully passed the syntax check.');
}

if (! $options['no-secdb'] && important_step("Update SecDB file(s) 'db/tiki-secdb_{$version}_mysql.sql'")) {
	if(updateSecdb($TWV->version))
		important_step("Commit SecDB file changes", true, "[REL] SecDB for $secdbVersion");
}

if ($isPre) {
	if (! $options['no-packaging'] && important_step("Build packages files")) {
		build_packages($packageVersion);
		echo color("\nMake sure these tarballs are tested by at least 3 different people.\n\n", 'cyan');
	} else {
		echo color("This was the last step.\n", 'cyan');
	}
} else {


	if (! $options['no-tagging']) {
		$fb = full($branch);
		$ft = full($tag);

		$tagAlreadyExists = isset(get_info($ft)->entry);
		if ($tagAlreadyExists && important_step("The Tag '$tag' already exists: Delete the existing tag in order to create a new one")) {
			$commit_msg = "[REL] Deleting tag '$tag' in order to create a new one";
			if ($options['no-commit']) {
				print "Skipping actual commit ('$commit_msg') because no-commit = true\n";
			} else {
				`svn rm $ft -m "$commit_msg"`;
				$tagAlreadyExists = false;
				info(">> Tag '$tag' deleted.");
			}
		}
		if (! $tagAlreadyExists) {
			update_working_copy('.');
			$revision = (int) get_info(ROOT)->entry->commit['revision'];
			if (important_step("Tag release using branch '$branch' at revision $revision")) {
				$commit_msg = '[REL] Tagging release';
				if ($options['no-commit']) {
					print "Skipping actual commit ('$commit_msg') because no-commit = true\n";
				} else {
					`svn copy $fb -r$revision $ft -m "$commit_msg"`;
					info(">> Tag '$tag' created.");
				}
			}
		}
	}

	if (! $options['no-packaging'] && important_step("Build packages files")) {
		build_packages($packageVersion);
		echo color("\nUpload the files on SourceForge.\nInstructions can be found here: http://sourceforge.net/p/forge/documentation/Files/\n", 'cyan');
		echo color("\nAlternately, do it by hand after replacing SF_LOGIN with your sf.net login\nand after replacing PATH according to https://sourceforge.net/projects/tikiwiki/files/\ncd ~/tikipack/$packageVersion; scp tiki-$packageVersion.* SF_LOGIN@frs.sourceforge.net:/home/frs/project/t/ti/tikiwiki/Tiki_PATH/$packageVersion \n\n", 'cyan');
	} else {
		echo color("This was the last step.\n", 'cyan');
	}
}

// Helper functions


/**
 *
 * Will remove old secdb files and generate a new one based on working copy files.
 *
 * @param $version string The current tiki version to use eg. 17.0 or 21.0RC1
 * @return bool true on success
 */

function updateSecdb($version)
{
	// first unset any preexisting files.
	echo (">>");
	$svn = preg_match('/svn$/', $version);

	// if we are not creating a release skip deleting old files.
	if (!$svn) {
	$files = glob(ROOT . '/db/tiki-secdb_*_mysql.sql');
	foreach ($files as $file) {
			$file = escapeshellarg($file);
			`svn delete $file --force`;
		}
		echo (' Removed '.count($files).' old secdb files.');
	}

	$file = "/db/tiki-secdb_{$version}_mysql.sql";

	if (!$fp = @fopen(ROOT.$file, 'w')){
		error('The SecDB file "' . ROOT.$file . '" is not writable or can\'t be created.');
		return false;
	}
	$queries = array();
	md5_check_dir(ROOT, $version, $queries);

	if (! empty($queries)) {
		sort($queries);
		fwrite($fp, "start transaction;\n");
		fwrite($fp, "DELETE FROM `tiki_secdb` WHERE `tiki_version` = '$version';\n\n");
		foreach ($queries as $q) {
			fwrite($fp, "$q\n");
		}
		fwrite($fp, "commit;\n");
	}
	fclose($fp);

	echo (" $file was generated.\n");
	if (!$svn) {
		$file = escapeshellarg(ROOT.$file); // escape file name for use in command line.
		`svn add $file`;
	}
	return true;
}

/**
 * @param $dir
 * @param $version
 * @param $queries
 */
function md5_check_dir($dir, $version, &$queries)
{
	$d = dir($dir);
	$link = null;

	while (false !== ($e = $d->read())) {
		$entry = $dir . '/' . $e;
		if (is_dir($entry)) {
			// do not descend and no CVS/Subversion files
			if ($e != '..' && $e != '.' && $e != 'CVS' && $e != '.svn' && $entry != ROOT . '/temp' && $entry != ROOT . '/vendor_custom') {
				md5_check_dir($entry, $version, $queries);
			}
		} else {
			if (preg_match('/\.(sql|css|tpl|js|php)$/', $e) && realpath($entry) != __FILE__ && $entry != './db/local.php') {
				$file = '.' . substr($entry, strlen(ROOT));

				// Escape filename. Since this requires a connection to MySQL (due to the charset), do so conditionally to reduce the risk of connection failure. 
				if (! preg_match('/^[a-zA-Z0-9\/ _+.-]+$/', $file) ) {
					if (! $link) {
						$link = mysqli_connect();

						if (mysqli_connect_errno()) {
							global $phpCommand, $phpCommandArguments;
							error(
								"SecDB step failed because some filenames need escaping but no MySQL connection has been found (" . mysqli_connect_error() . ")."
								. "\nTry this command line instead (replace HOST, USER and PASS by a valid MySQL host, user and password) :"
								. "\n\n\t" . $phpCommand
								. " -d mysqli.default_host=HOST -d mysqli.default_user=USER -d mysqli.default_pw=PASS "
								. $phpCommandArguments . "\n"
							);
						}
					}
					$file = @mysqli_real_escape_string($link,$file);
				}

				if (is_readable($entry)) {
					$hash = md5_file($entry);
					$queries[] = "INSERT INTO `tiki_secdb` (`filename`, `md5_value`, `tiki_version`) VALUES('$file', '$hash', '$version');";
				}
			}
		}
	}
	$d->close();
}

/**
 *
 * Deletes a directory and its contents.
 *
 * @param string $dir directory to delete.
 * @return string|bool returns the filename that an error occurred in, false otherwise
 */

function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				@chmod($dir."/".$object,0777);
				if (filetype($dir."/".$object) === 'dir'){
					$error = rrmdir($dir."/".$object);
					if ($error)
						return $error;
				}else
					if (!@unlink($dir."/".$object))
						return 'Could not delete '.$dir."/".$object."\n";
			}
		}
		reset($objects);
		@unlink($dir.'/.DS_store');
		if (!@rmdir($dir)){
			return 'Could not delete '.$dir."\n";
		}
	}
	return false;
}


/**
 *
 * Recursivley deletes specific files or directories
 *
 * @param string $src Directory to search through
 * @param array $files An array of file names to delete.
 */

function removeFiles($src,$files) {
	$dir = opendir($src);
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			$full = $src . '/' . $file;
			if ( is_dir($full) ) {
				$flag = false;

				foreach ($files as $delfile) {
					if (basename($full) === $delfile) {
						rrmdir($full);
						$flag =true;
						break;
					}
				}
				if (!$flag)
					removeFiles($full, $files);

			}else {
				foreach($files as $delfile) {
					if (basename($full) === $delfile) {
						@chmod($full, 0777);
						unlink($full);
						break;
					}
				}
			}
		}
	}
	closedir($dir);
}


/**
 *
 * Recursively sets permissions. Files get 775 and directories 664.
 *
 * @param string $src The directory to set permissions for
 */

function setPermissions($src) {
	$dir = opendir($src);
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			$full = $src . '/' . $file;
			if ( is_dir($full) ) {
				setPermissions($full);
				chmod($full,0755
				);
			}
			else {
				chmod($full,0664);

			}
		}
	}
	closedir($dir);
}


/**
 *
 * Prepares and generates the release packages.
 *
 * @param string $releaseVersion	Version of tiki that is being released.
 */

function build_packages($releaseVersion)
{
	global $options;

	$workDir=$_SERVER['HOME']."/tikipack";
	$relDir = $workDir. '/tiki-' .$releaseVersion;

	echo  "Seting up ~/tikipack directory\n";
	if (!is_dir($workDir)) {
		if (!mkdir($workDir)) {
			error('Cant make ' . $workDir."\n");
			die();
		}
	}

	// remove previous files if they exist.
	if (is_dir($relDir)) {
		echo "Removing previous files\n";
		$shellout = rrmdir($relDir);
		if ($shellout)
			die ($shellout."\n");
	}
	$removeFiles = glob($relDir.'.*');
	foreach ($removeFiles as $file){
		unlink($file);
	}

	// create a export in tikipack to work with
	echo "Creating SVN export from working copy\n";
	$shellout = shell_exec('svn export '.escapeshellarg(ROOT).' '.escapeshellarg($relDir).' 2>&1');
	if ($options['debug-packaging'])
		echo $shellout."\n";


	if (!is_file($relDir.'/vendor_bundled/composer.json')){
		echo 'composer.json not found. Aborting.'."\n";
		die();
	}

	if (is_file($workDir.'/composer.phar')){
		if (!unlink($workDir.'/composer.phar')){
			echo "Can't delete tikipack/composer.phar. Aborting."."\n";
			die();
		}
	}

	echo "Downloading composer.phar"."\n";
	if (!file_put_contents($workDir.'/composer.phar', file_get_contents('http://getcomposer.org/composer.phar'))){
		echo "Can't create tikipack/composer.phar. Aborting."."\n";
		die();
	}

	echo 'Installing dependencies through composer'."\n";
	$shellout =  shell_exec('php '.escapeshellarg($workDir.'/composer.phar').' install -d '.escapeshellarg($relDir.'/vendor_bundled').' --prefer-dist --no-dev 2>&1');
	if ($options['debug-packaging'])
		echo $shellout."\n";

	if (strpos($shellout,'Fatal error:') || strpos($shellout,'Installation failed,')){
		echo 'Composer Instillation Failed. Exiting'."\n";
		die();
	}
	if ($options['debug-packaging'])
		echo $shellout."\n";

	echo "Removing development files\n";
	$shellout = rrmdir($relDir.'/tests');
	if ($shellout)
		die ($shellout."\n");

	$shellout = rrmdir($relDir.'/db/convertscripts');
	if ($shellout)
		die ($shellout."\n");

	$shellout = rrmdir($relDir.'/doc/devtools');
	if ($shellout)
		die ($shellout."\n");

	removeFiles($relDir,array('.cvsignore','.svnignore','.gitignore','.gitkeep','.eslintignore','.eslintrc','.npmignore','.editorconfig','.hound.yml','.travis.yml','.jshintignore','.github'));

	echo "Removing language file comments\n";
	foreach (scandir($relDir.'/lang') as $strip) {
		if (is_file($relDir . '/lang/' .$strip.'/language.php'))
			$shellout = shell_exec('php '.escapeshellarg(dirname(__FILE__).'/stripcomments.php').' '. escapeshellarg($relDir . '/lang/' .$strip.'/language.php').' 2>&1');
		if ($shellout)
			die ($shellout."\n");
	}

	echo "Setting file permissions\n";
	setPermissions($relDir);

	echo "Creating tiki-$releaseVersion.tar.gz\n";
	$shellout =  shell_exec("tar -pczf ".escapeshellarg($relDir.".tar.gz")." --exclude '*.DS_Store' ".escapeshellarg($relDir).' 2>&1');
	if ($options['debug-packaging'])
		echo $shellout."\n";

	echo "Creating tiki-$releaseVersion.bz2\n";
	$shellout =  shell_exec("tar -pcjf ".escapeshellarg($relDir.".tar.bz2")." --exclude '*.DS_Store' ".escapeshellarg($relDir).' 2>&1');
	if ($options['debug-packaging'])
		echo $shellout."\n";

	echo "Creating tiki-$releaseVersion.zip\n";
	$shellout =  shell_exec("zip -r ".escapeshellarg($relDir.".zip").' '.escapeshellarg($relDir).' -x "*.DS_Store" -9 2>&1');
	if ($options['debug-packaging'])
		echo $shellout."\n";

	echo "Creating tiki-$releaseVersion.7z\n";
	$shellout =  shell_exec("7za a -mx9 ".escapeshellarg($relDir.".7z").' '.escapeshellarg($relDir).' -r -x!*.DS_Store 2>&1');
	if (strpos($shellout, 'command not found'))
		error("7za not installed. Archive creation failed.\n");
	if ($options['debug-packaging'])
		echo $shellout."\n";

	echo "\nTo upload the 'tarballs', copy-paste and execute the following line (and change '\$SF_LOGIN' by your SF.net login):\n";
echo "cd $relDir; scp tiki-$releaseVersion.tar.gz tiki-$releaseVersion.tar.bz2 tiki-$releaseVersion.zip \$SF_LOGIN@frs.sourceforge.net:uploads\n";

	info(">> Packages files have been built in ~/tikipack/$releaseVersion :\n");
}

/**
 * @param $dir
 * @param $entries
 * @param $regexp_pattern
 * @return bool
 */
function get_files_list($dir, &$entries, $regexp_pattern)
{
	$d = dir($dir);
	while (false !== ($e = $d->read())) {
		$entry = $dir . '/' . $e;
		if (is_dir($entry)) {
			// do not descend and no CVS/Subversion files
			if ($e != '..' && $e != '.' && $e != 'CVS' && $e != '.svn' && $entry != './temp/templates_c' && $entry != './vendor_bundled/vendor') {
				if (! get_files_list($entry, $entries, $regexp_pattern)) {
					return false;
				}
			}
		} elseif (preg_match($regexp_pattern, $e) && realpath($entry) != __FILE__) {
			$entries[] = $entry;
		}
	}
	$d->close();
	return true;
}

/**
 * @param $alreadyDone
 * @param $toDo
 * @param $message
 */
function display_progress_percentage($alreadyDone, $toDo, $message)
{
	$onePercent = ceil($toDo / 100);
	if ($alreadyDone % $onePercent === 0 || $alreadyDone == $toDo) {
		$percentage = ($alreadyDone >= $toDo - $onePercent) ? 100 : min(100, $alreadyDone / $onePercent);
		printf("\r$message", $percentage);
	}
}

function zone_is_empty() {
	// dummy function to keep smarty happy
}

/**
 * @param $error_msg
 */
function check_smarty_syntax(&$error_msg)
{
	global $tikidomain, $prefs;
	$tikidomain = '';
	// Initialize $prefs with some variables needed by the tra() function and smarty autosave plugin
	$prefs = array(
		'lang_use_db' => 'n',
		'language' => 'en',
		'site_language' => 'en',
		'feature_ajax' => 'n'
	);

	// Load Tiki Smarty
	$prefs['smarty_compilation'] = 'always';
	$prefs['smarty_security'] = 'y';
	$prefs['maxRecords'] = 25;
	$prefs['log_tpl'] = 'y';
	$prefs['feature_sefurl_filter'] = 'y';
	$prefs['site_layout'] = 'basic';
	require_once 'vendor_bundled/vendor/smarty/smarty/libs/Smarty.class.php';
	require_once 'lib/init/smarty.php';
	// needed in Smarty_Tiki
	define('TIKI_PATH', getcwd());
	require_once 'lib/core/TikiAddons.php';
	require_once 'lib/smarty_tiki/prefilter.tr.php';
	require_once 'lib/smarty_tiki/prefilter.jq.php';
	require_once 'lib/smarty_tiki/prefilter.log_tpl.php';
	$smarty = new Smarty_Tiki();
	set_error_handler('check_smarty_syntax_error_handler');

	$templates_dir = TIKI_PATH . '/templates';

	$errors_found = false;
	$entries = array();
	get_files_list($templates_dir, $entries, '/\.tpl$/');

	$nbEntries = count($entries);
	for ($i = 0; $i < $nbEntries; $i++) {
		display_progress_percentage($i, $nbEntries, '%d%% of files passed the Smarty syntax check');

		if (strpos($entries[$i], 'tiki-mods.tpl') === false) {

			$template_file = substr($entries[$i], strlen($templates_dir) + 1);

			try {
				$_tpl = $smarty->createTemplate($template_file, null, null, null, false);
				$_tpl->compileTemplateSource();
			} catch (Exception $e) {
				echo color("\nError: " . $e->getMessage(), 'red') . "\n";
				$errors_found = true;
			}

		}
	}
	restore_error_handler();

	echo "\n";

	if ($errors_found) {
		die('Fix the Smarty errors and try again please.');
	}
}


/**
 * @param $errno
 * @param $errstr
 * @param string $errfile
 * @param int $errline
 * @param array $errcontext
 */
function check_smarty_syntax_error_handler($errno, $errstr, $errfile = '', $errline = 0, $errcontext = array())
{
	if (strpos($errstr, 'filemtime(): stat failed for') === false) {	// smarty seems to emit these for every file
		echo "\n" . color($errstr, 'red') . "\n";
	}
	return true;
}

/**
 * @param $dir
 * @param $error_msg
 * @param $hide_php_warnings
 * @return bool
 */
function check_php_syntax(&$dir, &$error_msg, $hide_php_warnings)
{
	global $phpCommand;
	$checkPhpCommand = $phpCommand . (ERROR_REPORTING_LEVEL > 0 ? ' -d error_reporting=' . (int) ERROR_REPORTING_LEVEL : '');

	$entries = array();
	get_files_list($dir, $entries, '/\.php$/');

	$nbEntries = count($entries);
	for ($i = 0; $i < $nbEntries; $i++) {
		display_progress_percentage($i, $nbEntries, '%d%% of files passed the PHP syntax check');
		$return_var = 0;
		$output = null;
		exec("$checkPhpCommand -l {$entries[$i]} 2>&1", $output, $return_var);
		$fullOutput = implode("\n", $output);

		if (strpos($fullOutput, 'Segmentation fault') !== false) {
			// If php -l command segfaults, wait and retry (it seems to happen quite often on some environments for this command)
			echo "\r[Retrying due to a Segfault...]";
			sleep(1);
			$i--;
		} elseif ($return_var !== 0) {
			// Handle PHP errors
			$fullOutput = trim($fullOutput);
			$error_msg = ($fullOutput == '') ? "\nPHP Parsing error in '{$entries[$i]}' ($return_var)\n" : "\n$fullOutput";
			return false;
		} elseif (! $hide_php_warnings && ($nb_lines = count($output)) > 1 && ! preg_match(THIRD_PARTY_LIBS_PATTERN, $entries[$i])) {
			// Handle PHP warnings / notices (this just displays a yellow warning, it doesn't return false or an error_msg)
			// and exclude some third party libs when displaying warnings from the PHP syntax check, because we can't fix it directly by the way.
			echo "\r";
			foreach ($output as $k => $line) {
				// Remove empty lines and last line (because in case of a simple warning, the last line simply says 'No syntax errors...')
				if (trim($line) == '' || $k == $nb_lines - 1) {
					continue;
				}
				echo color("$line\n", 'yellow');
			}
			display_progress_percentage($i, $nbEntries, '%d%% of files passed the PHP syntax check');
		}
		unset($output, $return_var);
	}

	echo "\n";
	return true;
}

/**
 * @return array|bool
 */
function get_options()
{
	if ($_SERVER['argc'] <= 1) {
		return false;
	}

	$argv = array();
	$options = array(
		'howto' => false,
		'help' => false,
		'http-proxy' => false,
		'svn-mirror-uri' => false,
		'no-commit' => false,
		'no-check-svn' => false,
		'no-check-php' => false,
		'no-check-php-warnings' => false,
		'no-check-smarty' => false,
		'no-first-update' => false,
		'no-readme-update' => false,
		'no-lang-update' => false,
		'no-changelog-update' => false,
		'no-copyright-update' => false,
		'no-secdb' => false,
		'no-packaging' => false,
		'no-tagging' => false,
		'force-yes' => false,
		'debug-packaging' => false,
		'only-secdb' => false,
	);

	// Environment variables provide default values for parameter options. e.g. export TIKI_NO_SECDB=true
	$prefix = "TIKI-";
	foreach ($options as $option => $optValue) {
		$envOption = $prefix.$option;
		$envOption = str_replace("-", "_", $envOption);
		if (isset($_ENV[$envOption])) {
			$envValue = $_ENV[$envOption];
			$options[$option] = $envValue;
		}
	}

	foreach ($_SERVER['argv'] as $arg) {
		if (substr($arg, 0, 2) == '--') {
			if (($opt = substr($arg, 2)) != '' && isset($options[$opt])) {
				$options[$opt] = true;
			} elseif (substr($arg, 2, 11) == 'http-proxy=') {
				if (($proxy = substr($arg, 13)) != '') {
					$options[substr($arg, 2, 10)] = stream_context_create(
						array(
							'http' => array(
								'proxy' => 'tcp://' . $proxy,
								'request_fulluri' => true
							)
						)
					);
				} else {
					$options[substr($arg, 2, 10)] = true;
				}
			} elseif (substr($arg, 2, 15) == 'svn-mirror-uri=') {
				if (($uri = substr($arg, 17)) != '') {
					$options[substr($arg, 2, 14)] = $uri;
				}
			} else {
				error("Unknown option $arg. Try using --help option.\n");
			}
		} else {
			$argv[] = $arg;
		}
	}
	$_SERVER['argv'] = $argv;
	unset($argv);

	if ($options['http-proxy'] === true) {
		error("The --http-proxy option need a value. Use it this way: --http-proxy=HOST_DOMAIN:PORT_NUMBER");
	}

	if ($_SERVER['argc'] == 2) {
		$_SERVER['argv'][] = '';
	}

	return $options;
}

/**
 * @param $msg
 * @param bool $increment_step
 * @param bool $commit_msg
 * @return bool
 */
function important_step($msg, $increment_step = true, $commit_msg = false)
{
	global $options;
	static $step = 0;

	// Auto-Skip the step if this is a commit step and if there is nothing to commit
	if ($commit_msg && ! has_uncommited_changes('.')) {
		return false;
	}

	// Increment step number if needed
	if ($increment_step) {
		$step++;
	}

	if ($commit_msg && $options['no-commit']) {
		print "Skipping actual commit ('$commit_msg') because no-commit = true\n";
		return false;
	}

	if ($options['force-yes']) {
		important("\n$step) $msg...");
		$do_step = true;
	} else {
		important("\n$step) $msg?");

		$prompt = '[Y/n/q/?] ';
		if (function_exists('readline')) {
			// readline function requires php readline extension...
			$c = readline($prompt);
		} else {
			echo $prompt;
			$c = rtrim(fgets(STDIN), "\n");
		}

		switch (strtolower($c)) {
			case 'y': case '':
				$do_step = true;
				break;
			case 'n':
				info(">> Skipping step $step.");
				$do_step = false;
				break;
			case 'q':
				die;
				break;
			default:
				if ($c != '?') {
					info(color(">> Unknown answer '$c'.", 'red'));
				}
				info(">> You have to type 'y' (Yes), 'n' (No) or 'q' (Quit) and press Enter.");
				return important_step($msg, false);
		}
	}

	if ($commit_msg && $do_step && ($revision = commit($commit_msg))) {
		info(">> Commited revision $revision.");
	}

	return $do_step;
}

/**
 * @param $newVersion
 * @return array|bool
 */
function update_changelog_file($newVersion)
{
	$handle = false;
	if (! is_readable(CHANGELOG) || ! is_writable(CHANGELOG) || ! ($handle = @fopen(CHANGELOG, "r"))) {
		error('The changelog file "' . CHANGELOG . '" is not readable or writable.');
	}

	$majorVersion = substr($newVersion, 0, strpos($newVersion, '.'));
	$parseLogs = $sameFinalVersion = $skipBuffer = false;
	$lastReleaseMajorNumber = -1;
	$lastReleaseNumber = '';
	$minRevision = $currentParsedRevision = 0;
	$lastReleaseLogs = array();
	$versionMatches = array();
	$newChangelog = '';
	$newChangelogEnd = '';

	if ($handle) {
		while (! feof($handle)) {
			$buffer = fgets($handle);
			if (empty($buffer)) {
				continue;
			}

			if (preg_match('/^Version (\d+)\.(\d+)/', $buffer, $versionMatches)) {
				$lastReleaseNumber = $versionMatches[1].'.'.$versionMatches[2];
				if ($lastReleaseNumber == $newVersion) {
					// The changelog file already contains log for the same final version
					$sameFinalVersion = true;
					$skipBuffer = true;
				}
				$parseLogs = true;
				$lastReleaseMajorNumber = $versionMatches[1];
			} elseif ($parseLogs) {
				$matches = array();
				if (preg_match('/^r(\d+) \|/', $buffer, $matches)) {
					$skipBuffer = false;
					if ($minRevision == 0) {
						$minRevision = (int) $matches[1];
					}
					$currentParsedRevision = (int) $matches[1];
				} elseif (! $skipBuffer && $currentParsedRevision > 0 && $buffer[0] != '-') {
					if (isset($lastReleaseLogs[$currentParsedRevision])) {
						$lastReleaseLogs[$currentParsedRevision] .= $buffer;
					} else {
						$lastReleaseLogs[$currentParsedRevision] = $buffer;
					}
				}
			}
			if ($lastReleaseMajorNumber != -1 && $lastReleaseMajorNumber < $majorVersion) {
				$newChangelogEnd .= generate_changelog_version_header($lastReleaseNumber);
				$newChangelogEnd .= "Changelog for Tiki version ".$lastReleaseNumber.", or older, available at:\n";
				$newChangelogEnd .= "https://sourceforge.net/p/tikiwiki/code/HEAD/tree/tags/".$lastReleaseNumber."/changelog.txt\n\n";
				break; // truncate the rest of the file
			}
			if (! $skipBuffer) {
				if ($lastReleaseMajorNumber == -1) {
					$newChangelog .= $buffer;
				} else {
					$newChangelogEnd .= $buffer;
				}
			}
		}
		fclose($handle);
	}

	$newChangelog .= generate_changelog_version_header($newVersion);

	$return = array('nbCommits' => 0, 'sameFinalVersion' => $sameFinalVersion);
	$matches = array();
	if ($minRevision > 0) {
		if (preg_match_all('/^r(\d+) \|.*\n\n(.*)\-{46}/Ums', get_logs('.', $minRevision), $matches, PREG_SET_ORDER)) {
			foreach ($matches as $logEntry) {

				// Do not keep merges and release-related logs
				$commitFlag = substr(trim($logEntry[2]), 0, 5);
				if ($commitFlag == '[MRG]' || $commitFlag == '[REL]') {
					continue;
				}

				// Add log entries only if they were not already listed (same revision number or same log message) in the previous version
				if (! isset($lastReleaseLogs[$logEntry[1]]) && ! in_array("\n".$logEntry[2], $lastReleaseLogs)) {
					$newChangelog .= $logEntry[0]."\n";

					$lastReleaseLogs[] = "\n".$logEntry[2];
					if ($return['nbCommits'] == 0) {
						$return['firstRevision'] = $logEntry[1];
					}
					$return['lastRevision'] = $logEntry[1];
					$return['nbCommits']++;
				}
			}
		}
	}

	return file_put_contents(CHANGELOG, $newChangelog . $newChangelogEnd) ? $return : false;
}

/**
 * Generate the header for a given version, used in the changelog
 * @param string $version
 * @return string
 */
function generate_changelog_version_header($version)
{
	$majorVersion = substr($version, 0, strpos($version, '.'));
	$releaseNotesURL = '<http://doc.tiki.org/Tiki' . $majorVersion . '>';

	$versionHeader = <<<EOS
Version $version
$releaseNotesURL
------------------

----------------------------------------------

EOS;
	return $versionHeader;
}

/**
 * @param $newVersion
 * @return array|bool
 */
function update_copyright_file($newVersion)
{
	if (! is_readable(COPYRIGHTS) || ! is_writable(COPYRIGHTS)) {
		error('The copyright file "' . COPYRIGHTS . '" is not readable or writable.');
	}
	global $nbCommiters, $options;
	$nbCommiters = 0;
	$contributors = array();

	$repositoryUri = empty($options['svn-mirror-uri']) ? TIKISVN : $options['svn-mirror-uri'];
	if (strpos($repositoryUri, '/') === 0) {
		$repositoryUri = 'file://' . $repositoryUri;
	}
	$repositoryInfo = get_info($repositoryUri);

	$oldContributors = parse_copyrights();
	get_contributors_data($repositoryUri, $contributors, 1, (int) $repositoryInfo->entry->commit['revision']);
	ksort($contributors);

	$totalContributors = count($contributors);
	$now = gmdate('Y-m-d');

	$copyrights = <<<EOS
Tiki Copyright
----------------

The following list attempts to gather the copyright holders for Tiki
as of version $newVersion.

Accounts listed below with commits have contributed source code to CVS or SVN.
Please note that even more people contributed on various other aspects (documentation,
bug reporting, testing, etc.)

This is how we implement the Tiki Social Contract.
http://tiki.org/Social+Contract

List of members of the Community
As of $now, the community has:
  * $totalContributors members on SourceForge.net,
  * $nbCommiters of those people who made at least one code commit

This list is automatically generated and alphabetically sorted
from subversion repository by the following script:
  doc/devtools/release.php

Counting the commits is not as trivial as it may sound. If your number of commits
seems incorrect, it could be that the script is not detecting them all. This
has been reported especially for commits early on in the project. Nonetheless,
the list provides a general idea.

====================================================================

EOS;

	$return = array('newCommits' => 0, 'newContributors' => 0);
	foreach ($contributors as $author => $infos) {
		if (isset($oldContributors[$author])) {
			if ($oldContributors[$author] != $infos) {
				// Quickfix to keep old dates which may be different due to which time zone is used
				if (isset($oldContributors[$author]['First Commit'])) {
					$infos['First Commit'] = $oldContributors[$author]['First Commit'];
					if (isset($oldContributors[$author]['Number of Commits']) && $oldContributors[$author]['Number of Commits'] == $infos['Number of Commits']) {
						$infos['Last Commit'] = $oldContributors[$author]['Last Commit'];
					}
				}
				if (isset($infos['Number of Commits'])) {
					if (isset($oldContributors[$author]['Number of Commits'])) {
						$return['newCommits'] += ($infos['Number of Commits'] - $oldContributors[$author]['Number of Commits']);
					}
				}
			}
		} else {
			$return['newContributors']++;
		}
		$copyrights .= "\nNickname: $author";
		$orderedKeys = array('Name', 'First Commit', 'Last Commit', 'Number of Commits', 'SF Role');
		foreach ($orderedKeys as $k) {
			if (empty($infos[$k]) || ($k == 'Name' && $infos[$k] == $author)) {
				continue;
			}
			$copyrights .= "\n$k: " . $infos[$k];
		}
		$copyrights .= "\n";
	}

	return file_put_contents(COPYRIGHTS, $copyrights) ? $return : false;
}

/**
 * @return array|bool
 */
function parse_copyrights()
{
	if (! $copyrights = @file(COPYRIGHTS)) {
		return false;
	}

	$return = array();
	$curNickname = '';

	foreach ($copyrights as $line) {
		if (empty($line)) {
			continue;
		}
		if (substr($line, 0, 10) == 'Nickname: ') {
			$curNickname = rtrim(substr($line, 10));
			$return[$curNickname] = array();
		} elseif ($curNickname != '' && ($pos = strpos($line, ':')) !== false) {
			$return[$curNickname][substr($line, 0, $pos)] = rtrim(substr($line, $pos + 2));
		}
	}

	return $return;
}

/**
 * @param $path
 * @param $contributors
 * @param $minRevision
 * @param $maxRevision
 * @param int $step
 * @return mixed
 */
function get_contributors_data($path, &$contributors, $minRevision, $maxRevision, $step = 20000)
{
	global $nbCommiters;

	if (empty($contributors)) {
		get_contributors_sf_data($contributors);
		info(">> Retrieved members list from Sourceforge.");
	}

	$minByStep = max($maxRevision - $step, $minRevision);
	$lastLogRevision = $maxRevision;
	echo "\rRetrieving logs from revision $minByStep to $maxRevision ...\t\t\t";
	$logs = get_logs($path, $minByStep, $maxRevision);
	if (preg_match_all('/^r(\d+) \|\s([^\|]+)\s\|\s(\d+-\d+-\d+)\s.*\n\n(.*)\-+\n/Ums', $logs, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $logEntry) {
			$mycommits[$logEntry[1]] = array($logEntry[2],$logEntry[3]);
		}
		krsort($mycommits);

		foreach ($mycommits as $commitnum => $commitinfo) {
			if ($lastLogRevision > 0 && $commitnum != $lastLogRevision - 1 && $lastLogRevision != $maxRevision) {
				print "\nProblem with commit " . ($lastLogRevision - 1) . "\n (trying {$commitnum} after $lastLogRevision)";
				die;
			}

			$lastLogRevision = $commitnum;
			$author = strtolower($commitinfo[0]);

			// Remove empty author or authors like (no author), which may be translated depending on server locales
			if (empty($author) || $author{0} == '(') {
				continue;
			}

			if (!isset($contributors[$author])) {
				$contributors[$author] = array();
			}

			$contributors[$author]['Author'] = $commitinfo[0];
			$contributors[$author]['First Commit'] = $commitinfo[1];

			if (isset($contributors[$author]['Number of Commits'])) {
				$contributors[$author]['Number of Commits']++;
			} else {
				$contributors[$author]['Last Commit'] = $commitinfo[1];
				$nbCommiters++;
				$contributors[$author]['Number of Commits'] = 1;
			}
		}
	}

	if ($lastLogRevision > $minRevision) {
		get_contributors_data($path, $contributors, $minRevision, $lastLogRevision - 1, $step);
	}

	return $contributors;
}

/**
 * @param $contributors
 */
function get_contributors_sf_data(&$contributors)
{
	global $options;
	$matches = array();

	if (! function_exists('iconv')) {
		error("PHP 'iconv' function is not available on this system. Impossible to get SF.net data.");
	}

	$html = $options['http-proxy'] ? file_get_contents(SF_TW_MEMBERS_URL, 0, $options['http-proxy']) : file_get_contents(SF_TW_MEMBERS_URL);

	if (!empty($html) && preg_match('/(<table.*<\/\s*table>)/sim', $html, $matches)) {
		$usersInfo = array();
		if (preg_match_all('/<tr[^>]*>' . str_repeat('\s*<td[^>]*>(.*)<\/td>\s*', 3).'<\/\s*tr>/Usim', $matches[0], $usersInfo, PREG_SET_ORDER)) {
			foreach ($usersInfo as $k => $userInfo) {
				$userInfo = array_map('trim', array_map('strip_tags', $userInfo));
				$user = strtolower($userInfo['2']);
				if (empty($user)) {
					continue;
				}
				$contributors[$user] = array(
					'Name' => html_entity_decode(iconv("ISO-8859-15", "UTF-8", $userInfo['1']), ENT_COMPAT, 'UTF-8'),
					'SF Role' => $userInfo['3']
				);
			}
		}
	} else {
		error('Impossible to get SF.net users information. If you need to use a web proxy, try the --http-proxy option.');
		die;
	}
}

/**
 * @param $releaseVersion
 * @param $mainVersion
 * @return bool
 */
function update_readme_file($releaseVersion, $mainVersion)
{
	if (! is_readable(README) || ! is_writable(README)) {
		error('The README file "' . README . '" is not readable or writable.');
		die;
	}

	$year = gmdate('Y');
	$copyrights_file = COPYRIGHTS_FILENAME;
	$license_file = LICENSE_FILENAME;

	$majorVersion = substr($mainVersion, 0, strpos($mainVersion, '.'));
	$release_notes_url = 'http://doc.tiki.org/Tiki' . $majorVersion;
	// Changed from Tiki 12 to point to http://doc.tiki.org/Tiki12 instead of http://tiki.org/ReleaseNotes30

	$readme = <<<EOF
Tiki! The wiki with a lot of features!
Version $releaseVersion


DOCUMENTATION

* The documentation for $mainVersion version is ever evolving at http://doc.tiki.org.
  You're encouraged to contribute.

* It is highly recommended that you refer to the online documentation:
* http://doc.tiki.org/Installation for a setup guide

* Notes about this release are accessible from $release_notes_url
* Tikiwiki has an active IRC channel, #tikiwiki on irc.freenode.net

INSTALLATION

* There is a file INSTALL in this directory with notes on how to setup and
  configure Tiki. Again, see http://doc.tiki.org/Installation for the latest install help.

UPGRADES

* Read the online instructions if you want to upgrade your Tiki from a previous release http://doc.tiki.org/Upgrade

COPYRIGHT

Copyright (c) 2002-$year, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
Tiki was started under the name tikiwiki by Luis Argerich, Garland Foster, Eduardo Polidor, et. al. 
All Rights Reserved. See $copyrights_file for details and a complete list of authors.
Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See $license_file for details.

... Have fun!

Note to Tiki developers: update this text through release.php.
EOF;

	return (bool) file_put_contents(README, $readme);
}

function display_usage()
{
	echo "Usage: php doc/devtools/release.php [ Options ] <version-number> [ <subrelease> ]
Examples:
	php doc/devtools/release.php 2.0 preRC3
	php doc/devtools/release.php 2.0 RC3
	php doc/devtools/release.php 2.0

Options:
	--howto			: display the Tiki release HOWTO
	--help			: display this help
	--http-proxy=HOST:PORT	: use an http proxy to get copyright data on sourceforge
	--svn-mirror-uri=URI	: use another repository URI to update the copyrights file (to avoid retrieving data from sourceforge, which is usually slow)
	--no-commit		: do not commit any changes back to SVN
	--no-check-svn		: do not check if there is uncommited changes on the checkout used for the release
	--no-check-php		: do not check syntax of all PHP files
	--no-check-php-warnings	: do not display PHP warnings and notices during the PHP syntax check
	--no-check-smarty	: do not check syntax of all Smarty templates
	--no-first-update	: do not svn update the checkout used for the release as the first step
	--no-readme-update	: do not update the '" . README_FILENAME . "' file
	--no-lang-update	: do not update lang/*/language.php files
	--no-changelog-update	: do not update the '" . CHANGELOG_FILENAME . "' file
	--no-copyright-update	: do not update the '" . COPYRIGHTS_FILENAME . "' file
	--no-secdb		: do not update SecDB footprints
	--only-secdb		: only generate a secdb database
	--no-packaging		: do not build packages files
	--no-tagging		: do not tag the release on the remote svn repository
	--force-yes		: disable the interactive mode (same as replying 'y' to all steps)
	--debug-packaging	: run tikirelease.sh with the -x option.
Notes:
	Subreleases begining with 'pre' will not be tagged.
";
	die;
}

function display_howto()
{
	echo <<<EOS
--------------------------
   HOWTO release Tiki
--------------------------

Please see: https://dev.tiki.org/How+to+release

EOS;
	shell_exec('open ' . escapeshellarg('https://dev.tiki.org/How+to+release'));
	exit;
}
