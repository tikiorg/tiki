<?php

define( 'TOOLS', dirname(__FILE__) );
define( 'ROOT', realpath( TOOLS . '/../..' ) );

require_once TOOLS . '/svntools.php';

if( $_SERVER['argc'] <= 1 )
	die( "Usage: php doc/devtools/release.php <version-number> [ <subrelease> ]
Examples:
	php doc/devtoos/release.php 2.0 preRC3
	php doc/devtoos/release.php 2.0 RC3
	php doc/devtoos/release.php 2.0

Notes:
	Subreleases begining with pre will not be tagged.
" );

if( has_uncommited_changes( '.' ) )
	die( "Uncommited changes exist in the working folder.\n" );

update_working_copy( '.' );

if( $_SERVER['argc'] == 2 )
	$_SERVER['argv'][] = '';

list( $script, $version, $subrelease ) = $_SERVER['argv'];

if( ! preg_match( "/^\d+\.\d+$/", $version ) )
	die( "Version number should be in X.X format.\n" );

$isPre = strpos( $subrelease, 'pre' ) === 0;

if( $isPre )
{
	$subrelease = substr( $subrelease, 3 );
	$pre = 'pre';
}
else
	$pre = '';

$mainversion = $version{0} . '.0';

if( empty( $subrelease ) )
{
	$branch = "branches/$mainversion";
	$tag = "tags/$version";
	$packageVersion = $version;
	if( ! empty( $pre ) )
		$packageVersion .= ".$pre";
	$secdbVersion = $version;

}
else
{
	$branch = "branches/$mainversion";
	$tag = "tags/$version$subrelease";
	$packageVersion = "$version.$pre$subrelease";
	$secdbVersion = "$version$subrelease";
}

write_secdb( ROOT . "/db/tiki-secdb_{$version}_mysql.sql", ROOT, $secdbVersion );
`svn ci -m "[REL] SecDB for $secdbVersion"`;

$script = TOOLS . '/tikirelease.sh';
if( $isPre )
{
	`bash $script $packageVersion $branch`;

	echo "~/tikipack/$packageVersion :\n";
	passthru( "ls ~/tikipack/$packageVersion" );
	echo "Make sure these tarballs are tested by at least 3 different people.\n";
}
else
{
	$fb = full( $branch );
	$ft = full( $tag );

	$revision = (int) get_info( ROOT )->entry->commit['revision'];

	`svn copy $fb -r$revision $ft -m "[REL] Tagging release"`;

	`bash $script $packageVersion $tag`;

	echo "~/tikipack/$packageVersion :\n";
	passthru( "ls ~/tikipack/$packageVersion" );

	echo "Upload the files on SourceForge.\nInstructions: http://tinyurl.com/59uubv\n";
}

// Helper functions

function write_secdb( $file, $root, $version )
{
	$fp = fopen( $file, 'w+' );

	fwrite( $fp, "DELETE FROM `tiki_secdb` WHERE `tiki_version` = '$version';\n\n" );

	md5_check_dir( $root, $root, $fp, $version );

	fclose( $fp );
	`svn add $file`;
}

function md5_check_dir($root,$dir,$fp,$version) { // save all files in $result
  $d=dir($dir);
  while (false !== ($e = $d->read())) {
    $entry=$dir.'/'.$e;
    if(is_dir($entry)) {
      if($e != '..' && $e != '.' && $e != 'CVS' && $e != '.svn' && $entry!='./templates_c') { // do not descend and no CVS files
        md5_check_dir($root,$entry,$fp,$version);
      }
    } else {
       if(substr($e,-4,4)==".php" && realpath( $entry ) != __FILE__ && $entry!='./db/local.php') {
         // echo "creating sum of $entry <br />\n";
         $file = mysql_real_escape_string( '.' . substr( $entry, strlen( $root ) ) );
		 $hash = md5_file($entry);

		 fwrite( $fp, "INSERT INTO `tiki_secdb` (`md5_value`, `filename`, `tiki_version`, `severity`) VALUES('$hash', '$file', '$version', 0);\n" );
       }
    }
  }
  $d->close();
}

?>
