{if strlen($heading) > 0}
{eval var=$heading}
{else}
<div class="blogtitle">Blog: {$title}</div>
<div class="bloginfo">
{tr}Created by{/tr} {$creator}{tr} on {/tr}{$created|tiki_short_datetime}<br/>
{tr}Last modified{/tr} {$lastModif|tiki_short_datetime}<br/><br/>
<table width="100%">
<tr>
	<td>
		({$posts} {tr}posts{/tr} | {$hits} {tr}visits{/tr} | {tr}Activity={/tr}{$activity|string_format:"%.2f"})
	</td>
	<td style="text-align:right;">
		{if $tiki_p_blog_post eq "y"}
		{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y" or $public eq "y"}
		<a class="bloglink" href="tiki-blog_post.php?blogId={$blogId}"><img src='img/icons/edit.gif' border='0' alt='{tr}Post{/tr}' title='{tr}post{/tr}' /></a>
		{/if}
		{/if}
		{if $rss_blog eq "y"}
		<a class="bloglink" href="tiki-blog_rss.php?blogId={$blogId}"><img src='img/icons/mode_desc.gif' border='0' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
		{/if}
		{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y"}
		<a class="bloglink" href="tiki-edit_blog.php?blogId={$blogId}"><img src='img/icons/config.gif' border='0' alt='{tr}Edit blog{/tr}' title='{tr}Edit blog{/tr}' /></a>
		{/if}
		
		{if $user and $feature_user_watches eq 'y'}
		{if $user_watching_blog eq 'n'}
		<a href="{sameurl watch_event='blog_post' watch_object=$blogId watch_action='add'}"><img border='0' alt='{tr}monitor this blog{/tr}' title='{tr}monitor this blog{/tr}' src='img/icons/icon_watch.png' /></a>
		{else}
		<a href="{sameurl watch_event='blog_post' watch_object=$blogId watch_action='remove'}"><img border='0' alt='{tr}stop monitoring this blog{/tr}' title='{tr}stop monitoring this blog{/tr}' src='img/icons/icon_unwatch.png' /></a>
		{/if}
		{/if}

		
	</td>
</tr>
</table>	
</div>
<div class="blogdesc">{tr}Description:{/tr}{$description}</div>
{/if}
{if $use_find eq 'y'}
<div class="blogtools">
<table><tr><td>
<form action="tiki-view_blog.php" method="get">
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<input type="hidden" name="blogId" value="{$blogId}" />
{tr}Find:{/tr}<input type="text" name="find" /><input type="submit" name="search" value="{tr}find{/tr}" />
</form>
</td><td>
<!--
	{tr}Sort posts by:{/tr}
	<a class="bloglink" href="tiki-view_blog.php?blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Date{/tr}</a>
-->	
</td></tr></table>
</div>
{/if}
{section name=ix loop=$listpages}
<div>
<div class="posthead">
<table width="100%"><tr><td align="left">
<span class="posthead">
{if $use_title eq 'y'}
	{$listpages[ix].title}<br/>
	<small> {tr}posted by{/tr} {$listpages[ix].user} on {$listpages[ix].created|tiki_short_datetime}</small>
{else}
	{$listpages[ix].created|tiki_short_datetime}<small> {tr}posted by{/tr} {$listpages[ix].user}</small>
{/if}
</span>
</td><td align="right">
{if ($ownsblog eq 'y') or ($user and $listpages[ix].user eq $user) or $tiki_p_blog_admin eq 'y'}
<a class="blogt" href="tiki-blog_post.php?blogId={$listpages[ix].blogId}&amp;postId={$listpages[ix].postId}"><img border='0' src='img/icons/edit.gif' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a>
<a class="blogt" href="tiki-view_blog.php?blogId={$blogId}&amp;remove={$listpages[ix].postId}"><img src='img/icons/trash.gif' alt='{tr}Remove{/tr}' border='0' title='{tr}Remove{/tr}' /></a>
{/if}
{if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<a title="{tr}Save to notepad{/tr}" href="{sameurl savenotepad=$listpages[ix].postId}"><img border="0" src="img/icons/ico_save.gif" alt="{tr}save{/tr}" /></a>
{/if}
</td></tr></table>
</div>
<div class="postbody">
{$listpages[ix].parsed_data}
<hr/>
<table width="100%"><tr><td>
<small>
<a class="link" href="tiki-view_blog_post.php?blogId={$blogId}&amp;postId={$listpages[ix].postId}">{tr}Permalink{/tr}</a>
 ({tr}referenced by{/tr}: {$listpages[ix].trackbacks_from_count} {tr}posts{tr}  {tr}refereces{/tr}: {$listpages[ix].trackbacks_to_count} {tr}posts{/tr})
{if $allow_comments eq 'y' and $feature_blogposts_comments eq 'y'}
{$listpages[ix].comments} {tr}comments{/tr}
 [<a class="link" href="tiki-view_blog_post.php?find={$find}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;postId={$listpages[ix].postId}">{tr}view comments{/tr}</a>]
{/if}
</small>
</td><td style='text-align:right'>
<a href='tiki-print_blog_post.php?postId={$listpages[ix].postId}'><img src='img/icons/ico_print.gif' border='0' alt='{tr}print{/tr}' title='{tr}print{/tr}' /></a>
<a href='tiki-send_blog_post.php?postId={$listpages[ix].postId}'><img src='img/icons/email.gif' border='0' alt='{tr}email this post{/tr}' title='{tr}email this post{/tr}' /></a>
</td></tr></table>
</div>
</div>
{/section}
<br/>
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="blogprevnext" href="tiki-view_blog.php?find={$find}&amp;blogId={$blogId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="blogprevnext" href="tiki-view_blog.php?find={$find}&amp;blogId={$blogId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-view_blog.php?find={$find}&amp;blogId={$blogId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
{if $feature_blog_comments eq 'y'}
{include file=comments.tpl}
{/if}

