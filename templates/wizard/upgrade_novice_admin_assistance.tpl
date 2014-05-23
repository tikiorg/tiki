{* $Id: upgrade_novice_admin_assistance.tpl 51359 2014-05-17 15:50:05Z xavidp $ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_upgrade48x48.png" alt="{tr}Upgrade Wizard{/tr}" title="{tr}Upgrade Wizard{/tr}"/></div><div class="adminWizardIconright"><img src="img/icons/large/admin_assistance48x48.png" alt="{tr}Novice Admin Assistance{/tr}" title="{tr}Novice Admin Assistance{/tr}"/></div>
{tr}Improvements that can help novice admins to set up their tiki sites more easily and improve their usability{/tr}.
<br/><br/>
<div class="adminWizardContent">

    <fieldset>
        <legend>{tr}Basic Information about Wizards{/tr}</legend>
        {tr}Starting in Tiki12, some wizards were added to Tiki in order to help in the initial setup based on configuration templates like "Macros" (<b>Profiles Wizard</b>), as well as further site configuration (<b>Admin Wizard</b>), fine tunning the new features and preferences when upgrading (<b>Upgrade Wizard</b>), and to help you as site admin to collect more information from your users if you need it (<b>Users Wizard</b>){/tr}.
        <a href="http://doc.tiki.org/Wizards" target="tikihelp" class="tikihelp" title="{tr}Wizards:{/tr}
            {tr}Wizards oriented to help the site admin (Profiles, Admin and Upgrade wizards) come always enabled{/tr}.
                <br/><br/>
            {tr}The User Wizard comes disabled by default, and you have the option to enable it and configure it for your site{/tr}.
	    	">
            <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
        </a>
    </fieldset>

    <fieldset class="table clearfix featurelist">
        <legend> {tr}Wizards settings{/tr} </legend>
        <div class="adminWizardIconright"><img src="img/icons/large/wizard48x48.png" alt="{tr}Wizards{/tr}" title="{tr}Wizards{/tr}"/></div>
        {preference name=feature_wizard_user}
        {preference name=userTracker}
        <div class="adminoptionboxchild" id="userTracker_childcontainer">
            {preference name=feature_userWizardDifferentUsersFieldIds}
            <div class="adminoptionboxchild" id="feature_userWizardDifferentUsersFieldIds_childcontainer">
                {preference name=feature_userWizardUsersFieldIds}
            </div>
        </div>
        {preference name=wizard_admin_hide_on_login}

    </fieldset>

    <fieldset class="table clearfix featurelist">
        <legend> {tr}Email{/tr} </legend>
        <div class="adminWizardIconright"><img src="img/icons/large/stock_mail48x48.png" alt="{tr}Email{/tr}" title="{tr}Email{/tr}"/></div>
        {preference name=email_footer}
        {preference name=messu_truncate_internal_message}
    </fieldset>

</div>
