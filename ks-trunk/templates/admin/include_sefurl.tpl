{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}See also{/tr} <a class="rbox-link" href="tiki-admin.php?page=metatags">{tr}Meta-tags{/tr}</a>.
{/remarksbox}

<form class="admin" method="post" action="tiki-admin.php?page=sefurl">
	<div class="heading input_submit_container" style="text-align: right;">
		<input type="submit" name="save" value="{tr}Change preferences{/tr}" />
	</div>
	
	<fieldset class="admin">
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_sefurl visible="always"}

		{if $httpd eq 'IIS' and !$IIS_UrlRewriteModule}
			{remarksbox type="warning" title="{tr}Warning{/tr}"}
			{tr}SEFURL requires the <strong>URL Rewrite module</strong> for IIS. You do not seem to have this module installed.{/tr}
			{tr}Please see <a href="http://doc.tiki.org/Windows+Server+Install">Windows Server Install</a> on tiki.org for more information.{/tr}
			{/remarksbox}
		{else}
			{if $configurationFile eq 'missing'}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
				{tr _0=$enabledFileName}SEFURL will not work unless Tiki specific directives are deployed to the %0 file.{/tr}
				{tr _0="<strong>$referenceFileName</strong>" _1="<strong>$enabledFileName</strong>"}To enable this file, simply copy the %0 file (located in the main directory of your Tiki installation) to %1.{/tr}
				{tr _0=$enabledFileName}If you need to keep an existing (non Tiki) %0 file, just add Tiki directives to it.{/tr}
				{tr}When you upgrade Tiki (e.g. from version 7 to version 8), make sure to make use of the new URL rewriting configuration file.{/tr}
				{/remarksbox}
			{elseif $configurationFile eq 'no reference'}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
				{tr _0=$referenceFileName}%0 file is missing.{/tr} {tr}Unable to verify that your URL rewriting configuration is up to date.{/tr} {tr}SEFURL may not work completely or correctly if Tiki URL rewriting configuration is not current.{/tr}
				{/remarksbox}
			{elseif $configurationFile eq 'unexpected reference' or $configurationFile eq 'unexpected enabled'}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
				{tr _0=$enabledFileName}%0 is not in the expected format.{/tr} {tr}Unable to verify that your URL rewriting configuration is up to date.{/tr} {tr}SEFURL may not work completely or correctly if Tiki URL rewriting configuration is not current.{/tr}<br>
				{tr _0=$enabledFileName}%0 may simply be outdated.{/tr}
				{tr _0="<strong>$referenceFileName</strong>" _1="<strong>$enabledFileName</strong>"}To update this file, if it was not customized, copy the %0 file (located in the main directory of your Tiki installation) to %1, overwriting the latter.{/tr}
				{/remarksbox}
			{elseif $configurationFile eq 'outdated'}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
				{tr _0=$enabledFileName}%0 file is out of date.{/tr} {tr}SEFURL may not work completely or correctly if Tiki URL rewriting configuration is not current.{/tr}
				{tr _0="<strong>$referenceFileName</strong>" _1="<strong>$enabledFileName</strong>"}To update this file, if it was not customized, copy the %0 file (located in the main directory of your Tiki installation) to %1, overwriting the latter.{/tr}
				{/remarksbox}
			{/if}
			{if not empty($rewritebaseSetting)}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
					{tr}The RewriteBase directive seems not to be set up correctly. This is required for sefurl to function correctly.{/tr}<br>
					{tr _0=$enabledFileName _1=$rewritebaseSetting _2=$url_path}The current value in %0 is %1 but the base url for this site is %2{/tr}
				{/remarksbox}
			{/if}
		{/if}
	</fieldset>		
	
	<fieldset class="admin">
		
		<legend>{tr}Settings{/tr}</legend>
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
