{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}See also{/tr} <a class="alert-link" href="tiki-admin.php?page=sefurl">{tr}Search Engine Friendly URLs{/tr}</a>.
{/remarksbox}

<form class="form-horizontal" action="tiki-admin.php?page=metatags" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="metatags" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>

	{tabset name="admin_metatags"}
		{tab name="{tr}Meta tags{/tr}"}
			<h2>{tr}Meta tags{/tr}</h2>

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
			<h2>{tr}Geo Metatags{/tr} {if $prefs.feature_help eq 'y'}<a target="_blank" href="http://en.wikipedia.org/wiki/Geotagging#HTML_pages">{icon name='help'}</a>{/if}</h2>

			{preference name=metatag_geoposition}
			{preference name=metatag_georegion}
			{preference name=metatag_geoplacename}
		{/tab}
		{tab name="{tr}Robots{/tr}"}
			<h2>{tr}Robots{/tr}</h2>
			{* Need to show site_metatag_robots as real metatags are overridden at runtime *}

			{preference name=metatag_robots}
			{preference name=metatag_revisitafter}
		{/tab}
	{/tabset}

	<br>{* I cheated. *}
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="metatags" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>
