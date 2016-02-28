{* $Id$ *}
{* \brief Show wysiwyg help
 * included by tiki-show_help.tpl via smarty_block_add_help() *}

<h3>{tr}WYSIWYG Help{/tr}</h3>
<div class="help_section">
{if $prefs.feature_help eq 'y'}
<p>{tr}For more information, please see{/tr}
	<a href="{$prefs.helpurl}Wysiwyg+Editor" target="tikihelp" class="tikihelp" title="{tr}WYSIWYG Editor:{/tr} {tr}More help on WYSIWYG editing{/tr}">
		{tr}WYSIWYG Editor{/tr} {icon name='help' style="vertical-align:middle"}
	</a>
</p>
{/if}

<hr>
<p>
	{icon name="star"} {tr}The WYSIWYG editor in Tiki is <a href="http://ckeditor.com">CKEditor</a>{/tr}</p>
<p>
	{tr}To switch modes between WYSIWYG and Wiki edit modes click the {icon name="pencil_go"} button on the toolbar.{/tr}</p>
<p style="margin-left: 2em; ">
{if $prefs.wysiwyg_htmltowiki ne 'y'}
	{tr}<strong>Note: </strong>Using this button converts the page source from wiki to HTML, or vice versa.
	The conversion process is not entirely transparent, meaning that you may notice differences between the look of a page in different modes.
	This will improve in future updates but as the two systems have significant differences in what they can do it is unlikely to ever be totally transparent.{/tr}</p>
<p>
	{else}
		{tr}<strong>Note: </strong>Using this button changes the editor between WYSIWYG and the usual wiki editor, but leaves the page source in wiki syntax.{/tr}</p>
	{/if}
<p>
	{icon name="plugin"} {tr}Wiki plugins can be used and edited in WYSIWYG mode, double click the plugin to bring up the plugin edit popup form.<br>
	Here is an example of the {ldelim}BOX{rdelim} plugin:{/tr}</p>
<div style="background-color: #fff; padding: 1em;">
	<div class="tiki_plugin" plugin="box" style="position:relative; background-color: #eee; border: 1px solid #666;">
		<div contenteditable="false">
			<span style="float:left;position:absolute;z-index:10001">{icon name='edit'}</span>
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
	{icon name="error"} {tr}<strong>Caution:</strong> There are still limitations regarding use of plugins in the WYSIWYG editor.<br>
	If you intend to use plugins extensively in a page consider editing that page in wiki mode only.<br>
	Currently, cutting and pasting, or drag and drop of plugins in WYSIWYG will cause problems. Use source mode or the plain wiki editor if you need to move plugins around.{/tr}</p>

	{if $tiki_p_admin eq "y"}
		<hr style="border-width: 1px;" />
		<p>
			{tr}<strong>Admins:</strong><br>
			<em>This new implementation is designed to work best with some site preferences set in a certain way.
			These can all be found on the <a href="tiki-admin.php?page=wysiwyg">WYSIWYG admin page</a>.
			A profile to set these up correctly can be found on <a href="http://profiles.tiki.org/WYSIWYG_6x">profiles.tiki.org</a>
			which can be applied using the <a href="tiki-admin.php?page=profiles">profiles admin page</a>.</em>{/tr}
		</p>
	{/if}

</div>

