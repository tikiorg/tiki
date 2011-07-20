<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require dirname(__FILE__) . '/svntools.php';

// Perform basic checks
info( "Verifying..." );

if( ! isset( $_SERVER['argc'] ) || $_SERVER['argc'] != 2 )
	error( "Missing argument. Expecting branch to merge as argument.\n\nExamples:\n\tbranches/7.x\n\ttrunk" );

$local = get_info( '.' );

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

$revision = (int) get_info($source)->entry->commit['revision'];

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
