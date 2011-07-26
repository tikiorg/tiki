<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
define( 'INITIAL_ROW_COUNT',		3 );
define( 'INITIAL_COL_COUNT',		2 );

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
	var $sheetId;
	var $isSubSheet;
	var $instance;
	
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
	
	function TikiSheetFile ( $file, $type )
	{
		$grid = new TikiSheet();
		$grid->file = $file;
		$grid->type = $type;
		return $grid;
	}
	
	/** TikiSheet {{{2
	 * Initializes the data container.
	 */
	function TikiSheet( $sheetId = 0, $isSubSheet = false )
	{
		static $instanceCounter = -1;
		
		$this->sheetId = $sheetId;
		$this->isSubSheet = $isSubSheet;
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
		
		if (!$this->isSubSheet) {
			$instanceCounter++;
	 		$this->instance = $instanceCounter;
		}
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
		if( !preg_match( "/^([A-Z]+)([0-9]+)$/", $id, $parts ) )
			return false;

		if( !isset( $this->indexes[ $parts[1] ] ) )
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
		if( !preg_match( "/^([A-Z]+)([0-9]+)$/", $id, $parts ) )
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
		if( isset( $this->dataGrid[$rowIndex][$columnIndex] ) && !isset( $sheet->dataGrid[$rowIndex][$columnIndex] ) )
			return false;

		if( isset( $this->calcGrid[$rowIndex][$columnIndex] ) && !isset( $sheet->calcGrid[$rowIndex][$columnIndex] ) )
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
	function getTableHtml( $incsubs = true, $date = null, $fromDb = true )
	{
		global $prefs, $sheetlib;
		
		$handler = new TikiSheetOutputHandler(null, ($this->parseValues == 'y' && $_REQUEST['parse'] != 'n'));
		ob_start();
		$this->export($handler);
		$data = ob_get_contents();
		ob_end_clean();
		
		if ($incsubs && !$this->isSubSheet && $fromDb) {
			$subsheets = $sheetlib->get_sheet_subsheets($this->sheetId);
			if (count($subsheets) > 0) {
				foreach ($subsheets as $sub) {
					$handler = new TikiSheetDatabaseHandler($sub['sheetId'], $date );
					$subsheet = new TikiSheet($sub['sheetId'], true);
					$subsheet->import($handler);
					$data .= $subsheet->getTableHtml( false );
				}
			}
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

		$this->rowCount = $maxRow + 1;
		$this->columnCount = $maxCol + 1;

		$base = array( 'width' => 1, 'height' => 1, 'format' => null, 'style' => '', 'class' => '' );
		for( $y = 0; $this->rowCount > $y; $y++ )
			for( $x = 0; $this->columnCount > $x; $x++ )
			{
				if( !isset( $this->dataGrid[$y] ) )
					$this->dataGrid[$y] = array();
				if( !isset( $this->calcGrid[$y] ) )
					$this->calcGrid[$y] = array();
				if( !isset( $this->cellInfo[$y] ) )
					$this->cellInfo[$y] = array();

				if( !isset( $this->dataGrid[$y][$x] ) )
					$this->dataGrid[$y][$x] = '';
				if( !isset( $this->calcGrid[$y][$x] ) )
					$this->calcGrid[$y][$x] = '';
				if( !isset( $this->cellInfo[$y][$x] ) )
					$this->cellInfo[$y][$x] = $base;

				
				$this->cellInfo[$y][$x] = array_merge( $base, $this->cellInfo[$y][$x] );
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
		if( $addIndex )
			$total += $row[$localMax]['width'] - 1;

		if( $total > $maxCol )
			$maxCol = $total;

		if( $addIndex )
		{
			foreach( $row as $info )
			{
				$total = $rowIndex +  $info['height'] - 1;

				if( $total > $maxRow )
					$maxRow = $total;
			}
		}
		else
		{
			if( $rowIndex > $maxRow )
				$maxRow = $rowIndex;
		}
	}

	/** getColumnCount {{{2
	 * Returns the column count.
	 */
	function getColumnCount()
	{
		return $this->columnCount;
	}

	/** getRange {{{2
	 * Reutrns an array containing the values located in
	 * a given range (ex: A1:B9)
	 */
	function getRange( $range )
	{
		if( preg_match( '/^([A-Z]+)([0-9]+):([A-Z]+)([0-9]+)$/', strtoupper($range), $parts ) )
		{
			$beginRow = $parts[2] - 1;
			$endRow = $parts[4] - 1;
			$beginCol = $this->getColumnNumber( $parts[1] );
			$endCol = $this->getColumnNumber( $parts[3] );

			if( $beginRow > $endRow )
			{
				$a = $endRow;
				$endRow = $beginRow;
				$beginRow = $a;
			}
			if( $beginCol > $endCol )
			{
				$a = $endCol;
				$endCol = $beginCol;
				$beginCol = $a;
			}

			$data = array();
			for( $row = $beginRow; $endRow + 1 > $row; $row++ )
				for( $col = $beginCol; $endCol + 1 > $col; $col++ )
					if( isset( $this->dataGrid[$row] ) && isset( $this->dataGrid[$row][$col] ) )
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
		if( preg_match( '/^([A-Z]+)([0-9]+):([A-Z]+)([0-9]+)$/', strtoupper($range), $parts ) ) {
			$this->rangeBeginRow = $parts[2] - 1;
			$this->rangeEndRow = $parts[4] - 1;
			$this->rangeBeginCol = $this->getColumnNumber( $parts[1] );
			$this->rangeEndCol = $this->getColumnNumber( $parts[3] );
		}
	}
	
	/** getRowCount {{{2
	 * Returns the row count.
	 */
	function getRowCount()
	{
		return $this->rowCount;
	}
	
	function getTitle()
	{
		global $sheetlib;
		$info = $sheetlib->get_sheet_info($this->sheetId);
		return $info['title'];
	}
	
	function getInstance()
	{
		return $this->instance;
	}
	
	/** import {{{2
	 * Fills the content of the calculation sheet with
	 * data from the given handler.
	 * @param $handler The format handler.
	 * @return True on success.
	 */
	function import( &$handler )
	{
		$this->dataGrid = array();
		$this->calcGrid = array();
		$this->cellInfo = array();
		$this->errorFlag = false;
		
		set_error_handler( array( &$this, "error_handler" ) );
		if( !$handler->_load( $this ) || $this->errorFlag )
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
		if( empty( $val ) )
			return substr( $this->COLCHAR, 0, 1 );

		$n = strpos( $this->COLCHAR, substr( $val, -1 ) ) + 1;

		if( $n < strlen( $this->COLCHAR ) )
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
		if( $col === null )
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
		if( empty( $format ) || !method_exists( new TikiSheetDataFormat, $format ) ) $format = null;
		$this->cellInfo[$this->usedRow][$this->usedCol]['format'] = $format;
	}
	
	/** setSize {{{2
	 * Sets the size of the last initialized cell.
	 * @param $width The cell's column span.
	 * @param $height The cell's row span.
	 */
	function setSize( $width, $height )
	{
		$this->cellInfo[$this->usedRow][$this->usedCol] = array( "width" => $width, "height" => $height, "style" => "", "class" => "");

		for( $y = $this->usedRow; $this->usedRow + $height > $y; $y++ )
			for( $x = $this->usedCol; $this->usedCol + $width > $x; $x++ )
				if( !($y == $this->usedRow && $x == $this->usedCol) )
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
	
	function parseCsv( &$sheet )
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
} // }}}1

/** TikiSheetFormHandler {{{1
 * Data handler to handle transactions from the web form.
 * The export format of the class is the actual form with
 * the required initialization. This class should support
 * all features.
 */
class TikiSheetFormHandler extends TikiSheetDataHandler
{
	var $data;
	
	/** Constructor {{{2
	 * Assigns the right form data source to the object.
	 * The most common value will be post as get has size
	 * restrictions.
	 * @param $method The form method used.
	 */
	function TikiSheetFormHandler( $method = "post" )
	{
		if( $method == "post" )
			$this->data = $_POST;
		else
			$this->data = $_GET;
	}
	
	// _load {{{2
	function _load( &$sheet )
	{
		foreach( $this->data as $key=>$value )
		{
			if( $sheet->initCell( $key ) )
			{
				$this->convert( $value, $v, $c, $w, $h, $f, $stl, $cl );
				$sheet->setValue( $v );
				$sheet->setCalculation( $c );
				$sheet->setSize( $w, $h );
				$sheet->setFormat( $f );
				$sheet->setStyle( $stl );
				$sheet->setClass( $cl );
			}
		}

		return true;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		global $prefs, $headerlib;
		
		$js = '';

		$js .= "	var g;\n";
		$js .= "	function initGrid()\n";
		$js .= "	{\n";
		
		$js .= "		g = new Grid( document.getElementById( 'Grid' ) );\n";
		
		for( $i = 0; $sheet->getRowCount() > $i; $i++ )
			$js .= "		new Row( g, null );\n";

		for( $i = 0; $sheet->getColumnCount() > $i; $i++ )
			$js .= "		new Column( g, null );\n";
	   
		$js .= "		g.draw();\n";

		$js .= "		var cell;\n";

		for( $y = 0; $sheet->getRowCount() > $y; $y++ )
		{
			for( $x = 0; $sheet->getColumnCount() > $x; $x++ )
			{
				$calc = str_replace('\n', ' ', $sheet->calcGrid[$y][$x]);
				$value = str_replace('\n', ' ', $sheet->dataGrid[$y][$x]);
				$width = $sheet->cellInfo[$y][$x]['width'];
				$height = $sheet->cellInfo[$y][$x]['height'];
				$format = $sheet->cellInfo[$y][$x]['format'];
				$style = $sheet->cellInfo[$y][$x]['style'];
				$class = $sheet->cellInfo[$y][$x]['class'];
				
				$calc = addslashes( $calc );
				$value = addslashes( $value );

				if( empty( $calc ) )
					$calc = $value;
				else
					$calc = "=" . $calc;

				if( empty( $format ) )
					$format = 'null';
				else
					$format = "'$format'";

				$js .= "		cell = g.getIndexCell( $y, $x );\n";
				$js .= "		cell.value = '{$calc}';\n";
				$js .= "		cell.endValue = '{$value}';\n";
				$js .= "		cell.format = {$format};\n";
				$js .= "		cell.style = {$style};\n";
				$js .= "		cell.class = {$class};\n";

				if( !empty( $width ) && !empty( $height ) && ($width != 1 || $height != 1) )
					$js .= "		cell.changeSize( {$height}, {$width} );\n";
			}
		}


		if ($prefs['feature_contribution'] == 'y') {
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			$contributions = $contributionlib->list_contributions();
			for ($i = $contributions['cant'] - 1; $i >= 0; -- $i) {
				$name = str_replace("'", "\\'", $contributions['data'][$i]['name']);
				$j = $contributions['data'][$i]['contributionId'];
				$js .= "		g.addContribution($j, '$name');\n";
			}
		}
	   
		$js .= "		g.draw();\n";
		$js .= "		g.refresh();\n";

		$js .= "	}\n";
		
		$headerlib->add_js($js);

		return true;
	}

	/** convert {{{2
	 * Converts the form cell format to readable data.
	 * [value]=[calc]<<<[width],[height]>>>format_name
	 * @param $formString The direct value from the form.
	 * @param $value Will contain the end value.
	 * @param $calc Will contain the calculation without the equal.
	 * @param $width Will contain the colspan.
	 * @param $height Will contain the rowspan.
	 * @param $format The format to be used to render the cell
	 *			indicates there is no limit.
	 * @param $style The cells html styles.
	 * @param $class The cells html classes.
	 * 
	 * @return False on error.
	 */
	function convert( $formString, &$value, &$calc, &$width, &$height, &$format, &$style, &$class )
	{
		$value = "";
		$calc = "";
		$width = 1;
		$height = 1;
		
		if( preg_match( "/^(.*[^\\\\])?=(.*[^\\\\])?<<<([0-9]+),([0-9]+)>>>([a-z0-9_]*)$/", stripslashes($formString), $parts ) )
		{
			$value = str_replace( "\\=", "=", $parts[1] );
			$calc = trim( $parts[2] );
			$width = $parts[3];
			$height = $parts[4];
			$format = $parts[5];
			$style = $parts[6];
			$class = $parts[7];

			return true;
		}
		else
			return false;
	}
	
	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_SAVE_CALC | TIKISHEET_SAVE_CELL | TIKISHEET_SAVE_FORMAT | TIKISHEET_LOAD_DATA | TIKISHEET_LOAD_CALC | TIKISHEET_LOAD_CELL | TIKISHEET_LOAD_FORMAT ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "1.1-test";
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
		if( $file = @fopen( $this->file, "r" ) )
		{
			$data = @fread( $file, filesize( $this->file ) );

			@fclose( $file );

			$data = unserialize( $data );

			if( $data === false )
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

		if( $this->file == "php://stdout" )
		{
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=export.tws");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
			echo $data;
			return true;
		}
		else
		{
			if( $file = @fopen( $this->file, "w" ) )
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
	var $file;
	var $lineLen;
	
	/** Constructor {{{2
	 * Initializes the the serializer on a file.
	 * @param $file The file path to save or load from.
	 */
	function TikiSheetCSVHandler( $file = "php://stdout", $inputEncoding = '', $outputEncoding = '', $lineLen = 1024 )
	{
		$this->file = $file;
		$this->lineLen = $lineLen;
		$this->data = strip_tags( file_get_contents($this->file) );
        $this->encoding = new Encoding ($inputEncoding, $outputEncoding);
	}

	// _load {{{2
	function _load( &$sheet )
	{
		return $this->parseCSV( $sheet );
	}

	// _save {{{2
	function _save( &$sheet )
	{
		$total = array();
        
        ksort ($sheet->dataGrid);
		foreach( $sheet->dataGrid as $row )
			if( is_array( $row ) )
            {
                ksort ($row);
				$total[] = implode( ",", $row );
            }

		if( is_array( $total ) )
			$total = implode( "\n", $total );
            
        $total = $this->encoding->convert_encoding ($total);

		if( $this->file == "php://stdout" )
		{
			header("Content-type: text/comma-separated-values");
			header("Content-Disposition: attachment; filename=export.csv");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
			
			echo $total;

			return true;
		}
		else
		{
			if( $file = @fopen( $this->file, "w" ) )
			{
				if( !@fwrite( $file, $total ) )
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
		return "CSV File";
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_LOAD_DATA ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "1.0-test";
	}
 } // }}}1
 
 /** TikiSheetCSVHandler {{{1
 * Class that stores the sheet representation in a
 * standard text file as a serialized PHP object.
 */
class TikiSheetFileGalleryCSVHandler extends TikiSheetDataHandler
{
	var $file;
	
	/** Constructor {{{2
	 * Initializes the the serializer on a file.
	 * @param $file The file path to save or load from.
	 */
	function TikiSheetFileGalleryCSVHandler( $fileId = 0 , $maxrows = 300, $maxcols = 26)
	{
		include_once('lib/filegals/filegallib.php');
		global $prefs, $headerlib, $filegallib;
		$fileInfo = $filegallib->get_file_info( $fileId );
		
		if ($fileInfo['filetype'] != 'text/csv') return false;
		
		$this->data = $fileInfo['data'];
		$this->maxrows = $maxrows;
		$this->maxcols = $maxcols;
		$this->truncated = false;
	}
	
	// _load {{{2
	function _load( &$sheet )
	{
		return $this->parseCsv($sheet);
	}

	// _save {{{2
	function _save( &$sheet )
	{
		
	}

	// name {{{2
	function name()
	{
		return "CSV File from Tiki Gallery";
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_LOAD_DATA ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "1.0-test";
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
		return $this->parseCsv( $sheet );
	}

    // _save {{{2
    function _save( &$sheet )
    {
        $total = array(); 
        foreach( $sheet->data as $row ) 
        {
            $total[] = $this->fputcsvexcel( $row ,';','"', $sheet->metadata->columns);
        }
        //print_r($sheet);
        if( is_array( $total ) )
            $total = implode( "\n", $total );
            
        $total = $this->encoding->convert_encoding ($total);

        if( $this->file == "php://stdout" )
        {
            header("Content-type: text/comma-separated-values");
            header("Content-Disposition: attachment; filename=export.csv");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");
            
            echo $total;

            return true;
        }
        else
        {
            if( $file = @fopen( $this->file, "w" ) )
            {
                if( !@fwrite( $file, $total ) )
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
    
    function fputcsvexcel( $row, $fd=';', $quot='"', $limit)
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
	var $sheetId;
	var $readDate;
	
	/** Constructor {{{2
	 * Assigns a sheet ID to the handler.
	 * @param $sheetId The ID of the sheet in the database.
	 * @param $db The database link to use.
	 */
	function TikiSheetDatabaseHandler( $sheetId , $date = null )
	{
		$this->sheetId = $sheetId;
		$this->readDate = ( $date ? $date : time() );
	}

	// _load {{{2
	function _load( &$sheet )
	{
		global $tikilib;
		
		$result = $tikilib->query( "
			SELECT `rowIndex`, `columnIndex`, `value`, `calculation`, `width`, `height`, `format`, `style`, `class`, `user`
			FROM `tiki_sheet_values`
			WHERE
				`sheetId` = ? AND 
				? >= `begin` AND 
				( 
					`end` IS NULL OR
					`end` > ? )
		", array( $this->sheetId, (int)$this->readDate, (int)$this->readDate ) );

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
		", array( $this->sheetId, (int)$this->readDate, (int)$this->readDate ) );

		if( $row = $result2->fetchRow() )
		{
			extract( $row );
			$sheet->configureLayout( $className, $headerRow, $footerRow, $parseValues );
		}

		return true;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		global $tikilib, $user, $prefs;
		// Load the current database state {{{3
		$current = new TikiSheet;
		$handler = new TikiSheetDatabaseHandler( $this->sheetId );
		$current->import( $handler );

		// Find differences {{{3
		$mods = array();
		for( $row = 0; $sheet->getRowCount() > $row; $row++ )
		{
			for( $col = 0; $sheet->getColumnCount() > $col; $col++ )
			{
				if( !$sheet->equals( $current, $row, $col ) )
					$mods[] = array( "row" => $row, "col" => $col );
			}
		}

		$stamp = time();

		$inserts = array();
		$updates = array();
		$updates[] = $stamp;
		$updates[] = $this->sheetId;

		// Update the database {{{3
		if( is_array( $mods ) )
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
				//if( !$sheet->isEmpty( $row, $col ) )
				$inserts[] = array( (int)$this->sheetId, $stamp, $row, $col, $value, $calc, $width, $height, $format, $style, $class, $user );

			}
		}

		$updates[] = $sheet->getRowCount();
		$updates[] = $sheet->getColumnCount();

		$conditions = str_repeat( "( rowIndex = ? AND columnIndex = ? ) OR ", ( count($updates) - 4 ) / 2 );
		if ($prefs['feature_actionlog'] == 'y') { // must keep the previous value to do the difference
			$query = "SELECT `rowIndex`, `columnIndex`, `value`, `style`, `class` FROM `tiki_sheet_values` WHERE `sheetId` = ? AND  `end` IS NULL";
			$result = $tikilib->query($query, array($this->sheetId));
			$old = array();
			while( $row = $result->fetchRow() ) {
				$old[$row['rowIndex'].'-'.$row['columnIndex']] = $row['value'];
				$old[$row['rowIndex'].'-'.$row['columnIndex']]['style'] = $row['style'];
				$old[$row['rowIndex'].'-'.$row['columnIndex']]['class'] = $row['class'];
			}
		}
			
		$tikilib->query( "UPDATE `tiki_sheet_values` SET `end` = ?  WHERE `sheetId` = ? AND `end` IS NULL AND ( {$conditions}`rowIndex` >= ? OR `columnIndex` >= ? )", $updates );

		if( count( $inserts ) > 0 )
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
				$contributionlib->assign_contributions($_REQUEST['contributions'], $this->sheetId, 'sheet', '', '', '');
			}			
			if (isset($_REQUEST['contributions']))
				$logslib->add_action('Updated', $this->sheetId, 'sheet', "add=$add&amp;del=$del&amp;sheetId=".$this->sheetId, '', '', '', '',  $_REQUEST['contributions']);
			else
				$logslib->add_action('Updated', $this->sheetId, 'sheet', "add=$add&amp;del=$del&amp;sheetId=".$this->sheetId);
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

		if( !$document->read( $this->file ) )
			return false;

		$data = $document->sheets[0];

		if( is_array( $data['cells'] ) )
			foreach( $data['cells'] as $row=>$cols )
			{
				if( is_array( $cols ) )
					foreach( $cols as $col=>$value )
					{
						$sheet->initCell( $row - 1, $col - 1 );
						
						$info = $data['cellsInfo'][$row][$col];

						if( !isset( $info['rowspan'] ) )
							$height = 1;
						else
							$height = $info['rowspan'];
						
						if( !isset( $info['colspan'] ) )
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
			if( is_array( $cols ) )
				foreach( $cols as $col=>$value )
				{
					if( isset( $sheet->calcGrid[$row][$col] ) )
					{
						$formula = "=" . $sheet->calcGrid[$row][$col];
						$out->writeFormula( $row, $col, utf8_decode( $formula ) );
					}
					else
						$out->write( $row, $col, $this->encoding->convert_encoding ( $value ) );

					$width = $height = 1;
					if( is_array( $sheet->cellInfo[$row][$col] ) )
						extract( $sheet->cellInfo[$row][$col] );

					if( $width != 1 || $height != 1 )
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
		if( $row = $result->fetchRow() )
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
			if( ( $begin = strpos( $data, '||', $pos ) ) === false ) break;;
			if( ( $end = strpos( $data, '||', $begin + 2 ) ) === false ) break;

			$pos = $end + 2;

			$content = substr( $data, $begin + 2, $end - $begin - 2 );
			
			if( strpos( $content, '|' ) !== false )
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
//		if( $sheet->headerRow + $sheet->footerRow > $sheet->getRowCount() )
//			return false;

		if ($sheet->getRangeBeginRow() > -1 &&
			$sheet->getRangeBeginRow() == $sheet->getRangeEndRow() &&
			$sheet->getRangeBeginCol() == $sheet->getRangeEndCol()) {
				if( isset( $sheet->dataGrid[$sheet->getRangeBeginRow()][$sheet->getRangeBeginCol()] ) ) {
					$data =  $sheet->dataGrid[$sheet->getRangeBeginRow()][$sheet->getRangeBeginCol()];
					if ($sheet->parseValues == 'y' && mb_ereg_match('[^A-Za-z0-9\s]', $data)) {	// needs to be multibyte regex here
						global $tikilib;
						$data = $tikilib->parse_data($data, array('suppress_icons' => true));
					}
					echo $data;
					return;
				}
			}

		$class = empty( $sheet->cssName ) ? "" : " class='{$sheet->cssName}'";
		$id = empty( $sheet->sheetId ) ? '' : " rel='sheetId{$sheet->sheetId}'";
		$title = " title='" . htmlspecialchars($sheet->getTitle(), ENT_QUOTES) . "'";
		$sub = $sheet->isSubSheet ? ' style="display:none;"' : '';
		echo "<table{$class}{$id}{$sub}{$title}>\n";

		if( !is_null( $this->heading ) )
			echo "	<caption>{$this->heading}</caption>\n";
		
		if( $sheet->headerRow > 0 && $sheet->getRangeBeginRow() < 0 )
		{
			echo "	<thead>\n";
			$this->drawRows( $sheet, 0, $sheet->headerRow );
			echo "	</thead>\n";
		}
		
		echo "	<colgroup>\n";
		$this->drawCols( $sheet, $sheet->getRangeBeginRow() < 0 ? $sheet->headerRow : $sheet->getRangeBeginRow(),
								 $sheet->getRangeEndRow() < 0 ? $sheet->getRowCount() - $sheet->footerRow : $sheet->getRangeEndRow() + 1 );
		echo "	</colgroup>\n";
		
		echo "	<tbody>\n";
		$this->drawRows( $sheet, $sheet->getRangeBeginRow() < 0 ? $sheet->headerRow : $sheet->getRangeBeginRow(),
								 $sheet->getRangeEndRow() < 0 ? $sheet->getRowCount() - $sheet->footerRow : $sheet->getRangeEndRow() + 1 );
		echo "	</tbody>\n";
		
		if( $sheet->footerRow > 0 && $sheet->getRangeBeginRow() < 0 )
		{
			echo "	<tfoot>\n";
			$this->drawRows( $sheet, $sheet->getRowCount() - $sheet->footerRow, $sheet->getRowCount() );
			echo "	</tfoot>\n";
		}

		echo "</table>\n";

		return true;
	}

	/** drawRows {{{2
	 * Draws out a defined set of rows from the sheet.
	 * @param $sheet The data container.
	 * @param $begin The index of the begining row. (included)
	 * @param $end The index of the end row (excluded)
	 */
	function drawRows( &$sheet, $begin, $end )
	{
		global $sheetlib;
		for( $i = $begin; ($end - 1) > $i; $i++ )
		{
			$td = "";
			$trStyleHeight = "";
			$trHeight = "20px";
			$trHeightIsSet = false;
			
			$endCol = $sheet->getRangeEndCol() < 0 ? $sheet->getColumnCount() : $sheet->getRangeEndCol() + 1;
			for( $j = $sheet->getRangeBeginCol(); $endCol > $j; $j++ )
			{
				$width = $height = '';
				if (!empty($sheet->cellInfo[$i][$j])) {
					extract( $sheet->cellInfo[$i][$j] );
				}
				$append = '';

				if( empty( $width ) || empty( $height ) || $width == 0 || $height == 0 )
					continue;

				$append = ' id="cell_c'.($j+1).'_r'.($i+1).'"';
					
				if( $width > 1 )
					$append .= " colspan='{$width}'";

				if( $height > 1 )
					$append .= " rowspan='{$height}'";
				
				if (!empty($sheet->calcGrid[$i][$j])) {
					$append .= ' formula="='.str_replace('"', "'", $sheet->calcGrid[$i][$j]).'"';
				}

				if( isset( $sheet->dataGrid[$i][$j] ) )
					$data = $sheet->dataGrid[$i][$j];
				else
					$data = '';

				$format = $sheet->cellInfo[$i][$j]['format'];
				if( !empty( $format ) )
					$data = TikiSheetDataFormat::$format( $data );
				
				$style = $sheet->cellInfo[$i][$j]['style'];
				if( !empty( $style ) ) {
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
				if( !empty( $class ) )
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
				$td .= "			<td$append>$data</td>\n";
			}
			echo "		<tr style='height: $trHeight;' height='".str_replace("px", "", $trHeight)."'>\n";
			echo $td;
			echo "		</tr>\n";
		}
	}
	
	/** drawCols {{{2
	 * Draws out a defined set of rows from the sheet.
	 * @param $sheet The data container.
	 * @param $begin The index of the begining row. (included)
	 * @param $end The index of the end row (excluded)
	 */
	function drawCols( &$sheet, $begin, $end )
	{
		global $sheetlib;
		$endCol = $sheet->getRangeEndCol() < 0 ? $sheet->getColumnCount() : $sheet->getRangeEndCol();
		for( $j = $sheet->getRangeBeginCol(); $endCol > $j; $j++ )
		{
			$style = $sheet->cellInfo[$begin][$j]['style'];
			$width = $sheetlib->get_attr_from_css_string($style, "width", "118px");
			echo "<col style='width: $width;' width='$width' />\n";
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
		echo "<table class=\"default\">\n";

		echo "	<thead>\n";
		echo "		<tr><th></th>\n";
		
		$prev = 'A';
		for( $j = 0; $sheet->getColumnCount() > $j; $j++ )
		{
			echo "			<th>$prev</th>\n";
			$prev = $sheet->increment( $prev );
		}
			
		echo "		</tr>\n";
		echo "	</thead>\n";
		
		echo "	<tbody>\n";
		$this->drawRows( $sheet, 0, $sheet->getRowCount() );
		echo "	</tbody>\n";
		
		echo "</table>\n";

		return true;
	}

	/** drawRows {{{2
	 * Draws out a defined set of rows from the sheet.
	 * @param $sheet The data container.
	 * @param $begin The index of the begining row. (included)
	 * @param $end The index of the end row (excluded)
	 */
	function drawRows( &$sheet, $begin, $end )
	{
		global $sheetlib;
		
		for( $i = $begin; $end > $i; $i++ )
		{
			$trHeight = "20px";
			for( $j = 0; $sheet->getColumnCount() > $j; $j++ )
			{
				$width = $height = "";
				extract( $sheet->cellInfo[$i][$j] );
				$append = "";

				if( empty( $width ) || empty( $height ) || $width == 0 || $height == 0 )
					continue;

				if( $width > 1 )
					$append .= " colspan='{$width}'";

				if( $height > 1 )
					$append .= " rowspan='{$height}'";

				if( isset( $sheet->dataGrid[$i][$j] ) )
					$data = $sheet->dataGrid[$i][$j];
				else
					$data = '';

				$format = $sheet->cellInfo[$i][$j]['format'];
				if( !empty( $format ) )
					$data = TikiSheetDataFormat::$format( $data );
					
				$style = $sheet->cellInfo[$i][$j]['style'];
				if( !empty( $style ) ) {
					$append .= " style='{$style}'";
					
					$trHeight = $sheetlib->get_attr_from_css_string($style, "height", "20px");
				}
					
				$class = $sheet->cellInfo[$i][$j]['class'];
				if( !empty( $class ) )
					$append .= " class='{$class}'";
					
				$td .= "			<td$append>$data</td>\n";
			}
			
			$tr = "		<tr  style='height: $trHeight;' height='$trHeight'><th>" . ($i + 1) . "</th>\n";
			$tr .= $td;
			$tr .= "	</tr>\n";
			
			echo $tr;
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
	function _load( TikiSheet &$sheet )
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


// Tikiwiki Sheet Library {{{1

class SheetLib extends TikiLib
{
	function get_sheet_info( $sheetId ) // {{{2
	{
		$result = $this->query( "SELECT * FROM `tiki_sheets` WHERE `sheetId` = ?", array( $sheetId ) );

		return $result->fetchRow();
	}

	function get_sheet_layout( $sheetId ) // {{{2
	{
		$result = $this->query( "SELECT `className`, `headerRow`, `footerRow`, `parseValues` FROM `tiki_sheet_layout` WHERE `sheetId` = ? AND `end` IS NULL", array( $sheetId ) );

		return $result->fetchRow();
	}
	
	function add_related_tracker($sheetdId, $trackerId) {
		global $relationlib; require_once('lib/attributes/relationlib.php');
		$relationlib->add_relation("tiki.sheet.tracker", "sheetId", $sheetdId, "trackerId", $trackerId);
	}
	
	function remove_related_tracker($sheetdId, $trackerId) {
		global $relationlib; require_once('lib/attributes/relationlib.php');
		$trackerIds = array();
		foreach($relationlib->get_relations_from("sheetId", $sheetdId, "tiki.sheet.tracker") as $result) {
			if ($result['itemId'] == $trackerId) {
				$relationlib->remove_relation($result['relationId']);
			}
		} 
	}
	
	function get_related_tracker_ids($sheetdId) {
		global $relationlib; require_once('lib/attributes/relationlib.php');
		$trackerIds = array();
		foreach($relationlib->get_relations_from("sheetId", $sheetdId, "tiki.sheet.tracker") as $result) {
			$trackerIds[] = $result['itemId'];
		}
		return $trackerIds;
	}
	
	function get_related_trackers_as_html($sheetId) {
		$trackerHtml = '';
		require_once ('lib/wiki-plugins/wikiplugin_trackerlist.php');
		foreach($this->get_related_tracker_ids($sheetId) as $trackerId) {
			$trackerHtml .= wikiplugin_trackerlist(null, array("trackerId" => $trackerId, "tableassheet" => "y"));
		}
		return $trackerHtml;
	}
	
	function get_sheet_subsheets( $sheetId ) // {{{2
	{
		$result = $this->fetchAll( "SELECT `sheetId` FROM `tiki_sheets` WHERE `parentSheetId` = ?", array( $sheetId ) );

		return $result;
	}
	
	function list_sheets( $offset = 0, $maxRecord = -1, $sort_mode = 'title_desc', $find = '' , $includeChildren = false) // {{{2
	{
		global $user, $tikilib, $userlib;
		switch( $sort_mode )
		{
			case "author_asc":
				$sort = "`author` ASC";
				break;
			case "author_desc":
				$sort = "`author` DESC";
				break;
			case "description_asc":
				$sort = "`description` ASC";
				break;
			case "description_desc":
				$sort = "`description` DESC";
				break;
			case "title_asc":
				$sort = "`title` ASC";
				break;
			case "title_desc":
				$sort = "`title` DESC";
				break;
			default:
				$sort = "`title` ASC";
				break;
		}
		$bindvars = array();
		$mid = '';
		if (!empty($find)) {
			$bindvars[] = "%$find%";
			if (empty($mid))
				$mid = ' WHERE ';
			$mid .= ' `title` like ? ';
		}
		
		if (!$includeChildren) {
			if (!$mid) {
				$mid .= ' WHERE ';
			} else {
				$mid .= ' AND ';
			}
			$mid .= ' (parentSheetId = 0 or parentSheetId = null) ';
		}
		
		$result = $this->query( "SELECT * FROM `tiki_sheets`  $mid ORDER BY $sort", $bindvars, $maxRecord, $offset );

		while( $row = $result->fetchRow() ) {
			if ($tikilib->user_has_perm_on_object($user, $row['sheetId'], 'sheet', 'tiki_p_view_sheet')) {
				if ($userlib->object_has_one_permission($row['sheetId'], 'sheet'))
					$row['individual'] = 'y';
				$row['tiki_p_edit_sheet'] = ($user && $user == $row['author']) || $tikilib->user_has_perm_on_object($user, $row['sheetId'], 'sheet', 'tiki_p_edit_sheet')?'y': 'n';
				$results['data'][] = $row;
			}
		}

		$results['cant'] = $this->getOne( "SELECT COUNT(*) FROM `tiki_sheets` $mid", $bindvars );

		return $results;
	}

	function remove_sheet( $sheetId ) // {{{2
	{
		global $prefs;
		$this->query( "DELETE FROM `tiki_sheets` WHERE `sheetId` = ?", array( $sheetId ) );
		$this->query( "DELETE FROM `tiki_sheet_values` WHERE `sheetId` = ?", array( $sheetId ) );
		$this->query( "DELETE FROM `tiki_sheet_layout` WHERE `sheetId` = ?", array( $sheetId ) );

		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Removed', $sheetId, 'sheet');
		}
	}

	function replace_sheet( $sheetId, $title, $description, $author, $parentSheetId = 0 ) // {{{2
	{
		global $prefs;

		if( $sheetId == 0 )
		{
			$this->query( "INSERT INTO `tiki_sheets` ( `title`, `description`, `author`, `parentSheetId` ) VALUES( ?, ?, ?, ? )", array( $title, $description, $author, $parentSheetId ) );

			$sheetId = $this->getOne( "SELECT MAX(`sheetId`) FROM `tiki_sheets` WHERE `author` = ?", array( $author ) );
			if ($prefs['feature_actionlog'] == 'y') {
				global $logslib; include_once('lib/logs/logslib.php');
				$query = 'select `sheetId` from `tiki_sheets` where `title`=? and `description`= ? and `author`=?';
				$id = $this->getOne($query, array($title, $description, $author ) );
				$logslib->add_action('Created', $id, 'sheet');
			}
		}
		else
		{
			$this->query( "UPDATE `tiki_sheets` SET `title` = ?, `description` = ?, `author` = ?, `parentSheetId` = ? WHERE `sheetId` = ?", array( $title, $description, $author, (int) $parentSheetId, (int) $sheetId ) );
		}
		return $sheetId;
	}
	
	function set_sheet_title( $sheetId, $title )
	{
		if ( $sheetId ) {
			$this->query( "UPDATE `tiki_sheets` SET `title` = ? WHERE `sheetId` = ?", array( $title, $sheetId ) );
		}
	}
	
	function setup_jquery_sheet()
	{
		global $headerlib;
		if (!$this->setup_jQuery_sheet_files) {
			$headerlib->add_cssfile( 'lib/jquery.sheet/jquery.sheet.css' );
			$headerlib->add_jsfile( 'lib/jquery.sheet/jquery.sheet.js' );
			$headerlib->add_jsfile( 'lib/jquery.sheet/jquery.sheet.advancedfn.js' );
			$headerlib->add_jsfile( 'lib/jquery.sheet/jquery.sheet.financefn.js' );
			$headerlib->add_jsfile( 'lib/jquery.sheet/parser.js' );
			$headerlib->add_jsfile( 'lib/sheet/grid.js' );
		
			// plugins
			$headerlib->add_jsfile( 'lib/jquery.sheet/plugins/jquery.scrollTo-min.js' );
			$headerlib->add_jsfile( 'lib/jquery.sheet/plugins/raphael-min.js', 'external' );
			$headerlib->add_jsfile( 'lib/jquery.sheet/plugins/g.raphael-min.js', 'external' );
			$this->setup_jQuery_sheet_files = true;
		}
	}
	
	function sheet_history( $sheetId )
	{
		return $this->fetchAll( "
			SELECT DISTINCT
				`tiki_sheet_values`.`begin` as stamp,
				`tiki_sheet_values`.`user`,
				DATE_FORMAT(FROM_UNIXTIME(`tiki_sheet_values`.`begin`), '%M %D %Y %h:%i:%s') as prettystamp
			FROM `tiki_sheet_values`
			INNER JOIN `tiki_sheets` ON `tiki_sheets`.`sheetId` = `tiki_sheet_values`.`sheetId`
			WHERE `tiki_sheets`.`sheetId` = ? OR `tiki_sheets`.`parentSheetId` = ?
			ORDER BY begin DESC", array( $sheetId, $sheetId ) );
	}
	
	function rollback_sheet($id, $readdate=null)
	{
		global $user, $sheetlib;
		
		if ($readdate) {
			$now = (int)time();
			
			 $this->query( "
				 UPDATE `tiki_sheet_values`
				 SET `end` = ?
				 WHERE
				 	`sheetId` = ? AND
				 	`end` IS NULL
			 ", array( $now, $id ) );
			 
			 $this->query("
				 INSERT INTO `tiki_sheet_values` (`sheetId`, `begin`, `rowIndex`, `columnIndex`, `value`, `calculation`, `width`, `height`, `format`, `user`, `style`, `class`, `clonedSheetId`)
				 SELECT `sheetId`, ?, `rowIndex`, `columnIndex`, `value`, `calculation`, `width`, `height`, `format`, `user`, `style`, `class`, `clonedSheetId`
				 FROM `tiki_sheet_values`
				 WHERE
				 	`sheetId` = ? AND
				    ? >= `begin` AND 
				    `end` > ?
			", array( $now, $id, $readdate, $readdate ) );
			 
		}
		
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Spreadsheet-Rollback', $id, 'sheet');
		}
		
		$children = $this->fetchAll( "SELECT `sheetId` FROM `tiki_sheets` WHERE `parentSheetId` = ?", array($id) );
		foreach($children as $child) {
			$this->rollback_sheet( $child['sheetId'], $readdate );
		}
		
		return $id;
	}
	
	function clone_sheet( $sheetId, $readdate = null, $parentSheetId = 0)
	{
		global $user, $prefs;
		
		if (!isset($readdate)) {
			$readdate = time();
		}
		
		$readdate = (int)$readdate;
		$parentSheetId = (int)$parentSheetId;
		
		//clone the parent sheet & get it's id
		$this->query( "
			INSERT INTO `tiki_sheets` (`title`, `description`, `author`, `parentSheetId`, `clonedSheetId`)
			SELECT CONCAT('CLONED - ', `title`), `description`, ?, ?, `sheetid`
			FROM `tiki_sheets`
			WHERE `sheetid` = ?
		", array( $user, $parentSheetId, $sheetId ) );
		
		$newSheetId = $this->getOne( "SELECT MAX(`sheetId`) FROM `tiki_sheets` WHERE `author` = ?", array( $user ) );
		//clone the sheet layout
		$this->query( "
			INSERT INTO `tiki_sheet_layout` (`sheetId`, `begin`, `end`, `headerRow`, `footerRow`, `className`, `parseValues`, `clonedSheetId`)
			SELECT ?, `begin`, `end`, `headerRow`, `footerRow`, `className`, `parseValues`, `sheetId`
			FROM `tiki_sheet_layout`
			WHERE `sheetid` = ?
		", array( $newSheetId, $sheetId ) );
		
		//clone sheet's values
	  $this->query( "
	      INSERT INTO `tiki_sheet_values` (`sheetId`, `begin`, `end`, `rowIndex`, `columnIndex`, `value`, `calculation`, `width`, `height`, `format`, `user`, `style`, `class`, `clonedSheetId`)
	      SELECT ?, `begin`, NULL, `rowIndex`, `columnIndex`, `value`, `calculation`, `width`, `height`, `format`, `user`, `style`, `class`, ?
	      FROM `tiki_sheet_values`
	    	WHERE
	        `sheetId` = ? AND 
	        ? >= `begin` AND 
	        (
	        	`end` IS NULL OR
	        	`end` > ?
	        )
      ", array( $newSheetId, $sheetId, $sheetId, $readdate, $readdate ) );
		
		//clone the children sheets if they exist
		$result = $this->query("SELECT `sheetId` FROM `tiki_sheets` WHERE `parentSheetId` = ?", array( $sheetId ) );
		while( $row = $result->fetchRow() )
		{
			if ($row['sheetId']) {
				$this->clone_sheet($row['sheetId'], $readdate, $newSheetId);
			}
		}
		
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Cloning', $sheetId, 'sheet');
			$logslib->add_action('Cloned', $newSheetId, 'sheet');
		}

		return $newSheetId;
	}
	
	function clone_layout( $sheetId, $className, $headerRow, $footerRow, $parseValues = 'n' ) // {{{2
	{
		if( $row = $this->get_sheet_layout( $sheetId ) )
		{
			if( $row[ 'className' ] == $className
			 && $row[ 'headerRow' ] == $headerRow
			 && $row[ 'footerRow' ] == $footerRow
			 && $row[ 'parseValues' ] == $parseValues )
				return true; // No changes have to be made
		}

		$headerRow = empty( $headerRow ) ? 0 : $headerRow;

		$footerRow = empty( $footerRow ) ? 0 : $footerRow;

		$stamp = time();

		$this->query( "UPDATE `tiki_sheet_layout` SET `end` = ? WHERE sheetId = ? AND `end` IS NULL", array( $stamp, $sheetId ) );
		$this->query( "INSERT INTO `tiki_sheet_layout` ( `sheetId`, `begin`, `className`, `headerRow`, `footerRow`, `parseValues` ) VALUES( ?, ?, ?, ?, ?, ? )",
												array( $sheetId, $stamp, $className, (int)$headerRow, (int)$footerRow, $parseValues ) );

		return true;
	}
	
	function save_sheet($data, $id, $file, $type = 'db')
	{
		global $user, $sheetlib;
		
		$sheets =  json_decode($data);
		$rc =  '';
		
		if ($id) {
			$grid = new TikiSheet($id);
			if (is_array($sheets)) {
				foreach ($sheets as $sheet) {
					$handler = new TikiSheetHTMLTableHandler($sheet);
					$res = $grid->import($handler);
					// Save the changes
					$rc .= strlen($rc) === 0 ? '' : ', ';
					if ($res) {
						if (!$sheet->metadata->sheetId) {
							if (!empty($sheet->metadata->title)) {
								$title = $sheet->metadata->title;
							} else {
								$title = $info['title'] . ' subsheet'; 
							}
							$newId = $sheetlib->replace_sheet( 0, $title, '', $user, $id );
							$rc .= tra('new') . " (id=$newId) ";
							$sheet->metadata->sheetId = $newId;
							$handler = new TikiSheetHTMLTableHandler($sheet);
							$res = $grid->import($handler);
						}
						if ($id && $res) {
							$handler = new TikiSheetDatabaseHandler( $sheet->metadata->sheetId );
							$grid->export($handler);
							$rc .= $grid->getColumnCount() . ' x ' . $grid->getRowCount() . ' ' . tra('sheet') . " (id=".$sheet->metadata->sheetId.")";
						}
						if (!empty($sheet->metadata->title)) {
							$sheetlib->set_sheet_title($sheet->metadata->sheetId, $sheet->metadata->title);
						}
					}
				}
			}
		} /*else {
			
			$grid = new TikiSheet();
			if ($type == 'csv') {
				foreach ($sheets as $sheet) {
					$handler = new TikiSheetCSVHandler( $file );
					if ($handler->_save($sheet)) {
						$res .= $file;
						$rc .= tra("file - ").$file;		
					}
				}
			} elseif ($type == 'excelcsv') {
				foreach ($sheets as $sheet) {
					$handler = new TikiSheetCSVExcelHandler( $file );
					if ($handler->_save($sheet)) {
						$res .= $file;
						$rc .= tra("file - ").$file;
					}
				}
			}
		}*/
		return ($res ?  tra('Saved'). ': ' . $rc : tra('Save failed'));
	}
	
	/** get_attr_from_css_string {{{2
	 * Grabs a css setting from a string
	 * @param $style A simple css style string used with an html dom object
	 * @param $attr The name of the css attribute you'd like to extract from $style
	 */
	function get_attr_from_css_string($style, $attr, $default)
	{
		global $sheetlib;
		$style = strtolower($style);
		$style = str_replace(' ', '', $style);
		
		$attr = strtolower($attr);
		
		$cssAttrs = explode(';', $style);
		foreach($cssAttrs as &$v) {
			$v = explode(':', $v);
		}
		
		$key = $sheetlib->array_searchRecursive($attr, $cssAttrs);
		$result;
		if ($key === false) {
			$result = $default;
		} else {
			$result = $cssAttrs[$key[0]][$key[1] + 1];
		}
		
		return ($result != 'auto' ? $result : $default);
	}
	
	// array_search with recursive searching, optional partial matches and optional search by key
	function array_searchRecursive( $needle, $haystack, $strict=false, $path=array() )
	{
		global $sheetlib;
		
	    if( !is_array($haystack) ) {
	        return false;
	    }
	 
	    foreach( $haystack as $key => $val ) {
	        if( is_array($val) && $subPath = $sheetlib->array_searchRecursive($needle, $val, $strict, $path) ) {
	            $path = array_merge($path, array($key), $subPath);
	            return $path;
	        } elseif ( (!$strict && $val == $needle) || ($strict && $val === $needle) ) {
	            $path[] = $key;
	            return $path;
	        }
	    }
	    return false;
	}
	
	function diff_sheets_as_html( $id, $dates = null )
	{
		global $prefs, $sheetlib;
		
		function count_longest( $array1, $array2 )
		{
			return (count($array1) > count($array2) ? count($array1) : count($array2));
		}
		
		function join_with_sub_grids( $id, $date )
		{
			global $prefs, $sheetlib;
			$result1 = "";
			$result2 = "";
			
			$handler = new TikiSheetDatabaseHandler($id, $date);
			$handler->setReadDate($date);
			$grid = new TikiSheet($id, true);
			$grid->import($handler);
			
			$subgrids = $sheetlib->get_sheet_subsheets($grid->sheetId);
			$i = 0;
			$grids = array($grid);
			foreach ($subgrids as $sub) {
				$handler = new TikiSheetDatabaseHandler($sub['sheetId'], $date);
				$handler->setReadDate($date);
				$subsheet = new TikiSheet($sub['sheetId'], true);
				$subsheet->import($handler);
				array_push($grids, $subsheet);
				$i++;
			}
			return $grids;
		}
		
		function sanitize_for_diff($val)
		{
			$val = str_replace("<br/>", 	"<br>", $val);
			$val = str_replace("<br />",	"<br>", $val);
			$val = str_replace("<br  />", 	"<br>", $val);
			$val = str_replace("<BR/>",		"<br>", $val);
			$val = str_replace("<BR />", 	"<br>", $val);
			$val = str_replace("<BR  />",	"<br>", $val);
			
			return explode("<br>", $val);
		}
		
		function diff_to_html($changes)
		{
			$result = array("", "");
			for ( $i = 0; $i < count_longest($changes->orig, $changes->final); $i++ )
			{
				$class = array("", "");
				$char = array("", "");
				$vals = array( trim( $changes->orig[$i] ), trim( $changes->final[$i] ) );
				
				if ($vals[0] && $vals[1]) {
					if ( $vals[0] != $vals[1] ) {
						$class[1] .= "diffadded";
					}
				} else if ($vals[0]) {
					$class[0] .= "diffadded";
					$class[1] .= "diffdeleted";
					$vals[1] = $vals[0];
					$char[1] = "-";
				} else if ($vals[1]) {
					$class[0] .= "diffdeleted";
					$class[1] .= "diffadded";
					$char[1] = "+";
				}
				
				if ( $vals[0] ) {
					$result[0] .= "<span class='$class[0]'>".$char[0].$vals[0]."</span><br />";
				}
				if ( $vals[1] ) {
					$result[1] .= "<span class='$class[1]'>".$char[1].$vals[1]."</span><br />";
				}
			} 
			return $result;
		}
		
		$grids1 = join_with_sub_grids($id, $dates[0]);
		$grids2 = join_with_sub_grids($id, $dates[1]);
		
		for ( $i = 0; $i < count_longest($grids1, $grids2); $i++ ) { //cycle through the sheets within a spreadsheet
			$result1 .= "<table title='".$grids1[$i]->getTitle()."'>";
			$result2 .= "<table title='".$grids2[$i]->getTitle()."'>";
			for ( $row = 0; $row < count_longest($grids1[$i]->dataGrid, $grids2[$i]->dataGrid); $row++ ) { //cycle through rows
				$result1 .= "<tr>";
				$result2 .= "<tr>";
				for ( $col = 0; $col < count_longest($grids1[$i]->dataGrid[$row], $grids2[$i]->dataGrid[$row]); $col++ ) { //cycle through columns
					$diff = new Text_Diff( sanitize_for_diff( $grids1[$i]->dataGrid[$row][$col] ), sanitize_for_diff( $grids2[$i]->dataGrid[$row][$col] ) );
					$changes = $diff->getDiff();
						
					//print_r($changes);
					
					$class = array('','');
					$values = array('','');
					
					//I left this diff switch, but it really isn't being used as of now, in the future we may though.
					switch ( get_class($changes[0]) ) {
						case 'Text_Diff_Op_copy':
							$values = diff_to_html($changes[0]);
							break;
						case 'Text_Diff_Op_change':
							$values = diff_to_html($changes[0]);
							break;
						case 'Text_Diff_Op_delete':
							$values = diff_to_html($changes[0]);
							break;
						case 'Text_Diff_Op_add':
							$values = diff_to_html($changes[0]);
							break;
						default:
							$values = diff_to_html($changes[0]);
					}
					$result1 .= "<td class='$class1'>".$values[0]."</td>";
					$result2 .= "<td class='$class2'>".$values[1]."</td>";
				}
				$result1 .= "</tr>";
				$result2 .= "</tr>";
			}
			$result1 .= "</table>";
			$result2 .= "</table>";
		}
			
		return array($result1, $result2);
	}
	
	function user_can_view($sheetId)
	{
		global $user;
		$objectperms = Perms::get( 'sheet', $sheetId );
		return ( $objectperms->view_sheet || $objectperms->admin );
	}
	
	function user_can_edit($sheetId)
	{
		global $user;
		$objectperms = Perms::get( 'sheet', $sheetId );
		return ( $objectperms->edit_sheet || $objectperms->admin );
	}
} // }}}1
$sheetlib = new SheetLib;
