<form class="form-horizontal" method="post" action="tiki-admin.php?page=polls">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			{if $prefs.feature_polls eq "y" and $tiki_p_admin_polls eq "y"}
				<a role="link" class="btn btn-link" href="tiki-admin_polls.php" title="{tr}List{/tr}">
					{icon name="list"} {tr}Polls{/tr}
				</a>
			{/if}
			{if $prefs.feature_surveys eq "y" and $tiki_p_admin_surveys eq "y"}
				<a role="link" class="btn btn-link" href="tiki-admin_surveys.php" title="{tr}List{/tr}">
					{icon name="list"} {tr}Surveys{/tr}
				</a>
			{/if}
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>

	{tabset name="admin_polls_and_surveys"}

		{if $prefs.feature_polls eq "y"}
			{tab name="{tr}Polls{/tr}"}
				<h2>{tr}Polls{/tr}</h2>
				<fieldset>
					<legend>{tr}Activate the feature{/tr}</legend>
					{preference name=feature_polls visible="always"}
				</fieldset>

				<fieldset class="table">
					<legend>{tr}Plugins{/tr}</legend>
					{preference name=wikiplugin_poll}
				</fieldset>

				<fieldset>
					<legend>{tr}Settings{/tr}{help url="Polls"}</legend>
					<input type="hidden" name="pollprefs" />
					{preference name=feature_poll_anonymous}
					{preference name=feature_poll_revote}
					{preference name=feature_poll_comments}
					<div class="adminoptionboxchild" id="feature_poll_comments_childcontainer">
						{preference name=poll_comments_per_page}
						{preference name=poll_comments_default_ordering}
					</div>
					{preference name=poll_list_categories}
					{preference name=poll_list_objects}
					{preference name=poll_multiple_per_object}
				</fieldset>
			{/tab}
		{/if}

		{if $prefs.feature_surveys eq "y"}
			{tab name="{tr}Surveys{/tr}"}
				<h2>{tr}Surveys{/tr}</h2>
				<fieldset class="table">
					<legend>{tr}Activate the feature{/tr}</legend>
					{preference name=feature_surveys visible="always"}
				</fieldset>
				<fieldset>
					{preference name=poll_surveys_textarea_hidetoolbar}
				</fieldset>
			{/tab}
		{/if}

	{/tabset}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>
</form>
