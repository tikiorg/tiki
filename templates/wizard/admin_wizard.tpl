{* $Id$ *}

<h1>{tr}Tiki Setup{/tr}</h1>
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Get Started{/tr}</legend>

<div style="display:block;margin-left: auto; margin-right: auto">
<div class="adminWizardIconleft"><img src="img/icons/large/wizard48x48.png" alt="{tr}Tiki Admin Wizard{/tr}" /><br/><br/></div>
{tr}The Tiki Admin Wizard helps you quickly configure key features and settings. Use the <a href="tiki-admin.php" target="_blank">Admin Panel</a> to configure other features and settings not included in this wizard{/tr}.
<br>
		<input type="submit" class="btn btn-default btn-sm" name="continue" value="{tr}Start admin wizard{/tr}" /><br>
</div>
</br>
<div style="display:block;margin-left: auto; margin-right: auto">
<div class="adminWizardIconleft"><img src="img/icons/large/profiles48x48.png" alt="{tr}Tiki Configuration Profiles{/tr}" /></div>
{tr}Tiki Profiles are a quick and easy way to setup a preconfigured application, e.g. a Blog site{/tr}.
</br>
		<input  type="submit" class="btn btn-default" name="use-default-prefs" value="{tr}Easy application setup using configuration profiles{/tr}" /><br>
</div>
</br>
<div style="display:block;margin-left: auto; margin-right: auto; margin-bottom: 10px">
<div class="adminWizardIconleft"><img src="img/icons/large/stock_missing-image48x48.png" alt="{tr}No wizard{/tr}" /></div>
{tr}Do it manually using the <a href="tiki-admin.php" target="_blank">Admin Panel</a>{/tr}.
</br>
		<input  type="submit" class="btn btn-default" name="skip" value="{tr}Skip setup and don't show again{/tr}" />
</br></br>
</div>
</fieldset>
<fieldset>
<legend>{tr}Server Fitness{/tr}</legend>
	{tr _0=$tiki_version}To check if your server meets the requirements for running Tiki version %0, please visit <a href="tiki-check.php" target="_blank">Tiki Server Compatibility Check</a>{/tr}.
</fieldset>
<br>
</div>
