{* $Id$ *}

<div class="media">
	<img class="pull-left" src="img/icons/large/wizard_user48x48.png" alt="{tr}Wizard completed{/tr}" />
	<div class="media-body">
		<img src="img/icons/tick.png" alt="{tr}Ok{/tr}" /> {tr}Congratulations{/tr}. {tr}You are done with the user wizard{/tr}.<br>
		<fieldset>
			<legend>{tr}Next?{/tr}</legend>
			<ul>
				{if $prefs.feature_userPreferences eq 'y'}
					<li>{tr _0="tiki-user_preferences.php"}Visit the Full <a href="%0">User Preferences</a> page to set the rest of your user preferences{/tr}.</li>
					{tr}Or...{/tr}<br/>
				{/if}
				<li>{tr}Click at the button <strong>Finish</strong> to end the user wizard and go back to the where you were{/tr}.</li>
			</ul>
		</fieldset>
	</div>
</div>
