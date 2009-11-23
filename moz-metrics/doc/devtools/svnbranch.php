<?php
require dirname(__FILE__) . '/svntools.php';

// Perform basic checks
info( "Verifying..." );

if( ! isset( $_SERVER['argc'] ) || $_SERVER['argc'] != 2 )
	error( "Missing argument. Expecting branch to create from trunk as argument.\n\nExamples:\n\tbranches/3.0\n\tbranches/experimental/foobar" );

$source = full( 'trunk' );
$branch = full( $_SERVER['argv'][1] );

$repo = get_info( $source );
$target = get_info( $branch );

if( isset( $target->entry ) )
	error( "The branch already exists." );

if( ! is_valid_branch( $branch ) )
	error( "The provided branch is not an acceptable branch location." );

$revision = (int) $repo->entry->commit['revision'];

// Execute
info( "Branching..." );

if( ! branch( $source, $branch, $revision ) )
	error( "Branch could not be created." );
