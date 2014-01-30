{* $Id$ *}

<fieldset>
	<legend>{tr}Get Started{/tr}</legend>

	<img src="img/icons/tick.png" alt="{tr}Ok{/tr}" />{tr _0=$tiki_version}Congratulations! You now have a working instance of Tiki %0{/tr}.<br><br>
    {tr}You may <a href="tiki-index.php">start using it right away</a>, or you may configure it to better meet your needs, using one of the configuration helpers below.{/tr}
    <br><br>

    <table>
        <tr>
            <td><div class="adminWizardIconleft"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Profiles Wizard{/tr}" title="{tr}Profiles Wizard{/tr}" /></div></td>
            <td>
                {tr}You may start by applying some of our configuration templates through the <b>Profiles Wizard</b>{/tr}.
                {tr}Each of these provides a shrink-wrapped solution that meets most of the needs of a particular kind of community or site (ex: Personal Blog space, Company Intranet, ...) or that extends basic setup with extra features configured for you{/tr}.
                {tr}If you are new to Tiki administration, we recommend that you start with this approach{/tr}.
                {tr}If the profile you selected does not quite meet your needs, you will still have the option of customizing it further with one of the approaches below{/tr}.
                <br>
                <input  type="submit" class="btn btn-default" name="use-default-prefs" value="{tr}Start Profiles Wizard{/tr}" />
                <br><br>
            </td>
        </tr>

        <tr>
            <td><div class="adminWizardIconleft"><img src="img/icons/large/wizard_admin48x48.png" alt="{tr}Configuration Walkthrough{/tr}" title="Configuration Walkthrough" /><br/><br/></div></td>
            <td>
                {tr}Alternatively, you may use the <b>Admin Wizard</b>{/tr}.
                {tr}This will guide you through the most common preference settings in order to customize your site{/tr}.
                {tr}Use this wizard if none of the <b>Site Profiles</b> look like a good starting point, or if you need to customize your site further{/tr}.
                <br>
                <input type="submit" class="btn btn-default" name="continue" value="{tr}Start Admin Wizard{/tr}" /><br><br>
            </td>
        </tr>

        <tr>
            <td><div class="adminWizardIconleft"><img src="img/icons/large/admin_panel48x48.png" alt="{tr}Admin Panel{/tr}" /></div></td>
            <td>
                {tr}Use the <b>Admin Panel</b> to manually browse through the full list of preferences{/tr}.
                <br>
                {button href="tiki-admin.php" _text="{tr}Go to the Admin Panel{/tr}"}
                <br><br>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset>
<legend>{tr}Server Fitness{/tr}</legend>
	{tr _0=$tiki_version}To check if your server meets the requirements for running Tiki version %0, please visit <a href="tiki-check.php" target="_blank">Tiki Server Compatibility Check</a>{/tr}.
</fieldset>

