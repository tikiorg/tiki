{* $Id$ *}

<h1 class="pagetitle">{tr}Welcome to the User Wizard{/tr}</h1>
<div class="userWizardIconleft"><img src="img/icons/large/wizard48x48.png" alt="{tr}Tiki User Wizard{/tr}" /></div>
{tr}This wizard will help you fill in data and main settings for your account on this website{/tr}.
{tr}Depending on the features enabled by the site admin, you will be offered more or less options{/tr}. 
{tr}If you can't set up your user preferences (Real name, keep your information public or private, time settings, ...), you can request the site admin to enable the user preferences feature{/tr}.<br/><br/>

<div class=userWizardContent">
<fieldset>
	<legend>{tr}User Wizard{/tr}</legend>
	<img src="img/icons/large/user.png" class="userWizardIconright" />
	<p class="wizard_page_title">
	<b>{tr}Welcome to the Tiki User Wizard{/tr}</b>.<br>
	{tr}The user wizard will help you set up your personal Tiki preferences.{/tr}
	{tr}Click at the "Start" button above to launch it{/tr}
	</p>
	<p>
	{tr}If you don't want to run the wizard, you can close it using the button below, or use the "Close" button on top (available on all wizard pages){/tr}.<br>
	<input type="submit" class="btn btn-default btn-sm" name="close" value="{tr}Close the wizard{/tr}" />
	</p>
	<p>
	<b>Tiki version {$tiki_version}</b>. {tr}To learn more about this Tiki release, go to <a href="http://doc.tiki.org/tiki12">Tiki 12</a>{/tr}.<br/><br/>	
	</p>
</fieldset>
</div>
