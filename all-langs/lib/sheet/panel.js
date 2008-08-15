/**
 * TikiSheet panel manipulation.
 * By Louis-Philippe Huberdeau
 * 2004
 */
var panels = new Array();
var pointer = new Object();

// Document initialization {{{1
document.onmousemove = function( e )
{
    pointer.x = e.screenX;
    pointer.y = e.screenY;

    if( pointer.moving != null )
    {
        pointer.moving.move();
    }
}
// }}}1

// Tools {{{1
function drawPanels( location )
{
    var total = "";
    
    for( key in panels )
    {
        total += panels[key].draw();
    }

    location.innerHTML = total;
}

function getPanel( panel )
{
    for( key in panels )
    {
        if( panels[key].title == panel )
            return panels[key];
    }

    return false;
}

function addPanel( panel )
{
    panels.push( panel );
}
// }}}1

// Panel Object {{{1
function Panel( title, x, y )
{
    this.title = title;
    this.x = x;
    this.y = y;
    this.content = "";

    this.div = null;
}

// Draw {{{2
Panel.prototype.draw = function()
{
    var total = '<div class="panel" id="' + this.title + '" style="left: ' + String( this.x ) + '; top: ' + String( this.y ) + ';">';

    total += '<table onMouseDown="getPanel( \'' + this.title + '\' ).bringToFront()">';
    total += '<tr><th onMouseUp="getPanel( \'' + this.title + '\' ).stopMove()" onMouseDown="getPanel( \'' + this.title + '\' ).startMove()" onMouseMove="getPanel( \'' + this.title + '\' ).move()">' + this.title + '</th></tr>';
    total += '<tr><td>' + this.content + '</td></tr>';
    total += '</table>';
    
    total += '</div>';

    return total;
}
// }}}2

// Events {{{2
Panel.prototype.startMove = function()
{
    this.relX = pointer.x - this.x;
    this.relY = pointer.y - this.y;

    pointer.moving = this;

    this.div = document.getElementById( this.title );
    this.div.className = 'panelMoving';
}

Panel.prototype.move = function()
{
    if( pointer.moving == null )
        return;

    var s = this.div.style;

    s.left = pointer.x - this.relX;
    s.top = pointer.y - this.relY;
}

Panel.prototype.stopMove = function()
{
    pointer.moving = null;

    s = this.div.style;

    this.x = parseInt( s.left );
    this.y = parseInt( s.top );

    this.div.className = "panel";
}

Panel.prototype.bringToFront = function()
{
    var val = 1;
    for( key in panels )
    {
        if( panels[key] == this )
            val = 5;
        else
            val = 1;

        document.getElementById( panels[key].title ).style.zIndex = val;
    }
}
// }}}2

Panel.prototype.setContent = function( content )
{
    this.content = content;
}
// }}}1
