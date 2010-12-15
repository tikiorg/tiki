<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// analyse_file_path groups files by type, e.g. library, etc.

// Usage:
// From TikiWiki root, run:
// php doc/devtools/securitycheck.php > securityreport.html
// visit securityreport.html (where your TikiWiki is)
//

if( isset( $_SERVER['REQUEST_METHOD'] ) ) die;

// Add the imported libraries located in lib/
$thirdpartyLibs = array(
	'\./lib/pear.*',
	'\./lib/smarty.*',
	'\./lib/adodb.*',
	'\./lib/debug.*',
	'\./lib/diff.*',
	'\./lib/pdflib.*',
	'\./lib/ckeditor/.*',
	'\./lib/graph-engine/.*',
	'\./lib/core/Zend.*',
	'\./lib/htmlparser/,*',
	'\./lib/htmlpurifier/,*',
	'\./lib/hawhaw.*',
	'\./lib/ical.*',
	'\./lib/images.*',
	'\./lib/feedcreator.*',
	'\./lib/sheet.*',
	'\./lib/Horde/Yaml.*',
	'\./lib/ajax/xajax/.*',
	'\./lib/core/DeclFilter.*',
	'\./lib/core/JitFilter.*',
	'\./lib/core/Multilingual.*',
        '\./lib/core/TikiFilter.*',
	'\./lib/core/WikiParser.*',
	'\./lib/core/test/.*',
	'\./lib/core/WikiParser.*',
	'\./lib.*', /* as per NKO 4:18 19-MAY-09 */
);

/* NOt in build
        '\./lib/core/test/.*',
	'\./db/convertscripts/.*',
	'\./doc/devtools/.*', 
	'\./db/local.php/.*', 
*/

/*
The following need to be added as features
FIX LATER
 ./tiki-login_openid.php

The following are DELIBERATELY PUBLIC.
 ./tiki-change_password.php
 ./tiki-cookie-jar.php
 ./tiki-error_simple.php
 ./tiki-information.php
 ./tiki-install.php
 ./tiki-jsplugin.php
 ./tiki-live_support_chat_frame.php
 ./tiki-login_scr.php


The following do actually have features, but the fix check checker 
needs to be changed to accept access->check_permissions() so that also that it loads tikisetup.php
 ./tiki-orphan_pages.php
 ./tiki-plugins.php
./tiki-switch_perspective.php

The following need to be refactored to a lib
 ./tiki-testGD.php

This file is just comments
 ./about.php

 
*/

$safePaths = array(
	'\./lib/wiki-plugins.*',
	'\./lib/wiki-plugins-dist.*',
	'\./lib/tree.*',
);

if( !file_exists( 'tiki-setup.php' ) )
	die( "Please run this script from tiki root.\n" );

include_once ('lib/setup/twversion.class.php');
$TWV = new TWVersion();

if (!$TWV->version)
     die( "Could not find version information.\n" );

$ver = explode( '.', $TWV->version );
$major = (count($ver) >= 1) ? $ver[0]:'?';
$minor = (count($ver) >= 2) ? $ver[1]: '?';
$revision = (count($ver) >= 3) ?  $ver[2]: '?';

function get_content( $filename )
{
	static $last, $content;

	if( $filename == $last )
		return $content;
	
	$content = file_get_contents( $last = $filename );

	return $content;
}

function feature_pattern( &$featureNameIndex ) // {{{
{
	global $major, $minor, $revision;
	$featureName = "((feature_\w+)|wiki_feature_3d|lang_use_db|allowRegister|validateUsers|cachepages)";
	$q = "[\"']";
	if( $major == 1 && $minor == 9 )
	{
		$featureNameIndex = array( 2, 7 );
		$tl = '\\$tikilib->get_preference';
		return "/(\\\${$featureName}\s*(!=|==)=?\s*$q(y|n)[\"'])|($tl\s*\(\s*$q{$featureName}$q\s*(,\s*{$q}n?$q)?\s*\)\s*(==|!=)=?\s*$q(y|n)$q)/";
	}
	elseif( ($major == 1 && $minor == 10) || $major >= 2 )
	{
		$featureNameIndex = 1;
		return "/\\\$prefs\s*\[$q(\w+)$q\]\s*(!=|==)=?\s*$q(y|n)$q/";
	}
} // }}}

function permission_pattern( &$permissionNameIndex ) // {{{
{
	global $major, $minor, $revision;
	$permissionNameIndex = 1;
	return "/\\$(tiki_p_\w+)\s*(!=|==)=?\s*[\"'](y|n)[\"']/";
} // }}}

function includeonly_pattern() // {{{
{
	return "/strpos\s*\(\s*\\\$_SERVER\s*\[\s*[\"']SCRIPT_NAME[\"']\s*\]\s*,\s*basename\s*\(\s*__FILE__\s*\)\s*\)\s*!==\s*(false|FALSE)/";
} // }}}

function includeonly_pattern3() // {{{
{
        return "/basename\s*\(\s*\\\$_SERVER\s*\[\s*[\"']SCRIPT_NAME[\"']\s*\]\s*\)\s*==\s*basename\s*\(\s*__FILE__\s*\)\s*\)/";
} // }}}


function includeonly_pattern2() // {{{
{
	return "/\\\$access\s*->\s*check_script\s*\(\s*\\\$_SERVER\s*\[\s*[\"']SCRIPT_NAME[\"']\s*\]\s*,\s*basename\s*\(\s*__FILE__\s*\)\s*\)/s";
	//return "/\\\$access\s*\->\s*check_script\s*\(\s*\\\$_SERVER\s*\[\s*[\"']SCRIPT_NAME[\"']\s*\]\s*,\s*basename\s*\(\s*__FILE__\s*\)\s*\)/";
} // }}}

function noweb_pattern() // {{{
{
	return "/if\s*\(\s*isset\s*\(\s*\\\$_SERVER\[\s*[\"']REQUEST_METHOD[\"']\]\s*\)\s*\)\s*die/";
} // }}}

function tikisetup_pattern() // {{{
{
	return "/(require(_once)?|include(_once)?)\s*\(?\s*['\"]tiki-setup.php['\"]/";
} // }}}

function scanfiles( $folder, &$files ) // {{{
{
  global $filesHash;
	$handle = opendir( $folder );
	if( !$handle )
	{
		printf( "Could not open folder: %s\n", $folder );
		return;
	}

	while( false !== $file = readdir( $handle ) )
	{
		// Skip self and parent
		if( $file{0} == '.' || $file{0} == '..' )
			continue;

		$path = "$folder/$file";

		if( is_dir( $path ) )
			scanfiles( $path, $files );
		else {
		  $analysis = analyse_file_path( $path );
		  $files[] = $analysis;
		  $filesHash[$path] = $analysis;
		}
	}
} // }}}

// TODO This is an inefficient function, but more flexible than in_array
function regex_match ( $path, $regex_possibles ) {
  //  print "Checking $path in ".join($regex_possibles, ",")."\n";

  foreach ($regex_possibles as $possible)  {
    //    print "Matching $path against $possible\n";
    if (preg_match( '%'.$possible.'%', $path)) {
      //print "Matches $possible\n\n";
      return true;
    }
  }
  return false;
}

function analyse_file_path( $path ) // {{{
{
	global $thirdpartyLibs;
	global $safePaths;
	$type = 'unknown';
	$name = basename( $path );
	if( strpos( $name, '.' ) !== false )
		$extension = substr( $name, strrpos( $name, '.' ) + 1 );
	else
		$extension = false;

	if( strpos( $path, '/CVS/' ) !== false )
		$type = 'cvs';
	elseif( strpos( $path, './templates_c/' ) === 0 )
		$type = 'cache';
	elseif( $extension == 'php' || $extension == 'inc' )
	{
		if( $name == 'index.php' )
			$type = 'blocker';
		elseif( $name == 'language.php' )
			$type = 'lang';
		elseif( strpos( $path, './lib/wiki-plugins' ) === 0 )
			$type = 'wikiplugin';
		elseif( strpos( $path, './lib/' ) === 0 )
		{
			if( regex_match( $path, $thirdpartyLibs ) )
				$type = '3dparty';
			elseif( regex_match( $path, $safePaths ) )
				$type = 'safe';
			else
				$type = 'lib';
		}
		elseif( strpos( $path, './tiki-' ) === 0 )
			$type = 'public';
		elseif( strpos( $path, './modules/' ) === 0 )
			$type = 'module';
		else
			$type = "include";
	}
	elseif( in_array( $extension, array( 'txt', 'png', 'jpg', 'html', 'css', 'sql', 'gif', 'afm', 'js' ) ) )
		$type = 'static';
	elseif( strpos( $path, './doc/devtools/' ) === 0 )
		$type = 'script';
	elseif( strpos( $path, './files/' ) === 0 )
		$type = 'user';
	elseif( $extension == 'sh' )
		$type = 'system';
	elseif( strpos( $path, '_htaccess' ) !== false )
		$type = 'system';
	elseif( in_array( basename( $path ), array( 'INSTALL', 'README' ) ) )
		$type = 'doc';
	elseif( $extension == 'tpl' )
		$type = 'template';

	return array(
		'filename' => basename( $path ),
		'path' => $path,
		'type' => $type,
		'extension' => $extension,
		'features' => array(),
		'permissions' => array(),
		'includeonce' => false,
		'noweb' => false,
		'tikisetup' => false,
		'unsafeextract' => false,
	);
} // }}}

function perform_feature_check( &$file ) // {{{
{
  global $features;
        $index = array();
	$feature_pattern = feature_pattern( $index );
	$index = (array) $index;
	$path =  $file['path'] ;

	preg_match_all( $feature_pattern, get_content($path), $parts );

	$featuresInFile = array();
	foreach( $index as $i )
		$featuresInFile = array_merge( $features, $parts[$i] );

	$featuresInFile = array_merge( $featuresInFile, access_check_call( $path, 'check_feature' ) );
	$featuresInFile = array_unique( $featuresInFile );
	$file['features'] = $featuresInFile;
	//	var_dump($featuresInFile);
	/*
	 This data structure seems to be typical, and very confusing.
	 An array of 3, with the zeroth element being a named element whose value is an array of one element.
	 other elements being named, not numbered

	 1array(3) { 
	 2  ["feature_directory"]=> 
	 3  array(1) { 
	 4    [0]=> 
	 5    string(28) "./tiki-directory_ranking.php" 
	 6  } 
	 7  [0]=> 
	 8  string(18) "feature_html_pages" 
	 9  [1]=> 
	 10  string(21) "feature_theme_control" 
	 11}
	*/
	/*
	// store, for each feature, which files are involved
	foreach ( $featuresInFile as $feature) {
	  if (is_string($feature)) {
	    if (preg_match('/feature/', $feature)) {
	      // SMELL sure to be a better way to do this.
	      //print "Listing as feature $feature\n";
	      $featuresListed = (array) $features[$feature];
	      array_push($featuresListed, $path);
	      $features[$feature] = $featuresListed;
	    }
	  // TODO SMELL: this regex should not be necessary, it should only contain features at this point.
	  // SMELL: it will also miss some vital elements.
	  }
	}
	*/
	return $featuresInFile;
} // }}}

function perform_permission_check( &$file ) // {{{
{
	$index = 0;
	
	$permission_pattern = permission_pattern( $index );

	preg_match_all( $permission_pattern, get_content( $file['path'] ), $parts );

	$permissions = array_unique( array_merge(
		access_check_call( $file['path'], 'check_permission' ),
		permission_check_accessors( $file['path'] ),
		$parts[$index]
	) );

	$file['permissions'] = $permissions;
} // }}}

function perform_includeonly_check( &$file ) // {{{
{
	$index = 0;
	$pattern = includeonly_pattern($index);

	preg_match_all( $pattern, get_content($file['path']), $parts );

        $pattern = includeonly_pattern2($index);

        preg_match_all( $pattern, get_content($file['path']), $parts2 );

	$pattern = includeonly_pattern3($index);

        preg_match_all( $pattern, get_content($file['path']), $parts3 );

	$file['includeonly'] = count( $parts[0] ) > 0 || count( $parts2[0] ) > 0 || count( $parts3[0] ) > 0;
} // }}}

function perform_noweb_check( &$file ) // {{{
{
	$index = 0;
	$pattern = noweb_pattern($index);

	preg_match_all( $pattern, get_content($file['path']), $parts );

	$file['noweb'] = count( $parts[0] ) > 0;
} // }}}

function perform_tikisetup_check( &$file ) // {{{
{
	$index = 0;

	$pattern = tikisetup_pattern($index);

	preg_match_all( $pattern, get_content($file['path']), $parts );

	$file['tikisetup'] = count( $parts[0] ) > 0;
} // }}}

function perform_extract_skip_check( &$file ) // {{{
{
	$pattern = "/extract\s*\([^\)]+\)/";

	preg_match_all( $pattern, get_content($file['path']), $parts );

	foreach( $parts[0] as $extract )
		if( strpos( $extract, 'EXTR_SKIP' ) === false )
			$file['unsafeextract'] = true;

} // }}}

function access_check_call( $file, $type ) // {{{
{
	$content = get_content( $file );
	$tokens = token_get_all( $content );

	$checks = array();

	foreach( $tokens as $key => $token ) {
		if( is_array( $token ) ) {
			if( $token[0] == T_VARIABLE && $token[1] == '$access' ) {
				if( $tokens[$key+1][0] == T_OBJECT_OPERATOR
					&& $tokens[$key+2][0] == T_STRING && $tokens[$key+2][1] == $type ) {
					$checks = array_merge( $checks, access_checks( $tokens, $key + 2 ) );
				}
			}
		}
	}

	return $checks;
} // }}}

function access_checks( $tokens, $from ) // {{{
{
	$end = count($tokens);

	$features = array();

	for( $i = $from; $end > $i; ++$i ) {
		$token = $tokens[$i];

		if( is_string( $token ) && $token == ';' ) {
			break;
		}

		if( is_array( $token ) && $token[0] == T_CONSTANT_ENCAPSED_STRING ) {
			$features[] = trim( $token[1], "\"'" );
		}
	}

	return $features;
} // }}}

function permission_check_accessors( $file ) // {{{
{
	$tokens = token_get_all( get_content( $file ) );

	$perms = array();

	foreach( $tokens as $key => $token ) {
		if( is_array( $token ) && ( $token[0] == T_IF || $token[0] == T_ELSEIF ) ) {
			$subset = tokenizer_get_subset( $tokens, $key );
			$perms = array_merge( $perms, permission_check_condition( $subset ) );
		}
	}
	
	return $perms;
} // }}}

function tokenizer_get_subset( $tokens, $from ) // {{{
{
	$out = array();

	$started = false;
	$count = 0;
	$end = count($tokens);

	for( $i = $from; $end > $i && ( ! $started || $count > 0 ); ++$i ) {
		$t = $tokens[$i];

		if( is_string( $t ) ) {
			if( $t == '(' ) {
				$started = true;
				$count++;
			} elseif( $t == ')' ) {
				$count--;
			}
		}

		$out[] = $t;
	}

	return $out;
} // }}}

function permission_check_condition( $tokens ) // {{{
{
	$permissions = array();

	foreach( $tokens as $i => $t ) {
		if( $t[0] == T_VARIABLE ) {
			if( 'perms' == substr( $t[1], -5 ) ) {
				if( $tokens[$i+1][0] == T_OBJECT_OPERATOR && $tokens[$i+2][0] == T_STRING ) {
					$perm = $tokens[$i+2][1];

					if( 'tiki_p_' != substr( $perm, 0, 7 ) ) {
						$perm = 'tiki_p_' . $perm;
					}

					$permissions[] = $perm;
				}
			}
		}
	}

	return $permissions;
} // }}}

/* Build Files structures */
// a hash of filenames, each element is a hash of attributes of that file
$filesHash = array(); 

// a hash of features, each element is a hash of filenames that use that feature
$features = array();

// note: the files[0..N] is intended to be replaced by the above hash.
$files = array();

// build these two files structures
scanfiles( '.', $files );
error_reporting(E_ALL);

/* Iterate each file, and perform checks */
$unsafe = array();
foreach( $files as $key=>$dummy )
{
	$file = &$files[$key];

	switch( $file['type'] )
	{
	case 'wikiplugin':
		perform_extract_skip_check( $file );

		if( $file['unsafeextract'] ) 
			$unsafe[] = $file;

					break;
	case 'public':
	case 'include':
	case 'script':
	case 'module':
	case 'lib':
	case '3rdparty':
		perform_feature_check( $file );
		perform_permission_check( $file );
		perform_includeonly_check( $file );
		perform_noweb_check( $file );
		perform_tikisetup_check( $file );

		if( ! $file['noweb'] && ! $file['includeonly'] && ! count( $file['features'] ) && ! count( $file['permissions'] ) ) 
			$unsafe[] = $file;

					break;
	}
}

function sort_cb( $a, $b )
{
	return strcmp( $a['path'], $b['path'] );
}

usort( $files, 'sort_cb' );
usort( $unsafe, 'sort_cb' );


?>
<html>
<head><title>Security Static Checker Output</title></head>
<body>
<p>TikiWiki Version: <?php echo "$major.$minor.$revision" ?></p>
<p>Audit Date: <?php echo date( 'Y-m-d H:i:s' ) ?></p>
<h1>Potentially unsafe files</h1>
<p>
To be safe, files must have either an include only check, block web access, have a feature check or have a permission check.
</p>
<ol>
	<?php foreach( $unsafe as $unsafeUrlAndFile ): 
	  $pathname = $unsafeUrlAndFile['path'];
$url = substr( $unsafeUrlAndFile['path'], 2 );
$fileRecord = $filesHash[$pathname];
$fileType = $fileRecord['type'];
	  ?>
	<li> <?php echo $fileType; ?> <a href="<?php echo htmlentities( $url ) ?>"><?php echo htmlentities( $pathname ) ?></a></li>
	<?php endforeach; ?>
</ol>
<h1>All files</h1>
<table border="1">
	<thead>
		<tr style="font-size:x-small">
			<th>File</th>
			<th>Include only check</th>
			<th>Not web accessible</th>
			<th>Includes tiki-setup</th>
			<th>Unsafe extract</th>
			<th>Permissions checked</th>
			<th>Features checked</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $files as $file ) if( in_array( $file['type'], array(
			'script', 'module', 'include', 'public', 'lib', '3rdparty', 'wikiplugin'
		) ) ): ?>
		<tr>
			<td><a href="<?php echo htmlentities( substr( $file['path'], 2 ) ) ?>"><?php echo htmlentities( $file['path'] ) ?></a></td>
			<td><?php if( isset($file['includeonly']) && $file['includeonly'] ) echo 'X' ?></td>
			<td><?php if( $file['noweb'] ) echo 'X' ?></td>
			<td><?php if( $file['tikisetup'] ) echo 'X' ?></td>
			<td><?php if( $file['unsafeextract'] ) echo 'X' ?></td>
			<td>
				<?php foreach( $file['permissions'] as $perm ): ?>
				<div><?php echo $perm ?></div>
				<?php endforeach; ?>
			</td>
			<td>
				<?php foreach( $file['features'] as $feature ): ?>
				<div><?php echo $feature ?></div>
				<?php endforeach; ?>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>

	<?php 
		foreach($features as $featureKey => $featureValue) {
			print "$featureKey :\n";
			foreach ($featureValue as $file) {
				print "<li>$file</li>";
			}
			print "<br/><br/>\n";
		} ?>

</body>
</html>

<!-- If you see this in your terminal window it's because you didn't read the usage. See the start of the file. -->
