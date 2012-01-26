{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}See also{/tr} <a class="rbox-link" href="tiki-admin.php?page=sefurl">{tr}Search Engine Friendly URLs{/tr}</a>.
{/remarksbox}

<form action="tiki-admin.php?page=metatags" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="metatags" value="{tr}Change preferences{/tr}" />
	</div>
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

	<fieldset>
		<legend>
			{tr}Geo URL{/tr} {if $prefs.feature_help eq 'y'}<a target="_blank" href="http://geourl.org/">{icon _id='help'}</a>{/if}
		</legend>
		{preference name=metatag_geoposition}
		{preference name=metatag_georegion}
		{preference name=metatag_geoplacename}
	</fieldset>

	<fieldset>
		<legend>{tr}Robots{/tr}</legend>
		{* Need to show site_metatag_robots as real metatags are overridden at runtime *}
				
		{preference name=metatag_robots}
		{preference name=metatag_revisitafter}
	</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="metatags" value="{tr}Change preferences{/tr}" />
	</div>
</form>
