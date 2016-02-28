{* $Id$ *}

{title help="Blogs"}{if isset($blogTitle)}{tr _0=$blogTitle}Blog: %0{/tr}{else}{tr}Blog Posts{/tr}{/if}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-edit_blog.php" _type="link" class="btn btn-link" _icon_name="add" _text="{tr}Create Blog{/tr}"}
	{button href="tiki-blog_post.php" _type="link" class="btn btn-link" _icon_name="create" _text="{tr}New Blog Post{/tr}"}
	{button href="tiki-list_blogs.php" _type="link" class="btn btn-link" _icon_name="list" _text="{tr}List Blogs{/tr}"}
	{if $posts or ($find ne '')}
		<div class="col-md-6 row-sidemargins-zero pull-right">
			{include file='find.tpl'}
		</div>
	{/if}
</div>
{if $posts and $tiki_p_blog_admin eq 'y'}
	<form name="checkboxes_on" method="post" action="tiki-list_posts.php" role="form" class="form">
	{query _type='form_input'}
{/if}
{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
{if $prefs.javascript_enabled !== 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}
<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
	<table class="table">
		<tr>
			{if $posts and $tiki_p_blog_admin eq 'y'}
				<th>
					{select_all checkbox_names='checked[]'}
				</th>
			{/if}
			<th>
				<a href="tiki-list_posts.php?{if isset($blogId)}blogId={$blogId}&amp;{/if}offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_asc'}title_desc{else}title_asc{/if}">
					{tr}Post Title{/tr}
				</a>
			</th>
			{if !isset($blogId)}
				<th>{tr}Blog Title{/tr}</th>
			{/if}
			<th>
				<a href="tiki-list_posts.php?{if isset($blogId)}blogId={$blogId}&amp;{/if}offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a>
			</th>
			<th class="text-right">{tr}Size{/tr} {tr}(bytes){/tr}</th>
			<th>
				<a href="tiki-list_posts.php?{if isset($blogId)}blogId={$blogId}&amp;{/if}offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}Author{/tr}</a>
			</th>
			<th></th>
		</tr>


		{section name=changes loop=$posts}{assign var=id value=$posts[changes].postId}
			<tr>
				<td class="checkbox-cell"><input type="checkbox" name="checked[]" value="{$id}"></td>
				<td class="text">{object_link type="blog post" id=$posts[changes].postId title=$posts[changes].title}</td>
				{if !isset($blogId)}
					<td class="text">
						<a class="blogname" href="tiki-list_posts.php?blogId={$posts[changes].blogId}" title="{$posts[changes].blogTitle|escape}">{$posts[changes].blogTitle|truncate:$prefs.blog_list_title_len:"...":true|escape}</a>
					</td>
				{/if}
				<td class="date">&nbsp;{$posts[changes].created|tiki_short_date}&nbsp;</td>
				<td class="integer">{$posts[changes].size}</td>
				<td>&nbsp;{$posts[changes].user}&nbsp;</td>
				<td class="action">
					{capture name=post_actions}
						{strip}
							{$libeg}<a href="tiki-blog_post.php?blogId={$posts[changes].blogId}&postId={$posts[changes].postId}">
								{icon name="edit" _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-list_posts.php?{if isset($blogId)}blogId={$blogId}&amp;{/if}offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$posts[changes].postId}" title=":{tr}Delete{/tr}">
								{icon name="remove" _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.post_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.post_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=7}
		{/section}
	</table>
</div>

{if $posts and $tiki_p_blog_admin eq 'y'}
		<div class="form-group">
			<label for="remove" class="control-label">{tr}Perform action with selected{/tr}</label>
			<div class="input-group col-sm-4">
				<select name="remove" class="form-control">
					<option value="y">{tr}Delete{/tr}</option>
				</select>
				<div class="input-group-btn">
					<input type="submit" class="btn btn-default" name="remove" value="{tr}Ok{/tr}">
				</div>
			</div>
		</div>
	</form>
{/if}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
