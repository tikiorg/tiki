<h1><a class="pagetitle" href="tiki-admin_forums.php">Admin Forums</a></h1>
<h2>{tr}Create/edit Forums{/tr}</h2>
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName=forum%20{$name}&amp;objectType=forum&amp;permType=forums&amp;objectId={$galleryId}">{tr}There are inddividual permissions set for this forum{/tr}</a>
{/if}
<form action="tiki-admin_forums.php" method="post">
<input type="hidden" name="forumId" value="{$forumId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$description}</textarea></td></tr>
<tr><td class="formcolor">{tr}Prevent flooding{/tr}:</td><td class="formcolor"><input type="checkbox" name="controlFlood" {if $controlFlood eq 'y'}checked="checked"{/if} /> 
{tr}Minimum time between posts{/tr}: 
<select name="floodInterval">
<option value="15" {if $floodInterval eq 15}selected="selected"{/if}>15 secs</option>
<option value="30" {if $floodInterval eq 30}selected="selected"{/if}>30 secs</option>
<option value="60" {if $floodInterval eq 60}selected="selected"{/if}>1 min</option>
<option value="120" {if $floodInterval eq 120}selected="selected"{/if}>2 mins</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Topics per page{/tr}:</td><td class="formcolor"><input type="text" name="topicsPerPage" value="{$topicsPerPage}" /></td></tr>
<tr><td class="formcolor">{tr}Section{/tr}:</td><td class="formcolor">
<select name="section">
<option value="" {if $section eq ""}selected="selected"{/if}>{tr}None{/tr}</option>
<option value="__new__"}>{tr}Create new{/tr}</option>
{section name=ix loop=$sections}
<option  {if $section eq $sections[ix]}selected="selected"{/if} value="{$sections[ix]}">{$sections[ix]}</option>
{/section}
</select>
<input name="new_section" type="text" />
</td></tr>
<tr><td class="formcolor">{tr}Moderator{/tr}:</td><td class="formcolor">
<select name="moderator">
{section name=ix loop=$users}
<option value="{$users[ix].user}" {if $moderator eq $users[ix].user}selected="selected"{/if}>{$users[ix].user}</option>
{/section}
</select>
</td></tr>
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}Default ordering for topics{/tr}:</td><td class="formcolor">
<select name="topicOrdering">
<option value="commentDate_desc" {if $topicOrdering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Date (desc){/tr}</option>
<option value="average_desc" {if $topicOrdering eq 'average_desc'}selected="selected"{/if}>{tr}Score (desc){/tr}</option>
<option value="replies_desc" {if $topicOrdering eq 'replies_desc'}selected="selected"{/if}>{tr}Replies (desc){/tr}</option>
<option value="hits_desc" {if $topicOrdering eq 'hits_desc'}selected="selected"{/if}>{tr}Reads (desc){/tr}</option>
<option value="lastPost_desc" {if $topicOrdering eq 'lastPost_desc'}selected="selected"{/if}>{tr}Last post (desc){/tr}</option>
<option value="title_desc" {if $topicOrdering eq 'title_desc'}selected="selected"{/if}>{tr}Title (desc){/tr}</option>
<option value="title_asc" {if $topicOrdering eq 'title_asc'}selected="selected"{/if}>{tr}Title (asc){/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Default ordering for threads{/tr}:</td><td class="formcolor">
<select name="threadOrdering">
<option value="commentDate_desc" {if $topicOrdering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Date (desc){/tr}</option>
<option value="average_desc" {if $topicOrdering eq 'average_desc'}selected="selected"{/if}>{tr}Score (desc){/tr}</option>
<option value="title_desc" {if $topicOrdering eq 'title_desc'}selected="selected"{/if}>{tr}Title (desc){/tr}</option>
<option value="title_asc" {if $topicOrdering eq 'title_asc'}selected="selected"{/if}>{tr}Title (asc){/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor"><input type="checkbox" name="useMail" {if $useMail eq 'y'}checked="checked"{/if} /> {tr}Send this forums posts to this email{/tr}:</td><td class="formcolor"><input type="text" name="mail" value="{$mail}" /></td></tr>
<tr><td class="formcolor"><input type="checkbox" name="usePruneUnreplied" {if $usePruneUnreplied eq 'y'}checked="checked"{/if} /> {tr}Prune unreplied messages after{/tr}:</td><td class="formcolor">
<select name="pruneUnrepliedAge">
<option value="86400" {if $pruneUnrepliedAge eq 86400}selected="selected"{/if}>1 day</option>
<option value="172800" {if $pruneUnrepliedAge eq 172800}selected="selected"{/if}>2 days</option>
<option value="432000" {if $pruneUnrepliedAge eq 432000}selected="selected"{/if}>5 days</option>
<option value="604800" {if $pruneUnrepliedAge eq 604800}selected="selected"{/if}>7 days</option>
<option value="1296000" {if $pruneUnrepliedAge eq 1296000}selected="selected"{/if}>15 days</option>
<option value="2592000" {if $pruneUnrepliedAge eq 2592000}selected="selected"{/if}>30 days</option>
<option value="5184000" {if $pruneUnrepliedAge eq 5184000}selected="selected"{/if}>60 days</option>
<option value="7776000" {if $pruneUnrepliedAge eq 7776000}selected="selected"{/if}>90 days</option>
</select>
</td></tr>
<tr><td class="formcolor"><input type="checkbox" name="usePruneOld" {if $usePruneOld eq 'y'}checked="checked"{/if} /> {tr}Prune old messages after{/tr}:</td><td class="formcolor">
<select name="pruneMaxAge">
<option value="86400" {if $pruneMaxAge eq 86400}selected="selected"{/if}>1 day</option>
<option value="172800" {if $pruneMaxAge eq 172800}selected="selected"{/if}>2 days</option>
<option value="432000" {if $pruneMaxAge eq 432000}selected="selected"{/if}>5 days</option>
<option value="604800" {if $pruneMaxAge eq 604800}selected="selected"{/if}>7 days</option>
<option value="1296000" {if $pruneMaxAge eq 1296000}selected="selected"{/if}>15 days</option>
<option value="2592000" {if $pruneMaxAge eq 2592000}selected="selected"{/if}>30 days</option>
<option value="5184000" {if $pruneMaxAge eq 5184000}selected="selected"{/if}>60 days</option>
<option value="7776000" {if $pruneMaxAge eq 7776000}selected="selected"{/if}>90 days</option>
</select>
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>Forums</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_forums.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'threads_desc'}threads_asc{else}threads_desc{/if}">{tr}topics{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'comments_desc'}comments_asc{else}comments_desc{/if}">{tr}coms{/tr}</a></td>
<td class="heading">{tr}users{/tr}</td>
<td class="heading">{tr}age{/tr}</td>
<td class="heading">{tr}ppd{/tr}</td>
<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastPost_desc'}lastPost_asc{else}lastPost_desc{/if}">{tr}last post{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}hits{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].name}</td>
<td style="text-align:right;" class="odd">{$channels[user].threads}</td>
<td style="text-align:right;" class="odd">{$channels[user].comments}</td>
<td style="text-align:right;" class="odd">{$channels[user].users}</td>
<td style="text-align:right;" class="odd">{$channels[user].age}</td>
<td style="text-align:right;" class="odd">{$channels[user].posts_per_day|string_format:"%.2f"}</td>
<td style="text-align:right;" class="odd">{$channels[user].lastPost|date_format:"%d of %b [%H:%M]"}</td>
<td style="text-align:right;" class="odd">{$channels[user].hits}</td>
<td class="odd">
{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
   <a class="link" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].forumId}">{tr}x{/tr}</a>
   <a class="link" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;forumId={$channels[user].forumId}">{tr}edit{/tr}</a>
   {if $channels[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName=Forum%20{$channels[user].name}&amp;objectType=forum&amp;permType=forums&amp;objectId={$channels[user].forumId}">{tr}perms{/tr}</a>{if $channels[user].individual eq 'y'}){/if}
{/if}
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].name}</td>
<td style="text-align:right;" class="even">{$channels[user].threads}</td>
<td style="text-align:right;" class="even">{$channels[user].comments}</td>
<td style="text-align:right;" class="even">{$channels[user].users}</td>
<td style="text-align:right;" class="even">{$channels[user].age}</td>
<td style="text-align:right;" class="even">{$channels[user].posts_per_day|string_format:"%.2f"}</td>
<td style="text-align:right;" class="even">{$channels[user].lastPost|date_format:"%d of %b [%H:%M]"}</td>
<td style="text-align:right;" class="even">{$channels[user].hits}</td>
<td class="even">
{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
   <a class="link" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].forumId}">{tr}x{/tr}</a>
   <a class="link" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;forumId={$channels[user].forumId}">{tr}edit{/tr}</a>
   {if $channels[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName=Forum%20{$channels[user].name}&amp;objectType=forum&amp;permType=forums&amp;objectId={$channels[user].forumId}">{tr}perms{/tr}</a>{if $channels[user].individual eq 'y'}){/if}
{/if}
</td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_forums.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_forums.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>

