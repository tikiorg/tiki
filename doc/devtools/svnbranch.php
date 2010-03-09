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
	error( "Missing argument. Expecting branch to create from trunk as argument.\n\nExamples:\n\tbranches/5.x\n\tbranches/experimental/foobar" );

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
