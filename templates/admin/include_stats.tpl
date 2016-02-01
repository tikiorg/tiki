{remarksbox type="tip" title="{tr}Development Notice{/tr}"}
	{tr}Please see the <a class='alert-link' target='tikihelp' href='http://dev.tiki.org/statistics'>Statistics page</a> on Tiki's developer site.{/tr}
{/remarksbox}

<form class="admin form-horizontal" action="tiki-admin.php?page=stats" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="statistics" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}"/>
			</div>
		</div>
	</div>

	{tabset}
		{tab name="{tr}Tiki Statistics{/tr}"}
			<fieldset>
				<legend>{tr}Tiki Statistics{/tr}</legend>
				{preference name=feature_stats}
				{preference name=feature_referer_stats}
				{preference name=count_admin_pvs}
			</fieldset>
		{/tab}

		{tab name="{tr}Google Analytics{/tr}"}
			<fieldset>
				<legend>{tr}Google Analytics{/tr}</legend>
				{preference name=site_google_analytics_account}
				{preference name=site_google_credentials}
			</fieldset>
		{/tab}

		{tab name="{tr}Piwik Analytics{/tr}"}
			<fieldset>
				<legend>{tr}Piwik Analytics{/tr}</legend>
				{preference name=site_piwik_analytics_server_url}
				{preference name=site_piwik_site_id}
			</fieldset>
		{/tab}
	{/tabset}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="statistics" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}"/>
			</div>
		</div>
	</div>
</form>
