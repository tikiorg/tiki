<a class="pagetitle" href="tiki-edit_blog.php">{tr}Edit Blog{/tr}: {$title}</a>
<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=BlogDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Edit Blog{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-edit_blog.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}edit blog tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- beginning of next bit -->




<br /><br />
<a class="linkbut" href="tiki-list_blogs.php">{tr}list blogs{/tr}</a>
<br /><br />
<h3>{tr}Current heading{/tr}</h3>
{if strlen($heading) > 0}
{eval var=$heading}
{/if}

{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName=blog%20{$title}&amp;objectType=blog&amp;permType=blogs&amp;objectId={$blogId}">{tr}There are individual permissions set for this blog{/tr}</a>
{/if}
<form method="post" action="tiki-edit_blog.php">
<input type="hidden" name="blogId" value="{$blogId|escape}" />
<table class="normal">
<tr><td class="editblogform">{tr}Title{/tr}</td><td class="editblogform"><input type="text" name="title" value="{$title|escape}" /></td></tr>
<tr><td class="editblogform">{tr}Description{/tr}</td><td class="editblogform"><textarea class="wikiedit" name="description" rows="2" cols="40" wrap="virtual">{$description|escape}</textarea></td></tr>
<tr><td class="editblogform">{tr}Number of posts to show{/tr}</td><td class="editblogform"><input type="text" name="maxPosts" value="{$maxPosts|escape}" /></td></tr>
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

{if $tiki_p_edit_templates eq 'y'}
<tr><td class="editblogform">{tr}Blog heading{/tr}</td><td class="editblogform">
<textarea name="heading" rows='10' cols='40'>{$heading|escape}</textarea>
</td></tr>
{/if}
{include file=categorize.tpl}
<tr><td class="editblogform">&nbsp;</td><td class="editblogform"><input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" /><input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
<br />
