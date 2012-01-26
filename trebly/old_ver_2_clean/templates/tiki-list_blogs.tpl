{* $Id$ *}
{title help="Blogs" admpage="blogs"}{tr}Blogs{/tr}{/title}

{if $tiki_p_create_blogs eq 'y'}
  <div class="navbar">
		{button href="tiki-edit_blog.php" _text="{tr}Create New Blog{/tr}"}
	</div>
{/if}
<div align="center">

{if $listpages or ($find ne '')}
  {include file='find.tpl'}
{/if}

<table class="normal">
{assign var=numbercol value=0}
<tr>
{if $prefs.blog_list_title eq 'y' or $prefs.blog_list_description eq 'y'}
	{assign var=numbercol value=`$numbercol+1`}
	<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Blog{/tr}</a></th>
{/if}
{if $prefs.blog_list_created eq 'y'}
	{assign var=numbercol value=`$numbercol+1`}
	<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></th>
{/if}
{if $prefs.blog_list_lastmodif eq 'y'}
	{assign var=numbercol value=`$numbercol+1`}
	<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last post{/tr}</a></th>
{/if}
{if $prefs.blog_list_user ne 'disabled'}
	{assign var=numbercol value=`$numbercol+1`}
	<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></th>
{/if}
{if $prefs.blog_list_posts eq 'y'}
	{assign var=numbercol value=`$numbercol+1`}
	<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'posts_desc'}posts_asc{else}posts_desc{/if}">{tr}Posts{/tr}</a></th>
{/if}
{if $prefs.blog_list_visits eq 'y'}
	{assign var=numbercol value=`$numbercol+1`}
	<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a></th>
{/if}
{if $prefs.blog_list_activity eq 'y'}
	{assign var=numbercol value=`$numbercol+1`}
	<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'activity_desc'}activity_asc{else}activity_desc{/if}">{tr}Activity{/tr}</a></th>
{/if}
{assign var=numbercol value=`$numbercol+1`}
<th>{tr}Action{/tr}</th>
</tr>

{cycle values="odd,even" print=false}
{section name=changes loop=$listpages}
<tr class="{cycle}">
{if $prefs.blog_list_title eq 'y' or $prefs.blog_list_description eq 'y'}
	<td class="text">
		{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_read_blog eq 'y' ) }
			<a class="blogname" href="{$listpages[changes].blogId|sefurl:blog}" title="{$listpages[changes].title|escape}">
		{/if}
		{if $listpages[changes].title}
			{$listpages[changes].title|truncate:$prefs.blog_list_title_len:"...":true|escape}
		{else}
			&nbsp;
		{/if}
		{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_read_blog eq 'y' ) }
			</a>
		{/if}
		{if $prefs.blog_list_description eq 'y'}
			<div class="subcomment">{$listpages[changes].description|escape|nl2br}</div>
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
	<td>&nbsp;{$listpages[changes].user|avatarize}&nbsp;<br />
	&nbsp;{$listpages[changes].user|userlink}&nbsp;</td>
{else}
	<td class="username">&nbsp;{$listpages[changes].user|escape}&nbsp;</td>
{/if}
{/if}
{if $prefs.blog_list_posts eq 'y'}
	<td class="integer">&nbsp;{$listpages[changes].posts}&nbsp;</td>
{/if}
{if $prefs.blog_list_visits eq 'y'}
	<td class="integer">&nbsp;{$listpages[changes].hits}&nbsp;</td>
{/if}
{if $prefs.blog_list_activity eq 'y'}	
	<td class="integer">&nbsp;{$listpages[changes].activity}&nbsp;</td>
{/if}
<td class="action">
	{if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y')}
		{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_blog_create_blog eq 'y' ) }
			<a class="icon" href="tiki-edit_blog.php?blogId={$listpages[changes].blogId}">{icon _id='page_edit'}</a>
		{/if}
	{/if}
	{if $tiki_p_blog_post eq 'y'}
		{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_blog_post eq 'y' ) }
			{if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y') or ($listpages[changes].public eq 'y')}
				<a class="icon" href="tiki-blog_post.php?blogId={$listpages[changes].blogId}">{icon _id='pencil_add' alt="{tr}Post{/tr}"}</a>
			{/if}
		{/if}
	{/if}
	{if $tiki_p_blog_admin eq 'y' and $listpages[changes].allow_comments eq 'y'}
		<a class='icon' href='tiki-list_comments.php?types_section=blogs&blogId={$listpages[changes].blogId}'>{icon _id='comments' alt="{tr}List all comments{/tr}" title="{tr}List all comments{/tr}"}</a>
	{/if}
	{if $tiki_p_admin eq 'y' || $tiki_p_assign_perm_blog eq 'y'}
	    {if $listpages[changes].individual eq 'y'}
		<a class="icon" href="tiki-objectpermissions.php?objectName={$listpages[changes].title|escape:"url"}&amp;objectType=blog&amp;permType=blogs&amp;objectId={$listpages[changes].blogId}">{icon _id='key_active' alt="{tr}Active Perms{/tr}"}</a>
	    {else}
		<a class="icon" href="tiki-objectpermissions.php?objectName={$listpages[changes].title|escape:"url"}&amp;objectType=blog&amp;permType=blogs&amp;objectId={$listpages[changes].blogId}">{icon _id='key' alt="{tr}Perms{/tr}"}</a>
	    {/if}
	{/if}
        {if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y')}
                {if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_blog_create_blog eq 'y' ) }
                        &nbsp;&nbsp;<a class="icon" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].blogId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
                {/if}
        {/if}
	
</td>
</tr>
{sectionelse}
	{norecords _colspan=$numbercol}
{/section}
</table>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
</div>
