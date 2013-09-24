{* $Id$ *}

<h1>{tr}Select editor type{/tr}</h1>

{tr}Select editor type{/tr}
<div class="adminWizardIconleft"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Select editor type{/tr}" /></div>
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Select editor type{/tr}</legend>
	<img src="img/icons/large/wikipages.png" class="adminWizardIconright" />
	<br>
	<table style="border:0px;padding-left:20px">
	<tr>
	<td>
	<input type="radio" name="editorType" value="text" {if empty($editorType) || $editorType eq 'text'}checked="checked"{/if} /> {tr}Text only{/tr}</td><td> {tr}Use only the wiki text editor{/tr}.<br>
		{tr}This is the most compatible with Tiki functionality and the most stable editor mode{/tr}.<br>
	</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td><input type="radio" name="editorType" value="wysiwyg" {if $editorType eq 'wysiwyg'}checked="checked"{/if} /> {tr}Wysiwyg{/tr}</td><td>{tr}Use a What You See Is What You Get (Wysiwyg) editor{/tr}.<br>
		{tr}Preferred by many. Visual interface{/tr}.<br>
	</td>
	</tr>
	</table>
	<br>
</fieldset>
</div>
