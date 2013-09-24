{* $Id$ *}

<h1>{tr}Welcome to the User Wizard{/tr}</h1>
<div style="float:left; width:60px"><img src="img/icons/large/wizard48x48.png" alt="{tr}Tiki User Wizard{/tr}" /></div>
{tr}This wizard will help you fill in data and main settings for your account in this website{/tr}.
{tr}Depending on the features enabled by the site admin, you will be offered more or less options{/tr}. <br/>
{tr}If you can't set up your user preferences in this site (Real name, keep your information public or private, time settings, ...), you can always request the site admin to enable the user preferences feature, or others that you request them to enable (user messages, daily reports by email of site changes, etc){/tr}.

<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}User Wizard{/tr}</legend>
	<img src="img/icons/large/user.png" style="float:right" />	
	<p class="wizard_page_title">
	<b>{tr}Welcome to the Tiki User Wizard{/tr}</b>.<br>
	</p>
	<p>
	{tr}If you are a new user, you are recommended to use the User Wizard. Press "Start" to begin the wizard{/tr}.<br><br>
	{tr}However, if you are an experienced user and don't want the User Wizard ...{/tr} <br>
	<input type="submit" class="btn btn-default" name="skip" value="{tr}Skip wizard and don't show again{/tr}" />
	</p>
	<p>
	</p>
	<b>Tiki version {$tiki_version}</b>. {tr}To learn more about this Tiki release, go to <a href="http://doc.tiki.org/tiki12">Tiki 12</a>{/tr}.<br/><br/>	
	{tr}See also{/tr} <a href="tiki-user_preferences.php" target="_blank">{tr}User Preferences Panel{/tr}</a>
</fieldset>
</div>
