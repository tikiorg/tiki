<?php
require dirname(__FILE__) . '/svntools.php';

// Perform basic checks
if( ! isset( $_SERVER['argc'] ) || $_SERVER['argc'] != 2 )
	error( "Missing argument. Expecting branch to review as argument.\n\nExamples:\n\tbranches/experimental/plugin_ui" );

$source = full( $_SERVER['argv'][1] );
$trunk = full( 'trunk' );

if( ! is_experimental( $source ) )
	error( "This script is only valid to review experimental branches." );

$last = find_last_merge( $source, $trunk );

if( ! $last )
	error( "Could not find previous merge." );

$eS = escapeshellarg( $source );
$eT = escapeshellarg( $trunk );
passthru( "svn diff $eT@$last $eS" );

?>
