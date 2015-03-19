{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
		<i class="fa fa-gear fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	{tr}Configure general user & community features and settings related to sharing and social networks{/tr}.
	</br></br></br>
	<div class="media-body">
        {icon name="user" size=3 iclass="pull-right"}
		<fieldset>
			<legend>{tr}User Features{/tr}</legend>
			<div class="row">
				<div class="col-lg-6">
					{preference name=feature_mytiki}
					{preference name=feature_messages}
				</div>
				<div class="col-lg-6">
					{preference name=feature_userPreferences}
					{preference name=feature_wizard_user}
				</div>
			</div>
			<br>
			<em>
				{tr}Add a <b>User and Registration tracker</b>{/tr}
				<a href="http://doc.tiki.org/User+Tracker" target="tikihelp" class="tikihelp" title="{tr}User and Registration tracker: You can use trackers to collect additional information for users during registration or even later once they are registered users.{/tr}
					{tr}Some uses of this type of tracker could be{/tr}
					<ul>
						<li>{tr}To collect user information (such as mailing address or phone number){/tr}</li>
						<li>{tr}To require the user to acknowledge a user agreement{/tr}</li>
						<li>{tr}To prevent spammer registration, by asking new users to provide a reason why they want to join (the prompt should tell the user that his answer should indicate that he or she clearly understands what the site is about).{/tr}</li>
					</ul>
					{tr}The profile will enable the feature 'Trackers' for you and a few other settings required. Once the profile is applied, you will be provided with instructions about further steps that you need to perform manually.{/tr}"
				>
					{icon name="help"}
				</a> :
				<a href="tiki-admin.php?profile=User_Trackers&show_details_for=User_Trackers&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a> ({tr}new window{/tr})
			</em>
			<br/><br/>
			<em>{tr}To set up the <strong>User Watches</strong> and their associated settings, visit the page to {/tr} <a href="tiki-wizard_admin.php?&stepNr=15&url={$homepageUrl}">{tr}Set up Main features{/tr}</a></em>
            </br></br>
		</fieldset>
        {icon name="users" size=3 iclass="pull-right"}
		<fieldset>
			<legend>{tr}Community General Settings{/tr}</legend>
			<div class="row">
				<div class="col-lg-6">
					{preference name=users_prefs_allowMsgs}
					{preference name=feature_community_mouseover}
					<div class="adminoptionboxchild" id="feature_community_mouseover_childcontainer">
						{preference name=feature_community_mouseover_name}
						{preference name=feature_community_mouseover_gender}
						{preference name=feature_community_mouseover_picture}
						{preference name=feature_community_mouseover_score}
						{preference name=feature_community_mouseover_country}
						{preference name=feature_community_mouseover_email}
						{preference name=feature_community_mouseover_lastlogin}
						{preference name=feature_community_mouseover_distance}
					</div>
					{preference name=users_prefs_show_mouseover_user_info}
					{preference name=feature_contact}
					<div class="adminoptionboxchild" id="feature_contact_childcontainer">
						{preference name=contact_anon}
						{preference name=contact_priority_onoff}
					</div>
				</div>
				<div class="col-lg-6">
					{preference name=users_prefs_user_information}
					{preference name=users_prefs_mailCharset}
					<div class="adminoptionbox preference clearfix all"></div>
					{preference name=user_show_realnames}
					<div class="adminoptionboxchild" id="user_show_realnames_childcontainer">
						{preference name=user_selector_realnames_messu}
						{preference name=user_selector_realnames_tracker}
					</div>
				</div>
				<br>
				<em>{tr}See also{/tr} {tr}Community{/tr} <a href="tiki-admin.php?page=community&amp;cookietab=1" target="_blank">{tr}admin panel{/tr}</a> & <a href="https://doc.tiki.org/Community" target="_blank">{tr}in doc.tiki.org{/tr}</a></em>
			</div>
		</fieldset>
        {icon name="admin_share" size=3 iclass="pull-right"}
		<fieldset>
			<legend>{tr}Sharing & Networking{/tr}</legend>
			<div class="row">
				<div class="col-lg-3">
					{preference name=feature_share}
					<br>
					<em>{tr}See also{/tr} {tr}Share{/tr} <a href="tiki-admin.php?page=share" target="_blank">{tr}admin panel{/tr}</a> & <a href="https://doc.tiki.org/Share" target="_blank">{tr}in doc.tiki.org{/tr}</a></em>.
				</div>
				<div class="col-lg-6">
					{preference name=feature_friends}
					<div class="adminoptionboxchild" id="feature_friends_childcontainer">
						{preference name=social_network_type}
					</div>
					<br>
					<em>{tr}See also{/tr} "{tr}Community{/tr} <a href="tiki-admin.php?page=community&cookietab=2" target="_blank">{tr}admin panel{/tr}</a> > {tr}Social Network{/tr} > {tr}Friendship and Followers{/tr}" & <a href="https://doc.tiki.org/Friendship+Network" target="_blank">{tr}in doc.tiki.org{/tr}</a></em>.
				</div>
				<div class="col-lg-3">
					{preference name=feature_socialnetworks}
					<br>
					<em>{tr}See also{/tr} {tr}Social networks{/tr} <a href="tiki-admin.php?page=socialnetworks&amp;cookietab=1" target="_blank">{tr}admin panel{/tr}</a> & <a href="https://doc.tiki.org/Social+Networks" target="_blank">{tr}in doc.tiki.org{/tr}</a></em>
				</div>
			</div>
		</fieldset>
	</div>
</div>
