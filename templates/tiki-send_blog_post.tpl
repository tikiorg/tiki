{include file='header.tpl'}
<!--
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
<rdf:Description
    rdf:about="{$uri}"
    dc:identifer="{$uri}"
    dc:title="{if $blog_data.use_title eq 'y'}{$post_info.title} {tr}posted by{/tr} {$post_info.user} on {$post_info.created|tiki_short_datetime}{else}{$post_info.created|tiki_short_datetime}{tr}posted by{/tr} {$post_info.user}{/if}"
    trackback:ping="{$uri2}" />
</rdf:RDF>
-->
<h2>{tr}Send blog post{/tr}</h2>
{if $sent eq 'y'}
<h3>{tr}A link to this post was sent to the following addresses:{/tr}</h3>
<div class="wikitext">
{$addresses}
</div>
{else}
	<h3>{tr}Send post to this addresses{/tr}</h3>
	<form method="post" action="tiki-send_blog_post.php">
	<input type="hidden" name="postId" value="{$postId}" />
	<table class="normal">
	<tr>
		<td class="formcolor">{tr}List of email addresses separated by commas{/tr}</td>
		<td class="formcolor"><textarea cols="60" rows="5" name="addresses">{$addresses}</textarea></td>
	</tr>
	<tr>
		<td class="formcolor" colspan="2" style="text-align:center;"><input type="submit" name="send" value="{tr}send{/tr}" /></td>
	</tr>
	</table>
	</form>
{/if}	
<h2>{tr}Post{/tr}</h2>
<a class="link" href="tiki-view_blog.php?find={$find}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}">{tr}Return to blog{/tr}</a>
<br/><br/>
<div class="posthead">
<table width="100%"><tr><td align="left">
<span class="posthead">
{if $blog_data.use_title eq 'y'}
	{$post_info.title}<br/>
	<small> {tr}posted by{/tr} {$post_info.user} on {$post_info.created|tiki_short_datetime}</small>
{else}
	{$post_info.created|tiki_short_datetime}<small> {tr}posted by{/tr} {$post_info.user}</small>
{/if}
</span>
</td><td align="right">
{if ($ownsblog eq 'y') or ($user and $post_info.user eq $user) or $tiki_p_blog_admin eq 'y'}
<a class="blogt" href="tiki-blog_post.php?blogId={$post_info.blogId}&amp;postId={$post_info.postId}"><img border='0' src='img/icons/edit.gif' title='{tr}Edit{/tr}' alt='{tr}Edit{/tr}' /></a>
<a class="blogt" href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;remove={$post_info.postId}"><img border='0' src='img/icons2/delete.gif' title='{tr}Remove{/tr}' alt='{tr}Remove{/tr}' /></a>
{/if}
</td></tr></table>
</div>
<div class="postbody">
{$parsed_data}
<hr/>
<table width="100%">
<tr><td>
<small>
<a class="link" href="tiki-view_blog_post.php?blogId={$blogId}&amp;postId={$postId}">{tr}Permalink{/tr}</a>
({tr}referenced by{/tr}: {$post_info.trackbacks_from_count} {tr}posts{/tr} {tr}references{/tr}: {$post_info.trackbacks_to_count} {tr}posts{/tr})
{if $allow_comments eq 'y' and $feature_blogposts_comments eq 'y'}
{$listpages[ix].comments} {tr}comments{/tr}
 [<a class="link" href="tiki-view_blog_post.php?find={$find}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;postId={$listpages[ix].postId}">{tr}view comments{/tr}</a>]
{/if}
</small>
</td><td style='text-align:right'>
<a href='tiki-print_blog_post.php?postId={$postId}'><img src='img/icons/ico_print.gif' border='0' alt='{tr}print{/tr}' title='{tr}print{/tr}' /></a>
<a href='tiki-send_blog_post.php?postId={$postId}'><img src='img/icons/email.gif' border='0' alt='{tr}email this post{/tr}' title='{tr}email this post{/tr}' /></a>
</td></tr></table>
</div>
