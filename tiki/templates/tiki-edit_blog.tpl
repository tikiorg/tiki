<h1><a class="wiki" href="tiki-edit_blog.php">{tr}Edit Blog{/tr}: {$title}</a></h1>
<a class="link" href="tiki-list_blogs.php">list blogs</a>
<br/><br/>
<form method="post" action="tiki-edit_blog.php">
<input type="hidden" name="blogId" value="{$blogId}" />
<table width="100%">
<tr><td class="form">{tr}Title{/tr}</td><td><input type="text" name="title" value="{$title}" /></td></tr>
<tr><td class="form">{tr}Description{/tr}</td><td><textarea class="wikiedit" name="description" rows="5" cols="80" wrap="virtual">{$description}</textarea></td></tr>
<tr><td class="form">{tr}Number of posts to show{/tr}</td><td><input type="text" name="maxPosts" value="{$maxPosts}" /></td></tr>
<tr><td class="form">{tr}Allow other user to post in this blog{/tr}</td><td>
<input type="checkbox" name="public" {if $public eq 'y'}checked='checked'{/if}/>
</td></tr>
</table>
<div align="center">
<input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" />
</div>
</form>
<br/>
