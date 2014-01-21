{* $Id$ *}

{title help="Blogs"}{if isset($blogTitle)}{tr _0=$blogTitle}Blog: %0{/tr}{else}{tr}Blog{/tr}{/if}{/title}

<div class="navbar">
	{button href="tiki-edit_blog.php" _text="{tr}Create Blog{/tr}"}
	{button href="tiki-blog_post.php" _text="{tr}New Blog Post{/tr}"}
	{button href="tiki-list_blogs.php" _text="{tr}List Blogs{/tr}"}
</div>

{if $posts or ($find ne '')}
	{include file='find.tpl'}
{/if}

{if $posts and  $tiki_p_blog_admin eq 'y'}
	<form name="checkboxes_on" method="post" action="tiki-list_posts.php">
	{query _type='form_input'}
{/if}

<table class="table normal">
	<tr>
		{if $posts and  $tiki_p_blog_admin eq 'y'}
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
		<th>{tr}Size{/tr}</th>
		<th>
			<a href="tiki-list_posts.php?{if isset($blogId)}blogId={$blogId}&amp;{/if}offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}Author{/tr}</a>
		</th>
		<th>{tr}Actions{/tr}</th>
	</tr>

	{cycle values="odd,even" print=false}
	{section name=changes loop=$posts}{assign var=id value=$posts[changes].postId}
		<tr class="{cycle}">
			<td class="checkbox"><input type="checkbox" name="checked[]" value="{$id}"></td>
			<td class="text">{object_link type="blog post" id=$posts[changes].postId title=$posts[changes].title}</td>
			{if !isset($blogId)}
				<td class="text">
					<a class="blogname" href="tiki-list_posts.php?blogId={$posts[changes].blogId}" title="{$posts[changes].blogTitle|escape}">{$posts[changes].blogTitle|truncate:$prefs.blog_list_title_len:"...":true|escape}</a>
				</td>
			{/if}
			<td class="date">&nbsp;{$posts[changes].created|tiki_short_date}&nbsp;</td>
			<td class="integer">&nbsp;{$posts[changes].size}&nbsp;</td>
			<td>&nbsp;{$posts[changes].user}&nbsp;</td>
			<td class="action">
				<a class="link" href="tiki-blog_post.php?blogId={$posts[changes].blogId}&postId={$posts[changes].postId}">{icon _id='page_edit'}</a>
				<a class="link" href="tiki-list_posts.php?{if isset($blogId)}blogId={$blogId}&amp;{/if}offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$posts[changes].postId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=7}
	{/section}
</table>

{if $posts and  $tiki_p_blog_admin eq 'y'}
	<div class="formcolor">
		{tr}Perform action with checked:{/tr}
		{icon _id='cross' _tag='input_image' name='remove' value='y' alt="{tr}Delete{/tr}"}
	</div>
	</form>
{/if}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
