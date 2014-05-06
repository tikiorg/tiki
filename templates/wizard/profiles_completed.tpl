{* $Id: profiles_completed.tpl 51026 2014-04-27 17:18:07Z xavidp $ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Configuration Profiles Wizard completed{/tr}" title="{tr}Configuration Profiles Wizard{/tr}" /></div>
<div class="adminWizardContent">
<img src="img/icons/tick.png" alt="{tr}Ok{/tr}" />{tr}Congratulations{/tr}. {tr}You are done with the Configuration Profiles Wizard{/tr}.<br>

<fieldset>
	<legend>{tr}Next?{/tr}</legend>

<ul>
	<li>{tr _0="tiki-wizard_admin.php?&stepNr=1&url=tiki-index.php"}Visit the <a href="%0">Admin Wizard</a> to continue configuring your site{/tr}.</li>
	{if $prefs.feature_wizard_user eq 'y'}
		<li>{tr _0="tiki-wizard_user.php"}Visit the <a href="%0">User Wizard</a> to set some of your user preferences{/tr}.</li>
	{/if}
	<li>{tr}Or click at the button <strong>Finish</strong> to end the admin wizard and go back to the where you were{/tr}.</li>
</ul>
</fieldset>

</div>
