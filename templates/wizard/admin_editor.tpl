{* $Id$ *}

<h1>{tr}Set up your wiki editor{/tr}</h1>
<div style="float:left; width:60px"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Set up your Wiki environment{/tr}"></div>
{tr}You can choose to use by default the 'Compatible' Wiki text editor (the traditional one), OR a Wysiwyg editor ('Full' and/or 'Inline'){/tr}. {tr}If you don't choose any of the 'Full' or 'Inline' Wysiwyg editors, only the wiki text editor will be available{/tr}.
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}Wiki editor setup{/tr}</legend>
	{tr}Editor mode:{/tr}
	{preference name=feature_wysiwyg}
	<table style="border:0px;padding-left:20px">
	<tr>
	<td>
	<input type="radio" name="editorType" value="wiki" {if empty($editorType) || $editorType eq 'wiki'}checked="checked"{/if} /> {tr}Compatible{/tr}</td><td> {tr}Use wiki syntax for saved pages{/tr}.<br>
		{tr}This is the most compatible with Tiki functionality and the most stable editor mode{/tr}.<br>
		{tr}Limits some Wysiwyg operations{/tr}.<br>
	</td>
	</tr>
	<tr>
	<td></td><td>
	{preference name=feature_syntax_highlighter}
	{preference name=wysiwyg_default}
	</td>
	</tr>
	<tr>
	<td><input type="radio" name="editorType" value="html" {if $editorType eq 'html'}checked="checked"{/if} /> {tr}Saving HTML{/tr}</td><td>{tr}Use html syntax for saved pages{/tr}.<br>
		{tr}Has best compatibility with inline editing, but loses some wiki related features{/tr}.<br>
		{tr}You have to enable the full Wysiwyg editor{/tr}.<br>
	</td>
	</tr>
	<tr>
	<td></td><td>
	{preference name=wysiwyg_optional}
	{tr}If wysiwyg is optional, the wiki text editor is also available. Otherwise only the Wysiwyg editor is used{/tr}.<br>
	{tr}Please note that switching between html and wiki formats can cause problems for some pages{/tr}.
	</td>
	</tr>
	</table>
	{preference name=wysiwyg_inline_editing}
	{tr}Inline editing lets the user edit pages without a context switch{/tr}. {tr}The editor is embedded in the wiki page{/tr}.<br>
	<br>
	{tr}See also{/tr} <a href="tiki-admin.php?page=wysiwyg" target="_blank">{tr}Wysiwyg admin panel{/tr}</a>
</fieldset>
<br>
</div>

