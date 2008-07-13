<?php


// Usage:
// From TikiWiki root, run:
// php doc/devtools/securitycheck.php > securityreport.html
// visit securityreport.html (where your TikiWiki is)
//


if( isset( $_SERVER['REQUEST_METHOD'] ) ) die;

// Add the imported libraries located in lib/
$thirdpartyLibs = array(
	'pear',
	'phplayers',
	'jgraphpad',
	'smarty',
	'adodb',
	'pdflib',
);

$safePaths = array(
	'wiki-plugins',
	'wiki-plugins-dist',
	'tree',
	'phplayers_tiki',
);

if( !file_exists( 'tiki-setup.php' ) )
	die( "Please run this script from tiki root.\n" );

include_once ('lib/setup/twversion.class.php');
$TWV = new TWVersion();

if (!$TWV->version)
     die( "Could not find version information.\n" );

list( $major, $minor, $revision ) = explode( '.', $TWV->version );

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
	$featureName = "((feature_\w+)|wiki_feature_3d|lang_use_db|allowRegister|validateUsers)";
	$q = "[\"']";
	if( $major == 1 && $minor == 9 )
	{
		$featureNameIndex = array( 2, 7 );
		$tl = '\\$tikilib->get_preference';
		return "/(\\\${$featureName}\s*(!=|==)=?\s*$q(y|n)[\"'])|($tl\s*\(\s*$q{$featureName}$q\s*(,\s*{$q}n?$q)?\s*\)\s*(==|!=)=?\s*$q(y|n)$q)/";
	}
	elseif( $major == 1 && $minor == 10 )
	{
		$featureNameIndex = 1;
		return "/\\\$prefs\s*\[$q$featureName$q\]\s*(!=|==)=?\s*$q(y|n)$q/";
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

function includeonly_pattern2() // {{{
{
	return "/\\\$access\s*->\s*check_script\s*\\(\s*\\\$_SERVER\s*\\[\s*[\"']SCRIPT_NAME[\"']\s*\\]\s*,\s*basename\s*\\(\s*__FILE__\s*\\)\s*\\)/";
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
		else
			$files[] = analyse_file_path( $path );
	}
} // }}}

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
		elseif( strpos( $path, './lib/' ) === 0 )
		{
			$parts = explode( '/', $path );
			if( in_array( $parts[2], $thirdpartyLibs ) )
				$type = '3dparty';
			elseif( in_array( $parts[2], $safePaths ) )
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
	);
} // }}}

function perform_feature_check( &$file ) // {{{
{
	$index = array();
	$feature_pattern = feature_pattern( $index );
	$index = (array) $index;

	preg_match_all( $feature_pattern, get_content( $file['path'] ), $parts );

	$features = array();
	foreach( $index as $i )
		$features = array_merge( $features, $parts[$i] );

	$features = array_unique( $features );
	$file['features'] = $features;
} // }}}

function perform_permission_check( &$file ) // {{{
{
	$index = 0;
	$permission_pattern = permission_pattern( $index );

	preg_match_all( $permission_pattern, get_content( $file['path'] ), $parts );

	$permissions = array_unique( $parts[$index] );
	$file['permissions'] = $permissions;
} // }}}

function perform_includeonly_check( &$file ) // {{{
{
	$pattern = includeonly_pattern($index);

	preg_match_all( $pattern, get_content($file['path']), $parts );

	$pattern = includeonly_pattern2($index);

	preg_match_all( $pattern, get_content($file['path']), $parts2 );

	$file['includeonly'] = count( $parts[0] ) > 0 || count( $parts2[0] ) > 0;
} // }}}

function perform_noweb_check( &$file ) // {{{
{
	$pattern = noweb_pattern($index);

	preg_match_all( $pattern, get_content($file['path']), $parts );

	$file['noweb'] = count( $parts[0] ) > 0;
} // }}}

function perform_tikisetup_check( &$file ) // {{{
{
	$pattern = tikisetup_pattern($index);

	preg_match_all( $pattern, get_content($file['path']), $parts );

	$file['tikisetup'] = count( $parts[0] ) > 0;
} // }}}

$files = array();
scanfiles( '.', $files );

$unsafe = array();
foreach( $files as $key=>$dummy )
{
	$file = &$files[$key];

	switch( $file['type'] )
	{
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
	<?php foreach( $unsafe as $file ): ?>
	<li><a href="<?php echo htmlentities( substr( $file['path'], 2 ) ) ?>"><?php echo htmlentities( $file['path'] ) ?></a></li>
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
			<th>Permissions checked</th>
			<th>Features checked</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $files as $file ) if( in_array( $file['type'], array(
			'script', 'module', 'include', 'public', 'lib', '3rdparty'
		) ) ): ?>
		<tr>
			<td><a href="<?php echo htmlentities( substr( $file['path'], 2 ) ) ?>"><?php echo htmlentities( $file['path'] ) ?></a></td>
			<td><?php if( $file['includeonly'] ) echo 'X' ?></td>
			<td><?php if( $file['noweb'] ) echo 'X' ?></td>
			<td><?php if( $file['tikisetup'] ) echo 'X' ?></td>
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
</body>
</html>
