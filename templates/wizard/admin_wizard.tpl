{* $Id$ *}

<h1>{tr}Tiki Admin Wizard{/tr}</h1>

<div class="adminWizardIconleft">
	<img src="img/icons/large/wizard48x48.png" alt="{tr}Tiki Admin Wizard{/tr}" />
</div>
{tr}The Tiki Admin Wizard helps you quickly configure key features and settings. Use the <a href="tiki-admin.php" target="_blank">Admin Panel</a> to configure other features and settings not included in this wizard or when not using the wizard. Uncheck the checkbox above to keep this wizard from showing upon admin login{/tr}.
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Get Started{/tr}</legend>
		<input style="display:block;margin-left: auto; margin-right: auto" type="submit" class="btn btn-default" name="continue" value="{tr}Start wizard{/tr}" /><br>
		<input style="display:block;margin-left: auto; margin-right: auto" type="submit" class="btn btn-default" name="use-default-prefs" value="{tr}Use default settings and skip to end of wizard{/tr}" /><br>
		<input style="display:block;margin-left: auto; margin-right: auto; margin-bottom: 10px" type="submit" class="btn btn-default" name="skip" value="{tr}Skip wizard and don't show again{/tr}" />
</fieldset>
<br>
<fieldset>
<legend>{tr}Server Fitness{/tr}</legend>
	{tr _0=$tiki_version}To check if your server meets the requirements for running Tiki version %0, please visit <a href="tiki-check.php" target="_blank">Tiki Server Compatibility Check</a>{/tr}.
</fieldset>
<br>
</div>
