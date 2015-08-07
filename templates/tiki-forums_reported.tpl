{title help="Forums" admpage="forums"}{$forum_info.name}{/title}
<h4>
	{tr}Reported messages{/tr}
	<span class="badge">{$cant}</span>
	{icon name="refresh" href="tiki-forums_reported.php?forumId=$forumId" class="btn btn-link tips" title=":{tr}Refresh list{/tr}"}
</h4>

{* FILTERING FORM *}
{if $items or ($find ne '')}
	<form action="tiki-forums_reported.php" method="post" class="form">
		<div class="form-group">
			<input type="hidden" name="forumId" value="{$forumId|escape}">
			<input type="hidden" name="offset" value="{$offset|escape}">
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
			<div class="input-group">
				<input type="text" class="form-control" name="find" value="{$find|escape}" placeholder="{tr}Find{/tr}...">
				<div class="input-group-btn">
					<button type="submit" class="btn btn-default" name="filter">{tr}Filter{/tr}</button>
				</div>
			</div>
		</div>
	</form>
{/if}
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="tiki-forums_reported.php" method="post">
	<input type="hidden" name="forumId" value="{$forumId|escape}">
	<input type="hidden" name="offset" value="{$offset|escape}">
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
	<input type="hidden" name="find" value="{$find|escape}">
	<table class="table table-hover">
		<tr>
			{if $items}
				<th></th>
			{/if}
			<th>{tr}Message{/tr}</th>
			<th>{tr}Reported by{/tr}</th>
		</tr>

		{section name=ix loop=$items}
			<tr>
				<td class="checkbox-cell">
					<input type="checkbox" name="msg[{$items[ix].threadId}]">
				</td>
				<td class="text">
					<a class="link" href="tiki-view_forum_thread.php?topics_offset=0&amp;topics_sort_mode=commentDate_desc&amp;topics_threshold=0&amp;topics_find=&amp;forumId={$items[ix].forumId}&amp;comments_parentId={$items[ix].parentId}">{$items[ix].title|escape}</a>
				</td>
				<td style="text-align:left;">
					{$items[ix].user|username}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=2}
		{/section}
	</table>
	{if $items}
		{tr}Perfom action with checked:{/tr} <input type="submit" class="btn btn-warning btn-sm" name="del" value=" {tr}Un-report{/tr} ">
	{/if}

</form>
{* END OF LISTING *}

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
