<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

ini_set( 'include_path', ini_get( 'include_path' ) . ":lib/sheet" );

// Nice dependencies, mostly for excel support. Don't try changing the order.
require_once( "lib/pear/PEAR.php" );
require_once( "lib/sheet/excel/reader_ole.php" );
require_once( "lib/sheet/excel/reader.php" );
require_once( "lib/sheet/excel/writer/format.php" );
require_once( "lib/sheet/excel/writer/biffwriter.php" );
require_once( "lib/sheet/excel/writer/worksheet.php" );
require_once( "lib/sheet/excel/writer/workbook.php" );
require_once( "lib/sheet/excel/writer/parser.php" );
require_once( "lib/sheet/ole/pps.php" );
require_once( "lib/sheet/ole/pps/root.php" );
require_once( "lib/sheet/ole/pps/file.php" );
require_once( "lib/sheet/ole.php" );
require_once( "lib/sheet/excel/writer.php" );
//require_once( "lib/sheet/conf/config.inc.php" );
require_once( "lib/encoding/lib-encoding.php" );
include_once 'lib/diff/Diff.php';
include_once 'lib/diff/Renderer.php';
// Constants {{{1

/*
DATA:
End values will be preserved.

CALC:
The calculations will be preserved.

CELL:
The cell merging will be preserved.

FORMAT:
The display format of the data of the cell
*/

define( 'TIKISHEET_SAVE_DATA',		0x00010000 );
define( 'TIKISHEET_SAVE_CALC',		0x00020000 );
define( 'TIKISHEET_SAVE_CELL',		0x00040000 );
define( 'TIKISHEET_SAVE_FORMAT',	0x00080000 );

define( 'TIKISHEET_LOAD_DATA',		0x00000001 );
define( 'TIKISHEET_LOAD_CALC',		0x00000002 );
define( 'TIKISHEET_LOAD_CELL',		0x00000004 );
define( 'TIKISHEET_LOAD_FORMAT',	0x00000008 );

// Initial amount of rows and columns at TikiSheet initialisation
define( 'INITIAL_ROW_COUNT',		15 );
define( 'INITIAL_COL_COUNT',		5 );

// Map array indexes.
define( 'TS_THEAD',			0 );
define( 'TS_TBODY',			1 );
define( 'TS_TFOOT',			2 );

define( 'TS_DEFAULT',		'default' );
// }}}1

// Registration function {{{1
function TIKISHEET_REGISTER_HANDLER( $class )
{
	global $globalHandlers;
	$globalHandlers[] = $class;
}
// }}}1

/** TikiSheetDataFormat Class {{{1
 * Class containing the different supported data formats by TikiSheet.
 * The formats coded in this class should also exist in lib/sheet/formula.js
 */
class TikiSheetDataFormat
{
	function currency( $value, $before = '', $after = '' )
	{
		return $before . sprintf( "%.2f", (float)$value ) . $after;
	}

	function currency_ca( $value )
	{
		return TikiSheetDataFormat::currency( $value, '', '$' );
	}

	function currency_us( $value )
	{
		return TikiSheetDataFormat::currency( $value, '$' );
	}
} // }}}1

 /** TikiSheet Class {{{1
 * Calculation sheet data container. Used as a bridge between
 * different formats.
 * @author Louis-Philippe Huberdeau (lphuberdeau@phpquebec.org)
 */
class TikiSheet
{
 	// Attributes {{{2
	/**
	 * Two dimensional array, grid containing the end values ([y][x])
	 */
	var $dataGrid;

	/**
	 * Two dimensional array, grid containing the raw values ([y][x])
	 */
	var $calcGrid;

	/**
	 * Two dimensional array, grid containing an associative arrays
	 * with 'height' and 'width' values.
	 */
	var $cellInfo;

	/**
	 * Row and column count once finalized.
	 */
	var $rowCount;
	var $columnCount;

	/**
	 * Layout parameters.
	 */
	var $headerRow;
	var $footerRow;
	var $parseValues;
	var $cssName;

	/**
	 * Internal values.
	 */
	var $COLCHAR;
	var $indexes;
	var $lastIndex;
	var $lastID;

	var $usedRow;
	var $usedCol;

	var $errorFlag;

	var $contributions;
	var $id;

	var $rangeBeginRow = -1;
	var $rangeEndRow   = -1;
	var $rangeBeginCol = -1;
	var $rangeEndCol = -1;

	function getRangeBeginRow()
	{
		return $this->rangeBeginRow > -1 ? $this->rangeBeginRow : 0;
	}

	function getRangeEndRow()
	{
		return $this->rangeEndRow > -1 ? $this->rangeEndRow : $this->getRowCount();
	}

	function getRangeBeginCol()
	{
		return $this->rangeBeginCol > -1 ? $this->rangeBeginCol : 0;
	}

	function getRangeEndCol()
	{
		return $this->rangeEndCol > -1 ? $this->rangeEndCol : $this->getColumnCount();
	}

	// }}}2

	/** getHandlerList {{{2
	 * Returns an array containing the list of all valid
	 * handlers for general file import/export.
	 * @return An array.
	 * @static
	 */
	function getHandlerList()
	{
		return array(
			'TikiSheetSerializeHandler',
			'TikiSheetCSVHandler',
            'TikiSheetCSVExcelHandler',
			//'TikiSheetExcelHandler'
		);
	}// }}}2

	/** TikiSheet {{{2
	 * Initializes the data container.
	 */
	function TikiSheet() {
		$this->dataGrid = array();
		$this->calcGrid = array();
		$this->cellInfo = array();

		$this->COLCHAR = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$this->indexes = array( $this->COLCHAR[0] => 0 );
		$this->lastIndex = 0;
		$this->lastID = $this->COLCHAR[0];

		$this->rowCount = INITIAL_ROW_COUNT;
		$this->columnCount = INITIAL_COL_COUNT;

		$this->headerRow = 0;
		$this->footerRow = 0;
		$this->parseValues = 'n';
		$this->className = '';
	}

	/** configureLayout {{{2
	 * Assigns the different parameters for the output
	 * @param $className	The class that will be assigned
	 *						to the table tag of the output.
	 *						If used for an other output than
	 *						HTML, it can be used as an identifier
	 *						for the type of layout.
	 * @param $headerRow	The amount of rows that are considered
	 *						as part of the header.
	 * @param $footerRow	The amount of rows that are considered
	 *						as part of the footer.
	 * @param $parseValues	Parse cell values as wiki text if ='y'
	 * 						when using output handler
	 */
	function configureLayout( $className, $headerRow = 0, $footerRow = 0, $parseValues = 'n' )
	{
		$this->cssName = $className;
		$this->headerRow = $headerRow;
		$this->footerRow = $footerRow;
		$this->parseValues = $parseValues;
	}

	/** getColumnIndex {{{2
	 * Returns the index of the column from a cell ID.
	 * @param $id Cell ID in [A-Z]+[0-9]+ format.
	 * @return Zero-based column index.
	 */
	function getColumnIndex( $id )
	{
		if ( !preg_match( "/^([A-Z]+)([0-9]+)$/", $id, $parts ) )
			return false;

		if ( !isset( $this->indexes[ $parts[1] ] ) )
		{
			while( $this->lastID != $parts[1] )
			{
				$this->lastID = $this->increment( $this->lastID );
				$this->lastIndex++;

				$this->indexes[$this->lastID] = $this->lastIndex;
			}

			return $this->lastIndex;
		}
		else
			return $this->indexes[ $parts[1] ];
	}

	/** getRowIndex {{{2
	 * Returns the index of the row from a cell ID.
	 * @param $id Cell ID in [A-Z]+[0-9]+ format.
	 * @return Zero-based row index.
	 */
	function getRowIndex( $id )
	{
		if ( !preg_match( "/^([A-Z]+)([0-9]+)$/", $id, $parts ) )
			return false;

		return $parts[2] - 1;
	}

	/** equals {{{2
	 * Determines if the value, calculation and size are equal at
	 * certain coordinates in the current and the given sheet.
	 * @param $sheet The sheet to compare.
	 * @param $rowIndex The row coordinate.
	 * @param $columnIndex The column coordinate.
	 * @return True if all values are equal.
	 */
	function equals( &$sheet, $rowIndex, $columnIndex )
	{
		if ( isset( $this->dataGrid[$rowIndex][$columnIndex] ) && !isset( $sheet->dataGrid[$rowIndex][$columnIndex] ) )
			return false;

		if ( isset( $this->calcGrid[$rowIndex][$columnIndex] ) && !isset( $sheet->calcGrid[$rowIndex][$columnIndex] ) )
			return false;

		return $this->dataGrid[$rowIndex][$columnIndex] == $sheet->dataGrid[$rowIndex][$columnIndex]
			&& $this->calcGrid[$rowIndex][$columnIndex] == $sheet->calcGrid[$rowIndex][$columnIndex]
			&& $this->cellInfo[$rowIndex][$columnIndex]['width'] == $sheet->cellInfo[$rowIndex][$columnIndex]['width']
			&& $this->cellInfo[$rowIndex][$columnIndex]['height'] == $sheet->cellInfo[$rowIndex][$columnIndex]['height']
			&& $this->cellInfo[$rowIndex][$columnIndex]['format'] == $sheet->cellInfo[$rowIndex][$columnIndex]['format']
			&& $this->cellInfo[$rowIndex][$columnIndex]['style'] == $sheet->cellInfo[$rowIndex][$columnIndex]['style']
			&& $this->cellInfo[$rowIndex][$columnIndex]['class'] == $sheet->cellInfo[$rowIndex][$columnIndex]['class'];
	}

	/** export {{{2
	 * Exports the content of the calculation sheet
	 * to the given format handler.
	 * @param $handler The format handler.
	 * @return True on success.
	 */
	function export( &$handler )
	{
		return $handler->_save( $this );
	}

	/**
	 * @param bool $incsubs Include sub-sheets
	 * @param timestamp $date Date (revision) to read sub-sheets from
	 */
	function getTableHtml( $incsubs = true )
	{
		global $prefs, $sheetlib;

		$filegallib = TikiLib::lib("filegal");
		$fileInfo = $filegallib->get_file_info( $fileId );

		$handler = new TikiSheetOutputHandler(null, ($this->parseValues == 'y' && $_REQUEST['parse'] != 'n'));

		$this->export($handler);

		$data = $handler->output;

		if ($incsubs == true) {
			//get sheets from db first
			foreach ($sheetlib->get_related_sheet_ids($this->id) as $childSheetId) {
				$handler = new TikiSheetDatabaseHandler($childSheetId, $date );
				$childSheet = new TikiSheet();
				$childSheet->import($handler);
				$data .= $childSheet->getTableHtml( false );
			}
		}
			foreach ($sheetlib->get_related_file_ids($this->id) as $childFileId) {
				$fileInfo = $filegallib->get_file_info( $childFileId );

				switch ($fileInfo['filetype']) {
					case 'text/csv':
						$handler = new TikiSheetCSVHandler($fileInfo);
						break;
					default: $handler = false;
				}

				if (!empty($handler)) {
					$childSheet = new TikiSheet();
					$childSheet->import($handler);
					$data .= $childSheet->getTableHtml();
				}
			}

			foreach ($sheetlib->get_related_tracker_ids($this->id) as $childTrackerId) {
				$handler = new TikiSheetTrackerHandler($childTrackerId);
				$childSheet = new TikiSheet();
				$childSheet->import($handler);
				$data .= $childSheet->getTableHtml();
			}


		return $data;
	}

	/** finalize {{{2
	 * Analyses the content of the sheet and complete the
	 * the load.
	 */
	function finalize()
	{
		$maxRow = 0;
		$maxCol = 0;

		$this->finalizeGrid( $this->dataGrid, $maxRow, $maxCol );
		$this->finalizeGrid( $this->calcGrid, $maxRow, $maxCol );
		$this->finalizeGrid( $this->cellInfo, $maxRow, $maxCol, true );

		$this->rowCount = ($maxRow >= INITIAL_ROW_COUNT || $maxRow > 0 ? $maxRow : INITIAL_ROW_COUNT);
		$this->columnCount = ($maxCol >= INITIAL_COL_COUNT || $maxCol > 0 ? $maxCol : INITIAL_COL_COUNT);

		$base = array( 'width' => 1, 'height' => 1, 'format' => null, 'style' => '', 'class' => '' );
		for( $y = 0; $this->rowCount > $y; $y++ ) {
			for( $x = 0; $this->columnCount > $x; $x++ )
			{
				if ( !isset( $this->dataGrid[$y] ) )
					$this->dataGrid[$y] = array();
				if ( !isset( $this->calcGrid[$y] ) )
					$this->calcGrid[$y] = array();
				if ( !isset( $this->cellInfo[$y] ) )
					$this->cellInfo[$y] = array();

				if ( !isset( $this->dataGrid[$y][$x] ) )
					$this->dataGrid[$y][$x] = '';
				if ( !isset( $this->calcGrid[$y][$x] ) )
					$this->calcGrid[$y][$x] = '';
				if ( !isset( $this->cellInfo[$y][$x] ) )
					$this->cellInfo[$y][$x] = $base;


				$this->cellInfo[$y][$x] = array_merge( $base, $this->cellInfo[$y][$x] );
			}
		}
		return true;
	}

	/** finalizeGrid {{{2
	 * Locates the maximal values in a grid if they are above
	 * the initial ones.
	 * @param $grid The grid to scan
	 * @param $maxRow The highest row index.
	 * @param $maxCol The highest column index.
	 * @param $addIndex Boolean value, used for merged cells, determines
	 *					if the actual value should be added when calculating
	 *					the maximal values. As merged cells use more space,
	 *					they should be considered as more cells.
	 */
	function finalizeGrid( $grid, &$maxRow, &$maxCol, $addIndex = false )
	{
		foreach( $grid as $key=>$row )
			$this->finalizeRow( $row, $maxRow, $maxCol, $key, $addIndex );
	}

	/** finalizeRow {{{2
	 * Identifies the largest key in an array and set it as the
	 * new maximum.
	 * @param $row The row to scan.
	 * @param $max The current maximum value of the row.
	 * @param $addIndex Used for merged cells. Leave value blank (false)
	 *					if the current scan is not on the merged cell
	 *					grid. Other possible values are 'width' and 'height'
	 *					which should be used based on which side of the grid
	 *					is being scanned.
	 */
	function finalizeRow( $row, &$maxRow, &$maxCol, $rowIndex, $addIndex = false )
	{
		$localMax = max( array_keys( $row ) );

		$total = $localMax;
		if ( $addIndex )
			$total += $row[$localMax]['width'];

		if ( $total > $maxCol )
			$maxCol = $total;

		if ( $addIndex )
		{
			foreach( $row as $info )
			{
				$total = $rowIndex + $info['height'];

				if ( $total > $maxRow )
					$maxRow = $total;
			}
		}
		else
		{
			if ( $rowIndex > $maxRow )
				$maxRow = $rowIndex;
		}
	}

	/** getColumnCount {{{2
	 * Returns the column count.
	 */
	function getColumnCount()
	{
		return $this->columnCount == 0 ? INITIAL_COL_COUNT : $this->columnCount;
	}

	/** getRange {{{2
	 * Reutrns an array containing the values located in
	 * a given range (ex: A1:B9)
	 */
	function getRange( $range )
	{
		if ( preg_match( '/^([A-Z]+)([0-9]+):([A-Z]+)([0-9]+)$/', strtoupper($range), $parts ) )
		{
			$beginRow = $parts[2] - 1;
			$endRow = $parts[4] - 1;
			$beginCol = $this->getColumnNumber( $parts[1] );
			$endCol = $this->getColumnNumber( $parts[3] );

			if ( $beginRow > $endRow )
			{
				$a = $endRow;
				$endRow = $beginRow;
				$beginRow = $a;
			}
			if ( $beginCol > $endCol )
			{
				$a = $endCol;
				$endCol = $beginCol;
				$beginCol = $a;
			}

			$data = array();
			for( $row = $beginRow; $endRow + 1 > $row; $row++ )
				for( $col = $beginCol; $endCol + 1 > $col; $col++ )
					if ( isset( $this->dataGrid[$row] ) && isset( $this->dataGrid[$row][$col] ) )
						$data[] = $this->dataGrid[$row][$col];

			return $data;
		}
		else
			return false;
	}

	/** setRange {{{2
	 * Limits display (so far)
	 * a given range (ex: A1:B9)
	 */
	function setRange( $range )
	{
		if ( preg_match( '/^([A-Z]+)([0-9]+):([A-Z]+)([0-9]+)$/', strtoupper($range), $parts ) ) {
			$this->rangeBeginRow = (int)$parts[2] - 1;
			$this->rangeEndRow = (int)$parts[4];
			$this->rangeBeginCol = $this->getColumnNumber( $parts[1] );
			$this->rangeEndCol = $this->getColumnNumber( $parts[3] ) + 1;
		}
	}

	/** getRowCount {{{2
	 * Returns the row count.
	 */
	function getRowCount()
	{
		return $this->rowCount;
	}

	function name()
	{
		return $this->name;
	}

	/** import {{{2
	 * Fills the content of the calculation sheet with
	 * data from the given handler.
	 * @param $handler The format handler.
	 * @return True on success.
	 */
	function import( &$handler )
	{
		$this->name = $handler->name();
		$this->id = $handler->id;
		$this->type = $handler->type;
		$this->cssName = $handler->cssName;
		$this->rowCount = (isset($handler->rowCount) ? $handler->rowCount : $this->rowCount);
		$this->columnCount = (isset($handler->columnCount) ? $handler->columnCount : $this->columnCount);

		$this->dataGrid = array();
		$this->calcGrid = array();
		$this->cellInfo = array();
		$this->errorFlag = false;

		set_error_handler( array( &$this, "error_handler" ) );
		if ( !$handler->_load( $this ) || $this->errorFlag )
		{
			restore_error_handler();
			return false;
		}

		restore_error_handler();
		return $this->finalize();
	}

	/** increment {{{2
	 * Implementation of the column ID incrementation used
	 * on client side.
	 * @param The value to increment.
	 * @return The incremented value.
	 */
	function increment( $val )
	{
		if ( empty( $val ) )
			return substr( $this->COLCHAR, 0, 1 );

		$n = strpos( $this->COLCHAR, substr( $val, -1 ) ) + 1;

		if ( $n < strlen( $this->COLCHAR ) )
			return substr( $val, 0, -1 ) . substr( $this->COLCHAR, $n, 1 );
		else
			return $this->increment( substr( $val, 0, -1 ) ) . substr( $this->COLCHAR, 0, 1 );
	}

	/** initCell {{{2
	 * Indicates the next cell that will be filled.
	 * @param $cellID The Identifier of the cell or the row index
	 * 					if there are 2 parameters.
	 * @param $col The index of the column.
	 * @return True on success.
	 */
	function initCell( $cellID, $col = null )
	{
		if ( $col === null )
		{
			$this->usedRow = $this->getRowIndex( $cellID );
			$this->usedCol = $this->getColumnIndex( $cellID );
		}
		else
		{
			$this->usedRow = $cellID;
			$this->usedCol = $col;
		}

		return $this->usedRow !== false && $this->usedCol !== false;
	}

	/** isEmpty {{{2
	 * Determines if the value, calculation and size are equal at
	 * certain coordinates in the current and the given sheet.
	 * @param $rowIndex The row coordinate.
	 * @param $columnIndex The column coordinate.
	 * @return True if all values are empty.
	 */
	function isEmpty( $rowIndex, $columnIndex )
	{
		return $this->dataGrid[$rowIndex][$columnIndex] == ''
			&& $this->calcGrid[$rowIndex][$columnIndex] == ''
			&& ( $this->cellInfo[$rowIndex][$columnIndex]['width'] == ''
			||   $this->cellInfo[$rowIndex][$columnIndex]['width'] == 1 )
			&& ( $this->cellInfo[$rowIndex][$columnIndex]['height'] == ''
			||   $this->cellInfo[$rowIndex][$columnIndex]['height'] == 1 );
	}

	/** setCalculation {{{2
	 * Assigns a calculation to the currently initialized
	 * cell.
	 * @param $calculation The calculation to set.
	 */
	function setCalculation( $calculation )
	{
		$this->calcGrid[$this->usedRow][$this->usedCol] = $calculation;
	}

	/** setFormat {{{2
	 * Indicates the cell's data format during display.
	 * The format is a text identifier that matches a function
	 * name that will be executed.
	 */
	function setFormat( $format )
	{
		if ( empty( $format ) || !method_exists( new TikiSheetDataFormat, $format ) ) $format = null;
		$this->cellInfo[$this->usedRow][$this->usedCol]['format'] = $format;
	}

	/** setSize {{{2
	 * Sets the size of the last initialized cell.
	 * @param $width The cell's column span.
	 * @param $height The cell's row span.
	 */
	function setSize( $width, $height )
	{
		$this->cellInfo[$this->usedRow][$this->usedCol]["width"] = $width;
		$this->cellInfo[$this->usedRow][$this->usedCol]["height"] = $height;

		for( $y = $this->usedRow; $this->usedRow + $height > $y; $y++ )
			for( $x = $this->usedCol; $this->usedCol + $width > $x; $x++ )
				if ( !($y == $this->usedRow && $x == $this->usedCol) )
					$this->createDeadCell( $x, $y );
	}

	/** setValue {{{2
	 * Assigns a value to the currently initialized
	 * cell.
	 * @param $value The value to set.
	 */
	function setValue( $value )
	{
		$this->dataGrid[$this->usedRow][$this->usedCol] = $value;
	}

	/** setStyle {{{2
	 * Sets html style,if any, to the currently initialized
	 * cell.
	 * @param $style The value to set.
	 */
	function setStyle( $style = '' )
	{
		$this->cellInfo[$this->usedRow][$this->usedCol]['style'] = $style;
	}

	/** setClass {{{2
	 * Sets html class, if any, to the currently initialized
	 * cell.
	 * @param $class The value to set.
	 */
	function setClass( $class = '')
	{
		$this->cellInfo[$this->usedRow][$this->usedCol]['class'] = $class;
	}

	/** createDeadCell {{{2
	 * Assigns the cell as overlapped by a wide cell.
	 * @param $x Coordinate of the cell
	 * @param $y Coordinate of the cell
	 */
	function createDeadCell( $x, $y )
	{
		$this->dataGrid[$y][$x] = null;
		$this->cellInfo[$y][$x] = array( "width" => 0, "height" => 0, "format" => null, "style" => "", "class" => "" );
	}

	/** getClass {{{2
	 * Returns the class of a the current cell if it exist.
	 */
	function getClass()
	{
		if( isset($this->cellInfo[$this->usedRow][$this->usedCol]['class']))
			return $this->cellInfo[$this->usedRow][$this->usedCol]['class'];
		else
			return "";
	}

	/** getColumnNumber {{{2
	 * Returns the column number from the letter-style.
	 */
	function getColumnNumber( $letter )
	{
		$val = 0;
		$len = strlen( $letter );

		for( $i = 0; $len > $i; $i++ )
		{
			$pow = pow( 26, $len - $i - 1 );
			$val += $pow * ( ord( $letter[$i] ) - 64 );
		}
		$val--;

		return $val;
	}

	/** error_handler {{{2
	 * Callback error handler function. Used by import.
	 * @see http://ca.php.net/set_error_handler
	 */
	function error_handler( $errno, $errstr, $errfile, $errline )
	{
		echo $errstr . ': ' .  $errfile . ' (' . $errline . ')';
		$this->errorFlag = true;
	}
} // }}}1

/** TikiSheetDataHandler {{{1
 * Base data handler to link the sheet to the data
 * source. Before being sent as an handler, the object
 * must know the target location of the data if they
 * are required.
 */
class TikiSheetDataHandler
{
	/** _load {{{2
	 * Function called by import. The function must grab
	 * the information from the data source and store it
	 * the data container.
	 * @param $sheet The data sheet object to fill.
	 * @return true on success.
	 * @abstract
	 */
	var $maxrows = 300;
    var $maxcols = 26;
    var $output = "";

	function _load( &$sheet )
	{
		trigger_error( "Abstract method call. _load() not defined in " . get_class( $this ), E_USER_ERROR );
	}

	/** _save {{{2
	 * Function called by export. The function must read the
	 * data in the container and store it in the data source.
	 * @param $sheet The sheet object to read the data from.
	 * @return true on success.
	 * @abstract
	 */
	function _save( &$sheet )
	{
		trigger_error( "Abstract method call. _save() not defined in " . get_class( $this ), E_USER_ERROR );
	}

	/** name {{{2
	 * Identifies the handler in a readable form.
	 * @return The name of the handler.
	 */
	function name()
	{
		trigger_error( "Abstract method call. name() not defined in " . get_class( $this ), E_USER_ERROR );
	}
	/** supports {{{2
	 * Function to indicate the features that are supported
	 * by the handler.
	 * @param The feature constant.
	 * @return true if the feature is supported.
	 * @static
	 * @abstract
	 */
	function supports( $feature )
	{
		trigger_error( "Abstract method call. supports() not defined in " . get_class( $this ), E_USER_ERROR );
	}

	/** version {{{2
	 * Indicates the handler's version.
	 * @return The version number as a string.
	 */
	function version()
	{
		trigger_error( "Abstract method call. version() not defined in " . get_class( $this ), E_USER_ERROR );
	}
} // }}}1

/** TikiSheetSerializeHandler {{{1
 * Class that stores the sheet representation in a
 * standard text file as a serialized PHP object.
 */
class TikiSheetSerializeHandler extends TikiSheetDataHandler
{
	var $file;

	/** Constructor {{{2
	 * Initializes the the serializer on a file.
	 * @param $file The file path to save or load from.
	 */
	function TikiSheetSerializeHandler( $file = "php://stdout", $inputEncoding = '', $outputEncoding = '' )
	{
		$this->file = $file;
        $this->encoding = new Encoding ($inputEncoding, $outputEncoding);
	}

	// _load {{{2
	function _load( &$sheet )
	{
		if ( $file = @fopen( $this->file, "r" ) )
		{
			$data = @fread( $file, filesize( $this->file ) );

			@fclose( $file );

			$data = unserialize( $data );

			if ( $data === false )
				return false;

			$sheet->dataGrid = $data->dataGrid;
			$sheet->calcGrid = $data->calcGrid;
			$sheet->cellInfo = $data->cellInfo;

			return true;
		}
		else
			return false;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		$data = serialize( $sheet );

		if ( $this->file == "php://stdout" )
		{
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=export.tws");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
			$this->output = $data;
			return true;
		}
		else
		{
			if ( $file = @fopen( $this->file, "w" ) )
			{
				$return =  @fwrite( $file, $data );

				@fclose( $file );
				return $return;
			}
			else
				return false;
		}
	}

	// name {{{2
	function name()
	{
		return "TikiSheet File";
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_SAVE_CALC | TIKISHEET_SAVE_CELL | TIKISHEET_SAVE_FORMAT | TIKISHEET_LOAD_DATA | TIKISHEET_LOAD_CALC | TIKISHEET_LOAD_CELL | TIKISHEET_LOAD_FORMAT ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "1.0";
	}
 } // }}}1

/** TikiSheetCSVHandler {{{1
 * Class that stores the sheet representation in a
 * standard text file as a serialized PHP object.
 */
class TikiSheetCSVHandler extends TikiSheetDataHandler
{
	var $file = 'php://stdout';
	var $lineLen;

	/** Constructor {{{2
	 * Initializes the the serializer on a file.
	 * @param $file The file path to save or load from.
	 */
	function TikiSheetCSVHandler( $fileInfo, $inputEncoding = '', $outputEncoding = '', $lineLen = 1024 )
	{
		$this->lineLen = $lineLen;
		$this->data = strip_tags( $fileInfo['data'] );
		$this->name = $fileInfo['name'];
        $this->encoding = new Encoding ($inputEncoding, $outputEncoding);
		$this->type = "file";
		$this->id = $fileInfo['fileId'];
	}

	// _load {{{2
	function _load( &$sheet )
	{
		$rows = explode("\n", $this->data);
		for($i = 0; $i < count($rows) && $i < $this->maxrows; $i++) {
			$cols = str_getcsv($rows[$i]);

			for($j = 0; $j < count($cols) && $j < $this->maxcols; $j++) {
				$sheet->initCell( $i, $j );

				if ( !empty($cols[$j]) ) {
					if ($cols[$j][0] == '=' ) {
						$sheet->setCalculation( substr($cols[$j], 1) );
					} else {
						$sheet->setValue( $cols[$j] );
					}
				} else {
					$sheet->setValue( "" );
				}

				$sheet->setSize( 1, 1 );
			}
		}

		if ($i >= $this->maxrows || $j >= $this->maxcols) $this->truncated = true;

		return true;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		$total = array();

        ksort ($sheet->dataGrid);
		foreach( $sheet->dataGrid as $row )
			if ( is_array( $row ) )
            {
                ksort ($row);
				$total[] = implode( ",", $row );
            }

		if ( is_array( $total ) )
			$total = implode( "\n", $total );

        $total = $this->encoding->convert_encoding ($total);

		if ( $this->file == "php://stdout" )
		{
			header("Content-type: text/comma-separated-values");
			header("Content-Disposition: attachment; filename=export.csv");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");

			$this->output = $total;

			return true;
		}
		else
		{
			if ( $file = @fopen( $this->file, "w" ) )
			{
				if ( !@fwrite( $file, $total ) )
					return false;

				@fclose( $file );
				return true;
			}
			else
				return false;
		}
	}

	// name {{{2
	function name()
	{
		return $this->name;
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_LOAD_DATA ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "1.0";
	}
 } // }}}1


/** TikiSheetTrackerHandler {{{1
 * Class that stores the sheet representation in a
 * standard text file as a serialized PHP object.
 */
class TikiSheetTrackerHandler extends TikiSheetDataHandler
{
	var $file;
	var $lineLen;

	/** Constructor {{{2
	 * Initializes the the serializer on a file.
	 * @param $file The file path to save or load from.
	 */
	function TikiSheetTrackerHandler( $trackerId )
	{
		global $tikilib;
		$trklib = TikiLib::lib("trk");

		$this->id = $trackerId;
		$this->def = Tracker_Definition::get($trackerId);
		$this->info = $this->def->getInformation();
		$this->type = "tracker";
		$this->cssName = 'readonly';
	}

	// _load {{{2
	function _load( &$sheet ) {
		global $tikilib;

		$i = 0;
		$trackerName = $this->info['name'];
		$tracker = Tracker_Query::tracker($trackerName)
			->byName()
			->excludeDetails()
			->render(false)
			->query();

		foreach($tracker as $item) {
			$j = 0;
			foreach($item as $key => $field) {
				$sheet->initCell( $i, $j );

				if (!empty($field[0]) && $field[0] == '=' ) {
					$sheet->setCalculation( substr($field, 1) );
				}

				$sheet->setValue( $i == 0 ? $key : $field );

				$sheet->setSize( 1, 1 );
				$j++;
			}
			$i++;
		}

		return true;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		return false;
	}

	// name {{{2
	function name()
	{
		return $this->info['name'];
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_LOAD_DATA ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "1.0";
	}
 } // }}}1


/** TikiSheetTrackerHandler {{{1
 * Class that stores the sheet representation in a
 * standard text file as a serialized PHP object.
 */
class TikiSheetSimpleArrayHandler extends TikiSheetDataHandler
{
	var $values = array();

	function TikiSheetSimpleArrayHandler( $simpleArray = array() )
	{
		$this->values = $simpleArray['values'];
		$this->name = $simpleArray['name'];
		$this->type = "simpleArray";
		$this->cssName = 'readonly';
	}

	// _load {{{2
	function _load( &$sheet ) {
		$i = 0;

		foreach($this->values as $row) {
			$j = 0;
			foreach($row as $key => $col) {
				$sheet->initCell( $i, $j );

				if (!empty($col[0]) && $col[0] == '=' ) {
					$sheet->setCalculation( substr($col, 1) );
				}

				if (is_array($col)) {
					foreach($col as $colKey => $val) {
						if (empty($val)) {
							array_splice($col, $colKey, 1);
						}
					}
					$col = implode(",", $col);
				}

				$col = htmlspecialchars($col);

				$sheet->setValue( $i == 0 ? $key : $col );

				$sheet->setSize( 1, 1 );
				$j++;
			}
			$i++;
		}

		return true;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		return false;
	}

	// name {{{2
	function name()
	{
		return $this->name;
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_LOAD_DATA ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "1.0";
	}
 } // }}}1

 /** TikiSheetCSVExcelHandler {{{1
 * Class that stores the sheet representation in a
 * standard text file as a serialized PHP object. The difference
 * betwen this and standard CSV is that fields here are separarated by ';'
 */
class TikiSheetCSVExcelHandler extends TikiSheetDataHandler
{
    var $file;
    var $lineLen;

    /** Constructor {{{2
     * Initializes the the serializer on a file.
     * @param $file The file path to save or load from.
     */
    function TikiSheetCSVExcelHandler( $file = "php://stdout", $inputEncoding = '', $outputEncoding = '', $lineLen = 1024 )
    {
        $this->file = $file;
        $this->lineLen = $lineLen;
        $this->data = strip_tags(file_get_contents($this->file));
        $this->encoding = new Encoding ($inputEncoding, $outputEncoding);
    }

// _load {{{2
	function _load( &$sheet )
	{
		$rows = explode("\n", $this->data);
		for($i = 0; $i < count($rows) && $i < $this->maxrows; $i++) {
			$cols = preg_split("/[,;](?!(?:[^\\\",;]|[^\\\"],[^\\\"])+\\\")/", $rows[$i]);

			for($j = 0; $j < count($cols) && $j < $this->maxcols; $j++) {
				$sheet->initCell( $i, $j );
				$sheet->setValue( $cols[$j] );

				if ( isset($cols[$j]) ) {
					if (strlen( $cols[$j] )) {
						if ($cols[$j][0] == '=' ) {
							$sheet->setCalculation( substr($cols[$j], 1) );
						}
					}
				}

				$sheet->setSize( 1, 1 );
			}
		}

		if ($i >= $this->maxrows || $j >= $this->maxcols) $this->truncated = true;

		return true;
	}

    // _save {{{2
    function _save( &$sheet )
    {
        $total = array();

        foreach( $sheet->dataGrid as $row )
        {
            $total[] = implode(';', $row);
        }

        if ( is_array( $total ) )
            $total = implode( "\n", $total );

        $total = $this->encoding->convert_encoding ($total);

        if ( $this->file == "php://stdout" )
        {
            header("Content-type: text/comma-separated-values");
            header("Content-Disposition: attachment; filename=export.csv");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");

            $this->output = $total;

            return true;
        }
        else
        {
            if ( $file = @fopen( $this->file, "w" ) )
            {
                if ( !@fwrite( $file, $total ) )
                    return false;

                @fclose( $file );
                return true;
            }
            else
                return false;
        }
    }

    // name {{{2
    function name()
    {
        return "CSV-Excel File";
    }

    // supports {{{2
    function supports( $type )
    {
        return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_LOAD_DATA ) & $type ) > 0;
    }

    // version {{{2
    function version()
    {
        return "1.0";
    }

    private function fputcsvexcel( $row, $fd=';', $quot='"', $limit)
    {
       $str='';
       $i = 0;
       foreach ($row as $col) {
       		if ($i && $i < $limit) {
				$cell = ($col->formula ? $col->formula : $col->value);
	          	str_replace(
	           		Array($quot,        "\n"),
					Array($quot.$quot,  ''),
	                $cell
				);

				if (strchr($cell, $fd)) {
					$str.=$quot.$cell.$quot.$fd;
				} else {
					$str.=$cell.$fd;
				}
       		}
			$i++;
       }

       return  $str;
    }
 } // }}}1

/** TikiSheetDatabaseHandler {{{1
 * Class to handle transactions with the database.
 * The class and database structure allow data
 * rollbacks. The class does not allow to manipulate
 * the sheets themselves. The data will only be filled
 * and extracted based on the given sheet ID. As a default
 * value, the most recent entries will be read.
 *
 * The database loader will also select the appropriate
 * layout based on the timestamped database entries. Using
 * the database handler will not require to specify manually
 * using TikiSheet::configureLayout() as it is required by all
 * other known handler as this comment is being written.
 */
class TikiSheetDatabaseHandler extends TikiSheetDataHandler
{
	var $id;
	var $readDate;
	var $rowCount;
	var $columnCount;

	/** Constructor {{{2
	 * Assigns a sheet ID to the handler.
	 * @param $id The ID of the sheet in the database.
	 * @param $db The database link to use.
	 */
	function TikiSheetDatabaseHandler( $id , $date = null )
	{
		global $tikilib, $sheetlib;

		$this->id = $id;
		$this->readDate = ( $date ? $date : time() );

		$info = $sheetlib->get_sheet_info( $this->id);

		$this->type = "sheet";
		$this->name = $info['title'];
	}

	// _load {{{2
	function _load( &$sheet )
	{
		global $tikilib, $sheetlib;

		$result = $tikilib->query( "
			SELECT `rowIndex`, `columnIndex`, `value`, `calculation`, `width`, `height`, `format`, `style`, `class`, `user`
			FROM `tiki_sheet_values`
			WHERE
				`sheetId` = ? AND
				? >= `begin` AND
				(
					`end` IS NULL OR
					`end` > ?
				)
		", array( $this->id, (int)$this->readDate, (int)$this->readDate ) );

		while( $row = $result->fetchRow() )
		{
			extract( $row );
			$sheet->initCell( $rowIndex, $columnIndex );
			$sheet->setValue( $value );
			$sheet->setCalculation( $calculation );
			$sheet->setSize( $width, $height );
			$sheet->setFormat( $format );
			$sheet->setStyle( $style );
			$sheet->setClass( $class );
		}

		// Fetching the layout informations.
		$result2 = $tikilib->query( "
			SELECT `className`, `headerRow`, `footerRow`, `parseValues`
			FROM `tiki_sheet_layout`
			WHERE
				`sheetId` = ? AND
				? >= `begin` AND
				( `end` IS NULL OR `end` > ? )
		", array( $this->id, (int)$this->readDate, (int)$this->readDate ) );

		if ( $row = $result2->fetchRow() )
		{
			extract( $row );
			$sheet->configureLayout( $className, $headerRow, $footerRow, $parseValues );
		}

		return true;
	}

	function name() {
		return $this->name;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		global $tikilib, $user, $prefs;
		// Load the current database state {{{3
		$current = new TikiSheet;
		$handler = new TikiSheetDatabaseHandler( $this->id );
		$current->import( $handler );

		// Find differences {{{3
		$mods = array();
		for( $row = 0; $sheet->getRowCount() > $row; $row++ )
		{
			for( $col = 0; $sheet->getColumnCount() > $col; $col++ )
			{
				if ( !$sheet->equals( $current, $row, $col ) )
					$mods[] = array( "row" => $row, "col" => $col );
			}
		}

		$stamp = time();

		$inserts = array();
		$updates = array();
		$updates[] = $stamp;
		$updates[] = $this->id;

		// Update the database {{{3
		if ( is_array( $mods ) )
		{
			foreach( $mods as $coord )
			{
				extract( $coord );
				$value = $sheet->dataGrid[$row][$col];

				$calc = $sheet->calcGrid[$row][$col];
				$width = $sheet->cellInfo[$row][$col]['width'];
				$height = $sheet->cellInfo[$row][$col]['height'];
				$format = $sheet->cellInfo[$row][$col]['format'];
				$style = $sheet->cellInfo[$row][$col]['style'];
				$class = $sheet->cellInfo[$row][$col]['class'];

				$updates[] = $row;
				$updates[] = $col;

				//Now that sheets have styles, many things can change and the cell not have a value.
				//if ( !$sheet->isEmpty( $row, $col ) )
				$inserts[] = array( (int)$this->id, $stamp, $row, $col, $value, $calc, $width, $height, $format, $style, $class, $user );

			}
		}

		$updates[] = $sheet->getRowCount();
		$updates[] = $sheet->getColumnCount();

		$conditions = str_repeat( "( rowIndex = ? AND columnIndex = ? ) OR ", ( count($updates) - 4 ) / 2 );
		if ($prefs['feature_actionlog'] == 'y') { // must keep the previous value to do the difference
			$query = "SELECT `rowIndex`, `columnIndex`, `value`, `style`, `class` FROM `tiki_sheet_values` WHERE `sheetId` = ? AND  `end` IS NULL";
			$result = $tikilib->query($query, array($this->id));
			$old = array();
			while( $row = $result->fetchRow() ) {
				$old[$row['rowIndex'].'-'.$row['columnIndex']] = $row['value'];
				$old[$row['rowIndex'].'-'.$row['columnIndex']]['style'] = $row['style'];
				$old[$row['rowIndex'].'-'.$row['columnIndex']]['class'] = $row['class'];
			}
		}

		$tikilib->query( "UPDATE `tiki_sheet_values` SET `end` = ?  WHERE `sheetId` = ? AND `end` IS NULL AND ( {$conditions}`rowIndex` >= ? OR `columnIndex` >= ? )", $updates );

		if ( count( $inserts ) > 0 )
			foreach( $inserts as $values )
			{
				$tikilib->query( "INSERT INTO `tiki_sheet_values` (`sheetId`, `begin`, `rowIndex`, `columnIndex`, `value`, `calculation`, `width`, `height`, `format`, `style`, `class`, `user` ) VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )", $values );
			}

		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$add = 0;
			$del = 0;
			foreach( $inserts as $values ) {
				$add += strlen($values[4]);
				if (!empty($old[$values[2].'-'.$values[3]]))
					$del += strlen($old[$values[2].'-'.$values[3]]);
			}
			if ($prefs['feature_contribution'] == 'y' && isset($_REQUEST['contributions'])) {
				global $contributionlib; include_once('lib/contribution/contributionlib.php');
				$contributionlib->assign_contributions($_REQUEST['contributions'], $this->id, 'sheet', '', '', '');
			}
			if (isset($_REQUEST['contributions']))
				$logslib->add_action('Updated', $this->id, 'sheet', "add=$add&amp;del=$del&amp;sheetId=".$this->id, '', '', '', '',  $_REQUEST['contributions']);
			else
				$logslib->add_action('Updated', $this->id, 'sheet', "add=$add&amp;del=$del&amp;sheetId=".$this->id);
		}

		// }}}3

		return true;
	}

	/** setReadDate {{{2
	 * Modifies the instant at which the snapshot of the
	 * database is taken.
	 * @param $timestamp A unix timestamp.
	 */
	function setReadDate( $timestamp )
	{
		$this->readDate = $timestamp;
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_SAVE_CALC | TIKISHEET_SAVE_CELL | TIKISHEET_SAVE_FORMAT | TIKISHEET_LOAD_DATA | TIKISHEET_LOAD_CALC | TIKISHEET_LOAD_CELL | TIKISHEET_LOAD_FORMAT ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "1.0";
	}
} // }}}1

/** TikiSheetExcelHandler {{{1
 * Class that stores the sheet representation in a
 * standard text file as a serialized PHP object.
 */
class TikiSheetExcelHandler extends TikiSheetDataHandler
{
	var $file;
	var $disabled = true;
	/** Constructor {{{2
	 * Initializes the the serializer on a file.
	 * @param $file The file path to save or load from.
	 */
	function TikiSheetExcelHandler( $file = "php://stdout" , $inputEncoding = '', $outputEncoding = '' )
	{
		$this->file = $file;
        $this->encoding = new Encoding ($inputEncoding, $outputEncoding);
	}

	// _load {{{2
	function _load( &$sheet )
	{
		$document = new Spreadsheet_Excel_Reader();

		if ( !$document->read( $this->file ) )
			return false;

		$data = $document->sheets[0];

		if ( is_array( $data['cells'] ) )
			foreach( $data['cells'] as $row=>$cols )
			{
				if ( is_array( $cols ) )
					foreach( $cols as $col=>$value )
					{
						$sheet->initCell( $row, $col );

						$info = $data['cellsInfo'][$row][$col];

						if ( !isset( $info['rowspan'] ) )
							$height = 1;
						else
							$height = $info['rowspan'];

						if ( !isset( $info['colspan'] ) )
							$width = 1;
						else
							$width = $info['colspan'];

						$cellValue = $this->encoding->convert_encoding ( $value );
						$sheet->setValue( $cellValue );

						if ( isset($cellValue) ) {
							if (strlen( $cellValue )) {
								if ($cellValue[0] == '=' ) {
									$sheet->setCalculation( substr($cellValue, 1) );
								}
							}
						}
						$sheet->setSize( $width, $height );
					}
			}

		return true;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		$book = new Spreadsheet_Excel_Writer;

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=export.xls");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");

		$out = &$book->addWorksheet( "TikiSheet Export" );

		foreach( $sheet->dataGrid as $row=>$cols )
		{
			if ( is_array( $cols ) )
				foreach( $cols as $col=>$value )
				{
					if ( isset( $sheet->calcGrid[$row][$col] ) )
					{
						$formula = "=" . $sheet->calcGrid[$row][$col];
						$out->writeFormula( $row, $col, utf8_decode( $formula ) );
					}
					else
						$out->write( $row, $col, $this->encoding->convert_encoding ( $value ) );

					$width = $height = 1;
					if ( is_array( $sheet->cellInfo[$row][$col] ) )
						extract( $sheet->cellInfo[$row][$col] );

					if ( $width != 1 || $height != 1 )
					{
						$out->mergeCells( $row, $col, $row + $height - 1, $col + $width - 1 );
					}
				}
		}

		$book->close();
	}

	// name {{{2
	function name()
	{
		return "MS Excel File";
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_LOAD_DATA | TIKISHEET_LOAD_CELL | TIKISHEET_SAVE_CALC | TIKISHEET_SAVE_DATA ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "0.1-dev";
	}
 } // }}}1

/** TikiSheetOpenOfficeHandler {{{1
 * Class to generate OpenOffice sxc documents.
 */
class TikiSheetOpenOfficeHandler extends TikiSheetDataHandler
{
	/** Constructor {{{2
	 * Does nothing special.
	 */
	function TikiSheetOpenOfficeHandler( $file = "php://stdout" )
	{
	}

	// _save {{{2
	function _save( &$sheet )
	{
		// Get rid of debug output
		ob_start();

		APIC::import("org.apicnet.io.OOo.objOOo.OOoTable");
		$OOoCalc = APIC::loadClass("org.apicnet.io.OOo.OOoDoc");

		$OOoCalc->newCalc();
		$OOoCalc->setName("export.sxc");
		$OOoCalc->meta->setCreator("TikiSheet");
		$OOoCalc->meta->setTitle("TikiSheet Export");

		$OOoCalc->content->addFeuille();

		foreach( $sheet->dataGrid as $rowIndex=>$row )
			foreach( $row as $columnIndex=>$value )
				$OOoCalc->content->addcellData($rowIndex + 1, $columnIndex + 1, array("DATA" => $value));

		$OOoCalc->save();
		$OOoCalc->close();
		ob_end_clean();

		$OOoCalc->download();

		return true;
	}

	// name {{{2
	function name()
	{
		return "OpenOffice.org";
	}


	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "0.1-dev";
	}
} // }}}1

/** TikiSheetWikiTableHandler {{{1
 * Class that stores the sheet representation in a
 * standard text file as a serialized PHP object.
 */
class TikiSheetWikiTableHandler extends TikiSheetDataHandler
{
	var $pageName;

	/** Constructor {{{2
	 * Initializes the the serializer on a wiki page
	 * @param $file The name of the wiki page to perform actions on.
	 */
	function TikiSheetWikiTableHandler( $pageName )
	{
		$this->pageName = $pageName;
	}

	// _load {{{2
	function _load( &$sheet )
	{
		global $tikilib;

		$result = $tikilib->query( "SELECT `data` FROM `tiki_pages` WHERE `pageName` = ?", array( $this->pageName ) );
		if ( $row = $result->fetchRow() )
		{
			$tables = $this->getRawTables( $row['data'] );

			$row = 0;
			foreach( $tables as $table )
			{
				$table = explode( "\n", $table );

				foreach( $table as $line )
				{
					$line = explode( '|', trim( $line ) );

					foreach( $line as $col => $value )
					{
						$sheet->initCell( $row, $col );
						$sheet->setValue( $value );
						if ( isset($value) ) {
							if (preg_match("/^::(.*)::$/", $value, $matches)) {
								$sheet->setClass($sheet->getClass()." styleCenter");
								$value = $matches[1];
								$sheet->setValue( $value );
							}
							if (preg_match("/^__(.*)__$/", $value, $matches)) {
								$sheet->setClass($sheet->getClass()." styleBold");
								$value = $matches[1];
								$sheet->setValue( $value );
							}
							if (preg_match("/^''(.*)''$/", $value, $matches)) {
								$sheet->setClass($sheet->getClass()." styleItalic");
								$value = $matches[1];
								$sheet->setValue( $value );
							}
							if (strlen( $value )) {
								if ($value[0] == '=' ) {
									$sheet->setCalculation( substr($value, 1) );
								}
							}
						}
						$sheet->setSize( 1, 1 );
					}
					++$row;
				}
			}

			return true;
		}
		else
			return false;
	}

	/** getRawTables {{{2
	 * Returns an array containing all table-like structures
	 * in the wiki-content.
	 */
	function getRawTables( $data )
	{
		$pos = 0;
		$tables = array();
		while( true ) // Keep looping
		{
			if ( ( $begin = strpos( $data, '||', $pos ) ) === false ) break;;
			if ( ( $end = strpos( $data, '||', $begin + 2 ) ) === false ) break;

			$pos = $end + 2;

			$content = substr( $data, $begin + 2, $end - $begin - 2 );

			if ( strpos( $content, '|' ) !== false )
				$tables[] = $content;
		}


		return $tables;
	}

	// name {{{2
	function name()
	{
		return "CSV File";
	}

	// supports {{{2
	function supports( $type )
	{
		return ( TIKISHEET_LOAD_DATA & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "0.1-dev";
	}
 } // }}}1

/** TikiSheetOutputHandler {{{1
 * Class to output the data sheet as a standard HTML table.
 * Importing is not supported.
 */
class TikiSheetOutputHandler extends TikiSheetDataHandler
{
	var $heading;
	var $parseOutput;

	/** Constructor {{{2
	 * Identifies the caption of the table if it applies.
	 * @param $heading 			The heading
	 * @param $parseOutput		Parse wiki markup in cells if parseValues=y in sheet layout
	 */
	function TikiSheetOutputHandler( $heading = null, $parseOutput = true )
	{
		$this->heading = $heading;
		$this->parseOutput = $parseOutput;
	}

	// _save {{{2
	function _save( &$sheet )
	{
//		if ( $sheet->headerRow + $sheet->footerRow > $sheet->getRowCount() )
//			return false;

		$beginRow = $sheet->getRangeBeginRow();
		$endRow = $sheet->getRangeEndRow();

		$beginCol = $sheet->getRangeBeginCol();
		$endCol = $sheet->getRangeEndCol();

		if ($beginRow > -1 &&
			$beginRow == $endRow - 1 &&
			$beginCol == $endCol - 1
		) {
			if ( isset( $sheet->dataGrid[$beginRow][$beginCol] ) ) {
				$data =  $sheet->dataGrid[$beginRow][$beginCol];
				if ($sheet->parseValues == 'y' && mb_ereg_match('[^A-Za-z0-9\s]', $data)) {	// needs to be multibyte regex here
					global $tikilib;
					$data = $tikilib->parse_data($data, array('suppress_icons' => true));
				}
				$this->output = $data;
				return true;
			}
		}

		$class = empty( $sheet->cssName ) ? "" : " class='{$sheet->cssName}'";
		$id = empty( $sheet->id ) ? '' : " data-id='{$sheet->id}'";
		$title = " title='" . htmlspecialchars($sheet->name(), ENT_QUOTES) . "'";
		$sub = $sheet->isSubSheet ? ' style="display:none;"' : '';
		$type = (!empty($sheet->type) ? ' data-type="'.$sheet->type.'" ' : '');

		$this->output = "<table" . $class . $id . $sub . $title . $type . ">\n";

		if ( !is_null( $this->heading ) )
			$this->output .= "	<caption>{$this->heading}</caption>\n";

		if ( $sheet->headerRow > 0 && $beginRow < 0 )
		{
			$this->output .= "	<thead>\n";
			$this->drawRows( $sheet );
			$this->output .= "	</thead>\n";
		}

		$this->output .= "	<colgroup>\n";
		$this->drawCols( $sheet );
		$this->output .= "	</colgroup>\n";

		$this->output .= "	<tbody>\n";
		$this->drawRows( $sheet );
		$this->output .= "	</tbody>\n";

		if ( $sheet->footerRow > 0 && $beginRow < 0 )
		{
			$this->output .= "	<tfoot>\n";
			$this->drawRows( $sheet );
			$this->output .= "	</tfoot>\n";
		}

		$this->output .= "</table>\n";
		return true;
	}

	/** drawRows {{{2
	 * Draws out a defined set of rows from the sheet.
	 * @param $sheet The data container.
	 * @param $begin The index of the begining row. (included)
	 * @param $end The index of the end row (excluded)
	 */
	function drawRows( &$sheet )
	{
		global $sheetlib;

		$beginRow = $sheet->getRangeBeginRow();
		$endRow = $sheet->getRangeEndRow();

		$beginCol = $sheet->getRangeBeginCol();
		$endCol = $sheet->getRangeEndCol();

		for( $i = $beginRow; $i < $endRow; $i++ )
		{
			$td = "";
			$trStyleHeight = "";
			$trHeight = "20px";
			$trHeightIsSet = false;

			for( $j = $beginCol; $j < $endCol; $j++ )
			{
				$width = $height = '';
				if (!empty($sheet->cellInfo[$i][$j])) {
					extract( $sheet->cellInfo[$i][$j] );
				}

				$append = ' id="cell_c'.($j+1).'_r'.($i+1).'"';

				if ( $width > 1 )
					$append .= " colspan='{$width}'";

				if ( $height > 1 )
					$append .= " rowspan='{$height}'";

				if (!empty($sheet->calcGrid[$i][$j])) {
					$append .= ' formula="='.str_replace('"', "'", $sheet->calcGrid[$i][$j]).'"';
				}

				if ( isset( $sheet->dataGrid[$i][$j] ) )
					$data = $sheet->dataGrid[$i][$j];
				else
					$data = '';

				$format = $sheet->cellInfo[$i][$j]['format'];
				if ( !empty( $format ) )
					$data = TikiSheetDataFormat::$format( $data );

				$style = $sheet->cellInfo[$i][$j]['style'];
				if ( !empty( $style ) ) {
					//we have to sanitize the css style here
					$tdStyle = "";
					$color = $sheetlib->get_attr_from_css_string($style, "color", "");
					$bgColor = $sheetlib->get_attr_from_css_string($style, "background-color", "");
					$tdHeight = '';

					if ($trHeightIsSet == false) {
						$trHeight = $sheetlib->get_attr_from_css_string($style, "height", "20px");
						$trHeightIsSet = true;
					}

					if ($color) {
						$tdStyle .= "color:$color;";
					}
					if ($bgColor) {
						$tdStyle .= "background-color:$bgColor;";
					}

					$tdHeight = $trHeight;
					if ($tdHeight) {
						$tdStyle .= "height:$tdHeight;";
						$append .= " height='".str_replace("px", "", $tdHeight)."'";
					}

					$append .= " style='$tdStyle'";
				}

				$class = $sheet->cellInfo[$i][$j]['class'];
				if ( !empty( $class ) )
					$append .= ' class="'.$class.'"';

				if ($this->parseOutput && $sheet->parseValues == 'y') {
					global $tikilib;
					// only parse if we have non-alphanumeric or space chars
					if (mb_ereg_match('[^A-Za-z0-9\s]', $data)) {	// needs to be multibyte regex here
						$data = $tikilib->parse_data($data, array('suppress_icons' => true));
					}
					if (strpos($data, '<p>') === 0) {	// remove containing <p> tag
						$data = substr($data, 3);
						if (strrpos($data, '</p>') === strlen($data) - 4) {
							$data = substr($data, 0, -4);
						}
					}
				}
				$td .= "			<td".$append.">$data</td>\n";
			}

			if (!empty($td)) {
				$this->output .= "		<tr style='height: $trHeight;' height='".str_replace("px", "", $trHeight)."'>\n";
				$this->output .= $td;
				$this->output .= "		</tr>\n";
			}
		}
	}

	/** drawCols {{{2
	 * Draws out a defined set of rows from the sheet.
	 * @param $sheet The data container.
	 * @param $begin The index of the begining row. (included)
	 * @param $end The index of the end row (excluded)
	 */
	function drawCols( &$sheet )
	{
		global $sheetlib;
		$beginCol = $sheet->getRangeBeginCol();
		$endCol = $sheet->getRangeEndCol();
		for( $i = $beginCol; $i < $endCol; $i++ )
		{
			$style = $sheet->cellInfo[0][$i]['style'];
			$width = $sheetlib->get_attr_from_css_string($style, "width", "118px");
			$this->output .= "<col style='width: $width;' width='$width' />\n";
		}
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_SAVE_CELL | TIKISHEET_SAVE_FORMAT ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "1.0";
	}
} // }}}1

/** TikiSheetLabeledOutputHandler {{{1
 * Class to output the data sheet as a standard HTML table.
 * Importing is not supported.
 */
class TikiSheetLabeledOutputHandler extends TikiSheetDataHandler
{
	/** Constructor {{{2
	 */
	function TikiSheetLabeledOutputHandler()
	{
	}

	// _save {{{2
	function _save( &$sheet )
	{
		$this->output = "<table class=\"default\">\n";

		$this->output .= "	<thead>\n";
		$this->output .= "		<tr><th></th>\n";

		$prev = 'A';
		for( $j = 0; $sheet->getColumnCount() > $j; $j++ )
		{
			$this->output .= "			<th>$prev</th>\n";
			$prev = $sheet->increment( $prev );
		}

		$this->output .= "		</tr>\n";
		$this->output .= "	</thead>\n";

		$this->output .= "	<tbody>\n";
		$this->drawRows( $sheet );
		$this->output .= "	</tbody>\n";

		$this->output .= "</table>\n";

		return true;
	}

	/** drawRows {{{2
	 * Draws out a defined set of rows from the sheet.
	 * @param $sheet The data container.
	 * @param $begin The index of the begining row. (included)
	 * @param $end The index of the end row (excluded)
	 */
	function drawRows( &$sheet )
	{
		global $sheetlib;

		$beginRow = $sheet->getRangeBeginRow();
		$endRow = $sheet->getRangeEndRow();

		$beginCol = $sheet->getRangeBeginCol();
		$endCol = $sheet->getRangeEndCol();

		for( $i = $beginRow; $i <  $endRow; $i++ )
		{
			$trHeight = "20px";
			$td = '';
			for( $j = $beginCol; $j < $endCol; $j++ )
			{
				$width = $height = "";
				extract( $sheet->cellInfo[$i][$j] );
				$append = "";

				if ( empty( $width ) || empty( $height ) || $width == 0 || $height == 0 )
					continue;

				if ( $width > 1 )
					$append .= " colspan='{$width}'";

				if ( $height > 1 )
					$append .= " rowspan='{$height}'";

				if ( isset( $sheet->dataGrid[$i][$j] ) )
					$data = $sheet->dataGrid[$i][$j];
				else
					$data = '';

				$format = $sheet->cellInfo[$i][$j]['format'];
				if ( !empty( $format ) )
					$data = TikiSheetDataFormat::$format( $data );

				$style = $sheet->cellInfo[$i][$j]['style'];
				if ( !empty( $style ) ) {
					$append .= " style='{$style}'";

					$trHeight = $sheetlib->get_attr_from_css_string($style, "height", "20px");
				}

				$class = $sheet->cellInfo[$i][$j]['class'];
				if ( !empty( $class ) )
					$append .= " class='{$class}'";

				$td .= "			<td$append>$data</td>\n";
			}

			$tr = "		<tr  style='height: $trHeight;' height='$trHeight'><th>" . ($i + 1) . "</th>\n";
			$tr .= $td;
			$tr .= "	</tr>\n";

			$this->output .= $tr;
		}
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_SAVE_CELL | TIKISHEET_SAVE_FORMAT ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "1.0";
	}
} // }}}1

/** TikiSheetHTMLTableHandler
 * Class that imports a sheet from an HTML table
 * Designed to be used with jQuery.sheet.save_sheet
 */
class TikiSheetHTMLTableHandler extends TikiSheetDataHandler
{

	var $data;

	/** Constructor {{{2
	 * Initializes the the serializer on a wiki page
	 * @param $file The name of the wiki page to perform actions on.
	 */
	function TikiSheetHTMLTableHandler( $inHtml )
	{
		$this->data = $inHtml;
	}

	// _load {{{2
	function _load( &$sheet )
	{

		$d = $this->data;

		$rows = (int) $d->metadata->rows;
		$cols = (int) $d->metadata->columns;

		for ($r = 0; $r < $rows; $r++) {
			for ($c = 0; $c < $cols; $c++) {
				$ri = 'r'.$r;
				$ci = 'c'.$c;
				if (isset($d->data->$ri->$ci->value)) {
					$val = $d->data->$ri->$ci->value;
				} else {
					$val = '';
				}

				$sheet->initCell( $r, $c );
				if (isset($d->data->$ri->$ci->width) || isset($d->data->$ri->$ci->height)) {
					$sheet->setSize( isset($d->data->$ri->$ci->width) ? $d->data->$ri->$ci->width : 1,  isset($d->data->$ri->$ci->height) ? $d->data->$ri->$ci->height : 1 );
				} else {
					$sheet->setSize( 1, 1 );
				}
				$sheet->setValue( $val );
				if (isset($d->data->$ri->$ci->formula)) {
					$formula = substr($d->data->$ri->$ci->formula, 1, strlen($d->data->$ri->$ci->formula)-1);
					if (!empty($formula)) {
						$sheet->setCalculation($formula);
					}
				}
				if (isset($d->data->$ri->$ci->stl)) {
					$style = $d->data->$ri->$ci->stl;
					if (!empty($style)) {
						$sheet->setStyle($style);
					}
				}
				if (isset($d->data->$ri->$ci->cl)) {
					$class = $d->data->$ri->$ci->cl;
					if (!empty($class)) {
						$sheet->setClass($class);
					}
				}

			}
		}

		return true;
	}

	// name {{{2
	function name()
	{
		return "HTML Table";
	}

	// supports {{{2
	function supports( $type )
	{
		return ( TIKISHEET_LOAD_DATA & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "1.0";
	}
 } // }}}1
