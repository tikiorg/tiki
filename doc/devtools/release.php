<?php

define( 'TOOLS', dirname(__FILE__) );
define( 'ROOT', realpath( TOOLS . '/../..' ) );

require_once TOOLS . '/svntools.php';

if ( ! ( $options = get_options() ) || $options['help'] )
	display_usage();

if ( ! $options['no-check-svn'] && has_uncommited_changes('.') )
	error("Uncommited changes exist in the working folder.\n");

list( $script, $version, $subrelease ) = $_SERVER['argv'];

if ( ! preg_match("/^\d+\.\d+$/", $version) )
	error("Version number should be in X.X format.\n");

$isPre = strpos($subrelease, 'pre') === 0;
if ( $isPre ) {
	$subrelease = substr($subrelease, 3);
	$pre = 'pre';
} else {
	$pre = '';
}
$mainversion = $version{0} . '.0';

echo color("\nTiki release process started for version '$version.$subrelease'\n", 'cyan');
if ( $isPre )
	echo color("The script is running in 'pre-release' mode, which means that no subversion tag will be created.\n", 'yellow');

if ( important_step('Update working copy to the last revision') ) {
	update_working_copy('.');

	if ( has_uncommited_changes('.') )
		error("Uncommited changes exist in the working folder.\n");

	$revision = (int) get_info('.')->entry->commit['revision'];
	info(">> Checkout updated to revision $revision.");
}

if ( empty($subrelease) ) {
	$branch = "branches/$mainversion";
	$tag = "tags/$version";
	$packageVersion = $version;
	if ( ! empty($pre) )
		$packageVersion .= ".$pre";
	$secdbVersion = $version;
} else {
	$branch = "branches/$mainversion";
	$tag = "tags/$version$subrelease";
	$packageVersion = "$version.$pre$subrelease";
	$secdbVersion = "$version$subrelease";
}

if ( ! $options['no-lang-update'] && important_step("Update language files") ) {
	passthru("php get_strings.php quiet");
	$removeFiles = glob('lang/*/old.php');
	$removeFiles[] = 'temp/permstrings.tpl';
	$removeFiles[] = 'temp/prefnames.tpl';
	foreach ( $removeFiles as $rf ) unlink($rf);
	unset($removeFiles);
	info('>> Language files updated and temporary files removed.');
	if ( important_step("Commit updated language files") )
		commit("[REL] Update language.php files for $secdbVersion");
}

if ( ! $options['no-check-php'] && important_step("Check syntax of all PHP files") ) {
	$error_msg = '';
	$dir = '.';
	check_php_syntax($dir, $error_msg) or error($error_msg);
	info('>> Current PHP code successfully passed the syntax check.');
}

if ( ! $options['no-secdb'] && important_step("Generate SecDB file 'db/tiki-secdb_{$version}_mysql.sql'") ) {
	write_secdb( ROOT . "/db/tiki-secdb_{$version}_mysql.sql", ROOT, $secdbVersion );
	if ( important_step("Commit SecDB file") )
		commit("[REL] SecDB for $secdbVersion");
}

if ( $isPre ) {
	if ( important_step("Build packages files (based on the branch)") ) {
		build_packages($packageVersion, $branch);
		echo color("\nMake sure these tarballs are tested by at least 3 different people.\n\n", 'cyan');
	} else echo color("This was the last step.\n", 'cyan');
} else {
	$fb = full( $branch );
	$ft = full( $tag );
	$revision = (int) get_info( ROOT )->entry->commit['revision'];

	if ( important_step("Tag release using branch '$branch' at revision $revision") ) {
		`svn copy $fb -r$revision $ft -m "[REL] Tagging release"`;
		info('>> Tag created.');
	}

	if ( important_step("Build packages files (based on the '$tag' tag)") ) {
		build_packages($packageVersion, $tag);
		echo color("\nUpload the files on SourceForge.\nInstructions can be found here: http://tinyurl.com/59uubv\n\n", 'cyan');
	} else echo color("This was the last step.\n", 'cyan');
}

// Helper functions

function write_secdb( $file, $root, $version ) {
	$fp = fopen( $file, 'w+' );
	fwrite( $fp, "DELETE FROM `tiki_secdb` WHERE `tiki_version` = '$version';\n\n" );
	md5_check_dir( $root, $root, $fp, $version );
	fclose( $fp );

	if ( $file_exists = file_exists($file) ) {
		info(">> Existing SecDB file '$file' has been updated.");
		`svn add $file 2> /dev/null`;
	} else {
		info(">> SecDB file '$file' has been created.");
		`svn add $file`;
	}
}

function md5_check_dir($root,$dir,$fp,$version) { // save all files in $result
	$d = dir($dir);
	while (false !== ($e = $d->read())) {
		$entry = $dir . '/' . $e;
		if ( is_dir($entry) ) {
			// do not descend and no CVS/Subversion files
			if ( $e != '..' && $e != '.' && $e != 'CVS' && $e != '.svn' && $entry!='./templates_c') {
				md5_check_dir($root, $entry, $fp, $version);
			}
		} else {
			if ( substr($e, -4, 4) == ".php" && realpath( $entry ) != __FILE__ && $entry != './db/local.php' ) {
				$file = mysql_real_escape_string( '.' . substr( $entry, strlen( $root ) ) );
				$hash = md5_file($entry);
				fwrite( $fp, "INSERT INTO `tiki_secdb` (`md5_value`, `filename`, `tiki_version`, `severity`) VALUES('$hash', '$file', '$version', 0);\n" );
			}
		}
	}
	$d->close();
}

function build_packages($releaseVersion, $svnRelativePath) {
	$script = TOOLS . '/tikirelease.sh';
	`bash $script $releaseVersion $svnRelativePath`;
	info(">> Packages files have been built in ~/tikipack/$releaseVersion :\n");
	passthru( "ls ~/tikipack/$releaseVersion" );
}

function check_php_syntax(&$dir, &$error_msg) {
	print "."; usleep (100000);
	$d = dir($dir);

	while (false !== ($e = $d->read())) {
		$entry = $dir.'/'.$e;
		if(is_dir($entry)) {
			if($e != '..' && $e != '.' && $e != 'CVS' && $e != '.svn' && $entry!='./templates_c') { // do not descend and no CVS/Subversion files
				if ( ! check_php_syntax($entry, $error_msg) ) return false;
			}
		} else {
			if ( substr($e,-4,4) == ".php" && realpath( $entry ) != __FILE__ ) {
				$return_var = 0;
				$output = null;
				exec("php -l $entry", $output, $return_var);
				if ( $return_var !== 0 ) {
					$error_msg = "\nParsing error in '$entry' ($return_var)\n";
					return false;
				}
			}
		}
	}

	$d->close();
	unset($return_var, $output, $entry, $d);

	return true;
}

function get_options() {
	if ( $_SERVER['argc'] <= 1 ) return false;

	$argv = array();
	$options = array(
		'help' => false,
		'no-check-svn' => false,
		'no-check-php' => false,
		'no-lang-update' => false,
		'no-secdb' => false,
		'force-yes' => false
	);

	foreach ( $_SERVER['argv'] as $arg ) {
		if ( substr($arg, 0, 2) == '--' ) {
			if ( ( $opt = substr($arg, 2) ) != '' && isset($options[$opt]) ) {
				$options[substr($arg, 2)] = true;
			} else {
				error("Unknown option $arg. Try using --help option.\n");
			}
		} else {
			$argv[] = $arg;
		}
	}
	$_SERVER['argv'] = $argv;
	unset($argv);

	if ( $_SERVER['argc'] == 2 )
		$_SERVER['argv'][] = '';

	return $options;
}

function important_step($msg, $increment_step = true) {
	global $options;
	static $step = 0;
	if ( $increment_step ) $step++;

	if ( $options['force-yes'] ) {
		important("\n$step) $msg...");
		return true;
	} else {
		important("\n$step) $msg?");
		echo "[Y/n/q] ";
		$f=popen("read; echo \$REPLY","r");
		$c=fgets($f,100);
		pclose($f);
		switch ( strtolower($c) ) {
			case 'y': case '':
				return true;
				break;
			case 'n':
				info(">> Skipping step $step.");
				return false;
				break;
			case 'q':
				die;
				break;
			default:
				info(">> Unknown answer '$c'. You have to type 'y' (Yes), 'n' (No) or 'q' (Quit) and press Enter.");
				return important_step($msg, false);
		}
	}
}

function display_usage() {
	die( "Usage: php doc/devtools/release.php [ Options ] <version-number> [ <subrelease> ]
Examples:
	php doc/devtools/release.php 2.0 preRC3
	php doc/devtools/release.php 2.0 RC3
	php doc/devtools/release.php 2.0

Options:
	--help			: display this help
	--no-check-svn		: do not check if there is uncommited changes on the checkout used for the release
	--no-check-php		: do not check syntax of all PHP files
	--no-lang-update	: do not update lang/*/language.php files
	--no-secdb		: do not update SecDB footprints
	--force-yes		: disable the interactive mode (same as replying 'y' to all steps)

Notes:
	Subreleases begining with 'pre' will not be tagged.
" );
}

