<h2>{tr}Viewing blog post{/tr}</h2>
<a class="link" href="tiki-view_blog.php?find={$find}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}">{tr}Return to blog{/tr}</a>
<br/><br/>
<div class="posthead">
<table width="100%"><tr><td align="left">
<span class="posthead">{$post_info.created|tiki_short_datetime}</span>
</td><td align="right">
{if ($ownsblog eq 'y') or ($user and $post_info.user eq $user) or $tiki_p_blog_admin eq 'y'}
<a class="blogt" href="tiki-blog_post.php?blogId={$post_info.blogId}&amp;postId={$post_info.postId}">{tr}Edit{/tr}</a>
<a class="blogt" href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;remove={$post_info.postId}">{tr}Remove{/tr}</a>
{/if}
</td></tr></table>
</div>
<div class="postbody">
{$parsed_data}
</div>
{if $feature_blogposts_comments eq 'y'}
{include file=comments.tpl}
{/if}

