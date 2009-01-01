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
	var nChar = COLCHAR.indexOf( val.charAt( val.length - 1 ) ) - 1;
	
	if( nChar >= 0 )
	{
		return val.substr( 0, val.length - 1 ) + COLCHAR.charAt( nChar );
	}
	else
	{
		return decrement( val.substr( 0, val.length - 1 ) ) + COLCHAR.charAt( COLCHAR.length - 1 );
	}
}

// increment {{{2
function increment( val )
{
	var nChar = COLCHAR.indexOf( val.charAt( val.length - 1 ) ) + 1;
	
	if( nChar < COLCHAR.length )
	{
		return val.substr( 0, val.length - 1 ) + COLCHAR.charAt( nChar );
	}
	else
	{
		return increment( val.substr( 0, val.length - 1 ) ) + COLCHAR.charAt(0);
	}
}

// processValue {{{2
function processValue( id )
{
	var cell = globalGrid.getCell( id );
	if( cell.calc )
		return cell.endValue;
		
	var value = cell.value;

	if( value == null || value.substr( 0, 1 ) != "=" )
	{
		cell.endValue = value;
	}
	else
	{
		var temp = value.substr( 1 );

		temp = temp.replace( /\$?([A-Z]+)\$?([0-9]+):\$?([A-Z]+)\$?([0-9]+)/g,
			function( $0, $1, $2, $3, $4 )
			{
				var topLeft = globalGrid.getCell( $1 + $2 );
				var bottomRight = globalGrid.getCell( $3 + $4 );

				var row = topLeft.row;
				var col;

				var list = new Array;

				while( row != bottomRight.row.next )
				{
					col = topLeft.column;

					while( col != bottomRight.column.next )
					{
						c = row.getCell( col );

						if( c.master == null || c.master == c )
							list.push( c.getId() );

						col = col.next;
					}

					row = row.next;
				}

				return "[" + list.join(",") + "]";
			}
		);

		temp = temp.replace( /\$?([A-Z]+)\$?([0-9]+)/g, "parseFloat(processValue( '$1$2' ))" );

		cell.endValue = eval( temp );
	}

	field = globalGrid.target[id];

	cell.calc = true;

	if( field )
		field.value = cell.getDisplayValue();

	return cell.endValue;
}

// }}}1

// Extra Array functionalities {{{1
Array.prototype.remove = function( element )
{
	var key;
	for( key in this )
		if( this[key] == element )
		{
			this.splice( key, 1 );
			return true;
		}

	return false;
}

Array.prototype.contains = function( element )
{
	var key;
	for( key in this )
		if( this[key] == element )
			return true;

	return false;
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

	// Contributions  (sylvieg request)
	this.contributions = new Array();
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
	this.format = '';

	this.calc = false;

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
	this.row.cells.remove( this );
	this.column.cells.remove( this );
	this.grid.cells.remove( this );
}

// Row.remove {{{2
Row.prototype.remove = function()
{
	this.grid.nbRow--;
	var masters = new Array();

	for( var key = 0; this.cells.length > key; key++ )
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

	for( var key = 0; this.cells.length > key; key++ )
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

	for( var key = 0; this.cells.length > key; key++ )
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

	for( var key = 0; this.cells.length > key; key++ )
	{
		if( this.cells[key].value == null )
			continue;

		this.cells[key].value = this.cells[key].value.replace( /(\$?)([A-Z]+)(\$?)([0-9]+)/g,
			function( $0, $1, $2, $3, $4 )
			{
				if( $3 != "$" && globalGrid.getRow( $4 ).isAfter( globalFrom ) )
				{
					return $1 + $2 + $3 + String( parseInt( $4 ) - 1 );
				}
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

	for( var key = 0; this.cells.length > key; key++ )
	{
		if( this.cells[key].value == null )
			continue;

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

	for( var key = 0; this.cells.length > key; key++ )
	{
		if( this.cells[key].value == null )
			continue;

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
Row.prototype.getCell = function( column )
{
	for( var key = 0; this.cells.length > key; key++ )
	{
		if( this.cells[ key ].column == column )
			return this.cells[ key ];
	}

	alert( "An error occured, inexistant column in row.getCell" );
}

// Row.getIndexCell {{{2
Row.prototype.getIndexCell = function( index )
{
	for( var key = 0; this.cells.length > key; key++ )
		if( this.cells[key].column.index == index )
			return this.cells[key];

	return null;
}

// Column.getIndexCell {{{2
Column.prototype.getIndexCell = function( index )
{
	for( var key = 0; this.cells.length > key; key++ )
		if( this.cells[key].row.index == index )
			return this.cells[key];

	return null;
}

// Column.getCell {{{2
Column.prototype.getCell = function( row )
{
	for( var key = 0; this.cells.length > key; key++ )
	{
		if( this.cells[ key ].row == row )
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
	for( var key = 0; this.cells.length > key; key++ )
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
			current.id = COLCHAR.charAt(0);
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

// getDisplayValue {{{2
Cell.prototype.getDisplayValue = function()
{
	if( this.format == null )
		return this.endValue;

	var fnc;
	fnc = display[this.format];

	if( !fnc )
		return this.endValue;

	return fnc( this.endValue );
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

	total += '</table>\n' + this.drawContribution();

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
	var len = this.cells.length;
	for( var i = 0; len > i; i++ )
		this.cells[i].calc = false;

	for( var i = 0; len > i; i++ )
		processValue( this.cells[i].getId() );
}
// }}}1

// Submit {{{1
// Grid.prepareSubmit {{{2
Grid.prototype.prepareSubmit = function()
{
	var append;
	for( var key = 0; this.cells.length > key; key++ )
	{
		var c = this.cells[key];
		append = "<<<" + String(c.width) + "," + String(c.height) + ">>>" + String( c.format );

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
	for( var key = 0; this.cells.length > key; key++ )
	{
		curcell = this.cells[key];

		if( curcell.value == null )
			continue;
			
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

// Format cells {{{1
Grid.prototype.format = function( type )
{
	if( type == '' ) type = null;
	
	for( var key = 0; this.cells.length > key; key++ )
		if( this.cells[key].isSelected() )
			this.cells[key].format = type;
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

// Copy Values {{{1
Grid.prototype.copy = function( direction )
{
	var begin1, begin2, end1, end2, type, chtype;

	if( g.selection.isBlock() )
	{
		temp = g.selection.topLeft;
		topLeft = g.getIndexCell( temp.row, temp.column );
		bottomRight = g.getIndexCell( temp.row + g.selection.height - 1, temp.column + g.selection.width - 1 );
		
		switch( direction ) // Parameters {{{2
		{
		case "Left":
			begin1 = bottomRight.column;
			end1 = topLeft.column;
			begin2 = topLeft.row;
			end2 = bottomRight.row;
			type = 'previous';
			chtype = 'column';
			break;
		case "Right":
			begin1 = topLeft.column;
			end1 = bottomRight.column;
			begin2 = topLeft.row;
			end2 = bottomRight.row;
			type = 'next';
			chtype = 'column';
			break;
		case "Up":
			begin1 = bottomRight.row;
			end1 = topLeft.row;
			begin2 = topLeft.column;
			end2 = bottomRight.column;
			type = 'previous';
			chtype = 'row';
			break;
		case "Down":
			begin1 = topLeft.row;
			end1 = bottomRight.row;
			begin2 = topLeft.column;
			end2 = bottomRight.column;
			type = 'next';
			chtype = 'row';
			break;
		} // }}}2

		var current2 = begin2;
		// For each 'line' to process
		while( end2.next != current2 )
		{
			var current1 = begin1[type];
			var v = begin1.getCell( current2 ).value;

			while( end1[type] != current1 )
			{
				var cell = current1.getCell( current2 );

				// Increment/Decrement references {{{2
				if( chtype == 'row' )
				{
					window.diff = ( type == 'previous' ) ? -1 : 1;

					v = v.replace( /(\$?)([A-Z]+)(\$?)([0-9]+)/g,
						function( $0, $1, $2, $3, $4 )
						{
							if( $3 != "$" )
								return $1 + $2 + $3 + String( parseInt( $4 ) + window.diff );
							else
								return $0;
						}
					);
				}
				else
				{
					if( type == 'previous' )
					{
						v = v.replace( /(\$?)([A-Z]+)(\$?)([0-9]+)/g,
							function( $0, $1, $2, $3, $4 )
							{
								if( $1 != "$" )
									return $1 + decrement($2) + $3 + $4;
								else
									return $0;
							}
						);
					}
					else
					{
						v = v.replace( /(\$?)([A-Z]+)(\$?)([0-9]+)/g,
							function( $0, $1, $2, $3, $4 )
							{
								if( $1 != "$" )
									return $1 + increment($2) + $3 + $4;
								else
									return $0;
							}
						);
					}
				}
				// }}}2

				cell.value = v;
				current1 = current1[type];
			}
			
			current2 = current2.next;
		}

		this.refresh();
	}
}
// }}}1

// Contribution (sylvieg request) {{{1

Grid.prototype.addContribution = function( id, name )
{
	this.contributions.push( { id: id, name: name } );
}

Grid.prototype.drawContribution = function()
{
	var data = "";

	if( this.contributions.length > 0 )
	{
		var key;
		data = "<select name='contributions[]' multiple='multiple' size='3'>";
		
		for (var key=0; key < this.contributions.length; key++) 
			data += "<option value=\"" + this.contributions[key].id + "\">" + this.contributions[key].name + "</option>";

		data += "</select>";
	}

	return data;
}

// }}}1
