{* $Id$ *}
{* \brief Show wysiwyg help 
 * included by tiki-show_help.tpl via smarty_block_add_help() *}

{add_help show='y' title="{tr}Wiki Wysiwyg Help{/tr}" id="wiki_help"}

<h3>{tr}Wiki Wysiwyg Help{/tr}</h3>
<div class="help_section">
{if $prefs.feature_help eq 'y'} 
<p>{tr}For more information, please see{/tr}
	<a href="{$prefs.helpurl}Wysiwyg+Editor" target="tikihelp" class="tikihelp" title="{tr}Wysiwyg Editor:{/tr} {tr}More help on wysiwyg editing{/tr}">
		{tr}Wysiwyg Editor{/tr} {icon _id='help' style="vertical-align:middle"}
	</a>
</p>
{/if}
 
<hr>
<p>
	{icon _id="star"} {tr}WYSIWYG in wiki mode is an experimental feature in Tiki 7+ and uses
	<a href="http://ckeditor.com">CKEditor</a>{/tr}</p>
<p>
	{tr}To switch modes between WYSIWYG and Wiki edit modes click the {icon _id="pencil_go"} button on the toolbar.{/tr}</p>
<p style="margin-left: 2em; ">
	{tr}<strong>N.B. </strong>Using this button changes the editor between WYSIWYG and the usual wiki editor, but leaves the page source in wiki syntax.{/tr}</p>
<p>
	{icon _id="plugin"} {tr}Wiki plugins can be used and edited in WYSIWYG mode, double click the plugin to bring up the plugin edit popup form.<br>
	Here is an example of the {ldelim}BOX{rdelim} plugin:{/tr}</p>
<div style="background-color: #fff; padding: 1em;">
<div class="tiki_plugin" plugin="box" style="position:relative; background-color: #eee; border: 1px solid #666;">
	<div contenteditable="false">
		<img src="img/icons/wiki_plugin_edit.png" width="16" height="16" style="float:left;position:absolute;z-index:10001">
		<table align="center" width="80%">
			<tr><td>
				<div class='cbox ' style=' background:#fffff0'><div class='cbox-data' style=" background:#fffff0">
					{tr}This is the text in the box which is justified; the box has a cream background, takes 80% of the screen width and is centred.{/tr}
    			</div></div>
    		</td></tr>
    	</table>
	</div>
</div>
</div>
<p style="margin-left: 2em;">
	{icon _id="error"} {tr}<strong>Caution:</strong> This is a new feature and still work in progress.<br>
	If you intend to use plugins extensively in a page consider editing that page in wiki mode only.<br>
	Currently (in Tiki 9.0) drag and drop of plugins in WYSIWYG does not work.{/tr}</p>

	{if $tiki_p_admin eq "y"}
		<hr style="border-width: 1px;" />
		<p>
			{tr}<strong>Admins:</strong><br>
			<em>This new implementation is designed to work best with some site preferences set in a certain way.
			These can all be found on the <a href="tiki-admin.php?page=wysiwyg">wysiwyg admin page</a>.
			A profile to set these up correctly can be found on <a href="http://profiles.tiki.org/WYSIWYG_6x">profiles.tiki.org</a>
			which can be applied using the <a href="tiki-admin.php?page=profiles">profiles admin page</a>.</em>{/tr}</p>
	{/if}

</div>

{/add_help}
