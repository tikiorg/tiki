{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}See also{/tr} <a class="rbox-link" href="tiki-admin.php?page=sefurl">{tr}Search Engine Friendly URLs{/tr}</a>.
{/remarksbox}

<form action="tiki-admin.php?page=metatags" method="post">

    <div class="row">
        <div class="form-group col-lg-12 clearfix">
            <div class="pull-right">
                <input type="submit" class="btn btn-default btn-sm" name="metatags" value="{tr}Change preferences{/tr}">
            </div>
        </div>
    </div>

{tabset name="admin_metatags"}
	{tab name="{tr}Meta tags{/tr}"}
	<fieldset>
		<legend>
			{tr}Meta tags{/tr}
		</legend>

		<div class="adminoptionbox">
			{preference name=metatag_keywords}
			{preference name=metatag_freetags}
			{preference name=metatag_threadtitle}
			{preference name=metatag_imagetitle}
			{preference name=metatag_description}
			{preference name=metatag_pagedesc}
			{preference name=metatag_author}
		</div>
	</fieldset>
	{/tab}

	{tab name="{tr}Geo URL{/tr}"}
	<fieldset>
		<legend>
			{tr}Geo URL{/tr} {if $prefs.feature_help eq 'y'}<a target="_blank" href="http://geourl.org/">{icon _id='help'}</a>{/if}
		</legend>
		{preference name=metatag_geoposition}
		{preference name=metatag_georegion}
		{preference name=metatag_geoplacename}
	</fieldset>
	{/tab}
	{tab name="{tr}Robots{/tr}"}
	<fieldset>
		<legend>{tr}Robots{/tr}</legend>
		{* Need to show site_metatag_robots as real metatags are overridden at runtime *}
				
		{preference name=metatag_robots}
		{preference name=metatag_revisitafter}
	</fieldset>
	{/tab}
{/tabset}
    <br>{* I cheated. *}
    <div class="row">
        <div class="form-group col-lg-12 text-center">
            <input type="submit" class="btn btn-default btn-sm" name="metatags" value="{tr}Change preferences{/tr}">
        </div>
    </div>
</form>
