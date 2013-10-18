{* $Id$ *}

<h1>{tr}Set up your User & Community features{/tr}</h1>

<div class="adminWizardIconleft"><img src="img/icons/large/users48x48.png" alt="{tr}Set up your User & Community features{/tr}"></div>
{tr}Configure general user & community features and friendship network settings{/tr}.
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}User Features{/tr}</legend>
	{preference name=feature_mytiki}
	{preference name=feature_userPreferences}
	{preference name=feature_messages}

	{tr}See also{/tr} <a href="tiki-admin.php?page=community&cookietab=1" target="_blank">{tr}Community admin panel{/tr}</a> & <a href="https://doc.tiki.org/Community" target="_blank">{tr}Community in doc.tiki.org{/tr}</a>
</fieldset>
<fieldset>
	<legend>{tr}Community General Settings{/tr}</legend>
	{preference name=users_prefs_allowMsgs}
	{preference name=users_prefs_user_information}
	{preference name=feature_community_mouseover}
	{preference name=users_prefs_show_mouseover_user_info}
	{preference name=users_prefs_mailCharset}
	
</fieldset>
<fieldset>
	<legend>{tr}Social Network{/tr}</legend>
	{preference name=feature_friends}
</fieldset>

</div>
