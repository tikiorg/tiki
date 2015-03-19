{* $Id$ *}
<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
		<i class="fa fa-gear fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
    {tr}Configure the login, registration and validation preferences for the new accounts{/tr}.<br/></br></br>
	<div class="media-body">
        {icon name="admin_login" size=3 iclass="adminWizardIconright"}
		<fieldset>
			<legend>{tr}Registration & Log in options{/tr}</legend>
			<div style="position:relative;">
				<div class="adminoptionbox clearfix featurelist">
					{preference name=allowRegister}
					{preference name=validateUsers}
					{preference name=validateRegistration}
					{preference name=feature_banning}
					{preference name=useRegisterPasscode}
					<div class="adminoptionboxchild" id="useRegisterPasscode_childcontainer">
						{preference name=registerPasscode}
						{preference name=showRegisterPasscode}
					</div>
				</div>
			</div>
			<br/>
			<em>
				{tr}Add a <b>User and Registration tracker</b>{/tr}
				<a href="http://doc.tiki.org/User+Tracker" target="tikihelp" class="tikihelp" title="{tr}User and Registration tracker: You can use trackers to collect additional information for users during registration or even later once they are registered users.{/tr}
				{tr}Some uses of this type of tracker could be{/tr}
				<ul>
					<li>{tr}To collect user information (such as mailing address or phone number){/tr}</li>
					<li>{tr}To require the user to acknowledge a user agreement{/tr}</li>
					<li>{tr}To prevent spammer registration, by asking new users to provide a reason why they want to join (the prompt should tell the user that his answer should indicate that he or she clearly understands what the site is about).{/tr}</li>
				</ul>
				{tr}The profile will enable the feature 'Trackers' for you and a few other settings required. Once the profile is applied, you will be provided with instructions about further steps that you need to perform manually.{/tr}">
				{icon name="help"}
				</a> :
				<a href="tiki-admin.php?profile=User_Trackers&show_details_for=User_Trackers&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a> ({tr}new window{/tr})
			</em>
			<br/><br/>
		</fieldset>
		<div class="row">
			<div class="col-md-6">
				<fieldset>
					<legend>{tr}Username{/tr}</legend>
					{preference name=login_is_email}
					{preference name=lowercase_username}
				</fieldset>
			</div>
			<div class="col-md-6">
				<fieldset>
					<legend>{tr}Password{/tr}</legend>
					{preference name=forgotPass}
					{preference name=change_password}
					{preference name=min_pass_length}
				</fieldset>
			</div>
		</div>
		<em>{tr}See also{/tr} <a href="tiki-admin.php?page=login" target="_blank">{tr}Login admin panel{/tr}</a></em>
	</div>
</div>
