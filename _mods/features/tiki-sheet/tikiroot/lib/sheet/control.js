/**
 * TikiSheet controls.
 * By Louis-Philippe Huberdeau
 * 2004
 */

// Insert Row {{{1
function insertRowClick()
{
    element = document.getElementById( 'detail' );

    var str = controlInsertRowBefore;

    var current = g.firstRow;

    while( current != null )
    {
        str += '<option value="' + current.id + '">' + current.id + '</option>';
        current = current.next;
    }

    str += controlInsertRowAfter;

    element.innerHTML = str;
}

function insertRowSubmit( form )
{
    var r = g.getRow( form.row.value );
	var num = parseInt( form.qty.value );
    
    if( form.pos[0].value == "before" && form.pos[0].checked )
		for( var i = 0; num > i; i++ )
		{
			new Row( g, r.previous );
			g.identify();
		}
    else
		for( var i = 0; num > i; i++ )
		{
			new Row( g, r );
			g.identify();
		}

	g.draw();
	g.refresh();

    document.getElementById( 'detail' ).innerHTML = "";

	return false;
}

// Insert Column {{{1
function insertColumnClick()
{
    element = document.getElementById( 'detail' );

    var str = controlInsertColumnBefore;

    var current = g.firstColumn;

    while( current != null )
    {
        str += '<option value="' + current.id + '">' + current.id + '</option>';
        current = current.next;
    }

    str += controlInsertColumnAfter;

    element.innerHTML = str;
}

function insertColumnSubmit( form )
{
    var r = g.getColumn( form.column.value );
	var num = parseInt( form.qty.value );
    
    if( form.pos[0].value == "before" && form.pos[0].checked )
		for( var i = 0; num > i; i++ )
		{
			new Column( g, r.previous );
			g.draw();
		}
    else
		for( var i = 0; num > i; i++ )
		{
			new Column( g, r );
			g.draw();
		}

    g.refresh();

    document.getElementById( 'detail' ).innerHTML = "";

	return false;
}

// Remove Row {{{1
function removeRowClick()
{
    element = document.getElementById( 'detail' );

    var str = controlRemoveRowBefore;

    var current = g.firstRow;

    while( current != null )
    {
        str += '<option value="' + current.id + '">' + current.id + '</option>';
        current = current.next;
    }

    str += controlRemoveRowAfter;

    element.innerHTML = str;
}

function removeRowSubmit( form )
{
    row = g.getRow( form.row.value );
	row.remove();

    g.draw();
    g.refresh();

    document.getElementById( 'detail' ).innerHTML = "";

	return false;
}

// Remove Column {{{1
function removeColumnClick()
{
    element = document.getElementById( 'detail' );

    var str = controlRemoveColumnBefore;

    var current = g.firstColumn;

    while( current != null )
    {
        str += '<option value="' + current.id + '">' + current.id + '</option>';
        current = current.next;
    }

    str += controlRemoveColumnAfter;

    element.innerHTML = str;
}

function removeColumnSubmit( form )
{
    g.getColumn( form.column.value ).remove();

    g.draw();
    g.refresh();

    document.getElementById( 'detail' ).innerHTML = "";

	return false;
}

// Merge Cells {{{1
function mergeCellClick()
{
	if( g.selection.isBlock() )
	{
		row = g.selection.topLeft.row;
		col = g.selection.topLeft.column;
		cell = g.getIndexCell( row, col );
		cell.changeSize( g.selection.height, g.selection.width );
		g.draw();
		g.refresh();
	}
}

function restoreCellClick()
{
	if( g.selection.isBlock() )
	{
		cell = g.getIndexCell( g.selection.topLeft.row, g.selection.topLeft.column );
		cell.changeSize( 1, 1 );
		g.draw();
		g.refresh();
	}
}

// Copy Calculation {{{1
function copyCalculationClick()
{
    element = document.getElementById( 'detail' );
	
	element.innerHTML = controlCopyCalculation;
}

function copyCalculationSubmit( form )
{
	g.copy( form.clicked.value );

    document.getElementById( 'detail' ).innerHTML = "";

	return false;
}

// Format Cell
function formatCellClick()
{
    element = document.getElementById( 'detail' );
	
	var str = controlFormatCellBefore;
	for( key in display )
		str += '<option>' + key + '</option>';
	str += controlFormatCellAfter;

	element.innerHTML = str;
}

function formatCellSubmit( form )
{
	g.format( form.format.value );
	g.refresh();

    document.getElementById( 'detail' ).innerHTML = "";

	return false;
}
