<a class="pagetitle" href="tiki-admin_rssmodules.php">{tr}Admin RSS Modules{/tr}</a>
<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=RSSModules" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin RSS modules{/tr}"><img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_rssmodules.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin RSSmodules tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /> </a>{/if}


<!-- begin -->

<br /><br />
{if $preview eq 'y'}
<div class="simplebox">
<h2>{tr}Content for the feed{/tr}</h2>
<ul>
{section name=ix loop=$items}
<li><a href="{$items[ix].link}" class="link">{$items[ix].title}</a></li>
{/section}
</ul>
</div>
{/if}
{if $rssId > 0}
<h2>{tr}Edit this RSS module:{/tr} {$name}</h2>
<a class="linkbut" href="tiki-admin_rssmodules.php">[{tr}Create new RSS module{/tr}]</a><br /><br />
{else}
<h2>{tr}Create new RSS module{/tr}</h2>
{/if}
<form action="tiki-admin_rssmodules.php" method="post">
<input type="hidden" name="rssId" value="{$rssId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$description|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}URL{/tr}:</td><td class="formcolor"><input size="47" type="text" name="url" value="{$url|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Refresh Rate{/tr}:</td><td class="formcolor">
<select name="refresh">
<option value="1" {if $minutes eq 60}selected="selected"{/if}>1 {tr}minute{/tr}</option>
<option value="5" {if $refresh eq 300}selected="selected"{/if}>5 {tr}minutes{/tr}</option>
<option value="10" {if $refresh eq 600}selected="selected"{/if}>10 {tr}minutes{/tr}</option>
<option value="15" {if $refresh eq 900}selected="selected"{/if}>15 {tr}minutes{/tr}</option>
<option value="20" {if $refresh eq 1200}selected="selected"{/if}>20 {tr}minutes{/tr}</option>
<option value="30" {if $refresh eq 1800}selected="selected"{/if}>30 {tr}minutes{/tr}</option>
<option value="45" {if $refresh eq 2700}selected="selected"{/if}>45 {tr}minutes{/tr}</option>
<option value="60" {if $refresh eq 3600}selected{/if}>1 {tr}hour{/tr}</option>
<option value="90" {if $refresh eq 5400}selected="selected"{/if}>1.5 {tr}hours{/tr}</option>
<option value="120" {if $refresh eq 7200}selected="selected"{/if}>2 {tr}hours{/tr}</option>
<option value="360" {if $refresh eq 21600}selected="selected"{/if}>6 {tr}hours{/tr}</option>
<option value="720" {if $refresh eq 43200}selected="selected"{/if}>12 {tr}hours{/tr}</option>
<option value="1440" {if $refresh eq 86400}selected="selected"{/if}>1 {tr}day{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Show feed title{/tr}:<b>(work in progress)</b></td><td class="formcolor"><input type="checkbox" name="showTitle" {if $showTitle eq 'y'}checked="checked"{/if}></td></tr>
<tr><td class="formcolor">{tr}Show publish date{/tr}:</td><td class="formcolor"><input type="checkbox" name="showPubDate" {if $showPubDate eq 'y'}checked="checked"{/if}></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Rss Channels{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_rssmodules.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'rssId_desc'}rssId_asc{else}rssId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastUpdated_desc'}lastUpdated_asc{else}lastUpdated_desc{/if}">{tr}Last Update{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'refresh_desc'}refresh_asc{else}refresh_desc{/if}">{tr}Refresh{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastUpdated_desc'}showTitle_asc{else}showTitle_desc{/if}">{tr}Show Feed Title{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'refresh_desc'}showPubDate_asc{else}showPubDate_desc{/if}">{tr}Show Publish Date{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].rssId}</td>
<td class="odd">{$channels[user].name}</td>
<td class="odd">{$channels[user].description}</td>
<td class="odd">{$channels[user].url}</td>
<td class="odd">{$channels[user].lastUpdated|tiki_short_datetime}</td>
<td class="odd">{$channels[user].minutes} min</td>
<td class="odd">{$channels[user].showTitle}</td>
<td class="odd">{$channels[user].showPubDate}</td>
<td class="odd">
   <a class="link" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].rssId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this RSS Channel?{/tr}')">{tr}Remove{/tr}</a>
   <a class="link" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;rssId={$channels[user].rssId}">{tr}Edit{/tr}</a>
   <a class="link" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].rssId}">{tr}View{/tr}</a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].rssId}</td>
<td class="even">{$channels[user].name}</td>
<td class="even">{$channels[user].description}</td>
<td class="even">{$channels[user].url}</td>
<td class="even">{$channels[user].lastUpdated|tiki_short_datetime}</td>
<td class="even">{$channels[user].minutes} min</td>
<td class="even">{$channels[user].showTitle}</td>
<td class="even">{$channels[user].showPubDate}</td>
<td class="even">
   <a class="link" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].rssId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this RSS Channel?{/tr}')">{tr}Remove{/tr}</a>
   <a class="link" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;rssId={$channels[user].rssId}">{tr}Edit{/tr}</a>
   <a class="link" href="tiki-admin_rssmodules.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].rssId}">{tr}View{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_rssmodules.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_rssmodules.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_rssmodules.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

