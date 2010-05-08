/* 
 *  FCKPlugin.js
 *  ------------
 *  This was a generic file which is needed for plugins that are developed
 *  for FCKEditor. With the below statements that toolbar is created and
 *  several options are being activated.
 *
 *  See the online documentation for more information:
 *  http://wiki.fckeditor.net/
 */

// ### Switch command
var FCKTikiSwitchCommand = function()
{
	this.Name = 'Switch' ;
}

FCKTikiSwitchCommand.prototype.Execute = function()
{

	if ( typeof( top.switchEditor ) == 'function' )
	{
		// Get the linked field form.
		var oForm = FCK.GetParentForm();
		top.switchEditor("wiki", oForm);
	}

}

FCKTikiSwitchCommand.prototype.GetState = function()
{
	return FCK_TRISTATE_OFF ;
}

// Register the related commands.
FCKCommands.RegisterCommand(
	'tikiswitch',
	new FCKTikiSwitchCommand()
);

var otikiswitchItem = new FCKToolbarButton( 'tikiswitch', FCKConfig.tikiswitchBtn, null, null, false, true ); 
otikiswitchItem.IconPath = _TikiRoot + 'pics/icons/pencil_go.png'; 

FCKToolbarItems.RegisterItem( 'tikiswitch', otikiswitchItem );

