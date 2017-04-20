{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}See also{/tr} <a class="alert-link" href="tiki-admin.php?page=sefurl">{tr}Search Engine Friendly URLs{/tr}</a>. {tr}Also{/tr} <a target="_blank" href="http://en.wikipedia.org/wiki/Geotagging#HTML_pages">{tr}here{/tr}</a> {tr}for more information on geo tagging.{/tr}
{/remarksbox}

<form class="form-horizontal" action="tiki-admin.php?page=metatags" method="post">
	{ticket}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			{include file='admin/include_apply_top.tpl'}
		</div>
	</div>

	{tabset name="admin_metatags"}
		{tab name="{tr}Meta tags{/tr}"}
			<br>

			<div class="adminoptionbox">
				{preference name=metatag_keywords}
				{preference name=metatag_freetags}
				{preference name=metatag_threadtitle}
				{preference name=metatag_imagetitle}
				{preference name=metatag_description}
				{preference name=metatag_pagedesc}
				{preference name=metatag_author}
			</div>
		{/tab}

		{tab name="{tr}Geo Metatags{/tr}"}
			<br>

			{preference name=metatag_geoposition}
			{preference name=metatag_georegion}
			{preference name=metatag_geoplacename}
		{/tab}
		{tab name="{tr}Robots{/tr}"}
			<br>
			{* Need to show site_metatag_robots as real metatags are overridden at runtime *}

			{preference name=metatag_robots}
			{preference name=metatag_revisitafter}
		{/tab}
	{/tabset}
	{include file='admin/include_apply_bottom.tpl'}
</form>
