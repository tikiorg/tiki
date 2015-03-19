{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
		<i class="fa fa-gear fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
    {tr}Select the site language{/tr}.</br></br></br>
	<div class="media-content">
        {icon name="admin_i18n" size=3 iclass="adminWizardIconright"}
		<fieldset>
			<legend>{tr}Language{/tr}</legend>

			{preference name=language}
			<br>
			{preference name=feature_multilingual visible="always"}
			{preference name=lang_use_db}
		</fieldset>
	</div>
</div>
