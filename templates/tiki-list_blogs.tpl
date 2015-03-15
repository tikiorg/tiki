{* $Id$ *}
{title help="Blogs" admpage="blogs"}{tr}Blogs{/tr}{/title}

{if $tiki_p_create_blogs eq 'y' or $tiki_p_blog_admin eq 'y'}
	<div class="navbar">
		{button href="tiki-edit_blog.php" _text="{tr}Create Blog{/tr}" _class="navbar-btn"}
		{if $tiki_p_read_blog eq 'y' and $tiki_p_blog_admin eq 'y'}
			{button href="tiki-list_posts.php" class="btn btn-default" _text="{tr}List Blog Posts{/tr}"}
		{/if}
	</div>
{/if}

<div class="text-center">
	{if $listpages or ($find ne '')}
		{include file='find.tpl'}
	{/if}
</div>
<div class="table-responsive">
	<table class="table table-striped normal">
		{assign var=numbercol value=0}
		<tr>
			{if $prefs.blog_list_title eq 'y' or $prefs.blog_list_description eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Blog{/tr}</a></th>
			{/if}
			{if $prefs.blog_list_created eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></th>
			{/if}
			{if $prefs.blog_list_lastmodif eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last post{/tr}</a></th>
			{/if}
			{if $prefs.blog_list_user ne 'disabled'}
				{assign var=numbercol value=$numbercol+1}
				<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></th>
			{/if}
			{if $prefs.blog_list_posts eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'posts_desc'}posts_asc{else}posts_desc{/if}">{tr}Posts{/tr}</a></th>
			{/if}
			{if $prefs.blog_list_visits eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a></th>
			{/if}
			{if $prefs.blog_list_activity eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'activity_desc'}activity_asc{else}activity_desc{/if}">{tr}Activity{/tr}</a></th>
			{/if}
			{assign var=numbercol value=$numbercol+1}
			<th>{tr}Actions{/tr}</th>
		</tr>
		{section name=changes loop=$listpages}
			<tr>
				{if $prefs.blog_list_title eq 'y' or $prefs.blog_list_description eq 'y'}
					<td class="text">
						{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_read_blog eq 'y' )}
							<a class="blogname" href="{$listpages[changes].blogId|sefurl:blog}">
						{/if}
						{if $listpages[changes].title}
							{$listpages[changes].title|truncate:$prefs.blog_list_title_len:"...":true|escape}
						{else}
							&nbsp;
						{/if}
						{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_read_blog eq 'y' )}
							</a>
						{/if}
						{if $prefs.blog_list_description eq 'y'}
							<div class="help-block">{$listpages[changes].description|escape|nl2br}</div>
						{/if}
					</td>
				{/if}
				{if $prefs.blog_list_created eq 'y'}
					<td class="date">&nbsp;{$listpages[changes].created|tiki_short_date}&nbsp;</td><!--tiki_date_format:"%b %d" -->
				{/if}
				{if $prefs.blog_list_lastmodif eq 'y'}
					<td class="date">&nbsp;{$listpages[changes].lastModif|tiki_short_datetime}&nbsp;</td><!--tiki_date_format:"%d of %b [%H:%M]"-->
				{/if}
				{if $prefs.blog_list_user ne 'disabled'}
					{if $prefs.blog_list_user eq 'link'}
						<td class="username">&nbsp;{$listpages[changes].user|userlink}&nbsp;</td>
					{elseif $prefs.blog_list_user eq 'avatar'}
						<td>&nbsp;{$listpages[changes].user|avatarize}&nbsp;<br>
						&nbsp;{$listpages[changes].user|userlink}&nbsp;</td>
					{else}
						<td class="username">&nbsp;{$listpages[changes].user|escape}&nbsp;</td>
					{/if}
				{/if}
				{if $prefs.blog_list_posts eq 'y'}
					<td class="integer"><span class="badge">{$listpages[changes].posts}</span></td>
				{/if}
				{if $prefs.blog_list_visits eq 'y'}
					<td class="integer"><span class="badge">{$listpages[changes].hits}</span></td>
				{/if}
				{if $prefs.blog_list_activity eq 'y'}
					<td class="integer"><span class="badge">{$listpages[changes].activity}</span></td>
				{/if}
				<td class="action">
					{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_read_blog eq 'y' )}
						<a class="tips" href="{$listpages[changes].blogId|sefurl:blog}" title=":{tr}View{/tr}">{icon name="view"}</a>
					{/if}
					{if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y')}
						{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_blog_create_blog eq 'y' )}
							<a class="tips" href="tiki-edit_blog.php?blogId={$listpages[changes].blogId}" title=":{tr}Edit{/tr}">{icon name="edit"}</a>
						{/if}
					{/if}
					{if $tiki_p_blog_post eq 'y'}
						{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_blog_post eq 'y' )}
							{if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y') or ($listpages[changes].public eq 'y')}
								<a class="tips" href="tiki-blog_post.php?blogId={$listpages[changes].blogId}" title=":{tr}Post{/tr}">{icon name="post"}</a>
							{/if}
						{/if}
					{/if}
					{if $tiki_p_blog_admin eq 'y' and $listpages[changes].allow_comments eq 'y'}
						<a class='tips' href='tiki-list_comments.php?types_section=blogs&amp;blogId={$listpages[changes].blogId}' title=":{tr}Comments{/tr}">{icon name="comments"}</a>
					{/if}
					{if $tiki_p_admin eq 'y' || $tiki_p_assign_perm_blog eq 'y'}
						{permission_link mode=icon type="blog" permType="blogs" id=$listpages[changes].blogId title=$listpages[changes].title}
					{/if}
					{if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y')}
						{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_blog_create_blog eq 'y' )}
							<a class="tips" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].blogId}" title=":{tr}Delete{/tr}">{icon name="delete"}</a>
						{/if}
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=$numbercol}
		{/section}
	</table>
</div>
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
