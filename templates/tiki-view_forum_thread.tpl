{* $Id$ *}
{$forum_info.name|addonnavbar:'forum'}
{block name=title}
	{title help="forums" admpage="forums"}
		{$forum_info.name|addongroupname}
		{if $prefs.feature_forum_topics_archiving eq 'y' && $thread_info.archived eq 'y'}({tr}Archived{/tr}){/if}
	{/title}
{/block}

<div class="t_navbar btn-group margin-bottom-md">
	{if $tiki_p_admin_forum eq "y"}
		{button href="tiki-admin_forums.php?forumId=$forumId" class="btn btn-default" _text="{tr}Edit Forum{/tr}"}
	{/if}
	{if $tiki_p_admin_forum eq 'y' or !isset($all_forums) or $all_forums|@count > 1}
		{button href="tiki-forums.php" class="btn btn-default" _text="{tr}Forum List{/tr}"}
	{/if}
	{button href="tiki-view_forum.php?forumId=$forumId" class="btn btn-default" _text="{tr}Topic List{/tr}"}
</div>
{include file="utilities/feedback.tpl"}
{if $post_reported eq 'y'}
	{remarksbox type=warning title="{tr}The post has been reported and will be reviewed by a moderator.{/tr}"}{/remarksbox}
{/if}
<br>
<div id="thread-breadcrumb" class="breadcrumb">
	<a class="link" href="{if $prefs.feature_sefurl eq 'y'}forums{else}tiki-forums.php{/if}">
		{tr}Forums{/tr}
	</a>
	{$prefs.site_crumb_seper}
	<a class="link" href="{$forumId|sefurl:'forum'}">
		{$forum_info.name|addongroupname|escape}
	</a>{if isset($thread_info.topic.threadId) and $thread_info.topic.threadId}
		{$prefs.site_crumb_seper}
		<a class="link" href="{$thread_info.topic.threadId|sefurl:'forumthread'}{if $smarty.request.topics_offset}&amp;topics_offset={$smarty.request.topics_offset}{/if}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}">
			{$thread_info.topic.title}
		</a>
	{/if}
	{$prefs.site_crumb_seper}
	{$thread_info.title|escape}
</div>

{block name=thread_actions}
<div class="text-right margin-bottom-md">
	{if empty($thread_info.topic.threadId)}
		<span>
			{if ($prev_topic and $prev_topic ne $comments_parentId) or $next_topic}[ {if $prev_topic and $prev_topic ne $comments_parentId}<a href="tiki-view_forum_thread.php?comments_parentId={$prev_topic}&amp;topics_offset={$topics_prev_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}{$comments_per_page_param}{$thread_style_param}{$thread_sort_mode_param}{$comments_threshold_param}" class="link">{tr}prev topic{/tr}</a>{if $next_topic} | {/if}{/if}
			{if $next_topic}<a href="tiki-view_forum_thread.php?comments_parentId={$next_topic}&amp;topics_offset={$topics_next_offset}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}{$comments_per_page_param}{$thread_style_param}{$thread_sort_mode_param}{$comments_threshold_param}" class="link">{tr}next topic{/tr}</a>{/if} ]{/if}
		</span>
	{else}
		<span>
			{tr}You are viewing a reply to{/tr} <a class="link" href="tiki-view_forum_thread.php?comments_parentId={$thread_info.topic.threadId}{if $smarty.request.topics_offset}&amp;topics_offset={$smarty.request.topics_offset}{/if}{$topics_sort_mode_param}{$topics_threshold_param}{$topics_find_param}">{$thread_info.topic.title}</a>
		</span>
	{/if}
	        &nbsp;
	{if $prefs.javascript_enabled != 'y'}
		{$js = 'n'}
	{else}
		{$js = 'y'}
	{/if}
		<div class="btn-group">
			{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
			<a class="btn btn-link" data-toggle="dropdown" data-hover="dropdown" href="#">
				{icon name='menu-extra'}
			</a>
			<ul class="dropdown-menu dropdown-menu-right">
				<li class="dropdown-title">
					{tr}Thread actions{/tr}
				</li>
				<li class="divider"></li>
				<li>
					{if $pdf_export eq 'y'}
						<a href="{$smarty.server.PHP_SELF}?{query display='pdf'}">
							{icon name="pdf"} {tr}PDF{/tr}
						</a>
					{/if}
				</li>
				<li>
					<a href="{$smarty.server.PHP_SELF}?{query display='print'}">
						{icon name="print"} {tr}Print this page{/tr}
					</a>
				</li>
				<li>
					<a href="{$smarty.server.PHP_SELF}?{query display='print_all'}">
						{icon name="print"} {tr}Print all pages{/tr}
					</a>
				</li>
				{if $prefs.feature_forum_topics_archiving eq 'y' && $tiki_p_admin_forum eq 'y'}
					<li>
						{if $thread_info.archived eq 'y'}
							<a href="{$smarty.server.PHP_SELF}?{query archive="n"}">
								{icon name="file-archive-open"} {tr}Unarchive{/tr}
							</a>
						{else}
							<a href="{$smarty.server.PHP_SELF}?{query archive='y'}">
								{icon name="file-archive"} {tr}Archive{/tr}
							</a>
						{/if}
					</li>
				{/if}
				{if isset($tiki_p_forum_lock) and $tiki_p_forum_lock eq 'y'}
					<li>
						{if $thread_info.locked eq 'y'}
							<a href="{query _type='relative' lock='n'}">
								{icon name="unlock"} {tr}Unlock{/tr}
							</a>
						{else}
							<a href="{query _type='relative' lock='y'}">
								{icon name="lock"} {tr}Lock{/tr}
							</a>
						{/if}
					</li>
				{/if}
			</ul>
			{if $js == 'n'}</li></ul>{/if}
		</div>

</div>
{/block}


{if $openpost eq 'y'}
	{assign var="postclass" value="forumpostopen"}
{else}
	{assign var="postclass" value="forumpost"}
{/if}

<article class="top_post">
	{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and $prefs.freetags_show_middle eq 'y'
		and !$thread_info.topic.threadId}
		{include file='freetag_list.tpl'}
		<div class="text-right margin-bottom-sm">
			{wikiplugin _name="addfreetag" object="forum post:$comments_parentId"}{/wikiplugin}
		</div>
	{/if}

	{include file='comment.tpl' first='y' comment=$thread_info thread_style='commentStyle_plain'}
</article>

{include file='comments.tpl'}

<div class="form-group">
	<form class="form-horizontal" role="form" id='time_control' method="get" action="tiki-view_forum_thread.php">
		<input type="hidden" name="comments_offset" value="0"><!--Reset offset to 0 when applying a new filter -->
		<input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}">
		<input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}">
		<input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
		<input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}">
		<input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset|escape}">
		<input type="hidden" name="topics_find" value="{$smarty.request.topics_find|escape}">
		<input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode|escape}">
		<input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold|escape}">
		<input type="hidden" name="forumId" value="{$forumId|escape}">

		<label class="col-sm-2 control-label" for="userfile1">{tr}Show posts:{/tr}</label>
		<div class="col-sm-3">
			<select class="form-control" name="time_control" onchange="javascript:document.getElementById('time_control').submit();">
				<option value="" {if empty($smarty.request.time_control)}selected="selected"{/if}>
					{tr}All posts{/tr}
				</option>
				<option value="3600" {if isset($smarty.request.time_control) and $smarty.request.time_control eq 3600}selected="selected"{/if}>
					{tr}Last hour{/tr}
				</option>
				<option value="86400" {if isset($smarty.request.time_control) and $smarty.request.time_control eq 86400}selected="selected"{/if}>
					{tr}Last 24 hours{/tr}
				</option>
				<option value="172800" {if isset($smarty.request.time_control) and $smarty.request.time_control eq 172800}selected="selected"{/if}>
					{tr}Last 48 hours{/tr}
				</option>
			</select>
		</div>
	</form>
</div>

<div class="form-group pull-right">
	{if $prefs.feature_forum_quickjump eq 'y' && $all_forums|@count > 1}
		<form class="form-horizontal" role="form" id='quick' method="get" action="tiki-view_forum.php">
			<label class="col-sm-6 control-label" for="forumId">{tr}Jump to forum:{/tr}</label>
			<div class="col-sm-6">
				<select id="forumId" class="form-control" name="forumId" onchange="javascript:document.getElementById('quick').submit();">
					{section name=ix loop=$all_forums}
						<option value="{$all_forums[ix].forumId|escape}" {if $all_forums[ix].forumId eq $forumId}selected="selected"{/if}>
							{$all_forums[ix].name}
						</option>
					{/section}
				</select>
			</div>
		</form>
	{else}
		&nbsp;
	{/if}
</div>

{if isset($view_atts) and $view_atts eq 'y'}
	<h2 id="attachments">{tr}Attachments{/tr}</h2>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<tr>
				<th>{tr}Type{/tr}</th>
				<th>{tr}Filename{/tr}</th>
				<th>{tr}Size{/tr}</th>
				<th>{tr}Created{/tr}</th>
				<th>{tr}Action{/tr}</th>
			</tr>

			{foreach from=$atts.data item=att}
				<tr>
					<td class="icon">
						{$att.filename|iconify}
					</td>
					<td class="text">
						<a href="tiki-download_forum_attachment.php?attId={$att.attId}" title="{tr}Download{/tr}">
							{$att.filename|escape}
						</a>
					</td>
					<td class="integer">
						{$att.filesize|kbsize}
					</td>
					<td class="date">
						{$att.created|tiki_short_datetime}
					</td>
					<td class="action">
						<a href="tiki-download_forum_attachment.php?attId={$att.attId}" class="tips" title="{$att.filename|escape}:{tr}Download{/tr}">
							{icon name='floppy'}
						</a>
					</td>
				</tr>
			{/foreach}
		</table>
	</div>
	{pagination_links cant=$atts.cant offset=$atts.offset offset_arg='fa_offset' step=$atts.maxRecords _anchor='attachments'}{/pagination_links}
{/if}