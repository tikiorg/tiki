<?php

error_reporting( E_ALL );

require_once( 'tiki-setup.php' );
require_once( 'import/import_tiki_dump.php' );

header( 'Content-Type: text/plain' );

if( $tiki_p_admin != 'y' )
	die( 'Admin access required' );

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	//libxml_use_internal_errors(true);

	$dom = new DOMDocument;
	$dom->load( $_FILES['importfile']['tmp_name'] );
	if( ! $dom->schemaValidate( './import/mediawiki_dump.xsd' ) )
		die( 'File does not validate against schema. Try again.' );


	echo "Processing...\n";
	flush();

	$importer = new ImportTikiDump;
	$importer->import( $dom );

	exit;
}

header( 'Content-Type: text/html' );
?>
<html>
<head>
<title>Import MediaWiki-Style Tiki Dump</title>
</head>
<body>
<form method="post" enctype="multipart/form-data" action="">
	<input type="file" name="importfile"/>
	<input type="submit" value="Import"/>
</form>
</body>
</html>
