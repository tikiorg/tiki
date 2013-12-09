{* $Id$ *}


<fieldset>
	<legend>{tr}Get Started{/tr}</legend>

    {tr}Congratulations! You now have a working instance of Tiki{/tr}.
    <p/>
    {tr}You may <a href="tiki-index.php">start using it right away</a>, or you may configure it to better meet your needs, using one of the configuration wizards below.{/tr}
    <br>

    <table>
        <tr>
            <td><div class="adminWizardIconleft"><img src="img/icons/large/cookie_cutter.png" alt="{tr}Tiki Template Sites{/tr}" /></div></td>
            <td>
                {tr}You may start by applying one of our <b>Site Templates</b> (also known as "<b><i>Featured Profiles</i></b>" <img src="img/icons/large/profiles.png" width="20" alt="{tr}Profiles{/tr}" />){/tr}.
                {tr}Each of those has been carefully designed to meet most needs of a particular kind of community or site (ex: Personal Blog space, Company Intranet){/tr}.
                {tr}If you are new to Tiki administration, we recommend that you start with that approach{/tr}.
                {tr}If the template you selected does not quite meet your needs, you will still have the option of customizing it further with one of the approaches below{/tr}.
                <br>
                <input  type="submit" class="btn btn-default" name="use-default-prefs" value="{tr}Choose a site template{/tr}" />
                <br><br>
            </td>
        </tr>

        <tr>
            <td><div class="adminWizardIconleft"><img src="img/icons/large/barefeet48x48.png" alt="{tr}Configuration Walkthrough{/tr}" /><br/><br/></div></td>
            <td>
                {tr}Alternatively, you may use the <b>Configuration Walkthrough</b>{/tr} (also known as "<b><i>admin wizard</i></b>" <img src="img/icons/large/wizard32x32.png" width="20" alt="{tr}Wizards{/tr}" />).
                {tr}This will guide you through a subset of preferences that most commonly need to be changed{/tr}.
                {tr}Use this wizard if none of the <b>Site Templates</b> looks like a good starting point, or if you need to customize your site further{/tr}.
                <br>
                <input type="submit" class="btn btn-default" name="continue" value="{tr}Start Config Walkthrough{/tr}" /><br><br>
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

