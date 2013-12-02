{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/stock_quit48x48.png" alt="{tr}Set up Login{/tr}" /></div>
{tr}Configure the login, registration and validation preferences for the new accounts{/tr}.
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Registration & Log in options{/tr}</legend>
		<div style="position:relative;">
			<div class="adminoptionbox">
				{preference name=allowRegister}
				{preference name=validateUsers}
				{preference name=validateRegistration}
                {preference name=useRegisterPasscode}
                <div class="adminoptionboxchild" id="useRegisterPasscode_childcontainer">
                    {preference name=registerPasscode}
                    {preference name=showRegisterPasscode}
                </div>
                {preference name=feature_banning}
			</div>
		</div>
</fieldset>
<table style="width:100%">
    <tr>
        <td style="width:48%">
            <fieldset>
                <legend>{tr}Username{/tr}</legend>
                {preference name=login_is_email}
                {preference name=lowercase_username}
            </fieldset>
        </td>
        <td style="width:4%">
            &nbsp;
        </td>
        <td style="width:48%">
            <fieldset>
                <legend>{tr}Password{/tr}</legend>
                {preference name=forgotPass}
                {preference name=change_password}
                {preference name=min_pass_length}
            </fieldset>
        </td>
    </tr>
</table>
<em>{tr}See also{/tr} <a href="tiki-admin.php?page=login" target="_blank">{tr}Login admin panel{/tr}</a></em>
</div>
