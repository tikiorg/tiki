{title help="RSS+Modules""}{tr}Admin RSS Modules{/tr}{/title}

{remarksbox type="tip" title="{tr}Tips{/tr}"}{tr}This page is to configure settings of RSS feeds read/imported by Tiki. To generate/export RSS feeds, look for "RSS feeds" on the admin panel, or{/tr} <a class="rbox-link" href="tiki-admin.php?page=rss">{tr}Click Here{/tr}</a>.
<hr>{tr}To use RSS feeds in a text area (Wiki page, etc), a <a class="rbox-link" href="tiki-admin_modules.php">module</a> or a template, use {literal}{rss id=x}{/literal}, where x is the ID of the RSS feed.{/tr}{/remarksbox}

{if $preview eq 'y'}
<div class="simplebox">
<h2>{tr}Content for the feed{/tr}</h2>
<br />
{if $feedtitle ne ''}
<h3>{$feedtitle.title}</h3>
{/if}
<ul>
{section name=ix loop=$items}
<li><a href="{$items[ix].link}" class="link">{$items[ix].title}</a>{if $items[ix].pubDate ne ""}<br /><span class="rssdate">({$items[ix].pubDate})</span>{/if}</li>
{/section}
</ul>
</div>
{/if}
{if $rssId > 0}
<h2>{tr}Edit this RSS Module:{/tr} {$name}</h2>
<a href="tiki-admin_rssmodules.php">{tr}Create new RSS Module{/tr}</a>
{else}
<h2>{tr}Create new RSS Module{/tr}</h2>
{/if}
<form action="tiki-admin_rssmodules.php" method="post">
<input type="hidden" name="rssId" value="{$rssId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40" style="width:95%">{$description|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}URL{/tr}:</td><td class="formcolor"><input size="47" type="text" name="url" value="{$url|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Refresh rate{/tr}:</td>
<td class="formcolor">
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
</td></tr>
<tr><td class="formcolor">{tr}show feed title{/tr}:</td><td class="formcolor"><input type="checkbox" name="showTitle" {if $showTitle eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}show publish date{/tr}:</td><td class="formcolor"><input type="checkbox" name="showPubDate" {if $showPubDate eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}RSS channels{/tr}</h2>
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
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].rssId}</td>
<td class="{cycle advance=false}"><strong>{$channels[user].name}</strong><br />{$channels[user].description}<br /><a class="link" href="{$channels[user].url}">URL: {$channels[user].url|truncate:50:"...":true}</a><br />
Size: {$channels[user].size} kb<br />
</td>
<td class="{cycle advance=false}">{if $channels[user].lastUpdated eq '1000000'}{tr}Never{/tr}{else}{$channels[user].lastUpdated|tiki_short_datetime}{/if}<br />
Refresh rate: 
{$channels[user].refresh|duration}
</td>
<td class="{cycle advance=false}" style="text-align:center">{$channels[user].showTitle}</td>
<td class="{cycle advance=false}" style="text-align:center">{$channels[user].showPubDate}</td>
<td class="{cycle}">   <a class="link" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].rssId}" title="{tr}Delete{/tr}">{icon _id=cross alt="{tr}Delete{/tr}"}</a>
   <a class="link" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;rssId={$channels[user].rssId}" title="{tr}Edit{/tr}">{icon _id=page_edit}</a>
   <a class="link" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].rssId}" title="{tr}View{/tr}">{icon _id=feed alt="{tr}View feed{/tr}"}</a>
   <a class="link" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;refresh={$channels[user].rssId}" title="{tr}Refresh{/tr}">{icon _id=arrow_refresh alt="{tr}Refresh{/tr}"}</a>
</td>
</tr>
{sectionelse}
<tr><td colspan="6" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

</div>

