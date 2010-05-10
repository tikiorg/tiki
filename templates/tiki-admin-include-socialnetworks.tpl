<form action="tiki-admin.php?page=socialnetworks" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="socialnetworksset" value="{tr}Change preferences{/tr}" />
	</div>
	<fieldset class="admin">
		<legend>{tr}Twitter Settings{/tr}</legend>
		{remarksbox type="note" title="{tr}Note{/tr}"}
		{tr}To use Twitter integration, you must register this site as an application at <a href="http://twitter.com/oauth_clients/">http://twitter.com/oauth_clients/</a> and allow write access for the application.{/tr}
		{/remarksbox}
		<div class="adminoptionbox">
			{preference name=socialnetworks_twitter_consumer_key}
			{preference name=socialnetworks_twitter_consumer_secret}
		</div>

		<div class="heading input_submit_container" style="text-align: center">
			<input type="submit" name="socialnetworksset" value="{tr}Change preferences{/tr}" />
		</div>
	</fieldset>
</form>
