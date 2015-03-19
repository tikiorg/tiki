{* $Id$ *}

<div class="media">
    {icon name="check" size=2}{tr}Congratulations{/tr}. {tr}You are done with the Configuration Profiles Wizard{/tr}.<br>
    <br/><br/>
	<div class="media-body">
		<fieldset>
			<legend>{tr}Next?{/tr}</legend>
            <div class="row">
                <div class="col-md-9">
                <ul>
                    <li>{tr _0="tiki-wizard_admin.php?&stepNr=1&url=tiki-index.php"}Visit the <a href="%0">Configuration Wizard</a> to continue configuring your site{/tr}.</li>
                    {if $prefs.feature_wizard_user eq 'y'}
                        <li>{tr _0="tiki-wizard_user.php"}Visit the <a href="%0">User Wizard</a> to set some of your user preferences{/tr}.</li>
                    {/if}
                    <li>{tr}Or click at the button <strong>Finish</strong> to end the admin wizard and go back to the where you were{/tr}.</li>
                </ul>
                </div>
            </div>
		</fieldset>
	</div>
</div>
