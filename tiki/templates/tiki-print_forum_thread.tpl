{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-print_forum_thread.tpl,v 1.1.2.1 2008-01-08 16:01:42 nyloth Exp $ *}
<div style="margin:10px 20px 0px 20px">
<h1>{tr}Forum{/tr}: {$forum_info.name}</h1>
<div class="top_post">{include file="comment.tpl" first='y' comment=$thread_info thread_style='commentStyle_plain'}</div>
{include file="comments.tpl"}
<br />
<p class="editdate">
{tr}The original document is available at{/tr} <a href="{$base_url}tiki-view_forum_thread.php?{query fullscreen=NULL print=NULL}">{$base_url}tiki-view_forum_thread.php?{query fullscreen=NULL print=NULL}</a>
</p>
</div>
