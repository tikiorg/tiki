{* $Id: tiki-user_information.tpl 16912 2009-02-25 03:38:43Z luciash $ *}
{if $warning}
{remarksbox type="warning" title="{tr}Warning{/tr}"}
	{$warning}
{/remarksbox}
{/if}

<form class="admin" method="post" action="tiki-admin.php?page=sefurl">
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;">
		<input type="checkbox" id="feature_sefurl" name="feature_sefurl" {if $prefs.feature_sefurl eq 'y'}checked="checked"{/if} />
	</div>
	<div>
		<label for="feature_sefurl">{tr}Search engine friendly url{/tr}</label>
		{if $prefs.feature_help eq 'y'} {help url="Rewrite+Rules" desc="{tr}Search engine friendly url{/tr}"}{/if} <br />
	</div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;">
		<input type="checkbox" id="feature_sefurl_filter" name="feature_sefurl_filter" {if $prefs.feature_sefurl_filter eq 'y'}checked="checked"{/if} />
	</div>
	<div>
		<label for="feature_sefurl_filter">{tr}Search engine friendly url Postfilter{/tr}</label>
		{if $prefs.feature_help eq 'y'} {help url="Rewrite+Rules" desc="{tr}Search engine friendly url{/tr}"}{/if} <br />
	</div>
</div>
<div style="padding:0.5em;clear:both">
	<div>
		<label for="feature_sefurl_paths">{tr}List of Url Parameters that should go in the path{/tr}</label>
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
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;">
		<input type="checkbox" id="feature_sefurl_title_article" name="feature_sefurl_title_article" {if $prefs.feature_sefurl_title_article eq 'y'}checked="checked"{/if} />
	</div>
	<div>
		<label for="feature_sefurl_title_article">{tr}Display article title in the sefurl{/tr}</label>
	</div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;">
		<input type="checkbox" id="feature_sefurl_title_blog" name="feature_sefurl_title_blog" {if $prefs.feature_sefurl_title_blog eq 'y'}checked="checked"{/if} />
	</div>
	<div>
		<label for="feature_sefurl_title_blog">{tr}Display blog title in the sefurl{/tr}</label>
	</div>
</div>
<div class="heading input_submit_container" style="text-align: center;padding:1em;">
	 <input type="submit" name="save" value="{tr}Change Preferences{/tr}" />
</div>
</form>
