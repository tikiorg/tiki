{remarksbox type="tip" title="{tr}Tip{/tr}"}
		{tr}Tiki Wiki CMS Groupware is Free and Open Source Software (FOSS). It is a community-driven project which exists and improves thanks to the participation of people just like YOU.{/tr} <a href="http://info.tiki.org/Join+the+community">{tr}Join the community!{/tr}</a>
{/remarksbox}

<div class="adminoptionbox">
	<fieldset>
		<legend>{tr}Promote your site{/tr}</legend>
		{tr}To submit your site to Tiki.org:{/tr} <a href="tiki-register_site.php">{tr}Submit site{/tr}</a>
	</fieldset>
	<form class="admin" id="connect" name="connect" action="tiki-admin.php?page=connect" method="post">
		<fieldset>
			<legend>{tr}Help Tiki spread{/tr}</legend>
			{tr}Add the "Powered by" module to your site: {/tr} <a href="tiki-admin_modules.php">{tr}Click here to manage modules{/tr}</a>
		</fieldset>

		<fieldset>
			<legend>{tr}Help improve Tiki{/tr}</legend>
			{tr}To submit a feature request or to report a bug:{/tr} <a href="http://dev.tiki.org/Report+a+Bug">{tr}Click here to go to our development site{/tr}</a> 
		</fieldset>

		<fieldset>
			<legend>{tr}Tiki Connect{/tr}</legend>
			{remarksbox type="info" title="New Feature" icon="bricks"}
				{tr}Please note: Work in progress, not functioning as yet (r33357){/tr}
			{/remarksbox}
			{preference name="connect_feature"}
			<div class="adminoptionboxchild" id="connect_feature_childcontainer">
				{preference name="connect_send_info"}
				<div class="adminoptionboxchild" id="connect_send_info_childcontainer">
					{preference name="connect_site_title" default=$prefs.browsertitle}
					{preference name="connect_site_email" default=$def_admin_email}
					{preference name="connect_site_url" default=$base_url}
					{preference name="connect_site_keywords" default=$prefs.metatag_keywords}
					{preference name="connect_site_location" default=$def_loc}
				</div>
				{preference name="connect_frequency"}
				{preference name="connect_server"}
				{*preference name="connect_last_post"*}
			</div>

		</fieldset>

		<div class="heading input_submit_container" style="text-align: center;">
			<input type="submit" value="{tr}Change preferences{/tr}" />
		</div>
	</form>
</div>
