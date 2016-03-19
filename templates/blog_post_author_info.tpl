{* $Id$ *}
<div class="author_info">
	{if $blog_data.show_avatar eq 'y'}
		{$post_info.avatar}
	{else}
		{icon name="user" iclass="tips" ititle=":{tr}Published By{/tr}"}
	{/if}
	{if $blog_data.use_author eq 'y'}
		{$post_info.user|userlink}
	{/if}
	{if $blog_data.add_date eq 'y'}
		{icon name="calendar" iclass="tips" ititle=":{tr}Publish Date{/tr}"} {$post_info.created|tiki_short_date}
	{/if}
</div>
