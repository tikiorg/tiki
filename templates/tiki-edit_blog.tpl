<h1>
{if $blogId > 0}
<a class="pagetitle" href="tiki-edit_blog.php?blogId={$blogId}">{tr}Edit Blog{/tr}: {$title}</a>
{else}
<a class="pagetitle" href="tiki-edit_blog.php">{tr}Create Blog{/tr}</a>
{/if}
  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Blogs" target="tikihelp" class="tikihelp" title="{tr}Edit Blog{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}

      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-edit_blog.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Edit Blog Tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}</h1>

<div class="navbar">
<a class="linkbut" href="tiki-list_blogs.php">{tr}List Blogs{/tr}</a>
{if $blogId > 0}<a class="linkbut" href="tiki-view_blog.php?blogId={$blogId}">{tr}View Blog{/tr}</a>{/if}
</div>
{if $category_needed eq 'y'}
<div class="simplebox hoghlight">{tr}A category is mandatory{/tr}</div>
{/if}
<h2>{tr}Current heading{/tr}</h2>
{if strlen($heading) > 0}
{eval var=$heading}
{/if}

{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName={$title|escape:"url"}&amp;objectType=blog&amp;permType=blogs&amp;objectId={$blogId}">{tr}There are individual permissions set for this blog{/tr}</a>
{/if}
<form method="post" action="tiki-edit_blog.php" id="blog-edit-form">
<input type="hidden" name="blogId" value="{$blogId|escape}" />
<table class="normal">
<tr class="editblogform"><td><label for="blog-title">{tr}Title{/tr}</label></td><td><input type="text" name="title" id="blog-title" value="{$title|escape}" /></td></tr>
<tr class="editblogform"><td><label for="blog-desc">{tr}Description{/tr}</label><br />{include file="textareasize.tpl" area_name='blog-desc' formId='blog-edit-form'}</td><td><textarea class="wikiedit" name="description" id="blog-desc" rows="{$rows}" cols="{$cols}" wrap="virtual">{$description|escape}</textarea></td></tr>
{if $tiki_p_admin eq 'y'}<tr class="editblogform"><td>{tr}Creator{/tr}</td><td><select name="user">
{foreach from=$users key=userId item=u}
<option value="{$u|escape}"{if $u eq $blog_info.user} selected="selected"{/if}>{$u}</option> 	
{/foreach}
</select> 
</td></tr>{/if}
<tr class="editblogform"><td><label for="blogs-number">{tr}Number of posts to show{/tr}</label></td><td><input type="text" name="maxPosts" id="blogs-number" value="{$maxPosts|escape}" /></td></tr>
<tr class="editblogform"><td><label for="blogs-allow_others">{tr}Allow other user to post in this blog{/tr}</label></td><td>
<input type="checkbox" name="public" id="blogs-allow_others" {if $public eq 'y'}checked='checked'{/if}/>
</td></tr>
<tr class="editblogform"><td><label for="blogs-titles">{tr}Use titles in blog posts{/tr}</label></td><td>
<input type="checkbox" name="use_title" id="blogs-titles" {if $use_title eq 'y'}checked='checked'{/if}/>
</td></tr>
<tr class="editblogform"><td><label for="blogs-search">{tr}Allow search{/tr}</label></td><td>
<input type="checkbox" name="use_find" id="blogs-search" {if $use_find eq 'y'}checked='checked'{/if}/>
</td></tr>
<tr class="editblogform"><td><label for="blogs-comments">{tr}Allow comments{/tr}</label></td><td>
<input type="checkbox" name="allow_comments" id="blogs-comments" {if $allow_comments eq 'y' or $allow_comments eq 'c'}checked='checked'{/if} />
</td></tr>
<tr class="editblogform"><td>{tr}Show user avatar{/tr}</td><td>
<input type="checkbox" name="show_avatar" {if $show_avatar eq 'y'}checked='checked'{/if} />
</td></tr>

{if $prefs.feature_blog_heading eq 'y' and $tiki_p_edit_templates eq 'y'}
<tr class="editblogform">
  <td>
    <label for="blogs-heading">{tr}Blog heading{/tr}</label>
    <br />
    {include file="textareasize.tpl" area_name='blogs-heading' formId='blog-edit-form'}</td>
  <td>
    <textarea name="heading" id="blogs-heading" rows='10' cols='{$cols}'>{$heading|escape}</textarea>
  </td>
</tr>
{/if}
{include file=categorize.tpl}
<tr class="editblogform"><td>&nbsp;</td><td><input type="submit" class="wikiaction" name="preview" value="{tr}Preview{/tr}" /><input type="submit" class="wikiaction" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<br />
