<a class="pagetitle" href="tiki-edit_blog.php">{tr}Edit Blog{/tr}: {$title}</a><br/><br/>
<a class="bloglink" href="tiki-list_blogs.php">{tr}list blogs{/tr}</a>
<br/><br/>
<h3>{tr}Current heading{/tr}</h3>
{eval var=$heading}

{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName=blog%20{$title}&amp;objectType=blog&amp;permType=blogs&amp;objectId={$blogId}">{tr}There are individual permissions set for this blog{/tr}</a>
{/if}
<form method="post" action="tiki-edit_blog.php">
<input type="hidden" name="blogId" value="{$blogId}" />
<table class="editblogform">
<tr><td class="editblogform">{tr}Title{/tr}</td><td class="editblogform"><input type="text" name="title" value="{$title}" /></td></tr>
<tr><td class="editblogform">{tr}Description{/tr}</td><td class="editblogform"><textarea class="wikiedit" name="description" rows="2" cols="40" wrap="virtual">{$description}</textarea></td></tr>
<tr><td class="editblogform">{tr}Number of posts to show{/tr}</td><td class="editblogform"><input type="text" name="maxPosts" value="{$maxPosts}" /></td></tr>
<tr><td class="editblogform">{tr}Allow other user to post in this blog{/tr}</td><td class="editblogform">
<input type="checkbox" name="public" {if $public eq 'y'}checked='checked'{/if}/>
</td></tr>
<tr><td class="editblogform">{tr}Use titles in blog posts{/tr}</td><td class="editblogform">
<input type="checkbox" name="use_title" {if $use_title eq 'y'}checked='checked'{/if}/>
</td></tr>
<tr><td class="editblogform">{tr}Allow search{/tr}</td><td class="editblogform">
<input type="checkbox" name="use_find" {if $use_find eq 'y'}checked='checked'{/if}/>
</td></tr>
<tr><td class="editblogform">{tr}Allow comments{/tr}</td><td class="editblogform">
<input type="checkbox" name="allow_comments" {if $allow_comments eq 'y'}checked='checked'{/if}/>
</td></tr>

<tr><td class="editblogform">{tr}Blog heading{/tr}</td><td class="editblogform">
<textarea name="heading" rows='10' cols='40'>{$heading}</textarea>
</td></tr>
{include file=categorize.tpl}
<tr><td class="editblogform">&nbsp;</td><td class="editblogform"><input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" /><input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
<br/>
