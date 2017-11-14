{remarksbox type="tip" title="{tr}Notice{/tr}"}
	{tr}This is a new control panel on Tiki and work is still in progress.{/tr}
{/remarksbox}

<form class="admin form-horizontal" action="tiki-admin.php?page=stats" method="post">
	{ticket}
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			{include file='admin/include_apply_top.tpl'}
		</div>
	</div>

	{tabset}
		{tab name="{tr}Tiki Statistics{/tr}"}
			<br>
			<fieldset>
				{preference name=feature_stats}
				{preference name=feature_referer_stats}
				{preference name=count_admin_pvs}
			</fieldset>
		{/tab}

		{tab name="{tr}Google Analytics{/tr}"}
			<br>
			<fieldset>
				{preference name=site_google_analytics_account}
				{preference name=site_google_analytics_gtag}
				{preference name=site_google_credentials}
			</fieldset>
		{/tab}

		{tab name="{tr}Piwik Analytics{/tr}"}
			<br>
			<fieldset>
				{preference name=site_piwik_analytics_server_url}
				{preference name=site_piwik_site_id}
				{preference name=site_piwik_code syntax="javascript"}
			</fieldset>
		{/tab}
	{/tabset}
	{include file='admin/include_apply_bottom.tpl'}
</form>
