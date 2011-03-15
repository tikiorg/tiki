{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}See also{/tr} <a class="rbox-link" href="tiki-admin.php?page=metatags">{tr}Meta-tags{/tr}</a>.
{/remarksbox}

<form class="admin" method="post" action="tiki-admin.php?page=sefurl">
	<div class="heading input_submit_container" style="text-align: right;">
		<input type="submit" name="save" value="{tr}Change preferences{/tr}" />
	</div>
	<fieldset class="admin">
		{if $htaccess eq 'missing'}
		{remarksbox type="warning" title="{tr}Warning{/tr}"}	
		{tr}SEFURL will not work unless Tiki specific directives are deployed to the .htaccess file.{/tr}	
		{tr}To enable this file, simply rename the <strong>_htaccess</strong> file (located in the main directory of your Tiki installation) to <strong>.htaccess</strong>.{/tr}
		{tr}If you need to keep an existing (non Tiki) .htaccess file, just add Tiki directives to it.{/tr}
		{tr}When you upgrade (ex.: from Tiki4 to Tiki5), make sure to use the new _htaccess file.{/tr}
		{/remarksbox}
		{elseif $htaccess eq 'outdated'}
		{remarksbox type="warning" title="{tr}Warning{/tr}"}	
		{tr}.htaccess file is out of date. SEFURL may not work completely or incorrectly if Tiki htaccess directives are not current.{/tr}	
		{tr}To update this file, if it was not customized, overwrite the <strong>.htaccess</strong> file (located in the main directory of your Tiki installation) with <strong>_htaccess</strong>.{/tr}
		{/remarksbox}
		{/if}
		<legend>{tr}Settings{/tr}</legend>
		{preference name=feature_sefurl}
		{preference name=feature_sefurl_filter}

		<div style="padding:0.5em;clear:both">
			<label for="feature_sefurl_paths">
				{tr}List of Url Parameters that should go in the path{/tr}
			</label>
			{strip}
				{capture name=paths}
					{foreach name=loop from=$prefs.feature_sefurl_paths item=path}
						{$path}
						{if !$smarty.foreach.loop.last}/{/if}
					{/foreach}
				{/capture}
			{/strip}
			<input type="text" id="feature_sefurl_paths" name="feature_sefurl_paths" value="{$smarty.capture.paths|escape}" />
		</div>

		{preference name=feature_sefurl_title_article}
		{preference name=feature_sefurl_title_blog}
		{preference name=feature_sefurl_tracker_prefixalias}
		{preference name=feature_canonical_url}
	</fieldset>
	<div class="heading input_submit_container" style="text-align: center;padding:1em;">
		<input type="submit" name="save" value="{tr}Change preferences{/tr}" />
	</div>
</form>
