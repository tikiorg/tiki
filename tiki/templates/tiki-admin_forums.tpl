<h1><a class="pagetitle" href="tiki-admin_forums.php">{tr}Admin Forums{/tr}</a></h1>
<h2>{tr}Create/edit Forums{/tr}</h2>
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName=forum%20{$name}&amp;objectType=forum&amp;permType=forums&amp;objectId={$galleryId}">{tr}There are individual permissions set for this forum{/tr}</a>
{/if}
<form action="tiki-admin_forums.php" method="post">
<input type="hidden" name="forumId" value="{$forumId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$description}</textarea></td></tr>
<tr><td class="formcolor">{tr}Show description{/tr}:</td><td class="formcolor"><input type="checkbox" {if $show_description eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Prevent flooding{/tr}:</td><td class="formcolor"><input type="checkbox" name="controlFlood" {if $controlFlood eq 'y'}checked="checked"{/if} /> 
{tr}Minimum time between posts{/tr}: 
<select name="floodInterval">
<option value="15" {if $floodInterval eq 15}selected="selected"{/if}>15 {tr}secs{/tr}</option>
<option value="30" {if $floodInterval eq 30}selected="selected"{/if}>30 {tr}secs{/tr}</option>
<option value="60" {if $floodInterval eq 60}selected="selected"{/if}>1 {tr}min{/tr}</option>
<option value="120" {if $floodInterval eq 120}selected="selected"{/if}>2 {tr}mins{/tr}</option>
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
<tr><td class="formcolor">{tr}Moderator user{/tr}:</td><td class="formcolor">
<select name="moderator">
{section name=ix loop=$users}
<option value="{$users[ix].user}" {if $moderator eq $users[ix].user}selected="selected"{/if}>{$users[ix].user}</option>
{/section}
</select>
</td></tr>
<tr><td class="formcolor">{tr}Moderator group{/tr}:</td><td class="formcolor">
<select name="moderator_group">
<option value="" {if $moderator_group eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
{section name=ix loop=$groups}
<option value="{$groups[ix].groupName}" {if $moderator_group eq $groups[ix].groupName}selected="selected"{/if}>{$groups[ix].groupName}</option>
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
<option value="commentDate_asc" {if $topicOrdering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Date (asc){/tr}</option>
<option value="average_desc" {if $topicOrdering eq 'average_desc'}selected="selected"{/if}>{tr}Score (desc){/tr}</option>
<option value="title_desc" {if $topicOrdering eq 'title_desc'}selected="selected"{/if}>{tr}Title (desc){/tr}</option>
<option value="title_asc" {if $topicOrdering eq 'title_asc'}selected="selected"{/if}>{tr}Title (asc){/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor"><input type="checkbox" name="useMail" {if $useMail eq 'y'}checked="checked"{/if} /> {tr}Send this forums posts to this email{/tr}:</td><td class="formcolor"><input type="text" name="mail" value="{$mail}" /></td></tr>
<tr><td class="formcolor"><input type="checkbox" name="usePruneUnreplied" {if $usePruneUnreplied eq 'y'}checked="checked"{/if} /> {tr}Prune unreplied messages after{/tr}:</td><td class="formcolor">
<select name="pruneUnrepliedAge">
<option value="86400" {if $pruneUnrepliedAge eq 86400}selected="selected"{/if}>1 {tr}day{/tr}</option>
<option value="172800" {if $pruneUnrepliedAge eq 172800}selected="selected"{/if}>2 {tr}days{/tr}</option>
<option value="432000" {if $pruneUnrepliedAge eq 432000}selected="selected"{/if}>5 {tr}days{/tr}</option>
<option value="604800" {if $pruneUnrepliedAge eq 604800}selected="selected"{/if}>7 {tr}days{/tr}</option>
<option value="1296000" {if $pruneUnrepliedAge eq 1296000}selected="selected"{/if}>15 {tr}days{/tr}</option>
<option value="2592000" {if $pruneUnrepliedAge eq 2592000}selected="selected"{/if}>30 {tr}days{/tr}</option>
<option value="5184000" {if $pruneUnrepliedAge eq 5184000}selected="selected"{/if}>60 {tr}days{/tr}</option>
<option value="7776000" {if $pruneUnrepliedAge eq 7776000}selected="selected"{/if}>90 {tr}days{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor"><input type="checkbox" name="usePruneOld" {if $usePruneOld eq 'y'}checked="checked"{/if} /> {tr}Prune old messages after{/tr}:</td><td class="formcolor">
<select name="pruneMaxAge">
<option value="86400" {if $pruneMaxAge eq 86400}selected="selected"{/if}>1 {tr}day{/tr}</option>
<option value="172800" {if $pruneMaxAge eq 172800}selected="selected"{/if}>2 {tr}days{/tr}</option>
<option value="432000" {if $pruneMaxAge eq 432000}selected="selected"{/if}>5 {tr}days{/tr}</option>
<option value="604800" {if $pruneMaxAge eq 604800}selected="selected"{/if}>7 {tr}days{/tr}</option>
<option value="1296000" {if $pruneMaxAge eq 1296000}selected="selected"{/if}>15 {tr}days{/tr}</option>
<option value="2592000" {if $pruneMaxAge eq 2592000}selected="selected"{/if}>30 {tr}days{/tr}</option>
<option value="5184000" {if $pruneMaxAge eq 5184000}selected="selected"{/if}>60 {tr}days{/tr}</option>
<option value="7776000" {if $pruneMaxAge eq 7776000}selected="selected"{/if}>90 {tr}days{/tr}</option>
</select>
</td></tr>
<tr>
	<td class="formcolor">{tr}Topic list configuration{/tr}</td>
	<td class="formcolor">
		<table class="normal">
			<tr>
				<td>{tr}Replies{/tr}</td>
				<td>{tr}Reads{/tr}</td>
				<td>{tr}Points{/tr}</td>
				<td>{tr}Last post{/tr}</td>
				<td>{tr}author{/tr}</td>
			</tr>
			<tr>
				<td><input type="checkbox" name="topics_list_replies" {if $topics_list_replies eq 'y'}checked="checked"{/if} /></td>
				<td><input type="checkbox" name="topics_list_reads" {if $topics_list_reads eq 'y'}checked="checked"{/if} /></td>
				<td><input type="checkbox" name="topics_list_pts" {if $topics_list_pts eq 'y'}checked="checked"{/if} /></td>
				<td><input type="checkbox" name="topics_list_lastpost" {if $topics_list_lastpost eq 'y'}checked="checked"{/if} /></td>
				<td><input type="checkbox" name="topics_list_author" {if $topics_list_author eq 'y'}checked="checked"{/if} /></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Threads can be voted{/tr}</td>
	<td class="formcolor"><input type="checkbox" name="vote_threads" {if $vote_threads eq 'y'}checked="checked"{/if} /></td>
</tr>
<tr>
	<td class="formcolor">{tr}Forward messages to this forum to this email{/tr}</td>
	<td class="formcolor"><input type="text" name="outbound_address" value="{$outbound_address}" /></td>
</tr>
<tr>
	<td class="formcolor">{tr}Add messages from this email to the forum{/tr}</td>
	<td class="formcolor"><input type="text" name="inbound_address" value="{$inbound_address}" /></td>
</tr>
<tr>
	<td class="formcolor">{tr}Use topic smileys{/tr}</td>
	<td class="formcolor"><input type="checkbox" name="topic_smileys" {if $topic_smileys eq 'y'}checked="checked"{/if} /></td>
</tr>
<tr>
	<td class="formcolor">{tr}Show topic summary{/tr}</td>
	<td class="formcolor"><input type="checkbox" name="topic_summary" {if $topic_summary eq 'y'}checked="checked"{/if} /></td>
</tr>

<tr>
	<td class="formcolor">{tr}User information display{/tr}</td>
	<td class="formcolor">
	<table>
	<tr>
		<td>{tr}avatar{/tr}</td>
		<td>{tr}flag{/tr}</td>
		<td>{tr}posts{/tr}</td>
		<td>{tr}email{/tr}</td>
		<td>{tr}online{/tr}</td>
	</tr>
	<tr>
		<td><input type="checkbox" name="ui_avatar" {if $ui_avatar eq 'y'}checked="checked"{/if} /></td>
		<td><input type="checkbox" name="ui_flag" {if $ui_flag eq 'y'}checked="checked"{/if} /></td>
		<td><input type="checkbox" name="ui_posts" {if $ui_posts eq 'y'}checked="checked"{/if} /></td>
		<td><input type="checkbox" name="ui_email" {if $ui_email eq 'y'}checked="checked"{/if} /></td>
		<td><input type="checkbox" name="ui_online" {if $ui_online eq 'y'}checked="checked"{/if} /></td>
	</tr>		
	</table>
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Approval type{/tr}</td>
	<td class="formcolor">
		<select name="approval_type">
			<option value="all_posted" {if $approval_type eq 'all_posted'}selected="selected"{/if}>{tr}All posted{/tr}</option>
			<option value="queue_anon" {if $approval_type eq 'queue_anon'}selected="selected"{/if}>{tr}Queue anonymous posts{/tr}</option>
			<option value="queue_all" {if $approval_type eq 'queue_all'}selected="selected"{/if}>{tr}Queue all posts{/tr}</option>
		</select>
	</td>
</tr>

<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Forums{/tr}</h2>
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
<!--<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastPost_desc'}lastPost_asc{else}lastPost_desc{/if}">{tr}last post{/tr}</a></td>-->
<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}hits{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print="false"}
{section name=user loop=$channels}
<tr>
<td class="odd"><a class="link" href="tiki-view_forum.php?forumId={$channels[user].forumId}">{$channels[user].name}</a></td>
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].threads}</td>
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].comments}</td>
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].users}</td>
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].age}</td>
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].posts_per_day|string_format:"%.2f"}</td>
<!--<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].lastPost|tiki_short_datetime}</td>-->
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].hits}</td>
<td class="{cycle advance=false}">
{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
   <a class="link" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].forumId}"><img src='img/icons/trash.gif' alt='{tr}remove{/tr}' title='{tr}remove{/tr}' border='0' /></a>
   <a class="link" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;forumId={$channels[user].forumId}"><img src='img/icons/edit.gif' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' border='0' /></a>
   <a class="link" href="tiki-objectpermissions.php?objectName={tr}Forum{/tr}%20{$channels[user].name}&amp;objectType=forum&amp;permType=forums&amp;objectId={$channels[user].forumId}"><img src='img/icons/key.gif' border='0' alt='{tr}permissions{/tr}' title='{tr}permissions{/tr}' /></a>
{/if}
</td>
</tr>
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

