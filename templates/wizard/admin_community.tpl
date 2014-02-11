{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}" /></div><div class="adminWizardIconright"><img src="img/icons/large/users48x48.png" alt="{tr}Set up your User & Community features{/tr}"></div>
{tr}Configure general user & community features and friendship network settings{/tr}.
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}User Features{/tr}</legend>
		<div class="admin clearfix featurelist">
		{preference name=feature_mytiki}
		{preference name=feature_userPreferences}
		{preference name=feature_messages}
		{preference name=feature_wizard_user}
		</div>
	<br>
	<em>{tr}See also{/tr} <a href="tiki-admin.php?page=community&cookietab=1" target="_blank">{tr}Community admin panel{/tr}</a> & <a href="https://doc.tiki.org/Community" target="_blank">{tr}Community in doc.tiki.org{/tr}</a></em>
</fieldset>
<fieldset>
	<legend>{tr}Community General Settings{/tr}</legend>
		<div class="admin clearfix featurelist">
		{preference name=users_prefs_allowMsgs}
		{preference name=users_prefs_user_information}
		{preference name=feature_community_mouseover}
		{preference name=users_prefs_show_mouseover_user_info}
		{preference name=users_prefs_mailCharset}
		</div>	
</fieldset>
{if $prefs.feature_search eq 'y'}
	<fieldset>
		<legend>{tr}Social Network{/tr}</legend>
		{preference name=feature_friends}
	</fieldset>
{/if}

</div>
