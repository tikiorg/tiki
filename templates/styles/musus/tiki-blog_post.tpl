{popup_init src="lib/overlib.js"}
<a class="pagetitle" href="tiki-blog_post.php?blogId={$blogId}&amp;postId={$postId}">{tr}Edit Post{/tr}</a><br /><br />
{if $wysiwyg eq 'n'}
		<a class="linkbut" href="tiki-blog_post.php?wysiwyg=y">{tr}Use wysiwyg editor{/tr}</a>
{else}
		<a class="linkbut" href="tiki-blog_post.php?wysiwyg=n">{tr}Use normal editor{/tr}</a>
{/if}
{if $preview eq 'y'}
	{include file=tiki-preview_post.tpl}
{/if}
{if $blogId > 0 }<a class="linkbut" href="tiki-view_blog.php?blogId={$blogId}">{tr}view blog{/tr}</a>{/if}
<a class="linkbut" href="tiki-list_blogs.php">{tr}list blogs{/tr}</a>
<br /><br />
<div class="wikitext"><small>{tr}Note: if you want to use images please save the post first and you
will be able to edit/post images. Use the &lt;img&gt; snippet to include uploaded images in the textarea editor
or use the image URL to include images using the WYSIWYG editor. {/tr}</small></div>
<form enctype="multipart/form-data" name='blogpost' method="post" action="tiki-blog_post.php" id ='editpageform'>
<input type="hidden" name="wysiwyg" value="{$wysiwyg|escape}" />
<input type="hidden" name="postId" value="{$postId|escape}" />
<input type="hidden" name="blogId" value="{$blogId|escape}" />
<table>
<tr><td class="editblogform">{tr}Blog{/tr}</td><td class="editblogform">
<select name="blogId">
{section name=ix loop=$blogs}
<option value="{$blogs[ix].blogId|escape}" {if $blogs[ix].blogId eq $blogId}selected="selected"{/if}>{$blogs[ix].title}</option>
{/section}
</select>
</td></tr>
{assign var=area_name value="blogedit"}
{if $feature_smileys eq 'y'}
<tr class="editblogform"><td>{tr}Smileys{/tr}</td><td>
   {include file="tiki-smileys.tpl" area_name='blogedit'}
</td></tr>
{/if}
<tr class="editblogform"><td>{tr}Quicklinks{/tr}</td><td>
{assign var="area_name" value="blogedit"}
{include file="tiki-edit_help_tool.tpl"}
</td></tr>
{if $blog_data.use_title eq 'y'}
<tr class="editblogform"><td>{tr}Title{/tr}</td><td>
<input type="text" size="80" name="title" value="{$title|escape}" />
</td></tr>
{/if}
<tr class="editblogform"><td>{tr}Data{/tr}{if $wysiwyg eq 'n'}<br /><br />{include file="textareasize.tpl" area_name='blogedit' formId='editpageform'}{/if}</td><td>
<b>{tr}Use ...page... to separate pages in a multi-page post{/tr}</b><br />
<textarea id="blogedit" class="wikiedit" name="data" rows="{$rows}" cols="{$cols}" wrap="virtual">{$data|escape}</textarea>
<input type="hidden" name="rows" value="{$rows}"/>
<input type="hidden" name="cols" value="{$cols}"/>
{if $wysiwyg eq 'y'}
	<script type="text/javascript" src="lib/htmlarea/htmlarea.js"></script>
	<script type="text/javascript" src="lib/htmlarea/htmlarea-lang-en.js"></script>
	<script type="text/javascript" src="lib/htmlarea/dialog.js"></script>
	<style type="text/css">
		@import url(lib/htmlarea/htmlarea.css);
	</style>
	<script defer="defer">(new HTMLArea(document.forms['blogpost']['data'])).generate();</script>
{/if}
</td></tr>
{if $postId > 0}
	<tr><td class="editblogform">{tr}Upload image for this post{/tr}</td>
	<td class="editblogform">
	<input type="hidden" name="MAX_FILE_SIZE" value="1000000000">
	<input name="userfile1" type="file">
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
<tr><td class="editblogform">{tr}Send trackback pings to:{/tr}<small>{tr}(comma separated list of URIs){/tr}</small></td><td class="editblogform">
<textarea name="trackback" rows="3" cols="60">{section name=ix loop=$trackbacks_to}{if not $smarty.section.ix.first},{/if}{$trackbacks_to[ix]}{/section}</textarea>
</td></tr>
{if $blog_spellcheck eq 'y'}
<tr><td class="editblogform">{tr}Spellcheck{/tr}: </td><td class="editblogform"><input type="checkbox" name="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if}/></td></tr>
{/if}
<tr><td class="editblogform">&nbsp;</td><td class="editblogform"><input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" />
<input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" />
<input type="submit" class="wikiaction" name="save_exit" value="{tr}save and exit{/tr}" />
</td></tr>
</table>
</form>
<br />
{include file=tiki-edit_help.tpl}
