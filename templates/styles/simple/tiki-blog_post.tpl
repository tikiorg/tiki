{popup_init src="lib/overlib.js"}
<h1><a class="pagetitle" href="tiki-blog_post.php?blogId={$blogId}&amp;postId={$postId}">{tr}Edit Post{/tr}</a></h1>
{if $wysiwyg eq 'n'}
	<span class="button2"><a class="linkbut" href="tiki-blog_post.php?{if $blogId ne ''}blogId={$blogId}&amp;{/if}{if $postId ne ''}&amp;postId={$postId}{/if}&amp;wysiwyg=y">{tr}Use wysiwyg editor{/tr}</a></span>
{else}
	<span class="button2"><a class="linkbut" href="tiki-blog_post.php?{if $blogId ne ''}blogId={$blogId}&amp;{/if}{if $postId ne ''}&amp;postId={$postId}{/if}&amp;wysiwyg=n">{tr}Use normal editor{/tr}</a></span>
{/if}
{if $preview eq 'y'}
	{include file=tiki-preview_post.tpl}
{/if}
{if $blogId > 0 }
	<span class="button2">
	<a class="linkbut" href="tiki-view_blog.php?blogId={$blogId}">
		{tr}View Blog{/tr}
	</a>
	</span>
{/if}
<span class="button2">
	<a class="linkbut" href="tiki-list_blogs.php">
		{tr}List Blogs{/tr}
	</a>
</span>
<div class="wikitext">
	<small>{tr}Note: if you want to use images please save the post first and you
	will be able to edit/post images. Use the &lt;img&gt; snippet to include uploaded images in the textarea editor
	or use the image URL to include images using the WYSIWYG editor. {/tr}</small>
</div>
<form enctype="multipart/form-data" name='blogpost' method="post" action="tiki-blog_post.php" id ='editpageform'>
<input type="hidden" name="wysiwyg" value="{$wysiwyg|escape}" />
<input type="hidden" name="postId" value="{$postId|escape}" />
<input type="hidden" name="blogId" value="{$blogId|escape}" />

<div class="normal">
<div class="editblogform" style="float:left">
	{tr}Blog{/tr}
</div>
<div class="editblogform" style="float:left">
	<select name="blogId">
	{section name=ix loop=$blogs}
		<option value="{$blogs[ix].blogId|escape}" {if $blogs[ix].blogId eq $blogId}selected="selected"{/if}>{$blogs[ix].title}</option>
	{/section}
	</select>
</div>
{assign var=area_name value="blogedit"}
{if $prefs.feature_smileys eq 'y'}
	<div class="editblogform" style="clear:left">
		{tr}Smileys{/tr}
	</div>
	<div class="editblogform">
		{include file="tiki-smileys.tpl" area_name='blogedit'}
	</div>
{/if}
{if $blog_data.use_title eq 'y' || !$blogId}
	<div class="editblogform">
		{tr}Title{/tr}
	</div>
	<div class="editblogform">
		<input type="text" size="80" name="title" value="{$title|escape}" />
	</div>
{/if}
<div class="editblogform">
	{tr}Data{/tr}
	<div class="editblogform">
		<div class="editblogform" style="float:left">
			{if $wysiwyg eq 'n'}
				{include file="textareasize.tpl" area_name='blogedit' formId='editpageform'}
			{/if}
			{include file=tiki-edit_help_tool.tpl area_name="blogedit"}
		</div>
		<div style="float:left">
			<strong>{tr}Use ...page... to separate pages in a multi-page post{/tr}</strong><br />
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
		</div>
	</div>
</div>
{if $postId > 0}
<div>
	<div class="editblogform">
		{tr}Upload image for this post{/tr}
		<input type="hidden" name="MAX_FILE_SIZE" value="1000000000">
		<input name="userfile1" type="file">
	</div>
	{if count($post_images) > 0}
		<div class="editblogform">
			{tr}Images{/tr}
		</div>
		<div class="editblogform">
		{section name=ix loop=$post_images}
			<a class="link" href="tiki-view_blog_post_image.php?imgId={$post_images[ix].imgId}">
				{$post_images[ix].filename}
			</a> 
			<a href="tiki-blog_post.php?postId={$postId}&amp;remove_image={$post_images[ix].imgId}">
				<img border='0' src='img/icons/trash.gif' alt='{tr}Trash{/tr}'/>
			</a>
			<textarea rows="2" cols="40">{$post_images[ix].link|escape}</textarea><br />
			<textarea rows="1" cols="40">{$post_images[ix].absolute|escape}</textarea>
		{/section}
		</div>
	{/if}
</div>
{/if}
<div class="editblogform" style="clear:left">
	{tr}Mark entry as private:{/tr} <input type="checkbox" name="blogpriv" {if $blogpriv eq 'y'}checked="checked"{/if} />
</div>
<div class="editblogform">
	{tr}Send trackback pings to:{/tr}<small>{tr}(comma separated list of URIs){/tr}</small>
</div>
<div class="editblogform">
	<textarea name="trackback" rows="3" cols="60">{section name=ix loop=$trackbacks_to}{if not $smarty.section.ix.first},{/if}{$trackbacks_to[ix]}{/section}</textarea>
</div>
{if $prefs.blog_spellcheck eq 'y'}
	<div class="editblogform">
		{tr}Spellcheck{/tr}: <input type="checkbox" name="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if} />
	</div>
{/if}
{if $prefs.feature_freetags eq 'y'}
	{include file=freetag.tpl}
{/if}
<div class="editblogform">
	<input type="submit" class="wikiaction" name="save" value="{tr}Save{/tr}" />
	<input type="submit" class="wikiaction" name="preview" value="{tr}Preview{/tr}" />
	<input type="submit" class="wikiaction" name="save_exit" value="{tr}Save and Exit{/tr}" />
</div>
</div>
</form>
{include file=tiki-edit_help.tpl}
