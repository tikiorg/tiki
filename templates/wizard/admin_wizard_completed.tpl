{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_admin48x48.png" alt="{tr}Wizard completed{/tr}" /></div>
<div class="adminWizardContent">
<img src="img/icons/tick.png" alt="{tr}Ok{/tr}" />{tr}Congratulations{/tr}. {tr}You are done with the admin wizard{/tr}.<br>

<fieldset>
	<legend>{tr}Next?{/tr}</legend>

<ul>
	<li>{tr _0="tiki-admin.php?profile=&categories%5B%5D=12.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2"}Visit the <a href="%0">Profiles Admin Panel</a> to continue configuring your site{/tr}.</li>
	{if $prefs.feature_wizard_user eq 'y'}
		<li>{tr _0="tiki-wizard_user.php"}Visit the <a href="%0">User Wizard</a> to set some of your user preferences{/tr}.</li>
	{/if}
	<li>{tr}Or click at the button <strong>Finish</strong> to end the admin wizard and go back to the where you were{/tr}.</li>
</ul>
</fieldset>

</div>
