{title help="Feeds"}{tr}Admin External Feeds{/tr}{/title}

{remarksbox type="tip" title="{tr}Tips{/tr}"}
	{tr}This page is to configure settings of external feeds read/imported by Tiki. To generate/export feeds, look for "Feeds" on the admin panel, or{/tr}
	<a class="rbox-link" href="tiki-admin.php?page=rss">{tr}Click Here{/tr}</a>.
	<hr/>
	{tr}To use feeds in a text area (Wiki page, etc), a <a class="rbox-link" href="tiki-admin_modules.php">module</a> or a template, use {literal}{rss id=x}{/literal}, where x is the ID of the feed.{/tr}
{/remarksbox}

{if $preview eq 'y'}
	<div class="simplebox">
		<h2>{tr}Content for the feed{/tr}</h2>
		{if $feedtitle ne ''}
			<h3>{$feedtitle.title|escape}</h3>
		{/if}
		<ul>
			{section name=ix loop=$items}
				<li><a href="{$items[ix].link|escape}" class="link">{$items[ix].title|escape}</a>{if $items[ix].pubDate ne ""}<br /><span class="rssdate">({$items[ix].pubDate|escape})</span>{/if}</li>
			{/section}
		</ul>
	</div>
{/if}

{if $articleConfig}
	<h2>{tr}Article Generator{/tr}</h2>
	<p>{tr}The article generator will create a new article for every item read in the RSS feed.{/tr}</p>

	<form method="post" action="">
		<p>
			<input id="article_active" type="checkbox" name="enable" value="1"{if $articleConfig.active} checked="checked"{/if}/>
			<label for="article_active">{tr}Enable{/tr}</label>
		</p>
		{if $prefs.feature_submissions eq 'y'}
		<p>
			<input id="article_submission" type="checkbox" name="submission" value="1"{if $articleConfig.submission} checked="checked"{/if}/>
			<label for="article_submission">{tr}Use Article Submission System{/tr}</label>
		</p>
		{/if}
		<p>
			<label for="article_expiry">{tr}Expiration{/tr}</label>
			<input type="text" name="expiry" id="article_expiry" value="{$articleConfig.expiry|escape}" size="3"/> {tr}days{/tr}
		</p>
		<p>
			<label for="article_future_publish">{tr}Publish in the future{/tr}</label>
			<input type="text" name="future_publish" id="article_future_publish" value="{$articleConfig.future_publish|escape}" size="4"/> {tr}minutes{/tr} ({tr}-1 to use original publishing date from the feed{/tr})
		</p>
		<p>
			<label for="article_type">{tr}Type{/tr}</label>
			<select name="type" id="article_type">
				{foreach from=$types item=t}
					<option value="{$t.type|escape}"{if $t.type eq $articleConfig.atype} selected="selected"{/if}>{$t.type|escape}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<label for="article_topic">{tr}Topic{/tr}</label>
			<select name="topic" id="article_topic">
				<option value="0">{tr}None{/tr}
				{foreach from=$topics item=t}
					<option value="{$t.topicId|escape}"{if $t.topicId eq $articleConfig.topic} selected="selected"{/if}>{$t.name|escape}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<label for="article_rating">{tr}Rating{/tr}</label>
			<select name="rating" id="article_rating">
				{foreach from=$ratingOptions item=v}
					<option{if $v eq $articleConfig.rating} selected="selected"{/if}>{$v|escape}</option>
				{/foreach}
			</select>
		</p>
		<p>
			{include file=categorize.tpl}
		</p>
		<p>
			<input type="submit" value="{tr}Configure{/tr}"/>
		</p>
	</form>
{/if}

{if $rssId > 0}
	<h2>{tr}Edit this feed:{/tr} {$name|escape}</h2>
	<a href="tiki-admin_rssmodules.php">{tr}Create new external feed{/tr}</a>
{else}
	<h2>{tr}Create new external feed{/tr}</h2>
{/if}

<form action="tiki-admin_rssmodules.php" method="post">
	<input type="hidden" name="rssId" value="{$rssId|escape}" />
	<table class="formcolor">
		<tr>
			<td>{tr}Name:{/tr}</td>
			<td><input type="text" name="name" value="{$name|escape}" /></td>
		</tr>
		<tr>
			<td>{tr}Description:{/tr}</td>
			<td><textarea name="description" rows="4" cols="40" style="width:95%">{$description|escape}</textarea></td>
		</tr>
		<tr>
			<td>{tr}URL:{/tr}</td>
			<td><input size="47" type="text" name="url" value="{$url|escape}" /></td>
		</tr>
		<tr>
			<td>{tr}Refresh rate:{/tr}</td>
			<td>
				<select name="refresh">
					<option value="1" {if $refresh eq 60}selected="selected"{/if}>{60|duration}</option>
					<option value="5" {if $refresh eq 300}selected="selected"{/if}>{300|duration}</option>
					<option value="10" {if $refresh eq 600}selected="selected"{/if}>{600|duration}</option>
					<option value="15" {if $refresh eq 900}selected="selected"{/if}>{900|duration}</option>
					<option value="20" {if $refresh eq 1200}selected="selected"{/if}>{1200|duration}</option>
					<option value="30" {if $refresh eq 1800}selected="selected"{/if}>{1800|duration}</option>
					<option value="45" {if $refresh eq 2700}selected="selected"{/if}>{2700|duration}</option>
					<option value="60" {if $refresh eq 3600}selected="selected"{/if}>{3600|duration}</option>
					<option value="90" {if $refresh eq 5400}selected="selected"{/if}>{5400|duration}</option>
					<option value="120" {if $refresh eq 7200}selected="selected"{/if}>{7200|duration}</option>
					<option value="360" {if $refresh eq 21600}selected="selected"{/if}>{21600|duration}</option>
					<option value="720" {if $refresh eq 43200}selected="selected"{/if}>{43200|duration}</option>
					<option value="1440" {if $refresh eq 86400}selected="selected"{/if}>{86400|duration}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{tr}show feed title:{/tr}</td>
			<td><input type="checkbox" name="showTitle" {if $showTitle eq 'y'}checked="checked"{/if} /></td>
		</tr>
		<tr>
			<td>{tr}show publish date:{/tr}</td>
			<td><input type="checkbox" name="showPubDate" {if $showPubDate eq 'y'}checked="checked"{/if} /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
		</tr>
	</table>
</form>

<h2>{tr}External feeds{/tr}</h2>
<div align="center">
	{if $channels or ($find ne '')}
		{include file='find.tpl'}
	{/if}
	<table class="normal">
		<tr>
			<th>{self_link _sort_arg='sort_mode' _sort_field='rssId'}{tr}ID{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='lastUpdated'}{tr}Last update{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='showTitle'}{tr}Show Title?{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='showPubDate'}{tr}Show Date?{/tr}{/self_link}</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle values="even,odd" print=false}
		{section name=chan loop=$channels}
			<tr class="{cycle}">
				<td>{$channels[chan].rssId|escape}</td>
				<td>
					<strong>{$channels[chan].name|escape}</strong><br />
					{if $channels[chan].description}{$channels[chan].description|escape|nl2br}<br />{/if}
					Site: <a href="{$channels[chan].siteurl|escape}">{$channels[chan].sitetitle|escape}</a><br />
					Feed: <a class="link" href="{$channels[chan].url|escape}">{$channels[chan].url|truncate:50:"...":true}</a><br />
				</td>
				<td>
					{if $channels[chan].lastUpdated eq '1000000'}{tr}Never{/tr}{else}{$channels[chan].lastUpdated|tiki_short_datetime}{/if}<br />
					Refresh rate: {$channels[chan].refresh|duration}
				</td>
				<td style="text-align:center">{$channels[chan].showTitle|escape}</td>
				<td style="text-align:center">{$channels[chan].showPubDate|escape}</td>
				<td>
					<a class="link" href="tiki-admin_rssmodules.php?offset={$offset|escape}&amp;sort_mode={$sort_mode|escape}&amp;remove={$channels[chan].rssId|escape}" title="{tr}Delete{/tr}">{icon _id=cross alt="{tr}Delete{/tr}"}</a>
					<a class="link" href="tiki-admin_rssmodules.php?offset={$offset|escape}&amp;sort_mode={$sort_mode|escape}&amp;rssId={$channels[chan].rssId|escape}" title="{tr}Edit{/tr}">{icon _id=page_edit}</a>
					<a class="link" href="tiki-admin_rssmodules.php?offset={$offset|escape}&amp;sort_mode={$sort_mode|escape}&amp;view={$channels[chan].rssId|escape}" title="{tr}View{/tr}">{icon _id=feed alt="{tr}View feed{/tr}"}</a>
					<a class="link" href="tiki-admin_rssmodules.php?offset={$offset|escape}&amp;sort_mode={$sort_mode|escape}&amp;refresh={$channels[chan].rssId|escape}" title="{tr}Refresh{/tr}">{icon _id=arrow_refresh alt="{tr}Refresh{/tr}"}</a>
					{if $prefs.feature_articles eq 'y'}
						<a class="link" href="tiki-admin_rssmodules.php?offset={$offset|escape}&amp;sort_mode={$sort_mode|escape}&amp;article={$channels[chan].rssId|escape}" title="{tr}Article Generator{/tr}">{icon _id=newspaper_go alt="{tr}Article Generator{/tr}"}</a>
					{/if}
				</td>
			</tr>
		{sectionelse}
         {norecords _colspan=6}
		{/section}
	</table>

	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

</div>

