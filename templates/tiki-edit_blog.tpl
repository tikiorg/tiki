<a class="pagetitle" href="tiki-edit_blog.php">{tr}Edit Blog{/tr}: {$title}</a><br/><br/>
<a class="bloglink" href="tiki-list_blogs.php">list blogs</a>
<br/><br/>
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName=blog%20{$title}&amp;objectType=blog&amp;permType=blogs&amp;objectId={$blogId}">{tr}There are inddividual permissions set for this blog{/tr}</a>
{/if}
<form method="post" action="tiki-edit_blog.php">
<input type="hidden" name="blogId" value="{$blogId}" />
<table class="editblogform">
<tr><td class="editblogform">{tr}Title{/tr}</td><td class="editblogform"><input type="text" name="title" value="{$title}" /></td></tr>
<tr><td class="editblogform">{tr}Description{/tr}</td><td class="editblogform"><textarea class="wikiedit" name="description" rows="5" cols="40" wrap="virtual">{$description}</textarea></td></tr>
<tr><td class="editblogform">{tr}Number of posts to show{/tr}</td><td class="editblogform"><input type="text" name="maxPosts" value="{$maxPosts}" /></td></tr>
<tr><td class="editblogform">{tr}Allow other user to post in this blog{/tr}</td><td class="editblogform">
<input type="checkbox" name="public" {if $public eq 'y'}checked='checked'{/if}/>
</td></tr>
{include file=categorize.tpl}
<tr><td class="editblogform">&nbsp;</td><td class="editblogform"><input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
<br/>
