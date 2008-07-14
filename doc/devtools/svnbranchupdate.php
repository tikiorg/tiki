<?php
define( 'TIKISVN', 'https://tikiwiki.svn.sourceforge.net/svnroot/tikiwiki' );

function full( $relative ) { return TIKISVN . "/$relative"; }
function short( $full ) { return substr( $full, strlen(TIKISVN) + 1 ); };

function color( $string, $color )
{
	$avail = array(
		'red' => 31,
		'green' => 32,
		'yellow' => 33,
		'cyan' => 36,
	);

	if( !isset($avail[$color]) )
		return $string;

	return "\033[{$avail[$color]}m$string\033[0m";
}
function error( $message ) { die( color( $message, 'red' ) . "\n" ); }
function info( $message ) { echo $message . "\n"; }
function important( $message ) { echo color($message, 'green') . "\n"; }

function is_valid_merge_destination( $url )
{
	return $url == full( 'trunk' );
}

function is_valid_merge_source( $destination, $source )
{
	return $source == full( 'branches/2.0' );
}

function update_working_copy( $localPath )
{
	$localPath = escapeshellarg( $localPath );
	`svn up $localPath`;
}

function has_uncommited_changes( $localPath )
{
	$localPath = escapeshellarg( $localPath );
	
	$dom = new DOMDocument;
	$dom->loadXML( `svn status --xml $localPath` );
	
	$xp = new DOMXPath( $dom );
	$count = $xp->query( "/status/target/entry/wc-status[@item = 'added' or @item = 'conflicted' or @item = 'deleted' or @item = 'modified' or @item = 'replaced']" );

	return $count->length > 0;
}

function get_conflicts( $localPath )
{
	$localPath = escapeshellarg( $localPath );
	
	$dom = new DOMDocument;
	$dom->loadXML( `svn status --xml $localPath` );
	
	$xp = new DOMXPath( $dom );
	$list = $xp->query( "/status/target/entry/wc-status[@item = 'conflicted']" );

	return $list;
}

function find_last_merge( $localPath, $source )
{
	$short = preg_quote( short( $source ), '/' );
	$pattern = "/^\\[MRG\\].*$short\s+\d+\s+to\s+(\d+)/";

	$descriptorspec = array(
		0 => array( 'pipe', 'r' ),
		1 => array( 'pipe', 'w' ),
	);

	$process = proc_open( "svn log --stop-on-copy | grep ^.MRG", $descriptorspec, $pipes, $localPath );
	$rev = 0;

	if( is_resource( $process ) )
	{
		$fp = $pipes[1];

		while( ! feof( $fp ) )
		{
			$line = fread( $fp, 1024 );

			if( preg_match( $pattern, $line, $parts ) )
			{
				$rev = (int) $parts[1];
				break;
			}
		}

		fclose( $fp );
		proc_close( $process );
	}

	return $rev;
}

function merge( $localPath, $source, $from, $to )
{
	$source = escapeshellarg( $source );
	$from = (int) $from;
	$to = (int) $to;
	passthru( "svn merge $source -r$from:$to" );

	$short = short( $source );
	$message = "[MRG] Automatic merge, $short $from to $to";
	file_put_contents( 'svn-commit.tmp', $message );
}

// Perform basic checks
info( "Verifying..." );

if( ! isset( $_SERVER['argc'] ) || $_SERVER['argc'] != 2 )
	error( "Missing argument. Expecting branch to merge as argument." );

$local = simplexml_load_string( `svn info --xml .` );

if( ! isset( $local->entry ) )
	error( "Local copy not found." );
if( ! is_valid_merge_destination( $local->entry->url ) )
	error( "This script is likely not to be appropriate for this working copy. This script can be used in:\n\ttrunk" );

$source = full( $_SERVER['argv'][1] );

if( ! is_valid_merge_source( $local->entry->url, $source ) )
	error( "The provided source cannot be used to update this working copy." );

if( has_uncommited_changes( '.' ) )
	error( "Working copy has uncommited changes. Revert or commit them before merging a branch." );

// Proceed to update
info( "Updating..." );
update_working_copy( '.' );

$conflicts = get_conflicts( '.' );
if( $conflicts->length > 0 )
{
	$message = "Conflicts occured during the local copy update before merging. Fix the conflicts and start again.";
	foreach( $conflicts as $path )
	{
		$path = $path->parentNode->getAttribute( 'path' );
		$message .= "\n\t$path";
	}

	error( $message );
}

$revision = (int) $local->entry->commit['revision'];

// Do merge
info( "Merging..." );

$last = find_last_merge( '.', $source );

if( ! $last )
	error( "Could not find previous merge. Impossible to merge automatically." );

merge( '.', $source, $last, $revision );

important( "After verifications, commit using `svn ci -F svn-commit.tmp`" );

$conflicts = get_conflicts( '.' );
if( $conflicts->length > 0 )
{
	$message = "Conflicts occured during the merge. Fix the conflicts and start again.";
	foreach( $conflicts as $path )
	{
		$path = $path->parentNode->getAttribute( 'path' );
		$message .= "\n\t$path";
	}

	error( $message );
}

?>
