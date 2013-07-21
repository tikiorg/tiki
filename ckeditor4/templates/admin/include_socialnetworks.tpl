<form action="tiki-admin.php?page=socialnetworks" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="socialnetworksset" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset}
		{tab name="{tr}General{/tr}"}
			<fieldset class="admin">
				<legend>{tr}Social Network Integration{/tr}</legend>
				{preference name=feature_socialnetworks visible="always"}
			</fieldset>
			<fieldset class="admin">
				<legend>{tr}Internal Social Network{/tr}</legend>
				{preference name=activity_custom_events visible="always"}

				<div class="adminoptionboxchild" id="activity_custom_events_childcontainer">				
					
					{$headerlib->add_dracula()}
					{$headerlib->add_jsfile('lib/jquery_tiki/activity.js', 'external')}
					<div id="graph-canvas" class="graph-canvas" data-graph-nodes="{$event_graph.nodes|@json_encode|escape}" data-graph-edges="{$event_graph.edges|@json_encode|escape}"></div>
					<div><button href="#" id="graph-draw" class="button">{tr}Draw Event Diagram{/tr}</button></div>
					<div><button href="{service controller=managestream action=list}" id="show-rules">{tr}Show Rules{/tr}</button></div>
					{jq}
					$('#graph-draw').click(function(e) {
						$('#graph-canvas')
							.empty()
							.css('width', $window.width() - 50)
							.css('height', $window.height() - 130)
							.dialog({
								title: "Events",
								width: $window.width() - 20,
								height: $window.height() - 100
							})
							.drawGraph();
						return false;
					});
					{/jq}
				</div>
			</fieldset>
		{/tab}
		{tab name="{tr}Twitter{/tr}"}
			{remarksbox type="note" title="{tr}Note{/tr}"}
			<p>{tr}To use Twitter integration, you must register this site as an application at{/tr}
			 <a href="http://twitter.com/oauth_clients/" target="_blank">http://twitter.com/oauth_clients/</a>
			 {tr}and allow write access for the application{/tr}.<br>
			 {tr}Enter &lt;your site URL&gt;tiki-socialnetworks.php as callback URL{/tr}.</p>
			{/remarksbox}
			<div class="adminoptionbox">
				{preference name=socialnetworks_twitter_consumer_key}
				{preference name=socialnetworks_twitter_consumer_secret}
			</div>
		{/tab}
		{tab name="{tr}Facebook{/tr}"}
			{remarksbox type="note" title="{tr}Note{/tr}"}
			<p>{tr}To use Facebook integration, you must register this site as an application at{/tr}
			 <a href="http://developers.facebook.com/setup/" target="_blank">http://developers.facebook.com/setup/</a>
			 {tr}and allow extended access for the application{/tr}.<br>
			{tr}Enter &lt;your site URL&gt;tiki-socialnetworks.php?request_facebook as Site URL and &lt;your site&gt; as Site Domain{/tr}.</p>
			{/remarksbox}
			<div class="adminoptionbox">
				{preference name=socialnetworks_facebook_application_secr}
				{preference name=socialnetworks_facebook_application_id}
				{preference name=socialnetworks_facebook_login}
				{preference name=socialnetworks_facebook_autocreateuser}
				{preference name=socialnetworks_facebook_firstloginpopup}
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
			{remarksbox type="note" title="{tr}Note{/tr}"}
			<p>{tr}There is no need to set up a site-wide bit.ly account; every user can have his or her own, but this allows for site-wide statistics{/tr}<br>
			{tr}Go to{/tr} <a href="http://bit.ly/a/sign_up">http://bit.ly/a/sign_up</a> {tr}to sign up for an account{/tr}.<br>
			{tr}Go to{/tr} <a href="http://bit.ly/a/your_api_key">http://bit.ly/a/your_api_key</a> {tr}to retrieve the API key{/tr}.</p>
			{/remarksbox}
			<div class="adminoptionbox">
				{preference name=socialnetworks_bitly_login}
				{preference name=socialnetworks_bitly_key}
				{preference name=socialnetworks_bitly_sitewide}
			</div>
		{/tab}
	{/tabset}


	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="socialnetworksset" value="{tr}Change preferences{/tr}" />
	</div>
</form>
