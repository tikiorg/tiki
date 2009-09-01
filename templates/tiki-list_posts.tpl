{* $Id$ *}

{title help="Blogs"}{tr}Blogs{/tr}{/title}

<div class="navbar">
	{button href="tiki-edit_blog.php" _text="{tr}Edit Blog{/tr}"}
	{button href="tiki-blog_post.php" _text="{tr}Post{/tr}"}
	{button href="tiki-list_blogs.php" _text="{tr}List Blogs{/tr}"}
</div>

{if $listpages or ($find ne '')}
  {include file='find.tpl'}
{/if}

<table class="normal">
	<tr>
		<th>
			<a href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'postId_desc'}postId_asc{else}postId_desc{/if}">{tr}Id{/tr}</a>
		</th>
		<th>{tr}Blog Title{/tr}</th>
		<th>
			<a href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a>
		</th>
		<th>{tr}Size{/tr}</th>
		<th>
			<a href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a>
		</th>
		<th>{tr}Action{/tr}</th>
	</tr>

	{cycle values="odd,even" print=false}
	{section name=changes loop=$listpages}
		<tr>
			<td class="{cycle advance=false}">&nbsp;{$listpages[changes].postId}&nbsp;</td>
			<td class="{cycle advance=false}">
				&nbsp;
				<a class="blogname" href="tiki-edit_blog.php?blogId={$listpages[changes].blogId}" title="{$listpages[changes].blogTitle}">{$listpages[changes].blogTitle|truncate:$prefs.blog_list_title_len:"...":true|escape}</a>
				&nbsp;
			</td>
			<td class="{cycle advance=false}">&nbsp;{$listpages[changes].created|tiki_short_datetime}&nbsp;</td>
			<td class="{cycle advance=false}">&nbsp;{$listpages[changes].size}&nbsp;</td>
			<td class="{cycle advance=false}">&nbsp;{$listpages[changes].user}&nbsp;</td>
			<td class="{cycle}">
				<a class="link" href="tiki-blog_post.php?postId={$listpages[changes].postId}">{icon _id='page_edit'}</a>
				<a class="link" href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].postId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
			</td>
		</tr>
	{sectionelse}
		<tr>
			<td colspan="6" class="odd">
				<b>{tr}No records found{/tr}</b>
			</td>
		</tr>
	{/section}
</table>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
