/* 
 *  FCKPlugin.js
 *  ------------
 *  This is a generic file which is needed for plugins that are developed
 *  for FCKEditor. With the below statements that toolbar is created and
 *  several options are being activated.
 *
 *  See the online documentation for more information:
 *  http://wiki.fckeditor.net/
 */

// Register the related commands.
FCKCommands.RegisterCommand(
	'tikilink',
	new FCKDialogCommand(
		'tikilink',
		FCKConfig.tikilinkDlgTitle,
		FCKPlugins.Items['tikilink'].Path + 'fck_tikilink.php',
		400,
		600
	)
);
 
var otikilinkItem = new FCKToolbarButton( 'tikilink', FCKConfig.tikilinkBtn, null, null, false, true ); 
otikilinkItem.IconPath = _TikiRoot + '/pics/icons/page_white_link.png'; 

FCKToolbarItems.RegisterItem( 'tikilink', otikilinkItem );
