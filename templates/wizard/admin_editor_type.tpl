{* $Id$ *}

<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
	<i class="fa fa-gear fa-stack-2x"></i>
	<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
</span>
{tr}Select editor type{/tr}.</br></br></br>
<div class="adminWizardContent">
    {icon name="admin_textarea" size=3 iclass="adminWizardIconright"}
	<fieldset>
		<legend>{tr}Editor{/tr}</legend>
		<br>
		<table style="border:0px;padding-left:20px">
			<tr>
				<td>
					<input type="radio" name="editorType" value="text" {if empty($editorType) || $editorType eq 'text'}checked="checked"{/if} /> {tr}Only Plain Text Editor (Disable Wysiwyg){/tr}
                    {icon name="file-text-o" size=2 iclass="adminWizardIconright"}
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
                    {icon name="file-text-o" size=2 iclass="adminWizardIconright"}
                    {icon name="file-text" size=2 iclass="adminWizardIconright"}
					<div style="display:block; margin-left:40px">
						{tr}Use a What You See Is What You Get (Wysiwyg) editor, by default in all new pages or only in some when selected. Provides a visual interface preferred by many. You will be able to configure the Full Wysiwyg Editor options in a next wizard page{/tr}.
					</div>
				</td>
			</tr>
		</table>
		<br>
	</fieldset>
</div>
