<form class="form-horizontal" action="tiki-admin.php?page=socialnetworks" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="socialnetworksset" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>

	{tabset}
		{tab name="{tr}General{/tr}"}
			<h2>{tr}Social Network Integration{/tr}</h2>
			{preference name=feature_socialnetworks visible="always"}
		{/tab}
		{tab name="{tr}Twitter{/tr}"}
			<h2>{tr}Twitter{/tr}</h2>
			<div class="adminoptionbox">
				{preference name=socialnetworks_twitter_site}
				{preference name=socialnetworks_twitter_site_image}
			</div>
			{remarksbox type="note" title="{tr}Note{/tr}"}
				<p>
					{tr}To use Twitter integration, you must register this site as an application at{/tr}
					<a class="alert-link" href="http://twitter.com/oauth_clients/" target="_blank">http://twitter.com/oauth_clients/</a>
					{tr}and allow write access for the application{/tr}.<br>
					{tr}Enter &lt;your site URL&gt;tiki-socialnetworks.php as callback URL{/tr}.
				</p>
			{/remarksbox}
			<div class="adminoptionbox">
				{preference name=socialnetworks_twitter_consumer_key}
				{preference name=socialnetworks_twitter_consumer_secret}
			</div>
		{/tab}
		{tab name="{tr}Facebook{/tr}"}
			<h2>{tr}Facebook{/tr}</h2>
			<div class="adminoptionbox">
				{preference name=socialnetworks_facebook_site_name}
				{preference name=socialnetworks_facebook_site_image}
			</div>
			{remarksbox type="note" title="{tr}Note{/tr}"}
				<p>
					{tr}To use Facebook integration, you must register this site as an application at{/tr}
					<a class="alert-link" href="http://developers.facebook.com/setup/" target="_blank">http://developers.facebook.com/setup/</a>
					{tr}and allow extended access for the application{/tr}.<br>
					{tr}Enter &lt;your site URL&gt;tiki-socialnetworks.php?request_facebook as Site URL and &lt;your site&gt; as Site Domain{/tr}.
				</p>
			{/remarksbox}
			<div class="adminoptionbox">
				{preference name=socialnetworks_facebook_application_secr}
				{preference name=socialnetworks_facebook_application_id}
				{preference name=socialnetworks_facebook_login}
				{preference name=socialnetworks_facebook_autocreateuser}
				<div class="adminoptionboxchild" id="socialnetworks_facebook_autocreateuser_childcontainer">
					{preference name=socialnetworks_facebook_firstloginpopup}
					{preference name=socialnetworks_facebook_email}
					{preference name=socialnetworks_facebook_names}
				</div>
				{remarksbox type="note" title="{tr}Note{/tr}"}
					{tr}The following preferences affect what permissions the user is asked to allow Tiki to do by Facebook when authorizing it.{/tr}
				{/remarksbox}
				{preference name=socialnetworks_facebook_offline_access}
				{preference name=socialnetworks_facebook_publish_stream}
				{preference name=socialnetworks_facebook_manage_events}
				{preference name=socialnetworks_facebook_manage_pages}
				{preference name=socialnetworks_facebook_sms}
			</div>
		{/tab}
		{tab name="{tr}bit.ly{/tr}"}
			<h2>{tr}bit.ly{/tr}</h2>
			{remarksbox type="note" title="{tr}Note{/tr}"}
				<p>
					{tr}There is no need to set up a site-wide bit.ly account; every user can have his or her own, but this allows for site-wide statistics{/tr}<br>
					{tr}Go to{/tr} <a class="alert-link" href="http://bit.ly/a/sign_up">http://bit.ly/a/sign_up</a> {tr}to sign up for an account{/tr}.<br>
					{tr}Go to{/tr} <a class="alert-link" href="http://bit.ly/a/your_api_key">http://bit.ly/a/your_api_key</a> {tr}to retrieve the API key{/tr}.
				</p>
			{/remarksbox}
			<div class="adminoptionbox">
				{preference name=socialnetworks_bitly_login}
				{preference name=socialnetworks_bitly_key}
				{preference name=socialnetworks_bitly_sitewide}
			</div>
		{/tab}
	{tab name="{tr}LinkedIn{/tr}"}
		<h2>{tr}LinkedIn{/tr}</h2>
		{remarksbox type="note" title="{tr}Note{/tr}"}
		<p>
			{tr}To use LinkedIn integration, you must register this site as an application at{/tr}
			<a class="alert-link" href="https://www.linkedin.com/developer/apps" target="_blank">https://www.linkedin.com/developer/apps</a>
			{tr}and allow necessary permissions for the application{/tr}.<br>
			{tr}Enter &lt;your site URL&gt;tiki-socialnetworks_linkedin.php as Authorized OAuth Redirect URLs{/tr}.
		</p>
		{/remarksbox}
		<div class="adminoptionbox">
			{preference name=socialnetworks_linkedin_client_id}
			{preference name=socialnetworks_linkedin_client_secr}
			{preference name=socialnetworks_linkedin_login}
			{preference name=socialnetworks_linkedin_autocreateuser}
			<div class="adminoptionboxchild" id="socialnetworks_linkedin_autocreateuser_childcontainer">
				{preference name=socialnetworks_linkedin_email}
				{preference name=socialnetworks_linkedin_names}
			</div>
		</div>
	{/tab}
	{/tabset}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="socialnetworksset" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>
