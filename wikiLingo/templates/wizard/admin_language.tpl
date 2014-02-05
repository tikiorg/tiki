{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}"/></div><div class="adminWizardIconright"><img src="img/icons/large/i18n48x48.png" alt="{tr}Set up the language{/tr}" /></div>
{tr}Select the site language{/tr}.
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Language{/tr}</legend>

	{preference name=language}
	<br>
	{preference name=feature_multilingual visible="always"}
	{preference name=lang_use_db}

</fieldset>

</div>
