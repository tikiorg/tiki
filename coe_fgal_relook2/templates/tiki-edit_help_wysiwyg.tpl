{* $Id$ *}
{* \brief Show wysiwyg help 
 * included by tiki-show_help.tpl via smarty_block_add_help() *}

{add_help show='y' title="{tr}Wysiwyg Help{/tr}" id="wiki_help"}

<h3>{tr}Wysiwyg Syntax{/tr}</h3>
<div class="help_section">
{if $prefs.feature_help eq 'y'} 
<p>{tr}For more information, please see{/tr}
	<a href="{$prefs.helpurl}Wysiwyg+Editor" target="tikihelp" class="tikihelp" title="{tr}Wysiwyg Editor{/tr}: {tr}More help on wysiwyg editing{/tr}">
		{tr}Wysiwyg Editor{/tr} {icon _id='help' style="vertical-align:middle"}
	</a>
</p>
{/if}
 
<table width="95%" class="normal">
 <tr>
	<th>{tr}Wysiwyg{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
<tr class="{cycle}"><td width="20%">This needs writing</td></tr>
</table>

</div>

{/add_help}
