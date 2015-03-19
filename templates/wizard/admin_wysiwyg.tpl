{* $Id$ *}

<div class="media">
   	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
		<i class="fa fa-gear fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
    {tr}You can choose to use by default the 'Compatible' Wiki mode (content is saved in wiki syntax), or the HTML mode{/tr}.
	<div class="media">
        {icon name="admin_wysiwyg" size=3 iclass="adminWizardIconright"}
		<fieldset>
			<legend>{tr}Wysiwyg editor{/tr}</legend>
			{tr}Select the Wysiwyg editor mode{/tr}
			<div class="row">
				<div class="col-md-4">
					<input type="radio" name="editorType" value="wiki" {if empty($editorType) || $editorType eq 'wiki'}checked="checked"{/if} /> {tr}Compatible Wiki mode{/tr}
				</div>
				<div class="col-md-8">{tr}Use wiki syntax for saved pages{/tr}.<br>
					<p>
						{tr}This is the most compatible with Tiki functionality and the most stable editor mode{/tr}.<br>
						{tr}Tools and functions in the editor toolbar will be limited{/tr}.
					</p>

					<p>{preference name=wysiwyg_default}</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<input type="radio" name="editorType" value="html" {if $editorType eq 'html'}checked="checked"{/if} /> {tr}HTML mode{/tr}
				</div>
				<div class="col-md-8">
					<p>
						{tr}Use HTML syntax for saved pages{/tr}.<br>
						{tr}Best compatibility with inline editing{/tr}. {tr}Loses some wiki related features, such as SlideShow, and has some problems with SEFURL{/tr}.<br>
						{tr}Full editor toolbar{/tr}.
					</p>

					<p>{preference name=wysiwyg_optional}</p>
				</div>
			</div>
			{preference name=wysiwyg_inline_editing}
			<br>
			<em>{tr}See also{/tr} <a href="tiki-admin.php?page=wysiwyg" target="_blank">{tr}Wysiwyg admin panel{/tr}</a></em>
		</fieldset>
	</div>
</div>

