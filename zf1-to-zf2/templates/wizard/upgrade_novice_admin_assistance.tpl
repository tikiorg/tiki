{* $Id$ *}
<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Upgrade Wizard{/tr}" title="Upgrade Wizard">
		<i class="fa fa-arrow-circle-up fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
    {icon name="user-plus" size=3 iclass="pull-right"}
    {tr}Improvements that can help novice admins to set up their tiki sites more easily and improve their usability{/tr}.
    <br/><br/>
	<div class="media-body">
		<fieldset>
			<legend>{tr}Basic Information about Wizards{/tr}</legend>
			<p>
				{tr}Starting in Tiki12, some wizards were added to Tiki in order to help in the initial setup based on configuration templates like "Macros" (<b>Profiles Wizard</b>), as well as further site configuration (<b>Configuration Wizard</b>), fine tunning the new features and preferences when upgrading (<b>Upgrade Wizard</b>), and to help you as site admin to collect more information from your users if you need it (<b>Users Wizard</b>){/tr}.
				<a href="http://doc.tiki.org/Wizards" target="tikihelp" class="tikihelp" title="{tr}Wizards:{/tr}
					{tr}Wizards oriented to help the site admin (Profiles, Configuration and Upgrade wizards) come always enabled{/tr}.
					<br/><br/>
					{tr}The User Wizard comes disabled by default, and you have the option to enable it and configure it for your site{/tr}.
				">
                    {icon name="help" size=1}
				</a>
			</p>
		</fieldset>
		<fieldset class="table clearfix featurelist">
            {icon name="magic" size=3 iclass="pull-right"}
			<legend> {tr}Wizards settings{/tr} </legend>
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
            {icon name="envelope-o" size=3 iclass="pull-right"}
			<legend> {tr}Email{/tr} </legend>
			{preference name=email_footer}
			{preference name=messu_truncate_internal_message}
		</fieldset>
	</div>
</div>
