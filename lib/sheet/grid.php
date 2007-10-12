<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

ini_set( 'include_path', ini_get( 'include_path' ) . ":lib/sheet" );

// Nice dependencies, mostly for excel support. Don't try changing the order.
require_once( "PEAR.php" );
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
			'TikiSheetExcelHandler'
		);
	}// }}}2
	
	/** TikiSheet {{{2
	 * Initializes the data container.
	 */
	function TikiSheet()
	{
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
		$this->className = '';
	}
	
	/** configureLayout {{{2
	 * Assigns the different parameters for the output
	 * @param $className	The clas that will be assigned
	 *						to the table tag of the output.
	 *						If used for an other output than
	 *						HTML, it can be used as an identifier
	 *						for the type of layout.
	 * @param $headerRow	The amount of rows that are considered
	 *						as part of the header.
	 * @param $footerRow	The amount of rows that are considered
	 *						as part of the footer.
	 */
	function configureLayout( $className, $headerRow = 0, $footerRow = 0 )
	{
		$this->cssName = $className;
		$this->headerRow = $headerRow;
		$this->footerRow = $footerRow;
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
			&& $this->cellInfo[$rowIndex][$columnIndex]['format'] == $sheet->cellInfo[$rowIndex][$columnIndex]['format'];
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

		$base = array( 'width' => 1, 'height' => 1, 'format' => null );
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
		if( preg_match( "/^([A-Z]+)([0-9]+):([A-Z]+)([0-9]+)$/", $range, $parts ) )
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
	/** getRowCount {{{2
	 * Returns the row count.
	 */
	function getRowCount()
	{
		return $this->rowCount;
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
		$this->cellInfo[$this->usedRow][$this->usedCol] = array( "width" => $width, "height" => $height );

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

	/** createDeadCell {{{2
	 * Assigns the cell as overlapped by a wide cell.
	 * @param $x Coordinate of the cell
	 * @param $y Coordinate of the cell
	 */
	function createDeadCell( $x, $y )
	{
		$this->dataGrid[$y][$x] = null;
		$this->cellInfo[$y][$x] = array( "width" => 0, "height" => 0, "format" => null );
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
				$this->convert( $value, $v, $c, $w, $h, $f );
				$sheet->setValue( $v );
				$sheet->setCalculation( $c );
				$sheet->setSize( $w, $h );
				$sheet->setFormat( $f );
			}
		}

		return true;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		global $prefs;

		echo "	var g;\n";
		echo "	function initGrid()\n";
		echo "	{\n";
		
		echo "		g = new Grid( document.getElementById( 'Grid' ) );\n";
		
		for( $i = 0; $sheet->getRowCount() > $i; $i++ )
			echo "		new Row( g, null );\n";

		for( $i = 0; $sheet->getColumnCount() > $i; $i++ )
			echo "		new Column( g, null );\n";
	   
		echo "		g.draw();\n";

		echo "		var cell;\n";

		for( $y = 0; $sheet->getRowCount() > $y; $y++ )
		{
			for( $x = 0; $sheet->getColumnCount() > $x; $x++ )
			{
				$calc = $sheet->calcGrid[$y][$x];
				$value = $sheet->dataGrid[$y][$x];
				$width = $sheet->cellInfo[$y][$x]['width'];
				$height = $sheet->cellInfo[$y][$x]['height'];
				$format = $sheet->cellInfo[$y][$x]['format'];

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

				echo "		cell = g.getIndexCell( $y, $x );\n";
				echo "		cell.value = '{$calc}';\n";
				echo "		cell.endValue = '{$value}';\n";
				echo "		cell.format = {$format};\n";

				if( !empty( $width ) && !empty( $height ) && ($width != 1 || $height != 1) )
					echo "		cell.changeSize( {$height}, {$width} );\n";
			}
		}


		if ($prefs['feature_contribution'] == 'y') {
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			$contributions = $contributionlib->list_contributions();
			for ($i = $contributions['cant'] - 1; $i >= 0; -- $i) {
				$name = str_replace("'", "\\'", $contributions['data'][$i]['name']);
				$j = $contributions['data'][$i]['contributionId'];
				echo "		g.addContribution($j, '$name');\n";
			}
		}
	   
		echo "		g.draw();\n";
		echo "		g.refresh();\n";

		echo "	}\n";

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
	 * @return False on error.
	 */
	function convert( $formString, &$value, &$calc, &$width, &$height, &$format )
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
        $this->encoding = new Encoding ($inputEncoding, $outputEncoding);
	}

	// _load {{{2
	function _load( &$sheet )
	{
		if( $file = @fopen( $this->file, "r" ) )
		{
			$row = 0;
			while( $data = @fgetcsv( $file, $this->lineLen ) )
			{
				foreach( $data as $col=>$value )
				{
					$sheet->initCell( $row, $col );
					$sheet->setValue( $this->encoding->convert_encoding ( $value ) );
					$sheet->setSize( 1, 1 );
				}

				$row++;
			}

			@fclose( $file );

			return true;
		}
		else
			return false;
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
        $this->encoding = new Encoding ($inputEncoding, $outputEncoding);
    }

    // _load {{{2
    function _load( &$sheet )
    {
        if( $file = @fopen( $this->file, "r" ) )
        {
            $row = 0;
            while( $data = @fgetcsv( $file, $this->lineLen , ";", '"') )
            {
                foreach( $data as $col=>$value )
                {
                    $sheet->initCell( $row, $col );
                    $sheet->setValue( $this->encoding->convert_encoding ( $value ) );
                    $sheet->setSize( 1, 1 );
                }

                $row++;
            }

            @fclose( $file );

            return true;
        }
        else
            return false;
    }

    // _save {{{2
    function _save( &$sheet )
    {
        $total = array();
        
        ksort ($sheet->dataGrid);
        
        foreach( $sheet->dataGrid as $row ) 
        {
            if( is_array( $row ) ) 
            {
                ksort($row);
                $total[] = $this->fputcsvexcel( $row ,';','"');
            }
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
    
    function fputcsvexcel( $row, $fd=';', $quot='"')
    {
       $str='';
       foreach ($row as $cell) {
           str_replace(Array($quot,        "\n"),
                       Array($quot.$quot,  ''),
                       $cell);
           if (strchr($cell, $fd)) {
               $str.=$quot.$cell.$quot.$fd;
           } else {
               $str.=$cell.$fd;
           }
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
	function TikiSheetDatabaseHandler( $sheetId )
	{
		$this->sheetId = $sheetId;
		$this->readDate = time();
	}

	// _load {{{2
	function _load( &$sheet )
	{
		global $tikilib;
		
		$result = $tikilib->query( "SELECT `rowIndex`, `columnIndex`, `value`, `calculation`, `width`, `height`, `format`, `user` FROM `tiki_sheet_values` WHERE `sheetId` = ? AND ? >= `begin` AND ( `end` IS NULL OR `end` > ? )", array( $this->sheetId, (int)$this->readDate, (int)$this->readDate ) );

		while( $row = $result->fetchRow() )
		{
			extract( $row );
			$sheet->initCell( $rowIndex, $columnIndex );
			$sheet->setValue( $value );
			$sheet->setCalculation( $calculation );
			$sheet->setSize( $width, $height );
			$sheet->setFormat( $format );
		}

		// Fetching the layout informations.
		$result2 = $tikilib->query( "SELECT `className`, `headerRow`, `footerRow` FROM `tiki_sheet_layout` WHERE `sheetId` = ? AND ? >= `begin` AND ( `end` IS NULL OR `end` > ? )", array( $this->sheetId, (int)$this->readDate, (int)$this->readDate ) );

		if( $row = $result2->fetchRow() )
		{
			extract( $row );
			$sheet->configureLayout( $className, $headerRow, $footerRow );
		}

		return true;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		global $tikilib, $user, $prefs;
		// Load the current database state {{{3
		$current = &new TikiSheet;
		$handler = &new TikiSheetDatabaseHandler( $this->sheetId );
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

				$updates[] = $row;
				$updates[] = $col;

				if( !$sheet->isEmpty( $row, $col ) )
					$inserts[] = array( (int)$this->sheetId, $stamp, $row, $col, $value, $calc, $width, $height, $format, $user );

			}
		}

		$updates[] = $sheet->getRowCount();
		$updates[] = $sheet->getColumnCount();

		$conditions = str_repeat( "( rowIndex = ? AND columnIndex = ? ) OR ", ( sizeof($updates) - 4 ) / 2 );
		if ($prefs['feature_actionlog'] == 'y') { // must keep the previous value to do the difference
			$query = "SELECT `rowIndex`, `columnIndex`, `value` FROM `tiki_sheet_values` WHERE `sheetId` = ? AND  `end` IS NULL";
			$result = $tikilib->query($query, array($this->sheetId));
			$old = array();
			while( $row = $result->fetchRow() ) {
				$old[$row['rowIndex'].'-'.$row['columnIndex']] = $row['value'];
			}
		}
			
		$tikilib->query( "UPDATE `tiki_sheet_values` SET `end` = ?  WHERE `sheetId` = ? AND `end` IS NULL AND ( {$conditions}`rowIndex` >= ? OR `columnIndex` >= ? )", $updates );

		if( sizeof( $inserts ) > 0 )
			foreach( $inserts as $values )
			{
				$tikilib->query( "INSERT INTO `tiki_sheet_values` (`sheetId`, `begin`, `rowIndex`, `columnIndex`, `value`, `calculation`, `width`, `height`, `format`, `user` ) VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )", $values );
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
		$document = &new Spreadsheet_Excel_Reader();

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

						$sheet->setValue( $this->encoding->convert_encoding ( $value ) );
						$sheet->setSize( $width, $height );
					}
			}

		return true;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		$book = &new Spreadsheet_Excel_Writer;

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
	
	/** Constructor {{{2
	 * Identifies the caption of the table if it applies.
	 * @param $heading The heading
	 */
	function TikiSheetOutputHandler( $heading = null )
	{
		$this->heading = $heading;
	}
	
	// _save {{{2
	function _save( &$sheet )
	{
		if( $sheet->headerRow + $sheet->footerRow > $sheet->getRowCount() )
			return false;

		$class = empty( $sheet->cssName ) ? "" : " class='{$sheet->cssName}'";
		echo "<table{$class}>\n";

		if( !is_null( $this->heading ) )
			echo "	<caption>{$this->heading}</caption>\n";
		
		if( $sheet->headerRow > 0 )
		{
			echo "	<thead>\n";
			$this->drawRows( $sheet, 0, $sheet->headerRow );
			echo "	</thead>\n";
		}

		echo "	<tbody>\n";
		$this->drawRows( $sheet, $sheet->headerRow, $sheet->getRowCount() - $sheet->footerRow );
		echo "	</tbody>\n";
		
		if( $sheet->footerRow > 0 )
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
		for( $i = $begin; $end > $i; $i++ )
		{
			echo "		<tr>\n";

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
				echo "			<td$append>$data</td>\n";
			}
			
			echo "		</tr>\n";
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
		for( $i = $begin; $end > $i; $i++ )
		{
			echo "		<tr><th>" . ($i + 1) . "</th>\n";

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
				echo "			<td$append>$data</td>\n";
			}
			
			echo "		</tr>\n";
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
		$result = $this->query( "SELECT `className`, `headerRow`, `footerRow` FROM `tiki_sheet_layout` WHERE `sheetId` = ? AND `end` IS NULL", array( $sheetId ) );

		return $result->fetchRow();
	}
	
	function list_sheets( $offset = 0, $maxRecord = -1, $sort_mode = 'title_desc', $find = '' ) // {{{2
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

	function replace_sheet( $sheetId, $title, $description, $author ) // {{{2
	{
		global $prefs;

		if( $sheetId == 0 )
		{
			$this->query( "INSERT INTO `tiki_sheets` ( `title`, `description`, `author` ) VALUES( ?, ?, ? )", array( $title, $description, $author ) );

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
			$this->query( "UPDATE `tiki_sheets` SET `title` = ?, `description` = ?, `author` = ? WHERE `sheetId` = ?", array( $title, $description, $author, $sheetId ) );
		}
		return $sheetId;
	}

	function replace_layout( $sheetId, $className, $headerRow, $footerRow ) // {{{2
	{
		if( $row = $this->get_sheet_layout( $sheetId ) )
		{
			if( $row[ 'className' ] == $className
			 && $row[ 'headerRow' ] == $headerRow
			 && $row[ 'footerRow' ] == $footerRow )
				return true; // No changes have to be made
		}

		$headerRow = empty( $headerRow ) ? 0 : $headerRow;

		$footerRow = empty( $footerRow ) ? 0 : $footerRow;

		$stamp = time();

		$this->query( "UPDATE `tiki_sheet_layout` SET `end` = ? WHERE sheetId = ? AND `end` IS NULL", array( $stamp, $sheetId ) );
		$this->query( "INSERT INTO `tiki_sheet_layout` ( `sheetId`, `begin`, `className`, `headerRow`, `footerRow` ) VALUES( ?, ?, ?, ?, ? )", array( $sheetId, $stamp, $className, (int)$headerRow, (int)$footerRow ) );

		return true;
	}
	
} // }}}1

$sheetlib = &new SheetLib( $tikilib->db );
?>
