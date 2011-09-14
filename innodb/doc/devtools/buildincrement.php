<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require dirname(__FILE__) . '/svntools.php';

// Perform basic checks
info( "Verifying..." );

if( ! isset( $_SERVER['argc'] ) || $_SERVER['argc'] != 3 )
	error( "Missing argument. Expecting tagged version to build increment from and to.\n\nExamples:\n\t2.0 2.2\n\t2.1 2.2" );

$fromVersion = $_SERVER['argv'][1];
$toVersion = $_SERVER['argv'][2];

$from = full( "tags/$fromVersion" );
$to = full( "tags/$toVersion" );

$fromRep = get_info( $source );
$toRep = get_info( $branch );
$local = get_info( '.' );

if( ! isset( $fromRep->entry ) )
	error( "The origin tag does not exist." );
if( ! isset( $toRep->entry ) )
	error( "The destination tag does not exist." );
if( ! isset( $local->entry ) )
	error( "The current folder is not a local copy." );
if( has_uncommited_changes( '.' ) )
	error( "Local copy contains uncommited changes." );

info("Converting local copy to origin.");
`svn switch $from`;

$tar = "tikiwiki-inc-$fromVersion-to-$toVersion.tar";

info("Converting to destination and packaging.");
`svn switch $to | awk '/^[UA] / {print $2}' | grep -v devtools | xargs tar --exclude "*.svn*" -cf $tar`;
`gzip -5 $tar`;

info("Reverting to prior status.");
`svn switch {$local->entry->url}`;

echo "$tar.gz was created.\n";
