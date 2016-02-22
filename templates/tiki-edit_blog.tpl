{if $blogId > 0}
	{title help="Blogs" url="tiki-edit_blog.php?blogId=$blogId" admpage="blogs"}{tr}Edit Blog:{/tr} {$title}{/title}
{else}
	{title help="Blogs"}{tr}Create Blog{/tr}{/title}
{/if}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-list_blogs.php" _type="lionk" class="btn btn-link" _icon_name="list" _text="{tr}List Blogs{/tr}"}
	{if $blogId > 0}
		{assign var=thisblogId value=$blogId|sefurl:blog}
		{button href=$thisblogId class="btn btn-default" _text="{tr}View Blog{/tr}"}
	{/if}
</div>

{if isset($category_needed) && $category_needed eq 'y'}
	{remarksbox type='Warning' title="{tr}Warning{/tr}"}
		<div class="highlight"><em class='mandatory_note'>{tr}A category is mandatory{/tr}</em></div>
	{/remarksbox}
{/if}

{if $individual eq 'y'}
	{permission_link mode=link type=blog permType=blogs id=$blogId title=$title label="{tr}Individual permissions are set for this blog{/tr}"}
{/if}

<form method="post" action="tiki-edit_blog.php" id="blog-edit-form" class="form-horizontal" role="form">
	<input type="hidden" name="blogId" value="{$blogId|escape}">
	{tabset name='tabs_editblog'}
		{tab name="{tr}General Settings{/tr}"}
			<h2>{tr}General Settings{/tr}</h2>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="blog-title">{tr}Title{/tr}</label>
				<div class="col-sm-9">
					<input type="text" maxlength="200" name="title" id="blog-title" class="form-control" value="{$title|escape}" required="required">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="blog-desc">{tr}Description{/tr}</label>
				<div class="col-sm-9">
					<textarea class="wikiedit form-control" name="description" id="blog-desc" rows="10">{$description|escape}</textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="creator">{tr}Creator{/tr}</label>
				<div class="col-sm-9">
					<select name="creator" class="form-control">
						{if ($tiki_p_admin eq 'y' or $tiki_p_blog_admin eq 'y') and !empty($users)}
							{foreach from=$users key=userId item=u}
								<option value="{$u|escape}"{if $u eq $creator} selected="selected"{/if}>{$u|escape}</option>
							{/foreach}
						{else}
							<option value="{$user|escape}" selected="selected">{$user|escape}</option>
						{/if}
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-9 col-sm-offset-3">
					<div class="checkbox">
						<label for="blogs-allow_others">
							<input type="checkbox" name="public" id="blogs-allow_others" {if $public eq 'y'}checked='checked'{/if}>
							{tr}Allow other users to post in this blog{/tr}
						</label>
					</div>
					<div class="checkbox">
						<label for="blogs-always_owner">
							<input type="checkbox" name="alwaysOwner" id="blogs-always_owner" {if isset($alwaysOwner) and $alwaysOwner eq 'y'}checked='checked'{/if}>
							{tr}Even if others post to the blog, the author is always its administrator{/tr}
						</label>
					</div>
					<div class="checkbox">
						<label for="blogs-search">
							<input type="checkbox" name="use_find" id="blogs-search" {if $use_find eq 'y'}checked='checked'{/if}>
							{tr}Allow search{/tr}
						</label>
					</div>
					<div class="checkbox">
						<label for="blogs-comments">
							<input type="checkbox" name="allow_comments" id="blogs-comments" {if $allow_comments eq 'y' or $allow_comments eq 'c'}checked='checked'{/if}{if $prefs.feature_blogposts_comments ne 'y'} disabled="disabled"{/if}>
							{if $prefs.feature_blogposts_comments ne 'y'}Global post-level comments is disabled.{/if}
							{tr}Allow comments{/tr}
						</label>
					</div>
					<div class="checkbox">
						<label for="blogs-post-use-excerpt">
							<input type="checkbox" name="use_excerpt" id="blogs-post-use-excerpt" {if $use_excerpt eq 'y'}checked='checked'{/if}>
							{tr}Use post excerpt{/tr}
						</label>
					</div>
				</div>
			</div>
			{include file='categorize.tpl'}
		{/tab}
		{tab name="{tr}Display Options{/tr}"}
			<h2>{tr}Display Options{/tr}</h2>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="blogs-number">{tr}Displayed posts{/tr}</label>
				<div class="col-sm-2">
					<input type="text" name="maxPosts" id="blogs-number" class="form-control" value="{$maxPosts|escape}">
				</div>
				<div class="help-block">{tr}Number of posts to show per page{/tr}</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<div class="checkbox">
						<label for="blogs-title">
							<input type="checkbox" name="use_title" id="blogs-title" {if $use_title eq 'y'}checked='checked'{/if}>
							{tr}Display the blog title on the posts list page{/tr}
						</label>
					</div>
					<div class="checkbox">
						<label for="blogs-title-post">
							<input type="checkbox" name="use_title_in_post" id="blogs-title-post" {if $use_title_in_post eq 'y'}checked='checked'{/if}>
							{tr}Display the blog title on the post page{/tr}
						</label>
					</div>
					<div class="checkbox">
						<label for="blogs-description">
							<input type="checkbox" name="use_description" id="blogs-description" {if $use_description eq 'y'}checked='checked'{/if}>
							{tr}Display the blog description{/tr}
						</label>
					</div>
					<div class="checkbox">
						<label for="blogs-breadcrumbs">
							<input type="checkbox" name="use_breadcrumbs" id="blogs-breadcrumbs" {if $use_breadcrumbs eq 'y'}checked='checked'{/if}>
							{tr}Display breadcrumbs{/tr}
						</label>
					</div>
					<div class="checkbox">
						<label for="blogs-author">
							<input type="checkbox" name="use_author" id="blogs-author" {if $use_author eq 'y'}checked='checked'{/if}>
							{tr}Display the author in blog posts{/tr}
						</label>
					</div>
					<div class="checkbox">
						<label for="blogs-date">
							<input type="checkbox" name="add_date" id="blogs-date" {if $add_date eq 'y'}checked='checked'{/if}>
							{tr}Display the publish date in blog posts{/tr}
						</label>
					</div>
					<div class="checkbox">
						<label for="blogs-avatar">
							<input type="checkbox" name="show_avatar" id="blogs-avatar" {if $show_avatar eq 'y'}checked='checked'{/if}>
							{tr}Show user profile picture{/tr}
						</label>
					</div>
					<div class="checkbox">
						<label for="blogs-post-related">
							<input type="checkbox" name="show_related" id="blogs-post-related" {if $show_related eq 'y'}checked='checked'{/if} {if $prefs.feature_freetags ne 'y'}disabled="disabled"{/if}>
							{tr}Show the post's related content{/tr}
							{if $prefs.feature_freetags neq 'y'}
								<span class="help-block">{tr}The checkbox is disabled because the tags feature is disabled globally.{/tr}</span>
							{/if}
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="blogs-post-max-related">{tr}Related posts{/tr}</label>
				<div class="col-sm-2">
					<input type="text" name="related_max" id="blogs-post-max-related" class="form-control" value="{$related_max|escape}">
				</div>
				<div class="help-block">{tr}Maximum number of related posts to display{/tr}</div>
			</div>
			{if $prefs.feature_blog_heading eq 'y' and $tiki_p_edit_templates eq 'y'}
				<div class="form-group">
					<label class="col-sm-3 control-label" for="blogs-heading">{tr}Blog heading{/tr}</label>
					<div class="col-sm-9">
						<textarea name="heading" id="blogs-heading" rows='10' class="form-control">{$heading|escape}</textarea>
					</div>
				</div>
				{if strlen($heading) > 0 and $show_blog_heading_preview eq 'y'}
					{button href="#" _flip_id='blog_heading_preview' _class='link' _text="{tr}Heading preview{/tr}" _flip_default_open='n'}
					<div id="blog_heading_preview" style="display: {if $show_blog_heading_preview eq 'y'}block{else}none{/if};">
						{eval var=$heading}
					</div>
				{/if}
				<div class="form-group">
					<label class="col-sm-3 control-label" for="blogs-post-heading">{tr}Blog post heading{/tr}</label>
					<div class="col-sm-9">
						<textarea name="post_heading" id="blogs-post_heading" rows='10' class="form-control">{$post_heading|escape}</textarea>
					</div>
				</div>
			{/if}
		{/tab}
	{/tabset}
	{if $prefs.feature_blog_heading eq 'y' and $tiki_p_edit_templates eq 'y'}
		<input type="submit" class="wikiaction btn btn-default" name="preview" value="{tr}Heading preview{/tr}">
	{/if}
	<div class="form-group text-center">
		<input type="submit" class="wikiaction btn btn-primary" name="save" value="{tr}Save{/tr}">
	</div>
</form>
