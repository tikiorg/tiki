<?php

// Constants {{{1

/*
DATA:
End values will be preserved.

CALC:
The calculations will be preserved.

CELL:
The cell merging will be preserved.

FORMAT:
 -- Removed, will only be a template applied on final output.
*/

define( 'TIKISHEET_SAVE_DATA',		0x00010000 );
define( 'TIKISHEET_SAVE_CALC',		0x00020000 );
define( 'TIKISHEET_SAVE_CELL',		0x00040000 );

define( 'TIKISHEET_LOAD_DATA',		0x00000001 );
define( 'TIKISHEET_LOAD_CALC',		0x00000002 );
define( 'TIKISHEET_LOAD_CELL',		0x00000004 );

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
	var $mergeInfo;

	/**
	 * Row and column count once finalized.
	 */
	var $rowCount;
	var $columnCount;

	/**
	 * Internal values.
	 */
	var $COLCHAR;
	var $indexes;
	var $lastIndex;
	var $lastID;

	var $usedRow;
	var $usedCol;
	// }}}2
	
	/** TikiSheet {{{2
	 * Initializes the data container.
	 */
	function TikiSheet()
	{
		$this->dataGrid = array();
		$this->calcGrid = array();
		$this->mergeInfo = array();

		$this->COLCHAR = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$this->indexes = array( $this->COLCHAR[0] => 0 );
		$this->lastIndex = 0;
		$this->lastID = $this->COLCHAR[0];

		$this->rowCount = INITIAL_ROW_COUNT;
		$this->columnCount = INITIAL_COL_COUNT;
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
		return $this->dataGrid[$rowIndex][$columnIndex] == $sheet->dataGrid[$rowIndex][$columnIndex]
			&& $this->calcGrid[$rowIndex][$columnIndex] == $sheet->calcGrid[$rowIndex][$columnIndex]
			&& $this->mergeInfo[$rowIndex][$columnIndex]['width'] == $sheet->mergeInfo[$rowIndex][$columnIndex]['width']
			&& $this->mergeInfo[$rowIndex][$columnIndex]['height'] == $sheet->mergeInfo[$rowIndex][$columnIndex]['height'];
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
		$this->finalizeGrid( $this->mergeInfo, $maxRow, $maxCol, true );

		$this->rowCount = $maxRow + 1;
		$this->columnCount = $maxCol + 1;

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
		$this->mergeInfo = array();
		
		if( !$handler->_load( $this ) )
			return false;

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
		if( $col == null )
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
			&& ( $this->mergeInfo[$rowIndex][$columnIndex]['width'] == ''
			||   $this->mergeInfo[$rowIndex][$columnIndex]['width'] == 1 )
			&& ( $this->mergeInfo[$rowIndex][$columnIndex]['height'] == ''
			||   $this->mergeInfo[$rowIndex][$columnIndex]['height'] == 1 );
	}
	
	/** setCalculation {{{2
	 *
	 */
	function setCalculation( $calculation )
	{
		$this->calcGrid[$this->usedRow][$this->usedCol] = $calculation;
	}

	/** setSize {{{2
	 * Sets the size of the last initialized cell.
	 * @param $width The cell's column span.
	 * @param $height The cell's row span.
	 */
	function setSize( $width, $height )
	{
		$this->mergeInfo[$this->usedRow][$this->usedCol] = array( "width" => $width, "height" => $height );
	}
	
	/** setValue {{{2
	 *
	 */
	function setValue( $value )
	{
		$this->dataGrid[$this->usedRow][$this->usedCol] = $value;
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
				$this->convert( $value, $v, $c, $w, $h );
				$sheet->setValue( $v );
				$sheet->setCalculation( $c );
				$sheet->setSize( $w, $h );
			}
		}

		return true;
	}

	// _save {{{2
	function _save( &$sheet )
	{
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
				$width = $sheet->mergeInfo[$y][$x]['width'];
				$height = $sheet->mergeInfo[$y][$x]['height'];

				if( empty( $calc ) )
					$calc = $value;
				else
					$calc = "=" . $calc;

				echo "		cell = g.getIndexCell( $y, $x );\n";
				echo "		cell.value = '{$calc}';\n";
				echo "		cell.endValue = '{$value}';\n";

				if( !empty( $width ) && !empty( $height ) )
					echo "		cell.changeSize( {$height}, {$width} );\n";
			}
		}
	   
		echo "		g.refresh();\n";
		echo "		g.draw();\n";

		echo "	}\n";

		return true;
	}

	/** convert {{{2
	 * Converts the form cell format to readable data.
	 * [value]=[calc]<<<[width],[height]>>>
	 * @param $formString The direct value from the form.
	 * @param $value Will contain the end value.
	 * @param $calc Will contain the calculation without the equal.
	 * @param $width Will contain the colspan.
	 * @param $height Will contain the rowspan.
	 * @return False on error.
	 */
	function convert( $formString, &$value, &$calc, &$width, &$height )
	{
		$value = "";
		$calc = "";
		$width = 1;
		$height = 1;
		
		if( preg_match( "/^(.*[^\\\\])?=(.*[^\\\\])?<<<([0-9]+),([0-9]+)>>>$/", stripslashes($formString), $parts ) )
		{
			$value = str_replace( "\\=", "=", $parts[1] );
			$calc = trim( $parts[2] );
			$width = $parts[3];
			$height = $parts[4];

			return true;
		}
		else
			return false;
	}
	
	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_SAVE_CALC | TIKISHEET_SAVE_CELL | TIKISHEET_LOAD_DATA | TIKISHEET_LOAD_CALC | TIKISHEET_LOAD_CELL ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "0.1-dev";
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
	function TikiSheetSerializeHandler( $file )
	{
		$this->file = $file;
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
			$sheet->mergeInfo = $data->mergeInfo;

			return true;
		}
		else
			return false;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		if( $file = @fopen( $this->file, "w" ) )
		{
			$data = serialize( $sheet );

			$return =  @fwrite( $file, $data );

			@fclose( $file );
			return $return;
		}
		else
			return false;
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_SAVE_CALC | TIKISHEET_SAVE_CELL | TIKISHEET_LOAD_DATA | TIKISHEET_LOAD_CALC | TIKISHEET_LOAD_CELL ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "0.1-dev";
	}
 } // }}}1

/** TikiSheetDatabaseHandler {{{1
 * Class to handle transactions with the database.
 * The class and database structure allow data
 * rollbacks. The class does not allow to manipulate
 * the sheets themselves. The data will only be filled
 * and extracted based on the given sheet ID. As a default
 * value, the most recent entries will be read.
 */
class TikiSheetDatabaseHandler extends TikiSheetDataHandler
{
	var $sheetId;
	var $db;
	var $readDate;
	
	/** Constructor {{{2
	 * Assigns a sheet ID to the handler.
	 * @param $sheetId The ID of the sheet in the database.
	 * @param $db The database link to use.
	 */
	function TikiSheetDatabaseHandler( $sheetId, $db )
	{
		$this->sheetId = $sheetId;
		$this->db = $db;
		$this->readDate = time();
	}

	// _load {{{2
	function _load( &$sheet )
	{
		$result = mysql_query( "SELECT rowIndex, columnIndex, value, calculation, width, height FROM tiki_sheet_values WHERE {$this->readDate} >= begin AND ( end IS NULL OR end > {$this->readDate} )", $this->db );

		while( $row = mysql_fetch_assoc( $result ) )
		{
			extract( $row );
			$sheet->initCell( $rowIndex, $columnIndex );
			$sheet->setValue( $value );
			$sheet->setCalculation( $calculation );
			$sheet->setSize( $width, $height );
		}

		return true;
	}

	// _save {{{2
	function _save( &$sheet )
	{
		// Load the current database state {{{3
		$current = &new TikiSheet;
		$handler = &new TikiSheetDatabaseHandler( $this->sheetId, $this->db );
		$current->import( $handler );

		// Find differences {{{3
		for( $row = 0; $sheet->getRowCount() > $row; $row++ )
		{
			for( $col = 0; $sheet->getColumnCount() > $col; $col++ )
			{
				if( !$sheet->equals( $current, $row, $col ) )
					$mods[] = array( "row" => $row, "col" => $col );
			}
		}

		$stamp = time();

		if( is_array( $mods ) )
		{
			// Update the database {{{3
			foreach( $mods as $coord )
			{
				extract( $coord );
				$value = $sheet->dataGrid[$row][$col];
				$calc = $sheet->calcGrid[$row][$col];
				$width = $sheet->mergeInfo[$row][$col]['width'];
				$height = $sheet->mergeInfo[$row][$col]['height'];

				$updates[] = "( rowIndex = $row AND columnIndex = $col )";

				if( !$sheet->isEmpty( $row, $col ) )
					$inserts[] = "( {$this->sheetId}, $stamp, $row, $col, "
						. ( $value == '' ? 'null' : "'$value'" ) . ", "
						. ( $calc == '' ? 'null' : "'$calc'" ) . ", '$width', '$height' )";

			}
		}
			
		$result = mysql_query( "UPDATE tiki_sheet_values SET end = $stamp WHERE sheetId = {$this->sheetId} AND end IS NULL AND ( " . ( sizeof( $updates ) > 0 ? implode( " OR ", $updates ) . " OR " : '' ) . "rowIndex >= " . $sheet->getRowCount() . " OR columnIndex >= " . $sheet->getColumnCount() . " )", $this->db );

		if( sizeof( $inserts ) > 0 )
			$result = mysql_query( "INSERT INTO tiki_sheet_values (sheetId, begin, rowIndex, columnIndex, value, calculation, width, height ) VALUES" . implode( ", ", $inserts ), $this->db);

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
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_SAVE_CALC | TIKISHEET_SAVE_CELL | TIKISHEET_LOAD_DATA | TIKISHEET_LOAD_CALC | TIKISHEET_LOAD_CELL ) & $type ) > 0;
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
	var $headerRows;
	var $footerRows;
	
	function TikiSheetOutputHandler( $headerRows = 0, $footerRows = 0 )
	{
		$this->headerRows = $headerRows;
		$this->footerRows = $footerRows;
	}

	function _save( &$sheet )
	{
		if( $this->headerRows + $this->footerRows > $sheet->getRowCount() )
			return false;

		echo "<table>\n";
		
		if( $this->headerRows > 0 )
		{
			echo "	<thead>\n";
			$this->drawRows( $sheet, 0, $this->headerRows );
			echo "	</thead>\n";
		}

		echo "	<tbody>\n";
		$this->drawRows( $sheet, $this->headerRows, $sheet->getRowCount() - $this->footerRows );
		echo "	</tbody>\n";
		
		if( $this->footerRows > 0 )
		{
			echo "	<tfoot>\n";
			$this->drawRows( $sheet, $sheet->getRowCount() - $this->footerRows, $sheet->getRowCount() );
			echo "	</tfoot>\n";
		}

		echo "</table>\n";

		return true;
	}

	function drawRows( &$sheet, $begin, $end )
	{
		for( $i = $begin; $end > $i; $i++ )
		{
			echo "		<tr>\n";

			for( $j = 0; $sheet->getColumnCount() > $j; $j++ )
			{
				$width = $sheet->mergeInfo[$i][$j]['width'];
				$height = $sheet->mergeInfo[$i][$j]['height'];
				$append = "";

				if( empty( $width ) || empty( $height ) )
					continue;

				if( $width > 1 )
					$append .= " colspan='{$width}'";

				if( $height > 1 )
					$append .= " rowspan='{$height}'";

				echo "			<td$append>{$sheet->dataGrid[$i][$j]}</td>\n";
			}
			
			echo "		</tr>\n";
		}
	}

	// supports {{{2
	function supports( $type )
	{
		return ( ( TIKISHEET_SAVE_DATA | TIKISHEET_SAVE_CALC | TIKISHEET_SAVE_CELL ) & $type ) > 0;
	}

	// version {{{2
	function version()
	{
		return "0.1-dev";
	}
} // }}}1
?>
