{* $Id$ *}

<h1><a href="tiki-view_forum.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;forumId={$forum_info.forumId}" class="pagetitle">{tr}Forum{/tr}: {$forum_info.name}</a>{if $prefs.feature_forum_topics_archiving eq 'y' && $thread_info.archived eq 'y'}<em>({tr}Archived{/tr})</em>{/if}</h1>

{if $unread > 0}
<a class='link' href='messu-mailbox.php'>{tr}You have{/tr} {$unread} {tr} unread private messages{/tr}<br /><br /></a>
{/if}

{if $was_queued eq 'y'}
{remarksbox type="warning" title="{tr}Information{/tr}" icon="information"}
{tr}Your message has been queued for approval, the message will be posted after a moderator approves it.{/tr}
{/remarksbox}
{/if}

{if $post_reported eq 'y'}<br />
	<div class="simplebox highlight reported_note">{icon _id=information style="vertical-align:middle;align=left"} {tr}The post has been reported and will be reviewed by a moderator.{/tr}</div>
	<br />
{/if}

{if $tiki_p_admin_forum eq "y"}
<a class="linkbut" title="{tr}Edit Forum{/tr}" href="tiki-admin_forums.php?forumId={$forumId}">{tr}Edit Forum{/tr}</a><br />
{/if}

<a class="link" href="tiki-forums.php">{tr}Forums{/tr}</a>-&gt;<a class="link" href="tiki-view_forum.php?forumId={$forumId}">{$forum_info.name}</a>{if $thread_info.topic.threadId}-&gt;<a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;forumId={$forumId}&amp;comments_parentId={$thread_info.topic.threadId}">{$thread_info.topic.title}</a>{/if}-&gt;<a class="link" href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}&amp;forumId={$forumId}&amp;comments_parentId={$smarty.request.comments_parentId}">{$thread_info.title}</a>

<div style="text-align: right; margin-bottom: 15px;">
	<span>
	{if ($prev_topic and $prev_topic ne $comments_parentId) or $next_topic}[{if $prev_topic and $prev_topic ne $comments_parentId}<a href="tiki-view_forum_thread.php?forumId={$forumId}&amp;comments_parentId={$prev_topic}&amp;topics_offset={$topics_prev_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}{$comments_per_page_param}{$thread_style_param}{$thread_sort_mode_param}{$comments_threshold_param}" class="link">{tr}prev topic{/tr}</a>{if $next_topic} | {/if}{/if}
	{if $next_topic}<a href="tiki-view_forum_thread.php?forumId={$forumId}&amp;comments_parentId={$next_topic}&amp;topics_offset={$topics_next_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}{$comments_per_page_param}{$thread_style_param}{$thread_sort_mode_param}{$comments_threshold_param}" class="link">{tr}next topic{/tr}</a>{/if}]{/if}
	</span>
	<span style="margin-left:10px;">
		{if $pdf_export eq 'y'}<a href="{$smarty.server.PHP_SELF}?{query display="pdf"}" title="{tr}PDF{/tr}">{icon _id='page_white_acrobat' alt="{tr}PDF{/tr}"}</a>{/if}
		<a href="{$smarty.server.PHP_SELF}?{query display="print"}" title="{tr}Print this page only{/tr}">{icon _id='printer' alt="{tr}Print this page only{/tr}"}</a>
		<a href="{$smarty.server.PHP_SELF}?{query display="print_all"}" title="{tr}Print all pages{/tr}">{icon _id='printer_add' alt="{tr}Print all pages{/tr}"}</a>
		{if $prefs.feature_forum_topics_archiving eq 'y' && $tiki_p_admin_forum eq 'y'}
			{if $thread_info.archived eq 'y'}
		<a href="{$smarty.server.PHP_SELF}?{query archive="n"}" title="{tr}Unarchive{/tr}">{icon _id='package_go' alt='{tr}Unarchive{/tr}'}</a>
			{else}
		<a href="{$smarty.server.PHP_SELF}?{query archive="y"}" title="{tr}Archive{/tr}">{icon _id='package' alt='{tr}Archive{/tr}'}</a>
			{/if}
		{/if}
	</span>
</div>


{if $openpost eq 'y'}
	{assign var="postclass" value="forumpostopen"}
{else}
	{assign var="postclass" value="forumpost"}
{/if}

<div class="top_post">
  {if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and isset($freetags.data[0])}
    {include file="freetag_list.tpl"}
  {/if}

  {include file="comment.tpl" first='y' comment=$thread_info thread_style='commentStyle_plain'}
</div>

{include file="comments.tpl"}

{**** Seems buggy
	{if $comments_threshold ne 0}
	<div style="font-size: smaller;">{$comments_below} {tr}Comments below your current threshold{/tr}</div>
	{/if}
****}

<table id="forumjumpto" style="clear:both;" ><tr>

	<td style="text-align:left;">
		<form id='time_control' method="get" action="tiki-view_forum_thread.php">

			<input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
			<input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
			<input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />
			<input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
			<input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}" />
			<input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset|escape}" />
			<input type="hidden" name="topics_find" value="{$smarty.request.topics_find|escape}" />
			<input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode|escape}" />    
			<input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold|escape}" />    
			<input type="hidden" name="forumId" value="{$forumId|escape}" />

			<small>{tr}Show posts{/tr}:</small>
			<select name="time_control" onchange="javascript:document.getElementById('time_control').submit();">
				<option value="" {if $smarty.request.time_control eq ''}selected="selected"{/if}>{tr}All posts{/tr}</option>
				<option value="3600" {if $smarty.request.time_control eq 3600}selected="selected"{/if}>{tr}Last hour{/tr}</option>
				<option value="86400" {if $smarty.request.time_control eq 86400}selected="selected"{/if}>{tr}Last 24 hours{/tr}</option>
				<option value="172800" {if $smarty.request.time_control eq 172800}selected="selected"{/if}>{tr}Last 48 hours{/tr}</option>
			</select>
		</form>
	</td>

	<td style="text-align:right;">
		{if $prefs.feature_forum_quickjump eq 'y'}
		<form id='quick' method="get" action="tiki-view_forum.php">
			<small>{tr}Jump to forum{/tr}:</small>
			<select name="forumId" onchange="javascript:document.getElementById('quick').submit();">
				{section name=ix loop=$all_forums}
				<option value="{$all_forums[ix].forumId|escape}" {if $all_forums[ix].forumId eq $forumId}selected="selected"{/if}>{$all_forums[ix].name}</option>
				{/section}
			</select>
		</form>
		{else}
		&nbsp;
		{/if}
		</td>
</tr></table>
