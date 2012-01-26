<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

define( 'SVN_MIN_VERSION', 1.3 );
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

function check_svn_version() { return (float)trim(`svn --version --quiet 2> /dev/null`) > SVN_MIN_VERSION; }

function get_info( $path )
{
	$esc = escapeshellarg( $path );
	$info = @simplexml_load_string( `svn info --xml $esc 2> /dev/null` );

	return $info;
}

function is_valid_merge_destination( $url )
{
	return is_trunk( $url ) || is_experimental( $url );
}

function is_valid_merge_source( $destination, $source )
{
	if( is_trunk( $destination ) )
		return is_stable( $source );

	if( is_experimental( $destination ) )
		return is_trunk( $source );
	
	return false;
}

function is_valid_branch( $branch )
{
	return is_stable( $branch ) || is_experimental( $branch );
}

function is_stable( $branch )
{
	return dirname( $branch ) == full( 'branches' )
		&& preg_match( "/^\d+\.[\dx]+$/", basename( $branch ) );
}

function is_experimental( $branch )
{
	return dirname( $branch ) == full( 'branches/experimental' );
}

function is_trunk( $branch )
{
	return $branch == full( 'trunk' );
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

function find_last_merge( $path, $source )
{
	$short = preg_quote( short( $source ), '/' );
	$pattern = "/^\\[(MRG|BRANCH)\\].*$short'?\s+\d+\s+to\s+(\d+)/";
		
	$descriptorspec = array(
		0 => array( 'pipe', 'r' ),
		1 => array( 'pipe', 'w' ),
	);

	$ePath = escapeshellarg( $path );

	$process = proc_open( "svn log --stop-on-copy $ePath", $descriptorspec, $pipes );
	$rev = 0;

	if( is_resource( $process ) )
	{
		$fp = $pipes[1];

		while( ! feof( $fp ) )
		{
			$line = fgets( $fp, 1024 );

			if( preg_match( $pattern, $line, $parts ) )
			{
				$rev = (int) $parts[2];
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
	$short = short( $source );
	$source = escapeshellarg( $source );
	$from = (int) $from;
	$to = (int) $to;
	passthru( "svn merge $source -r$from:$to" );

	$message = "[MRG] Automatic merge, $short $from to $to";
	file_put_contents( 'svn-commit.tmp', $message );
}

function commit( $msg, $displaySuccess = true, $dieOnRemainingChanges = true )
{
	$msg = escapeshellarg( $msg );
	`svn ci -m $msg`;

	if ( $dieOnRemainingChanges && has_uncommited_changes('.') )
		error("Commit seems to have failed. Uncommited changes exist in the working folder.\n");

	return (int) get_info('.')->entry->commit['revision'];
}

function incorporate( $working, $source )
{
	$working = escapeshellarg( $working );
	$source = escapeshellarg( $source );

	passthru( $command = "svn merge $working $source" );
}

function branch( $source, $branch, $revision )
{
	$short = short( $source );

	$file = escapeshellarg( "$branch/tiki-index.php" );
	$source = escapeshellarg( $source );
	$branch = escapeshellarg( $branch );
	$message = escapeshellarg( "[BRANCH] Creation, $short 0 to $revision" );
	`svn copy $source $branch -m $message`;

	$f = @simplexml_load_string( `svn info --xml $file` );

	return isset( $f->entry );
}

function get_logs( $localPath, $minRevision, $maxRevision = 'HEAD' ) {
	if ( empty($minRevision) || empty($maxRevision) ) return false;
	$logs = `LANG=C svn log -r$maxRevision:$minRevision $localPath`;
	return $logs;
}
