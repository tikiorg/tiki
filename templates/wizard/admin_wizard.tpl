{* $Id$ *}

<h1>{tr}Tiki Admin Wizard{/tr}</h1>

<div class="adminWizardIconleft"><img src="img/icons/large/wizard48x48.png" alt="{tr}Tiki Admin Wizard{/tr}" />
</div>{tr}The Tiki Admin Wizard helps you quickly configure key features and settings. Use the Admin Panel to configure other features and settings not included in this wizard{/tr}.
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Admin Wizard{/tr}</legend>
	<img src="img/tiki/tikilogo.png" class="adminWizardIconright" />
	<p class="wizard_page_title">
	<b>{tr}Welcome to the Tiki Admin Wizard{/tr}</b>.<br>
	{tr}To learn more about this Tiki release, go to <a href="http://doc.tiki.org/tiki12">Tiki 12</a>{/tr}.
	</p>
	<p>
	{tr}The Admin Wizard is recommended for users new to Tiki. Press "Start" to begin the wizard{/tr}.
	{tr}Or click below to use the defaults rather than paging through the wizard{/tr}.<br>
	<input type="submit" class="btn btn-default" name="use-default-prefs" value="{tr}Use default preferences{/tr}" /><br>
	<br>
	{tr}To skip the wizard ...{/tr} <br>
	<input type="submit" class="btn btn-default" name="skip" value="{tr}Skip wizard and don't show again{/tr}" />
	</p>
	<p>
	{tr}To keep the wizard from opening when an admin logs in, uncheck the "Show on login" above from this and any other wizard page{/tr}.
	</p>
	<b>Tiki version {$tiki_version}</b><br>
	{tr}See also{/tr} <a href="tiki-admin.php" target="_blank">{tr}Admin panel{/tr}</a>
</fieldset>
<br>
<fieldset>
<legend>{tr}Server Fitness{/tr}</legend>
	{tr}To check if your server meets the requirements for running Tiki, please visit <a href="tiki-check.php" target="_blank">Tiki Server Compatibility Check</a>{/tr}.
</fieldset>
<br>
</div>


