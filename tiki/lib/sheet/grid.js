/**
 * TikiSheet Client-side grid manipulation.
 * By Louis-Philippe Huberdeau
 * 2004
 */
// Global Variables {{{1
var COLCHAR = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

var globalFrom;
var globalGrid;
// }}}1

// Generic value processing {{{1
// decrement {{{2
function decrement( val )
{
	var nChar = COLCHAR.indexOf( val[ val.length - 1 ] ) - 1;
	
	if( nChar >= 0 )
	{
		return val.substr( 0, val.length - 1 ) + COLCHAR[ nChar ];
	}
	else
	{
		return decrement( val.substr( 0, val.length - 1 ) ) + COLCHAR[ COLCHAR.length - 1 ];
	}
}

// increment {{{2
function increment( val )
{
	var nChar = COLCHAR.indexOf( val[ val.length - 1 ] ) + 1;
	
	if( nChar < COLCHAR.length )
	{
		return val.substr( 0, val.length - 1 ) + COLCHAR[ nChar ];
	}
	else
	{
		return increment( val.substr( 0, val.length - 1 ) ) + COLCHAR[0];
	}
}

// processValue {{{2
function processValue( value )
{
	if( value.substr( 0, 1 ) != "=" )
	{
		return value;
	}
	else
	{
		var temp = value.substr( 1 );

		temp = temp.replace( /(\.?)([a-zA-Z_]+)\(/g,
			function( $0, $1, $2 )
			{
				if( $1 == "." )
					return $0;
				
				if( alias != null && alias[$2.toUpperCase()] != null )
					return alias[$2.toUpperCase()] + "(";
				else
					return ( $2 + "(" ).toUpperCase();
			}
		);

		temp = temp.replace( /\$?([A-Z]+)\$?([0-9]+):\$?([A-Z]+)\$?([0-9]+)/g,
			function( $0, $1, $2, $3, $4 )
			{
				var topLeft = globalGrid.getCell( $1 + $2 );
				var bottomRight = globalGrid.getCell( $3 + $4 );

				var currow = topLeft.row;
				var col;

				var list = new Array;

				while( currow != bottomRight.row.next )
				{
					col = topLeft.column;

					while( col != bottomRight.column.next )
					{
						list.push( currow.getCell( col ).getId() );
						col = col.next;
					}

					currow = currow.next;
				}

				return "[" + list.join(",") + "]";
			}
		);

		temp = temp.replace( /\$?([A-Z]+)\$?([0-9]+)/g, "parseFloat(processValue(g.getCell( '$1$2' ).value))" );

		return eval( temp );
	}
}
// }}}1

// Constructors {{{1
// Grid {{{2
function Grid( target )
{
    globalGrid = this;
	this.firstRow = null;
	this.fistColumn = null;

	this.target = target;
	this.cells = new Array();

	this.nbRow = 0;
	this.nbCol = 0;

	this.focus = null;

	this.selection = new Selection(0,0);
}

// Cell {{{2
function Cell( grid, row, column )
{
	this.grid = grid;
	this.row = row;
	this.column = column;

	row.cells.push( this );
	column.cells.push( this );
	grid.cells.push( this );

	this.endValue = "";
	this.value = "";

	this.width = 1;
	this.height = 1;
}

// Row {{{2
function Row( grid, previous )
{
	this.grid = grid;
	this.grid.nbRow++;
	var next = ( previous != null ) ? previous.next : null;
	// Chain list rows {{{3
	if( previous == null )
	{
		this.next = grid.firstRow;
		
		if( this.next != null )
			this.next.previous = this;
			
		this.previous = null;
		grid.firstRow = this;
	}
	else
	{
		this.next = previous.next;
		this.previous = previous;
		previous.next = this;

		if( this.next != null )
			this.next.previous = this;
	}

	// Generate cells {{{3
	var current = grid.firstColumn, cell;
	this.cells = new Array();

	var masters = new Array();

	// Populate a cell fro every column
	while( current != null )
	{
		cell = new Cell( grid, this, current );

		// Sides are automatically excluded
		if( previous != null && next != null )
		{
			// Get the master (merged) cell of the given locations
			pMaster = current.getIndexCell( previous.index ).master;
			nMaster = current.getIndexCell( next.index ).master;

			// part of the same merged cell
			if( nMaster != null && nMaster == pMaster )
			{
				cell.height = 0;
				cell.width = 0;
				cell.master = nMaster;

				// Increment master cells only once {{{4
				var found = false;
				for( key in masters )
				{
					// If master cell already found
					if( masters[key] == nMaster )
					{
						found = true
						break;
					}
				}

				if( !found )
					masters.push( nMaster );
				// }}}4
			}
		}

		current = current.next;
	}

	// Fix references {{{3
	current = this.grid.firstRow;

	while( current != null )
	{
		if( previous == null )
			current.incrementReference( null );
		else
			current.incrementReference( previous )
			
		current = current.next;
	}

	for( key in masters )
	{
		masters[key].height++;
	}
}

// Column {{{2
function Column( grid, previous )
{
	this.grid = grid;
	this.grid.nbCol++;
	var next = ( previous != null ) ? previous.next : null;
	// Chain list columns {{{3
	if( previous == null )
	{
		this.next = grid.firstColumn;

		if( this.next != null )
			this.next.previous = this;

		this.previous = null;
		grid.firstColumn = this;
	}
	else
	{
		this.next = previous.next;
		this.previous = previous;
		previous.next = this;

		if( this.next != null )
			this.next.previous = this;
	}

	// Generate cells {{{3
	var current = grid.firstRow;
	this.cells = new Array();

	var masters = new Array();

	while( current != null )
	{
		cell = new Cell( grid, current, this );

		if( previous != null && next != null )
		{
			pMaster = current.getIndexCell( previous.index ).master;
			nMaster = current.getIndexCell( next.index ).master;

			if( pMaster != null && nMaster == pMaster )
			{
				cell.height = 0;
				cell.width = 0;
				cell.master = nMaster;

				// Increment master cells only once {{{4
				var found = false;
				for( key in masters )
				{
					// If master cell already found
					if( masters[key] == nMaster )
					{
						found = true
						break;
					}
				}

				if( !found )
					masters.push( nMaster );
				// }}}4
			}
		}
		current = current.next;
	}

	// Fix references {{{3
	current = this.grid.firstColumn;

	while( current != null )
	{
		if( previous == null )
			current.incrementReference( null );
		else
			current.incrementReference( previous );
			
		current = current.next;
	}

	for( key in masters )
	{
		masters[key].width++;
	}
}
// }}}1

// Remove {{{1
// Cell.remove {{{2
Cell.prototype.remove = function()
{
	for( key in this.row.cells )
	{
		if( this.row.cells[ key ] == this )
		{
			this.row.cells.splice( key, 1 );
			break;
		}
	}

	for( key in this.column.cells )
	{
		if( this.column.cells[ key ] == this )
		{
			this.column.cells.splice( key, 1 );
			break;
		}
	}

	for( key in this.grid.cells )
	{
		if( this.grid.cells[ key ] == this )
		{
			this.grid.cells.splice( key, 1 );
			break;
		}
	}
}

// Row.remove {{{2
Row.prototype.remove = function()
{
	this.grid.nbRow--;
	var masters = new Array();

	for( key in this.cells )
		if( this.cells[key] == this.cells[key].master )
			this.cells[key].changeSize(1,1);

	while( this.cells.length > 0 )
	{
		found = false;
		for( key in masters )
			if( masters[key] == this.cells[0].master )
				found = true;

		if( !found && this.cells[0].master != null )
			masters.push( this.cells[0].master );

		this.cells[0].remove();
	}

	for( key in masters )
		masters[key].height--;

	if( this.previous != null )
		this.previous.next = this.next;
	else
		this.grid.firstRow = this.next;

	if( this.next != null )
		this.next.previous = this.previous;

	var current = this.grid.firstRow;

	while( current != null )
	{
		current.decrementReference( this );
		current = current.next;
	}
}

// Column.remove {{{2
Column.prototype.remove = function()
{
	this.grid.nbCol--;
	var masters = new Array();

	for( key in this.cells )
		if( this.cells[key] == this.cells[key].master )
			this.cells[key].changeSize(1,1);

	while( this.cells.length > 0 )
	{
		found = false;
		for( key in masters )
			if( masters[key] == this.cells[0].master )
				found = true;

		if( !found && this.cells[0].master != null )
			masters.push( this.cells[0].master );

		this.cells[0].remove();
	}

	for( key in masters )
		masters[key].width--;

	if( this.previous != null )
		this.previous.next = this.next;
	else
		this.grid.firstColumn = this.next;

	if( this.next != null )
		this.next.previous = this.previous;

	var current = this.grid.firstColumn;

	while( current != null )
	{
		current.decrementReference( this );
		current = current.next;
	}
}
// }}}1

// Reference Update {{{1
// Column.decrementReference {{{2
Column.prototype.decrementReference = function( from )
{
	globalFrom = from;

	for( key in this.cells )
	{
		this.cells[key].value = this.cells[key].value.replace( /(\$?)([A-Z]+)(\$?)([0-9]+)/g,
			function( $0, $1, $2, $3, $4 )
			{
				if( $1 != "$" && globalGrid.getColumn( $2 ).isAfter( globalFrom ) )
					return $1 + decrement($2) + $3 + $4;
				else
					return $0;
			}
		);
	}
}

// Row.decrementReference {{{2
Row.prototype.decrementReference = function( from )
{
	globalFrom = from;

	for( key in this.cells )
	{
		this.cells[key].value = this.cells[key].value.replace( /(\$?)([A-Z]+)(\$?)([0-9]+)/g,
			function( $0, $1, $2, $3, $4 )
			{
				if( $3 != "$" && globalGrid.getRow( $4 ).isAfter( globalFrom ) )
					return $1 + 2 + $3 + String( parseInt( $4 ) - 1 );
				else
					return $0;
			}
		);
	}
}

// Column.incrementReference {{{2
Column.prototype.incrementReference = function( from )
{
	globalFrom = from;

	for( key in this.cells )
	{
		this.cells[key].value = this.cells[key].value.replace( /(\$?)([A-Z]+)(\$?)([0-9]+)/g,
			function( $0, $1, $2, $3, $4 )
			{
				if( $1 != "$" && globalGrid.getColumn( $2 ).isAfter( globalFrom ) )
					return $1 + increment($2) + $3 + $4;
				else
					return $0;
			}
		);
	}
}

// Row.incrementReference {{{2
Row.prototype.incrementReference = function( from )
{
	globalFrom = from;

	for( key in this.cells )
	{
		this.cells[key].value = this.cells[key].value.replace( /(\$?)([A-Z]+)(\$?)([0-9]+)/g,
			function( $0, $1, $2, $3, $4 )
			{
				if( $3 != "$" && globalGrid.getRow( $4 ).isAfter( globalFrom ) )
					return $1 + $2 + $3 + String( parseInt( $4 ) + 1 );
				else
					return $0;
			}
		);
	}
}
// }}}1

// Element Manipulation {{{1
// Column.isAfter {{{2
Column.prototype.isAfter = function( element )
{
	if( element == null )
		return true;

	current = element.next;

	while( current != null )
	{
		if( current == this )
			return true;

		current = current.next;
	}

	return false;
}

// Row.isAfter {{{2
Row.prototype.isAfter = function( element )
{
	if( element == null )
		return true;

	current = element.next;

	while( current != null )
	{
		if( current == this )
			return true;

		current = current.next;
	}

	return false;
}

// Row.getCell {{{2
Row.prototype.getCell = function( curcolumn )
{
	for( key in this.cells )
	{
		if( this.cells[ key ].column == curcolumn )
			return this.cells[ key ];
	}

	alert( "An error occured, inexistant column in row.getCell" );
}

// Row.getIndexCell {{{2
Row.prototype.getIndexCell = function( index )
{
	for( key in this.cells )
		if( this.cells[key].column.index == index )
			return this.cells[key];

	return null;
}

// Column.getIndexCell {{{2
Column.prototype.getIndexCell = function( index )
{
	for( key in this.cells )
		if( this.cells[key].row.index == index )
			return this.cells[key];

	return null;
}

// Column.getCell {{{2
Column.prototype.getCell = function( currow )
{
	for( key in this.cells )
	{
		if( this.cells[ key ].row == currow )
			return this.cells[ key ];
	}

	alert( "An error occured, inexistant row in column.getCell" );
}

// Cell.getId {{{2
Cell.prototype.getId = function()
{
	return this.column.id + this.row.id;
}

// Grid.getRow {{{2
Grid.prototype.getRow = function( id )
{
	var current = this.firstRow;

	while( current != null )
	{
		if( current.id == id )
			return current;

		current = current.next;
	}

	alert( "An error occured, inexistant row in grid.getRow" );
}

// Grid.getColumn {{{2
Grid.prototype.getColumn = function( id )
{
	var current = this.firstColumn;

	while( current != null )
	{
		if( current.id == id )
			return current;

		current = current.next;
	}

	alert( "An error occured, inexistant column in grid.getColumn" );
}

// Grid.getCell {{{2
Grid.prototype.getCell = function( id )
{
	for( key in this.cells )
	{
		if( this.cells[ key ].getId() == id )
			return this.cells[ key ];
	}

	alert( "An error occured, inexistant cell in grid.getCell" );
}

// Grid.getIndexCell {{{2
Grid.prototype.getIndexCell = function( row, col )
{
	for( i = 0; this.cells.length > i; i++ )
	{
		cell = this.cells[i];

		if( cell.column.index == col && cell.row.index == row )
			return cell;
	}

	return null;
}
// }}}1

// Value Update {{{1
// Grid.update {{{2
Grid.prototype.update = function( input )
{
	c = this.getCell( input.name );
	c.setValue( input.value );
}

// Cell.setValue {{{2
Cell.prototype.setValue = function( value )
{
	this.value = value;

	this.grid.refresh();
}

// Grid.identify {{{2
Grid.prototype.identify = function()
{
	var i = 0;
	var index = 0;
	var current = this.firstColumn;
	
	while( current != null )
	{
		if( current.previous == null )
			current.id = COLCHAR[0];
		else
			current.id = increment( current.previous.id );

		current.index = index++;
		current = current.next;
	}

	index = 0;
	current = this.firstRow;
	while( current != null )
	{
		current.id = String( ++i );
		current.index = index++
		current = current.next;
	}
}
// }}}1

// Draw {{{1
// Grid.draw {{{2
Grid.prototype.draw = function()
{
	var current = this.firstColumn;
	var total = '<input type="text" id="topbar" name="bar" onChange="g.focus.setValue( this.value );" />\n<table>\n';

	this.identify();

	total += "<tr>\n<th></th>\n";
	while( current != null )
	{
		total += "<th align='center'>" + current.id + "</th>\n";
		current = current.next;
	}
	total += "</tr>\n";

	current = this.firstRow;
	while( current != null )
	{
		total += current.draw();
		current = current.next;
	}

	total += '</table>\n';

	this.target.innerHTML =  total;
	this.selection = new Selection( this.nbRow, this.nbCol );
}

// Row.draw {{{2
Row.prototype.draw = function()
{
	var current = this.grid.firstColumn;
	var total = '<tr>\n<th>' + this.id + '</th>\n';

	while( current != null )
	{
		total += this.getCell( current ).draw();
		current = current.next;
	}

	total += '</tr>\n';

	return total;
}

// Cell.draw {{{2
Cell.prototype.draw = function()
{
	if( this.width != 0 && this.height != 0 )
		return '<td colspan="' + String(this.width) + '" rowspan="' + String(this.height) + '"><input type="text" name="' + this.getId() + '" value="' + this.endValue + '" onChange="g.update( this )" onFocus="g.focus = g.getCell( this.name ); g.target.bar.value = g.getCell( this.name ).value; g.singleSelect( this.name, event );" onmousedown="g.preFocus();" onClick="g.handleClick( this.name, event )" onDblClick="g.target.bar.focus(); g.focus = g.getCell( this.name );" /></td>\n';
	else
		return "<!--" + this.getId() + "-->";
}

// Grid.refresh {{{2
Grid.prototype.refresh = function()
{
	var target;
	for( key in this.cells )
	{
		var c = this.cells[key];
		c.endValue = processValue( c.value );
		if( ( target = this.target[ c.getId() ] ) != null )
			target.value = c.endValue;
	}
}
// }}}1

// Submit {{{1
// Grid.prepareSubmit {{{2
Grid.prototype.prepareSubmit = function()
{
	var append;
	for( key in this.cells )
	{
		var c = this.cells[key];
		append = "<<<" + String(c.width) + "," + String(c.height) + ">>>";

		if( this.target[ c.getId() ] == null )
			continue;

		if( c.endValue != c.value )
			this.target[ c.getId() ].value = String(c.endValue).replace( /=/, "\\=" ) + String( c.value ) + append;
		else
			this.target[ c.getId() ].value = c.value.replace( /=/, "\\=" ) + "=" + append;

	}
}
// }}}1

// Selection {{{1
// Grid.preFocus {{{2
Grid.prototype.preFocus = function()
{
	this.md = true;
}

// Grid.singleSelect {{{2
Grid.prototype.singleSelect = function( cell, event )
{
	if( this.md )
	{
		this.md = false;
		return;
	}

	this.selection.singleSelect( this.getCell(cell) );

	this.updateSelection();
}

// Grid.handleClick {{{2
Grid.prototype.handleClick = function( cell, event )
{
	element = this.getCell( cell );
	if( event.shiftKey )
	{
		this.selection.rangeTo( element );
	}
	else if( event.ctrlKey )
	{
		this.selection.toggle( element );
	}
	else
	{
		this.selection.singleSelect( element );
	}
		
	this.updateSelection();
}

// Grid.updateSelection {{{2
Grid.prototype.updateSelection = function()
{
	for( key in this.cells )
	{
		curcell = this.cells[key];
		id = curcell.getId();
		input = this.target[id];

		if( input == null )
			continue;
		
		if( curcell.isSelected() )
		{
			if( input.className == '' )
			{
				input.className = 'selected';
			}
		}
		else
		{
			if( input.className == 'selected' )
				input.className = '';
		}
	}
}

// Cell.isSelected {{{2
Cell.prototype.isSelected = function()
{
	return this.grid.selection.grid[this.row.index][this.column.index];
}

// }}}1

// Merge {{{1
// Cell.changeSize {{{2
Cell.prototype.changeSize = function( row, col )
{
	var i, j, baseRow, baseCol, maxRow, maxCol, cell;

	// Restore to normal {{{3
	baseRow = this.row.index;
	baseCol = this.column.index;
	maxRow = this.height;
	maxCol = this.width;

	if( row > maxRow )
		maxRow = row;
	if( col > maxCol )
		maxCol = col;

	maxRow += baseRow;
	maxCol += baseCol;
		
	for( i = baseRow; maxRow > i; i++ )
	{
		for( j = baseCol; maxCol > j; j++ )
		{
			cell = this.grid.getIndexCell( i, j );
			cell.width = 1;
			cell.height = 1;
			cell.master = null;
		}
	}

	// Hide concerned cells {{{3
	maxRow = baseRow + row;
	maxCol = baseCol + col;
		
	for( i = baseRow; maxRow > i; i++ )
	{
		for( j = baseCol; maxCol > j; j++ )
		{
			cell = this.grid.getIndexCell( i, j );

			cell.width = 0;
			cell.height = 0;
			cell.master = this;
		}
	}

	// Set the main cell span {{{3
	this.width = col;
	this.height = row;
}

// }}}1

// Selection Class {{{1
// Constructor {{{2
function Selection( rowCount, columnCount )
{
	this.grid = new Array;

	for( i = 0; rowCount >= i; i++ )
	{
		this.grid.push( new Array )
		for( j = 0; columnCount >= j; j++ )
			this.grid[i][j] = false;
	}

	this.row = rowCount;
	this.column = columnCount;

	this.originalSelect = new Object;

	this.width = 0;
	this.height = 0;
	this.topLeft = 0;
}

// Selection.isBlock {{{2
Selection.prototype.isBlock = function()
{
	var sRow, eRow, sCol, eCol;
	
	for( i = 0; this.row > i; i++ )
	{
		for( j = 0; this.column > j; j++ )
		{
			if( sRow == null && this.grid[i][j] )
			{
				sRow = i;

				for( temp = i; this.row > temp; temp++ )
				{
					if( !this.grid[temp+1][j] )
					{
						eRow = temp;
						break;
					}
				}
			}

			if( sRow != null && eRow != null && this.grid[i][j] )
				if( i < sRow || i > eRow )
					return false;

			if( sCol == null && this.grid[i][j] )
				sCol = j;

			if( sCol != null && eCol == null && !this.grid[i][j+1] )
				eCol = j;
			
			if( sCol != null && eCol != null && this.grid[i][j] )
				if( j < sCol || j > eCol )
					return false;
		}
	}

	this.height = eRow - sRow + 1;
	this.width = eCol - sCol + 1;

	this.topLeft = { row: sRow, column: sCol };

	return true;
}

// Selection.rangeTo {{{2
Selection.prototype.rangeTo = function( cell )
{
	oRow = this.originalSelect.row;
	oCol = this.originalSelect.column;
	tRow = cell.row.index;
	tCol = cell.column.index;

	iRow = ( tRow < oRow ) ? -1 : 1;
	iCol = ( tCol < oCol ) ? -1 : 1;

	var tempRow;

	while( oCol - iCol != tCol )
	{
		tempRow = oRow;

		while( tempRow - iRow != tRow )
		{
			this.grid[tempRow][oCol] = true;
			tempRow += iRow;
		}

		oCol += iCol;
	}
}

// Selection.set {{{2
Selection.prototype.set = function( cell, value )
{
	this.grid[cell.row.index][cell.column.index] = value;

	if( value )
	{
		this.originalSelect.row = cell.row.index;
		this.originalSelect.column = cell.column.index;
	}
}

// Selection.singleSelect {{{2
Selection.prototype.singleSelect = function( cell )
{
	for( i = 0; this.row > i; i++ )
		for( j = 0; this.column > j; j++ )
			this.grid[i][j] = false;
			
	this.set( cell, true );
}

// Selection.toggle {{{2
Selection.prototype.toggle = function( cell )
{
	this.set( cell, !this.grid[cell.row.index][cell.column.index] );
}
// }}}1
