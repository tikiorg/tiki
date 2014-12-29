{* $Id$ *}
<div style="margin:10px 20px 0px 20px">

	{title}{tr}Forum:{/tr} {$forum_info.name}{/title}

	<div class="top_post">
		{include file='comment.tpl' first='y' comment=$thread_info thread_style='commentStyle_plain'}
	</div>
	{include file='comments.tpl'}
	<br>
	<footer class="editdate">
		{tr}The original document is available at{/tr} <a href="{$base_url}tiki-view_forum_thread.php?{query fullscreen=NULL display=NULL PHPSESSID=NULL}">{$base_url}tiki-view_forum_thread.php?{query fullscreen=NULL display=NULL PHPSESSID=NULL}</a>
	</footer>
</div>
