/**
 * TikiSheet controls.
 * By Louis-Philippe Huberdeau
 * 2004
 */

// Insert Row {{{1
function insertRowClick()
{
    element = document.getElementById( 'detail' );

    var str = '<form name="insert" onSubmit="insertRowSubmit(this)">Insert Row:<br /> <input type="radio" name="pos" value="before" checked /> Before<br /> <input type="radio" name="pos" value="after" /> After<br /> <select name="row">';

    var current = g.firstRow;

    while( current != null )
    {
        str += '<option value="' + current.id + '">' + current.id + '</option>';
        current = current.next;
    }

    str += '</select><br /><input type="submit" name="submit" value="Insert Row" /></form>';

    element.innerHTML = str;
}

function insertRowSubmit( form )
{
    var r = g.getRow( form.row.value );
    
    if( form.pos[0].value == "before" && form.pos[0].checked )
        new Row( g, r.previous );
    else
        new Row( g, r );

    g.draw();
    g.refresh();

    document.getElementById( 'detail' ).innerHTML = "";

	return false;
}

// Insert Column {{{1
function insertColumnClick()
{
    element = document.getElementById( 'detail' );

    var str = '<form name="insert" onSubmit="insertColumnSubmit(this)">Insert Column:<br /> <input type="radio" name="pos" value="before" checked /> Before<br /> <input type="radio" name="pos" value="after" /> After<br /> <select name="column">';

    var current = g.firstColumn;

    while( current != null )
    {
        str += '<option value="' + current.id + '">' + current.id + '</option>';
        current = current.next;
    }

    str += '</select><br /><input type="submit" name="submit" value="Insert Column" /></form>';

    element.innerHTML = str;
}

function insertColumnSubmit( form )
{
    var r = g.getColumn( form.column.value );
    
    if( form.pos[0].value == "before" && form.pos[0].checked )
        new Column( g, r.previous );
    else
        new Column( g, r );

    g.draw();
    g.refresh();

    document.getElementById( 'detail' ).innerHTML = "";

	return false;
}

// Remove Row {{{1
function removeRowClick()
{
    element = document.getElementById( 'detail' );

    var str = '<form name="insert" onSubmit="removeRowSubmit(this)">Remove Row:<br /> <select name="row">';

    var current = g.firstRow;

    while( current != null )
    {
        str += '<option value="' + current.id + '">' + current.id + '</option>';
        current = current.next;
    }

    str += '</select><br /><input type="submit" name="submit" value="Remove Row" /></form>';

    element.innerHTML = str;
}

function removeRowSubmit( form )
{
    g.getRow( form.row.value ).remove();

    g.draw();
    //g.refresh();

    document.getElementById( 'detail' ).innerHTML = "";

	return false;
}

// Remove Column {{{1
function removeColumnClick()
{
    element = document.getElementById( 'detail' );

    var str = '<form name="insert" onSubmit="removeColumnSubmit(this)">Remove Column:<br /> <select name="column">';

    var current = g.firstColumn;

    while( current != null )
    {
        str += '<option value="' + current.id + '">' + current.id + '</option>';
        current = current.next;
    }

    str += '</select><br /><input type="submit" name="submit" value="Remove Column" /></form>';

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
		g.refresh();
		g.draw();
	}
}

function restoreCellClick()
{
	if( g.selection.isBlock() )
	{
		cell = g.getIndexCell( g.selection.topLeft.row, g.selection.topLeft.column );
		cell.changeSize( 1, 1 );
		g.refresh();
		g.draw();
	}
}
