<form action="tiki-admin.php?page=socialnetworks" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="socialnetworksset" value="{tr}Change preferences{/tr}" />
	</div>
	<fieldset class="admin">
		<legend>{tr}Twitter Settings{/tr}</legend>
		{remarksbox type="note" title="{tr}Note{/tr}"}
		<p>{tr}To use Twitter integration, you must register this site as an application at <a href="http://twitter.com/oauth_clients/" target="_blank">http://twitter.com/oauth_clients/</a> and allow write access for the application.{/tr}</p>
		<p>{tr}Enter &lt;your site URL&gt;tiki-socialnetworks.php as callback URL{/tr}</p>
		{/remarksbox}
		<div class="adminoptionbox">
			{preference name=socialnetworks_twitter_consumer_key}
			{preference name=socialnetworks_twitter_consumer_secret}
		</div>

		<div class="heading input_submit_container" style="text-align: center">
			<input type="submit" name="socialnetworksset" value="{tr}Change preferences{/tr}" />
		</div>
	</fieldset>

	<fieldset class="admin">
		<legend>{tr}Facebook Settings{/tr}</legend>
		{remarksbox type="note" title="{tr}Note{/tr}"}
		<p>{tr}To use Facebook integration, you must register this site as an application at <a href="http://developers.facebook.com/setup/" target="_blank">http://developers.facebook.com/setup/</a> and allow extended access for the application.{/tr}</p>
		<p>{tr}Enter &lt;your site URL&gt;tiki-socialnetworks.php as connect URL{/tr}</p>
		{/remarksbox}
		<div class="adminoptionbox">
			{preference name=socialnetworks_facebook_api_key}
			{preference name=socialnetworks_facebook_application_secr}
			{preference name=socialnetworks_facebook_application_id}
		</div>

		<div class="heading input_submit_container" style="text-align: center">
			<input type="submit" name="socialnetworksset" value="{tr}Change preferences{/tr}" />
		</div>
	</fieldset>
</form>
