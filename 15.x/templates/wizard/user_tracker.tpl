{* $Id$ *}

<div class="userWizardIconleft"><img src="img/icons/large/wizard_user48x48.png" alt="{tr}User Wizard{/tr}" /></div>
{tr}Use this form to fill in some extra information about you.{/tr}<br/>
<br/><br/>

{jq notonready=true} {* remove the button to save from the user tracker to leave only the one from the user wizard*}
	$("input[name=action0]").hide();
{/jq}

<div class="adminWizardContent">
	<fieldset>
		<legend>{tr}Extra information about you{/tr}</legend>
		<div class="userWizardIconright"><img src="img/icons/large/user_tracker48x48.png" alt="{tr}User Tracker{/tr}" /></div>
		{if $userTrackerData}
			{$userTrackerData}
		{/if}
	</fieldset>
</div>
