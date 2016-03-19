{* $Id$ *} 
{title url="tiki-blog_post.php?blogId=$blogId&amp;postId=$postId"}{if $postId gt 0}{tr}Edit Post{/tr}{else}{tr}New Post{/tr}{/if}{if !empty($blog_data.title)} - {$blog_data.title}{/if}{/title}

<div class="t_navbar btn-group form-group">
	{if $postId > 0}
		{button href=$postId|sefurl:blogpost class="btn btn-default" _text="{tr}View post{/tr}"} 
	{/if}

	{if $blogId gt 0}
		{assign var=thisblog value=$blogId|sefurl:blog}
		{button href=$thisblog class="btn btn-default" _text="{tr}View Blog{/tr}"}
	{/if}

	{if $blogs|@count gt 1}
		{* No need for users to go to blog list if they are already looking at the only blog *}
		{button href="tiki-list_blogs.php" class="btn btn-default" _text="{tr}List Blogs{/tr}"}
	{/if}
</div>

{if isset($contribution_needed) and $contribution_needed eq 'y'}
	{remarksbox type='Warning' title="{tr}Warning{/tr}"}
		<div class="highlight"><em class='mandatory_note'>{tr}A contribution is mandatory{/tr}</em></div>
	{/remarksbox}
{/if}

{if $preview eq 'y'}
	<div align="center" class="attention" style="font-weight:bold">{tr}Note: Remember that this is only a preview, and has not yet been saved!{/tr}</div>
	<article class="blogpost post post_single">
		{include file='blog_wrapper.tpl' blog_post_context='preview'}
	</article>
{/if}

{capture name=actionUrlParam}{strip}
	{if $postId > 0 && $blogId > 0}
		?blogId={$blogId}&postId={$postId}
	{elseif $postId > 0}
		?postId={$postId}
	{elseif $blogId > 0}
		?blogId={$blogId}
	{/if}
{/strip}{/capture}

<form enctype="multipart/form-data" name='blogpost' method="post" action="tiki-blog_post.php{$smarty.capture.actionUrlParam}" id ='editpageform' class="form-horizontal">
	<input type="hidden" name="allowhtml" value="{if $prefs.wysiwyg_htmltowiki eq 'n'}on{/if}">
	<input type="hidden" name="postId" value="{$postId|escape}">
	<fieldset class="tabcontent">
		{if $blogs|@count gt 1 and ( !isset($blogId) or $blogId eq 0 )}
			<div class="form-group">
				<label class="col-sm-2 control-label" for="blogId">{tr}Blog{/tr}</label>
				<div class="col-sm-10">
					<select name="blogId" id="blogId" class="form-control">
						{section name=ix loop=$blogs}
							<option value="{$blogs[ix].blogId|escape}" {if $blogs[ix].blogId eq $blogId}selected="selected"{/if}>{$blogs[ix].title|escape}</option>
						{/section}
					</select>
				</div>
			</div>
		{else}
			<input type="hidden" name="blogId" value="{$blogId|escape}">
		{/if}
		<div class="form-group">
			<div class="col-md-12">
				<label class="control-label" for="title">{tr}Title{/tr}</label>
				<input type="text" maxlength="255" class="form-control" name="title" id="blog_title" {if isset($post_info.title)}value="{$post_info.title|escape}"{/if}>
			</div>
		</div>
		{if $blog_data.use_excerpt eq 'y'}
			<div class="form-group">
				<div class="col-md-12">
					<label class="control-label" for="post_excerpt">{tr}Excerpt{/tr}</label>
					{textarea id="post_excerpt" class="form-control wikiedit" name="excerpt" rows="3"}{if isset($post_info.excerpt)}{$post_info.excerpt}{/if}{/textarea}
				</div>
			</div>
		{/if}
		<div class="form-group">
			<div class="col-md-12">
				<label class="control-label" for="blogedit">{tr}Body{/tr}</label>
				{textarea id='blogedit' class="form-control wikiedit" name="data"}{if isset($data)}{$data}{/if}{/textarea}
			</div>
		</div>
		{if $postId > 0 && $wysiwyg ne 'y'}
			{if count($post_images) > 0}
				<div class="form-group">
					<label class="col-sm-2 control-label" for="post_images">{tr}Images{/tr}</label>
					<div class="col-sm-10">
						<table>
							{section name=ix loop=$post_images}
								<tr>
									<td>
										<a class="link" href="tiki-view_blog_post_image.php?imgId={$post_images[ix].imgId}">{$post_images[ix].filename}</a>
									</td>
									<td>
										<textarea rows="2" cols="40">{$post_images[ix].link|escape}</textarea><br>
										<textarea rows="1" cols="40">{$post_images[ix].absolute|escape}</textarea>
									</td>
									<td>
										<a href="tiki-blog_post.php?postId={$postId}&amp;remove_image={$post_images[ix].imgId}">{icon name='trash' iclass='tips' ititle=":{tr}Delete{/tr}"}</a>
									</td>
								</tr>
							{/section}
						</table>
					</div>
				</div>
			{/if}
		{/if}
		{if $prefs.geo_locate_blogpost eq 'y'}
			<div class="form-group">
				<label class="col-md-4 control-label" for="geolocation">{tr}Location{/tr}</label>
				<div class="col-md-8">
					{$headerlib->add_map()}
					<div class="map-container form-control" data-geo-center="{defaultmapcenter}" data-target-field="geolocation" style="height: 250px;"></div>
					<input type="hidden" name="geolocation" id="geolocation" value="{$geolocation_string}">
				</div>
			</div>
		{/if}
		<div class="form-group">
			<label class="col-md-4 control-label" for="blogpriv">{tr}Private{/tr}</label>
			<div class="col-md-8">
				<input type="checkbox" name="blogpriv" id="blogpriv" {if $blogpriv eq 'y'}checked="checked"{/if}>
			</div>
		</div>
		{if $prefs.feature_blog_edit_publish_date eq 'y'}
			<div class="form-group">
				<label class="col-md-4 control-label" for="show_pubdate">{tr}Publish Date{/tr}</label>
				<div class="col-md-8">
					{if isset($post_info.created)}
						{$created = $post_info.created}
					{else}
						{$created = ''}
					{/if}
					{html_select_date prefix="publish_" time=$created start_year="-5" end_year="+10" field_order=$prefs.display_field_order} {tr}at{/tr}
					{html_select_time prefix="publish_" time=$created display_seconds=false use_24_hours=$use_24hr_clock}
				</div>
			</div>
		{/if}
		{if $prefs.feature_freetags eq 'y' and $tiki_p_freetags_tag eq 'y'}
			{include file='freetag.tpl'}
		{/if}
		{if $prefs.feature_contribution eq 'y'}
			{include file='contribution.tpl'}
		{/if}
		{include file='categorize.tpl'}
	</fieldset>
	<div class="text-center">
		<input type="submit" class="wikiaction btn btn-default" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false">
		<input type="submit" class="wikiaction btn btn-primary" name="save" value="{tr}Save{/tr}" onclick="needToConfirm=false">
		<input type="hidden" name="referer" value="{$referer|escape}">
		<input type="submit" class="btn btn-link" name="cancel" onclick='document.location="{$referer|escape:'html'}";needToConfirm=false;return false;' value="{tr}Cancel{/tr}">
	</div>
</form>
