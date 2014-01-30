{* $Id$ *}

{tr}Select editor type{/tr}.
<div class="adminWizardIconleft"><img src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}" /></div><div class="adminWizardIconright"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Select editor type{/tr}" /></div>
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Select editor type{/tr}</legend>
	<img src="img/icons/large/wikipages.png" class="adminWizardIconright" />
	<br>
	<table style="border:0px;padding-left:20px">
	<tr>
	<td>
		<input type="radio" name="editorType" value="text" {if empty($editorType) || $editorType eq 'text'}checked="checked"{/if} />  {tr}Only Plain Text Editor (Disable Wysiwyg){/tr}
		<div style="display:block; margin-left:40px">
			{tr}Use only the plain text editor, which is the most stable editor mode and most compatible with Tiki functionality. The Full Wysiwyg Editor will be disabled, but you will still be able to insert wysiwyg sections through the Plain Text editor with <a href="https://doc.tiki.org/PluginWysiwyg" alt="Link to Plugin Wysiwyg doc. page" target="blank">Plugin Wysiwyg</a>{/tr}.
		</div>
	</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>
		<input type="radio" name="editorType" value="wysiwyg" {if $editorType eq 'wysiwyg'}checked="checked"{/if} /> {tr}Wysiwyg{/tr}
		<div style="display:block; margin-left:40px">
			{tr}Use a What You See Is What You Get (Wysiwyg) editor, by default in all new pages or only in some when selected. Provides a visual interface preferred by many. You will be able to configure the Full Wysiwyg Editor options in a next wizard page{/tr}.
		</div>
	</td>
	</tr>
	</table>
	<br>
</fieldset>
</div>
