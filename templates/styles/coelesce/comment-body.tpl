{* $Id$ *}
{*<div class="clearfix content">*}
{if $thread_style != 'commentStyle_headers'}
{*<div class="clearfix postbody-content">*}
	{$comment.parsed}
	{* <span class="signature"><!-- SIGNATURE --></span> *}
{*</div>*}
{/if}

{*</div>*}

{if $thread_style != 'commentStyle_headers' and count($comment.attachments) > 0}
<div class="attachments">
	{section name=ix loop=$comment.attachments}
	<a class="link" href="tiki-download_forum_attachment.php?attId={$comment.attachments[ix].attId}">
	{icon _id='attach' alt="{tr}Attachment{/tr}"}
	{$comment.attachments[ix].filename} ({$comment.attachments[ix].filesize|kbsize})</a>
	{if $tiki_p_admin_forum eq 'y'}
	<a class="link"
		{if $first eq 'y'}
		href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}{$topics_sort_mode_param}{$topics_find_param}{$topics_threshold_param}&amp;comments_offset={$smarty.request.topics_offset}{$thread_sort_mode_param}&amp;comments_threshold={$smarty.request.topics_threshold}{$comments_find_param}&amp;forumId={$forum_info.forumId}{$comments_per_page_param}&amp;comments_parentId={$comments_parentId}&amp;remove_attachment={$comment.attachments[ix].attId}"
		{else}
		href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_find={$smarty.request.topics_find}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;comments_offset={$smarty.request.topics_offset}&amp;thread_sort_mode={$smarty.request.topics_sort_mode}&amp;comments_threshold={$smarty.request.topics_threshold}&amp;comments_find={$smarty.request.topics_find}&amp;forumId={$forum_info.forumId}&amp;comments_per_page={$comments_per_page}&amp;comments_parentId={$comments_parentId}&amp;remove_attachment={$comment.attachments[ix].attId}"
		{/if}
	>{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
	{/if}
	<br />
	{/section}
</div>
{/if}
