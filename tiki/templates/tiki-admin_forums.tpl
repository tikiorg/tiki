{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_forums.tpl,v 1.73.2.4 2008-01-30 15:33:48 nyloth Exp $ *}
<h1><a class="pagetitle" href="tiki-admin_forums.php">{tr}Admin Forums{/tr}</a>
 
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Forums" target="tikihelp" class="tikihelp" title="{tr}Forums{/tr}">{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_forums.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Admin Forums Template{/tr}">{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>

<div class="navbar">
{if $forumId > 0 or $dup_mode eq 'y'}
<a href="tiki-admin_forums.php" class="linkbut">{tr}Create new forum{/tr}</a>
{/if}
{if $dup_mode ne 'y'}
<a class="linkbut" href="tiki-admin_forums.php?dup_mode=y">{tr}Duplicate forum{/tr}</a>
{/if}
{if $forumId > 0}
<a href="tiki-view_forum.php?forumId={$forumId}" class="linkbut">{tr}View this forum{/tr}</a>
{/if}
<a href="tiki-forum_import.php" class="linkbut">{tr}Import forums{/tr}</a>
<a href="#editforums" class="linkbut">{tr}List forums{/tr}</a>
{if $tiki_p_admin eq 'y'}
<a title="{tr}Configure/Options{/tr}" href="tiki-admin.php?page=forums">{icon _id='wrench' alt='{tr}Configure/Options{/tr}'}</a>
{/if}
</div>

{if $dup_mode != 'y'}
{if $forumId > 0}
<h2>{tr}Edit this Forum:{/tr} {$name}</h2>
{else}
<h2>{tr}Create New Forum{/tr}</h2>
{/if}
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType=forum&amp;permType=forums&amp;objectId={$forumId}">{tr}There are individual permissions set for this forum{/tr}</a>
{/if}
<form action="tiki-admin_forums.php" method="post">
<input type="hidden" name="forumId" value="{$forumId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" size="50" value="{$name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$description|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Show description{/tr}:</td><td class="formcolor"><input type="checkbox" name="show_description" {if $show_description eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Prevent flooding{/tr}:</td><td class="formcolor"><input type="checkbox" name="controlFlood" {if $controlFlood eq 'y'}checked="checked"{/if} /> 
{tr}Minimum time between posts{/tr}: 
<select name="floodInterval">
<option value="15" {if $floodInterval eq 15}selected="selected"{/if}>15 {tr}secs{/tr}</option>
<option value="30" {if $floodInterval eq 30}selected="selected"{/if}>30 {tr}secs{/tr}</option>
<option value="60" {if $floodInterval eq 60}selected="selected"{/if}>1 {tr}min{/tr}</option>
<option value="120" {if $floodInterval eq 120}selected="selected"{/if}>2 {tr}mins{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Section{/tr}:</td><td class="formcolor">
<select name="section">
<option value="" {if $forumSection eq ""}selected="selected"{/if}>{tr}None{/tr}</option>
<option value="__new__"}>{tr}Create new{/tr}</option>
{section name=ix loop=$sections}
<option  {if $forumSection eq $sections[ix]}selected="selected"{/if} value="{$sections[ix]|escape}">{$sections[ix]}</option>
{/section}
</select>
<input name="new_section" type="text" />
</td></tr>
<tr><td class="formcolor">{tr}Moderator user{/tr}:</td><td class="formcolor">
<select name="moderator">
<option value="" {if $moderator eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
{foreach key=id item=one from=$users}
<option value="{$one|escape}" {if $moderator eq $one}selected="selected"{/if}>{$one|username}</option>
{/foreach}
</select>
</td></tr>
<tr><td class="formcolor">{tr}Moderator group{/tr}:</td><td class="formcolor">
<select name="moderator_group">
<option value="" {if $moderator_group eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
{section name=ix loop=$groups}
<option value="{$groups[ix]|escape}" {if $moderator_group eq $groups[ix]}selected="selected"{/if}>{$groups[ix]}</option>
{/section}
</select>
</td></tr>
<tr><td class="formcolor">{tr}Password protected{/tr}</td><td class="formcolor">
<select name="forum_use_password">
<option value="n" {if $forum_use_password eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
<option value="t" {if $forum_use_password eq 't'}selected="selected"{/if}>{tr}Topics only{/tr}</option>
<option value="a" {if $forum_use_password eq 'a'}selected="selected"{/if}>{tr}All posts{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Forum password{/tr}</td><td class="formcolor"><input type="text" name="forum_password" value="{$forum_password|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Only allow replies to the first message (flat forum){/tr}:</td><td class="formcolor"><input type="checkbox" name="is_flat" {if $is_flat eq 'y'}checked="checked"{/if} /></td></tr>

{include file=categorize.tpl}

<tr><td class="formcolor"><input type="checkbox" name="useMail" {if $useMail eq 'y'}checked="checked"{/if} /> {tr}Send this forums posts to this email{/tr}:</td><td class="formcolor"><input type="text" name="mail" value="{$mail|escape}" /></td></tr>
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
				<td class="formcolor">{tr}Replies{/tr}</td>
				<td class="formcolor">{tr}Reads{/tr}</td>
				<td class="formcolor">{tr}Points{/tr}</td>
				<td class="formcolor">{tr}Last post{/tr}</td>
				<td class="formcolor">{tr}Author{/tr}</td>
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
	<td class="formcolor">{tr}Posts can be rated{/tr}</td>
	<td class="formcolor"><input type="checkbox" name="vote_threads" {if $vote_threads eq 'y'}checked="checked"{/if} /></td>
</tr>
<tr>
	<td class="formcolor">{tr}Display last post titles{/tr}</td>
	<td class="formcolor">
		<select name="forum_last_n">
			<option value="0" {if $forum_last_n eq 0}selected="selected"{/if}>{tr}no display{/tr}</option>
			<option value="5" {if $forum_last_n eq 5}selected="selected"{/if}>5</option>
			<option value="10" {if $forum_last_n eq 10}selected="selected"{/if}>10</option>
			<option value="20" {if $forum_last_n eq 20}selected="selected"{/if}>20</option>
		</select>
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Forward messages to this forum to this e-mail address, in a format that can be used for sending back to the inbound forum e-mail address{/tr}</td>
	<td class="formcolor"><input type="text" name="outbound_address" size="50" value="{$outbound_address|escape}" /></td>
</tr>
<tr>
	<td class="formcolor">{tr}Send mails even when the post is generated by inbound mail{/tr}</td>
	<td class="formcolor"><input type="checkbox" name="outbound_mails_for_inbound_mails" {if $outbound_mails_for_inbound_mails eq 'y'}checked="checked"{/if} /></td>
</tr>
<tr>
	<td class="formcolor">{tr}Append a reply link to outbound mails{/tr}</td>
	<td class="formcolor"><input type="checkbox" name="outbound_mails_reply_link" {if $outbound_mails_reply_link eq 'y'}checked="checked"{/if} /></td>
</tr>
<tr>
	<td class="formcolor">{tr}Originating e-mail address for mails from this forum{/tr}</td>
	<td class="formcolor"><input type="text" name="outbound_from" size="50" value="{$outbound_from|escape}" /></td>
</tr>
<tr>
	<td class="formcolor">{tr}Add messages from this email to the forum{/tr}</td>
	<td class="formcolor">
		<table>
		<tr>
			<td class="formcolor">{tr}POP3 server{/tr}:</td>
			<td><input type="text" name="inbound_pop_server" value="{$inbound_pop_server|escape}" /></td>
		</tr>
		<tr>
			<td class="formcolor">{tr}User{/tr}:</td>
			<td><input type="text" name="inbound_pop_user" value="{$inbound_pop_user|escape}" /></td>
		</tr>
		<tr>
			<td class="formcolor">{tr}Password{/tr}:</td>
			<td><input type="password" name="inbound_pop_password" value="{$inbound_pop_password|escape}" /></td>
		</tr>
		</table>
	</td>
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
	<table >
	<tr>
		<td class="formcolor">{tr}Avatar{/tr}</td>
		<td class="formcolor">{tr}Flag{/tr}</td>
		<td class="formcolor">{tr}Posts{/tr}</td>
		<td class="formcolor">{tr}User Level{/tr}</td>
		<td class="formcolor">{tr}eMail{/tr}</td>
		<td class="formcolor">{tr}Online{/tr}</td>
	</tr>
	<tr>
		<td><input type="checkbox" name="ui_avatar" {if $ui_avatar eq 'y'}checked="checked"{/if} /></td>
		<td><input type="checkbox" name="ui_flag" {if $ui_flag eq 'y'}checked="checked"{/if} /></td>
		<td><input type="checkbox" name="ui_posts" {if $ui_posts eq 'y'}checked="checked"{/if} /></td>
		<td><input type="checkbox" name="ui_level" {if $ui_level eq 'y'}checked="checked"{/if} /></td>
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
<tr>
	<td class="formcolor">{tr}Attachments{/tr}</td>
	<td class="formcolor">
		<select name="att">
			<option value="att_no" {if $att eq 'att_no'}selected="selected"{/if}>{tr}No attachments{/tr}</option>
			<option value="att_all" {if $att eq 'att_all'}selected="selected"{/if}>{tr}Everybody can attach{/tr}</option>
			<option value="att_perm" {if $att eq 'att_perm'}selected="selected"{/if}>{tr}Only users with attach permission{/tr}</option>
			<option value="att_admin" {if $att eq 'att_admin'}selected="selected"{/if}>{tr}Moderators and admin can attach{/tr}</option>
		</select>
		<br />
		{tr}Store attachments in:{/tr}
		<table>
			<tr>
				<td class="formcolor">
				<input type="radio" name="att_store" value="db" {if $att_store eq 'db'}checked="checked"{/if} /> {tr}Database{/tr}
				</td>
			</tr>
			<tr>
				<td class="formcolor">
				<input type="radio" name="att_store" value="dir" {if $att_store eq 'dir'}checked="checked"{/if} /> {tr}Path{/tr}: <input type="text" name="att_store_dir" value="{$att_store_dir|escape}" size="14" />
				</td>
			</tr>
			<tr>
				<td class="formcolor">
				{tr}Max attachment size (bytes){/tr}: <input type="text" name="att_max_size" value="{$att_max_size|escape}" /><br /><i>{tr}Max:{/tr} {$maxAttachSize}</i>
				</td>
			</tr>
		</table>
	</td>
</tr>

<tr class="formcolor">
<td>{tr}Set topics preferences{/tr}</td>
<td>
<a class="link" href="javascript:flip('topicconfig');flip('topicprefshow','inline');flip('topicprefhide','inline');">
<span id="topicprefshow" style="display:{if isset($smarty.session.tiki_cookie_jar.show_topicconfig) and $smarty.session.tiki_cookie_jar.show_topicconfig eq 'y'}none{else}inline{/if};">{tr}Show topics preferences{/tr}</span>
<span id="topicprefhide" style="display:{if isset($smarty.session.tiki_cookie_jar.show_topicconfig) and $smarty.session.tiki_cookie_jar.show_topicconfig eq 'y'}inline{else}none{/if};">{tr}hide topics preferences{/tr}</span>
</a>
</td></tr>

<tr><td colspan="2"><table id="topicconfig" style="display:{if isset($smarty.session.tiki_cookie_jar.show_topicconfig) and $smarty.session.tiki_cookie_jar.show_topicconfig eq 'y'}block{else}none{/if}; border: 0;">

<tr><td>{tr}Default ordering for topics{/tr}:</td>
<td>
<select name="topicOrdering">
<option value="commentDate_desc" {if $topicOrdering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Date (desc){/tr}</option>
<option value="commentDate_asc" {if $topicOrdering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Date (asc){/tr}</option>
<option value="average_desc" {if $topicOrdering eq 'average_desc'}selected="selected"{/if}>{tr}Score (desc){/tr}</option>
<option value="replies_desc" {if $topicOrdering eq 'replies_desc'}selected="selected"{/if}>{tr}Replies (desc){/tr}</option>
<option value="hits_desc" {if $topicOrdering eq 'hits_desc'}selected="selected"{/if}>{tr}Reads (desc){/tr}</option>
<option value="lastPost_desc" {if $topicOrdering eq 'lastPost_desc'}selected="selected"{/if}>{tr}Last post (desc){/tr}</option>
<option value="title_desc" {if $topicOrdering eq 'title_desc'}selected="selected"{/if}>{tr}Title (desc){/tr}</option>
<option value="title_asc" {if $topicOrdering eq 'title_asc'}selected="selected"{/if}>{tr}Title (asc){/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Topics per page{/tr}:</td><td class="formcolor"><input type="text" name="topicsPerPage" value="{$topicsPerPage|escape}" /></td></tr>

</table></td></tr>

{if $prefs.forum_thread_defaults_by_forum eq 'y'}
<tr class="formcolor"><td>{tr}Set thread preferences{/tr}</td><td>
<a class="link" href="javascript:flip('threadconfig');flip('threadprefshow','inline');flip('threadprefhide','inline');">
<span id="threadprefshow" style="display:{if isset($smarty.session.tiki_cookie_jar.show_threadconfig) and $smarty.session.tiki_cookie_jar.show_threadconfig eq 'y'}none{else}inline{/if};">{tr}show threads preferences{/tr}</span>
<span id="threadprefhide" style="display:{if isset($smarty.session.tiki_cookie_jar.show_threadconfig) and $smarty.session.tiki_cookie_jar.show_threadconfig eq 'y'}inline{else}none{/if};">{tr}hide threads preferences{/tr}</span>
</a>
</td></tr>

<tr><td colspan="2"><table id="threadconfig" style="display:{if isset($smarty.session.tiki_cookie_jar.show_threadconfig) and $smarty.session.tiki_cookie_jar.show_threadconfig eq 'y'}block{else}none{/if}; border: 0;">

<tr><td>{tr}Default ordering for threads{/tr}:</td>
<td>
<select name="threadOrdering">
<option value="" {if $threadOrdering eq ''}selected="selected"{/if}>{tr}default{/tr}</option>
<option value="commentDate_desc" {if $threadOrdering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
<option value="commentDate_asc" {if $threadOrdering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
<option value="points_desc" {if $threadOrdering eq 'points_desc' or $threadOrdering eq 'average_desc'}selected="selected"{/if}>{tr}Score{/tr}</option>
<option value="title_desc" {if $threadOrdering eq 'title_desc'}selected="selected"{/if}>{tr}Title (desc){/tr}</option>
<option value="title_asc" {if $threadOrdering eq 'title_asc'}selected="selected"{/if}>{tr}Title (asc){/tr}</option>
</select>
</td></tr>
<tr><td>{tr}Default style for threads{/tr}:</td>
<td>
<select name="threadStyle">
<option value="" {if $threadStyle eq ''}selected="selected"{/if}>{tr}default{/tr}</option>
<option value="commentStyle_plain" {if $threadStyle eq 'commentStyle_plain'}selected="selected"{/if}>{tr}Plain{/tr}</option>
<option value="commentStyle_threaded" {if $threadStyle eq 'commentStyle_threaded'}selected="selected"{/if}>{tr}Threaded{/tr}</option>
<option value="commentStyle_headers" {if $threadStyle eq 'commentStyle_headers'}selected="selected"{/if}>{tr}Headers Only{/tr}</option>
</select>
</td></tr>
<tr><td>{tr}Default number of comments per page{/tr}:</td>
<td>
<select name="commentsPerPage">
<option value="" {if $commentsPerPage eq '' }selected="selected"{/if}>{tr}default{/tr}</option>
<option value="10" {if $commentsPerPage eq 10 }selected="selected"{/if}>10</option>
<option value="20" {if $commentsPerPage eq 20 }selected="selected"{/if}>20</option>
<option value="30" {if $commentsPerPage eq 30 }selected="selected"{/if}>30</option>
<option value="999999" {if $commentsPerPage eq 999999 }selected="selected"{/if}>{tr}All{/tr}</option>
</select>
</td></tr>

</table></td></tr>
{/if}

<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

{else}{*duplicate*}
<h2>{tr}Duplicate Forum{/tr}</h2>
<form action="tiki-admin_forums.php" method="post">
<table class="normal">
<tr class="formcolor"><td>{tr}Name{/tr}</td><td><input type="text" size="50" name="name" value="{$name|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Description{/tr}</td><td><textarea name="description" rows="4" cols="50">{$description|escape}</textarea></td></tr>
<tr class="formcolor"><td>{tr}Forum{/tr}</td>
<td>
<select name="forumId">
{section name=ix loop=$allForums}
<option value="{$allForums[ix].forumId}">{$allForums[ix].name}</option>
{/section}
</select>
</td>
</tr>
<tr class="formcolor"><td>{tr}Duplicate categories{/tr}</td><td><input type="checkbox" name="dupCateg" /></td></tr>
<tr class="formcolor"><td>{tr}Duplicate perms{/tr}</td><td><input type="checkbox" name="dupPerms" /></td></tr>
<tr class="formcolor"><td></td><td><input type="submit" name="duplicate" value="{tr}Duplicate{/tr}" /></td></tr>
</table>
</form>
{/if}

<a name="editforums" id="editforums"></a>
<h2>{tr}List Forums{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_forums.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<form>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}#editforums">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'threads_desc'}threads_asc{else}threads_desc{/if}#editforums">{tr}Topics{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'comments_desc'}comments_asc{else}comments_desc{/if}#editforums">{tr}Coms{/tr}</a></td>
<td class="heading">{tr}Users{/tr}</td>
<td class="heading">{tr}Age{/tr}</td>
<td class="heading">{tr}PPD{/tr}</td>
<!--<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastPost_desc'}lastPost_asc{else}lastPost_desc{/if}">{tr}Last Post{/tr}</a></td>-->
<td class="heading"><a class="tableheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}"><a class="link" href="tiki-view_forum.php?forumId={$channels[user].forumId}" title="{tr}View{/tr}">{$channels[user].name}</a></td>
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].threads}</td>
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].comments}</td>
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].users}</td>
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].age}</td>
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].posts_per_day|string_format:"%.2f"}</td>
<!--<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].lastPost|tiki_short_datetime}</td>-->
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].hits}</td>
<td class="{cycle}">
<a class="link" href="tiki-view_forum.php?forumId={$channels[user].forumId}" title="{tr}View{/tr}">{icon _id='table' </a alt="{tr}View{/tr}"}

{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
  <a class="link" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;forumId={$channels[user].forumId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>

  {if $channels[user].individual eq 'y'}
    <a class="link" href="tiki-objectpermissions.php?objectName=Forum+{$channels[user].name|escape}&amp;objectType=forum&amp;permType=forums&amp;objectId={$channels[user].forumId}" title="{tr}Active Perms{/tr}">{icon _id='key_active' alt='{tr}Active Perms{/tr}'}</a>
  {else}
    <a class="link" href="tiki-objectpermissions.php?objectName=Forum+{$channels[user].name|escape}&amp;objectType=forum&amp;permType=forums&amp;objectId={$channels[user].forumId}" title="{tr}Perms{/tr}">{icon _id='key' alt="{tr}Perms{/tr}"}</a>
   {/if}
  <a class="link" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].forumId}" title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
{/if}
<input type="checkbox" name="checked[]" value="{$channels[user].forumId|escape}" {if $smarty.request.checked and in_array($channels[user].forumId,$smarty.request.checked)}checked="checked"{/if} />
</td>
</tr>
{/section}
</table>
<div style="text-align:right;">
<script type="text/javascript"> /* <![CDATA[ */
	document.write('<label for="clickall">{tr}select all{/tr}</label> ');
	document.write('<input type="checkbox" id="clickall" onclick="switchCheckboxes(this.form,\'checked[]\',this.checked)"/>');
/* ]]> */</script>
<br />
{tr}Perform action with checked:{/tr} 
<select name="batchaction" onchange="show('groups');">
	<option value="">{tr}with checked{/tr}</option>
	{if $tiki_p_admin_forum eq 'y'}<option value="delsel_x">{tr}Delete{/tr}</option>{/if}
</select>
<input type="submit" name="batchaction" value="{tr}OK{/tr}" />
</div>
</form>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_forums.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}#editforums">{tr}Prev{/tr}</a>]
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
[<a class="prevnext" href="tiki-admin_forums.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}#editforums">{tr}Next{/tr}</a>]
{/if}
</div>
</div>
