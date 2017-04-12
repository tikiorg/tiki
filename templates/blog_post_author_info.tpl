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
		<span style="font-size: 80%">{icon name="clock-o" iclass="tips" ititle=":{tr}Publish Date{/tr}"}</span> {$post_info.created|tiki_long_date}
	{/if}
</div>
