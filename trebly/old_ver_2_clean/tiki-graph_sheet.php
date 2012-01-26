<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/sheet/grid.php');
require_once ('lib/graph-engine/gd.php');
require_once ('lib/graph-engine/pdflib.php');
require_once ('lib/graph-engine/ps.php');
require_once ('lib/graph-engine/graph.pie.php');
require_once ('lib/graph-engine/graph.bar.php');
require_once ('lib/graph-engine/graph.multiline.php');

function handle_series( $serie, &$sheet )
{
	if( !$range = $sheet->getRange( $serie ) )
		$range = array_map( 'trim', explode( ',', $serie ) );

	if( !is_array( $range ) )
		return array();

	return $range;
}

// Various validations {{{1

$access->check_feature('feature_sheet');

$info = $sheetlib->get_sheet_info( $_REQUEST['sheetId'] );
if (empty($info)) {
	$smarty->assign('Incorrect parameter');
	$smarty->display('error.tpl');
	die;
}

$objectperms = Perms::get( 'sheet', $_REQUEST['sheetId'] );
if ($tiki_p_admin != 'y' && !$objectperms->view_sheet && !($user && $info['author'] == $user)) {
	$smarty->assign('msg', tra('Permission denied'));
	$smarty->display('error.tpl');
	die;
}

// This condition will be removed when a php-based renderer will be written
if( !function_exists( 'pdf_new' ) && !function_exists( 'imagepng' ) )
{
	$smarty->assign('msg', tra("No valid renderer found. GD or PDFLib required.") );

	$smarty->display("error.tpl");
	die;
}

if( !isset( $_REQUEST['sheetId'] ) )
{
	$smarty->assign('msg', tra("No sheet specified.") );

	$smarty->display("error.tpl");
	die;
}
// }}}1

$valid_graphs = array( 'PieChartGraphic', 'MultilineGraphic', 'MultibarGraphic', 'BarStackGraphic' );

$smarty->assign('sheetId', $_REQUEST["sheetId"]);

// Individual permissions are checked because we may be trying to edit the gallery

// Init smarty variables to blank values
//$smarty->assign('theme','');


$smarty->assign('title', $info['title']);
$smarty->assign('description', $info['description']);

$smarty->assign('page_mode', 'form' );

// Process the insertion or modification of a gallery here

$grid = new TikiSheet;

$sheetId = $_REQUEST['sheetId'];

if( isset( $_REQUEST['title'] ) )
{
	if( !in_array( $_REQUEST['graphic'], $valid_graphs ) )
		die( "Unknown Graphic." );

	$cache_file = "temp/cache/tsge_" . md5( $_SERVER['REQUEST_URI'] );

	if( !isset( $_REQUEST['renderer'] ) )
		$_REQUEST['renderer'] = null;
	switch( $_REQUEST['renderer'] )
	{
	case 'PNG':
		$renderer = new GD_GRenderer( $_REQUEST['width'], $_REQUEST['height'], 'png' );
		$ext = 'png';
		break;
	case 'JPEG':
		$renderer = new GD_GRenderer( $_REQUEST['width'], $_REQUEST['height'], 'jpg' );
		$ext = 'jpg';
		break;
	case 'PDF':
		$renderer = new PDFLib_GRenderer( $_REQUEST['format'], $_REQUEST['orientation'] );
		$ext = 'pdf';
		break;
	case 'PS':
		$renderer = new PS_GRenderer( $_REQUEST['format'], $_REQUEST['orientation'] );
		$ext = 'ps';
		break;
	default:
		$smarty->assign('msg', tra("You must select a renderer."));

		$smarty->display("error.tpl");
		die;
	}

	if( file_exists( $cache_file ) && time() - filemtime( $cache_file ) < 3600 )
	{
		$renderer->httpHeaders( "graph.$ext" );
		readfile( $cache_file );
		exit;
	}

	$handler = new TikiSheetDatabaseHandler( $sheetId );
	$grid->import( $handler );

	$graph = $_REQUEST['graphic'];
	$graph = new $graph;

	// Create Output
	$series = array();
	foreach( $_REQUEST['series'] as $key => $value )
		if( !empty( $value ) )
		{
			$s = handle_series( $value, $grid );
			if( count( $s ) > 0 )
				$series[$key] = $s;
		}

	if( !$graph->setData( $series ) )
	{
		$smarty->assign('msg', tra("Invalid Series for current graphic.") );

		$smarty->display("error.tpl");
		die;
	}

	if( !empty( $_REQUEST['title'] ) )
		$graph->setTitle( $_REQUEST['title'] );

	if( isset( $_REQUEST['independant'] ) )
	{
		$graph->setParam( 'grid-independant-location', $_REQUEST['independant'] );
		$graph->setParam( 'grid-vertical-position', $_REQUEST['vertical'] );
		$graph->setParam( 'grid-horizontal-position', $_REQUEST['horizontal'] );
	}

	$graph->draw( $renderer );

	ob_start();
	$renderer->httpOutput( "graph.$ext" );
	$content = ob_get_contents();
	ob_end_flush();

	file_put_contents( $cache_file, $content );

	exit;
}
else
{
	if( isset( $_GET['graphic'] ) && in_array( $_GET['graphic'], $valid_graphs ) )
	{
		$graph = $_GET['graphic'];
		$g = new $graph;
		$series = array();
		foreach( array_keys( $g->getRequiredSeries() ) as $s )
			if( $s == 'y0' )
			{
				$series[] = 'y0';
				$series[] = 'y1';
				$series[] = 'y2';
				$series[] = 'y3';
				$series[] = 'y4';
			}
			else
				$series[] = $s;

		$smarty->assign( 'mode', 'param' );
		$smarty->assign( 'series', $series );
		$smarty->assign( 'graph', $graph );
		$smarty->assign( 'renderer', $_GET['renderer'] );

		ob_start();

		$handler = new TikiSheetDatabaseHandler( $sheetId );
		$grid->import( $handler );

		$grid->export( new TikiSheetLabeledOutputHandler );
		$dataGrid = ob_get_contents();
		ob_end_clean();
		$smarty->assign( 'dataGrid', $dataGrid );

		if( function_exists( 'pdf_new' ) )
		{
			$smarty->assign( 'format', $_GET['format'] );
			$smarty->assign( 'orientation', $_GET['orientation'] );
		}
		if( function_exists( 'imagepng' ) )
		{
			$smarty->assign( 'im_width', $_GET['width'] );
			$smarty->assign( 'im_height', $_GET['height'] );
		}

		if( is_a( $g, 'GridBasedGraphic' ) )
			$smarty->assign( 'showgridparam', true );
	}
	else
	{
		$smarty->assign( 'mode', 'graph' );
		$smarty->assign( 'hasgd', function_exists( 'imagepng' ) && function_exists( 'imagejpeg' ) );
		$smarty->assign( 'haspdflib', function_exists( 'pdf_new' ) );
		$smarty->assign( 'hasps', function_exists( 'ps_new' ) );
	}
}

// Display the template
$smarty->assign('mid', 'tiki-graph-sheets.tpl');
$smarty->display("tiki.tpl");
