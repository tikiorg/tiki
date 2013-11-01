{* $Id$ *}

<h1>{tr}Select editor type{/tr}</h1>

{tr}Select editor type{/tr}.
<div class="adminWizardIconleft"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Select editor type{/tr}" /></div>
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Select editor type{/tr}</legend>
	<img src="img/icons/large/wikipages.png" class="adminWizardIconright" />
	<br>
	<table style="border:0px;padding-left:20px">
	<tr>
	<td>
		<input type="radio" name="editorType" value="text" {if empty($editorType) || $editorType eq 'text'}checked="checked"{/if} />  {tr}Raw text{/tr}
		<div style="display:block; margin-left:20px">
			{tr}Use only the raw text editor, which is the most stable editor mode and most compatible with Tiki functionality{/tr}.
		</div>
	</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>
		<input type="radio" name="editorType" value="wysiwyg" {if $editorType eq 'wysiwyg'}checked="checked"{/if} /> {tr}Wysiwyg{/tr}
		<div style="display:block; margin-left:20px">
			{tr}Use a What You See Is What You Get (Wysiwyg) editor. Provides a visual interface preferred by many{/tr}.
		</div>
	</td>
	</tr>
	</table>
	<br>
</fieldset>
</div>
