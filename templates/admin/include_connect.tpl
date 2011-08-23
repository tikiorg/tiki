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
			{tr}Add the "Powered by" module to your site: {/tr} <a href="tiki-admin_modules.php?textFilter=powered&cookietab=3">{tr}Click here to manage modules{/tr}</a>
		</fieldset>

		<fieldset>
			<legend>{tr}Help improve Tiki{/tr}</legend>
			{tr}To submit a feature request or to report a bug:{/tr} <a href="http://dev.tiki.org/Report+a+Bug">{tr}Click here to go to our development site{/tr}</a> 
		</fieldset>

		<fieldset>
			<legend>{tr}Tiki Connect{/tr}</legend>
			{remarksbox type="info" title="{tr}New Feature{/tr}" icon="bricks"}
				{tr}Please note: Work in progress, coming soon... (r36230){/tr}
			{/remarksbox}
			{preference name="connect_feature"}
			<div class="adminoptionboxchild" id="connect_feature_childcontainer">
				<div class="navbar">
					{button _script="#" _text="{tr}Preview info{/tr}" _title="{tr}See what is going to be sent{/tr}" _id="connect_list_btn"}
					{button _script="#" _text="{tr}Send Info{/tr}" _title="{tr}Send the data{/tr}" _id="connect_send_btn"}

					{if !empty($connect_defaults_json)}
						{button _text="{tr}Fill form{/tr}" _title="{tr}Fill this form in based on other preferences{/tr}" _id="connect_defaults_btn" _script="#"}
						{jq}
$("#connect_defaults_btn a").click(function(){
	var connect_defaults = {{$connect_defaults_json}};
	for (el in connect_defaults) {
		$("input[name=" + el + "]").val(connect_defaults[el]);
	}
	return false;
});
						{/jq}
					{/if}
				</div>
				{preference name="connect_send_info"}
				<div class="adminoptionboxchild" id="connect_send_info_childcontainer">
					{preference name="connect_site_title"}
					{preference name="connect_site_email"}
					{preference name="connect_site_url"}
					{preference name="connect_site_location"}
					{preference name="connect_site_keywords"}
				</div>
				{preference name="connect_send_anonymous_info"}
				{preference name="connect_frequency"}
				{preference name="connect_server"}
				{preference name="connect_last_post"}
				{preference name="connect_server_mode"}
				{preference name="connect_guid"}
			</div>

		</fieldset>

		<div class="heading input_submit_container" style="text-align: center;">
			<input type="submit" value="{tr}Change preferences{/tr}" />
		</div>
	</form>
</div>
