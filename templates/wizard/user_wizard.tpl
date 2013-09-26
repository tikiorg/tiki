{* $Id$ *}

<h1>{tr}Welcome to the User Wizard{/tr}</h1>
<div class="userWizardIconleft"><img src="img/icons/large/wizard48x48.png" alt="{tr}Tiki User Wizard{/tr}" /></div>
{tr}This wizard will help you fill in data and main settings for your account in this website{/tr}.
{tr}Depending on the features enabled by the site admin, you will be offered more or less options{/tr}. <br/>
{tr}If you can't set up your user preferences in this site (Real name, keep your information public or private, time settings, ...), you can always request the site admin to enable the user preferences feature, or others that you request them to enable (user messages, daily reports by email of site changes, etc){/tr}.

<div class=userWizardContent">
<fieldset>
	<legend>{tr}User Wizard{/tr}</legend>
	<img src="img/icons/large/user.png" class="userWizardIconright" />
	<p class="wizard_page_title">
	<b>{tr}Welcome to the Tiki User Wizard{/tr}</b>.<br>
	{tr}The user wizard will help you set up your personal Tiki preferences.{/tr}
	</p>
	<p>
	{tr}If you don't want to run the wizard, you can close it using the button below, or use the "Close" button on top which is available on all wizard pages.{/tr}<br>
	<input type="submit" class="btn btn-default" name="close" value="{tr}Close the wizard{/tr}" />
	</p>
	<p>
	<b>Tiki version {$tiki_version}</b>. {tr}To learn more about this Tiki release, go to <a href="http://doc.tiki.org/tiki12">Tiki 12</a>{/tr}.<br/><br/>	
	{tr}See also{/tr} <a href="tiki-user_preferences.php" target="_blank">{tr}User Preferences Panel{/tr}</a>
	</p>
</fieldset>
</div>
