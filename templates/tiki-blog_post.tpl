{popup_init src="lib/overlib.js"}
<h1><a class="pagetitle" href="tiki-blog_post.php?blogId={$blogId}&amp;postId={$postId}">{tr}Edit Post{/tr}</a></h1><br />
{if $wysiwyg eq 'n'}
<span class="button2"><a class="linkbut" href="tiki-blog_post.php?{if $blogId ne ''}blogId={$blogId}&amp;{/if}{if $postId ne ''}&amp;postId={$postId}{/if}&amp;wysiwyg=y">{tr}Use wysiwyg editor{/tr}</a></span>
{else}
<span class="button2"><a class="linkbut" href="tiki-blog_post.php?{if $blogId ne ''}blogId={$blogId}&amp;{/if}{if $postId ne ''}&amp;postId={$postId}{/if}&amp;wysiwyg=n">{tr}Use normal editor{/tr}</a></span>
{/if}
{if $contribution_needed eq 'y'}
<div class="simplebox highlight">{tr}A contribution is mandatory{/tr}</div>
{/if}
{if $preview eq 'y'}
	{include file=tiki-preview_post.tpl}
{/if}
{if $blogId > 0 }
<span class="button2"><a class="linkbut" href="tiki-view_blog.php?blogId={$blogId}">{tr}view blog{/tr}</a></span>
{/if}
<span class="button2"><a class="linkbut" href="tiki-list_blogs.php">{tr}list blogs{/tr}</a></span>
<br /><br />

  <div class="rbox" name="tip">
  <div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
  <div class="rbox-data" name="tip">{tr}If you want to use images please save the post first and you will be able to edit/post images. Use the &lt;img&gt; snippet to include uploaded images in the textarea editor or use the image URL to include images using the WYSIWYG editor. {/tr}</div>
  </div>
  <br />

<form enctype="multipart/form-data" name='blogpost' method="post" action="tiki-blog_post.php" id ='editpageform'>
<input type="hidden" name="wysiwyg" value="{$wysiwyg|escape}" />
<input type="hidden" name="postId" value="{$postId|escape}" />
<input type="hidden" name="blogId" value="{$blogId|escape}" />
<table class="normal">
<tr><td class="editblogform">{tr}Blog{/tr}</td><td class="editblogform">
<select name="blogId">
{section name=ix loop=$blogs}
<option value="{$blogs[ix].blogId|escape}" {if $blogs[ix].blogId eq $blogId}selected="selected"{/if}>{$blogs[ix].title}</option>
{/section}
</select>
</td></tr>
{assign var=area_name value="blogedit"}
{if $feature_smileys eq 'y'}
<tr><td class="editblogform">{tr}Smileys{/tr}</td><td class="editblogform">
   {include file="tiki-smileys.tpl" area_name='blogedit'}
</td></tr>
{/if}
{if $blog_data.use_title eq 'y' || !$blogId}
<tr><td class="editblogform">{tr}Title{/tr}</td><td class="editblogform">
<input type="text" size="80" name="title" value="{$title|escape}" />
</td></tr>
{/if}
<tr><td class="editblogform">{tr}Data{/tr}
{if $wysiwyg eq 'n'}<br /><br />{include file="textareasize.tpl" area_name='blogedit' formId='editpageform'}{/if}
<br />
{include file=tiki-edit_help_tool.tpl area_name="blogedit"}
</td><td class="editblogform">
<b>{tr}Use ...page... to separate pages in a multi-page post{/tr}</b><br />
<textarea id='blogedit' class="wikiedit" name="data" rows="{$rows}" cols="{$cols}" wrap="virtual">{$data|escape}</textarea>
<input type="hidden" name="rows" value="{$rows}"/>
<input type="hidden" name="cols" value="{$cols}"/>
{if $wysiwyg eq 'y'}
	<script type="text/javascript" src="lib/htmlarea/htmlarea.js"></script>
	<script type="text/javascript" src="lib/htmlarea/htmlarea-lang-en.js"></script>
	<script type="text/javascript" src="lib/htmlarea/dialog.js"></script>
	<style type="text/css">
		@import url(lib/htmlarea/htmlarea.css);
	</style>
	<script defer='defer'>(new HTMLArea(document.forms['blogpost']['data'])).generate();</script>
{/if}
</td></tr>
{if $postId > 0}
	<tr><td class="editblogform">{tr}Upload image for this post{/tr}</td>
	<td class="editblogform">
	<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
	<input name="userfile1" type="file" />
	</td></tr>
	{if count($post_images) > 0}
		<tr><td class="editblogform">{tr}Images{/tr}</td>
		<td class="editblogform">
		<table>
		{section name=ix loop=$post_images}
		<tr>
			<td>
				<a class="link" href="tiki-view_blog_post_image.php?imgId={$post_images[ix].imgId}">{$post_images[ix].filename}</a> 
			</td>
			<td>
				<textarea rows="2" cols="40">{$post_images[ix].link|escape}</textarea><br />
				<textarea rows="1" cols="40">{$post_images[ix].absolute|escape}</textarea>
			</td>
			<td>
				<a href="tiki-blog_post.php?postId={$postId}&amp;remove_image={$post_images[ix].imgId}"><img border='0' src='img/icons/trash.gif' alt='{tr}Trash{/tr}'/></a>
			</td>
		</tr>
		{/section}
		</table>
		</td></tr>
	{/if}
{/if}
<tr><td class="editblogform">{tr}Mark entry as private:{/tr}</td>
  <td class="editblogform"><input type="checkbox" name="blogpriv" {if $blogpriv eq 'y'}checked="checked"{/if} /></td></tr>
{if $blog_spellcheck eq 'y'}
<tr><td class="editblogform">{tr}Spellcheck{/tr}: </td><td class="editblogform"><input type="checkbox" name="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if} /></td></tr>
{/if}
{if $feature_freetags eq 'y' and $tiki_p_freetags_tag eq 'y'}
  {include file=freetag.tpl}
{/if}
{if $feature_contribution eq 'y'}
{include file="contribution.tpl"}
{/if}
<tr><td class="editblogform">&nbsp;</td><td class="editblogform"><input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" />
<input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" />
<input type="submit" class="wikiaction" name="save_exit" value="{tr}save and exit{/tr}" />
</td></tr>
</table>
</form>
<br />
{include file=tiki-edit_help.tpl}
