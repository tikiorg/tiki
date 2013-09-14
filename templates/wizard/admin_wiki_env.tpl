{* $Id$ *}

<h1>{tr}Set up your wiki editor{/tr}</h1>
<form action="tiki-wizard_admin.php" method="post">

<div style="float:left; width:60px"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Set up your Wiki environment{/tr}"></div>
{tr}If you disable Wysiwyg, only the wiki text editor will be available{/tr}.
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}Wiki editor setup{/tr}</legend>
	<p>
	{tr}Use Wysiwyg{/tr} <input type="checkbox" name="useWysiwyg" {if isset($useWysiwyg) AND $useWysiwyg eq 'y'}checked="checked"{/if} />
	</p>
	<table style="border:0px">
	<tr>
	<td>
	<input type="radio" name="editorType" value="wiki" {if empty($editorType) || $editorType eq 'wiki'}checked="checked"{/if} /> {tr}Compatible{/tr}</td><td> {tr}Use wiki syntax for saved pages{/tr}.<br>
		{tr}This is the most compatible with Tiki functionality and the most stable editor mode{/tr}.<br>
		{tr}Limits some Wysiwyg operations{/tr}.<br>
	</td>
	</tr>
	<tr>
	<td><input type="radio" name="editorType" value="html" {if $editorType eq 'html'}checked="checked"{/if} /> {tr}Full Wysiwyg{/tr}</td><td>{tr}Use html syntax for saved pages{/tr}.<br>
		{tr}Has best compatibility with inline editing, but loses some wiki related features{/tr}.<br>
		{tr}Enables the full Wysiwyg editor{/tr}.<br>
	</td>
	</tr>
	</table>
	<input type="checkbox" name="useInlineEditing" {if isset($useInlineEditing) AND $useInlineEditing eq 'y'}checked="checked"{/if} /> {tr}Use inline editing{/tr} <img src="img/icons/error.png" alt="{tr}Experimental{/tr}" title="{tr}Experimental{/tr}" /><br>
	{tr}Inline editing lets the user edit pages without a context switch{/tr}. {tr}The editor is embedded in the wiki page{/tr}.<br>
	<br>
	{tr}See also{/tr} <a href="tiki-admin.php?page=wysiwyg" target="_blank">{tr}Wysiwyg admin panel{/tr}</a>
</fieldset>
<br>
</div>

