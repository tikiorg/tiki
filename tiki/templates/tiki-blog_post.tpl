{if $preview eq 'y'}
{include file=tiki-preview_post.tpl}
{/if}
<h1><a class="wiki" href="tiki-blog_post.php">{tr}Edit Post{/tr}</a></h1>
{if $blogId > 0 }
<a class="link" href="tiki-view_blog.php?blogId=$blogId">view blog</a>
{/if}
<a class="link" href="tiki-list_blogs.php">list blogs</a>
<br/><br/>
<form method="post" action="tiki-blog_post.php">
<input type="hidden" name="postId" value="{$postId}" />
<table width="100%">
<tr><td class="form">{tr}Blog{/tr}</td><td>
<select name="blogId">
{section name=ix loop=$blogs}
<option value="{$blogs[ix].blogId}" {if $blogs[ix].blogId eq $blogId}selected="selected"{/if}>{$blogs[ix].title}</option>
{/section}
</select>
</td></tr>
<tr><td class="form">{tr}Data{/tr}</td><td><textarea class="wikiedit" name="data" rows="5" cols="80" wrap="virtual">{$data}</textarea></td></tr>
</td></tr>
</table>
<div align="center">
<input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" />
<input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" />
</div>
</form>
<br/>
