{if $preview eq 'y'}
{include file=tiki-preview_post.tpl}
{/if}
<a class="pagetitle" href="tiki-blog_post.php?blogId={$blogId}">{tr}Edit Post{/tr}</a><br/><br/>
[{if $blogId > 0 }
<a class="bloglink" href="tiki-view_blog.php?blogId={$blogId}">view blog</a>|
{/if}
<a class="bloglink" href="tiki-list_blogs.php">list blogs</a>]
<br/><br/>
<form method="post" action="tiki-blog_post.php">
<input type="hidden" name="postId" value="{$postId}" />
<table class="editblogform">
<tr><td class="editblogform">{tr}Blog{/tr}</td><td class="editblogform">
<select name="blogId">
{section name=ix loop=$blogs}
<option value="{$blogs[ix].blogId}" {if $blogs[ix].blogId eq $blogId}selected="selected"{/if}>{$blogs[ix].title}</option>
{/section}
</select>
</td></tr>
<tr><td class="editblogform">{tr}Data{/tr}</td><td class="editblogform"><textarea class="wikiedit" name="data" rows="5" cols="80" wrap="virtual">{$data}</textarea></td></tr>
</td></tr>
<tr><td class="editblogform">&nbsp;</td><td class="editblogform"><input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" />
<input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
<br/>
