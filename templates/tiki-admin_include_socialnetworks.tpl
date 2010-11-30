<form action="tiki-admin.php?page=socialnetworks" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="socialnetworksset" value="{tr}Change preferences{/tr}" />
	</div>

	<fieldset class="admin">
	<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_socialnetworks}
	</fieldset>

	<fieldset class="admin">
		<legend>{tr}Twitter Settings{/tr}</legend>
		{remarksbox type="note" title="{tr}Note{/tr}"}
		<p>{tr}To use Twitter integration, you must register this site as an application at{/tr}
		 <a href="http://twitter.com/oauth_clients/" target="_blank">http://twitter.com/oauth_clients/</a>
		 {tr}and allow write access for the application{/tr}.<br />
		 {tr}Enter &lt;your site URL&gt;tiki-socialnetworks.php as callback URL{/tr}.</p>
		{/remarksbox}
		<div class="adminoptionbox">
			{preference name=socialnetworks_twitter_consumer_key}
			{preference name=socialnetworks_twitter_consumer_secret}
		</div>
	</fieldset>

	<fieldset class="admin">
		<legend>{tr}Facebook Settings{/tr}</legend>
		{remarksbox type="note" title="{tr}Note{/tr}"}
		<p>{tr}To use Facebook integration, you must register this site as an application at{/tr}
		 <a href="http://developers.facebook.com/setup/" target="_blank">http://developers.facebook.com/setup/</a>
		 {tr}and allow extended access for the application{/tr}.<br />
		{tr}Enter &lt;your site URL&gt;tiki-socialnetworks.php?request_facebook as Site URL and &lt;your site&gt; as Site Domain{/tr}.</p>
		{/remarksbox}
		<div class="adminoptionbox">
			{preference name=socialnetworks_facebook_api_key}
			{preference name=socialnetworks_facebook_application_secr}
			{preference name=socialnetworks_facebook_application_id}
		</div>
	</fieldset>
	
	<fieldset class="admin">
		<legend>{tr}bit.ly Settings{/tr}</legend>
		{remarksbox type="note" title="{tr}Note{/tr}"}
		<p>{tr}There is no need to set up a site-wide bit.ly account; every user can have his or her own, but this allows for site-wide statistics{/tr}<br />
		{tr}Go to{/tr} <a href="http://bit.ly/a/sign_up">http://bit.ly/a/sign_up</a> {tr}to sign up for an account{/tr}.<br />
		{tr}Go to{/tr} <a href="http://bit.ly/a/your_api_key">http://bit.ly/a/your_api_key</a> {tr}to retrieve the API key{/tr}.</p>
		{/remarksbox}
		<div class="adminoptionbox">
		 	{preference name=socialnetworks_bitly_login}
		 	{preference name=socialnetworks_bitly_key}
		 	{preference name=socialnetworks_bitly_sitewide}
		</div>
	</fieldset>

	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="socialnetworksset" value="{tr}Change preferences{/tr}" />
	</div>
</form>
