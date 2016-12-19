{* $Id$ *}
{title help="Blogs" admpage="blogs"}{tr}Blogs{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{if $tiki_p_create_blogs eq 'y' or $tiki_p_blog_admin eq 'y'}
		<div class="pull-left">
			{button href="tiki-edit_blog.php" _icon_name="create" _text="{tr}Create Blog{/tr}" _type="link" class="btn btn-link"}
			{if $tiki_p_read_blog eq 'y' and $tiki_p_blog_admin eq 'y'}
				{button href="tiki-list_posts.php" _type="link" class="btn btn-link" _icon_name="list" _text="{tr}List Posts{/tr}"}
			{/if}
		</div>
	{/if}
	{if $listpages or ($find ne '')}
		<div class="col-sm-5 pull-right">
			{include file='find.tpl'}
		</div>
	{/if}
</div>

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

<div class="{if $js === 'y'}table-responsive{/if}"> {*the table-responsive class cuts off dropdown menus *}
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
				<th class="text-right"><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'posts_desc'}posts_asc{else}posts_desc{/if}">{tr}Posts{/tr}</a></th>
			{/if}
			{if $prefs.blog_list_visits eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th class="text-right"><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a></th>
			{/if}
			{if $prefs.blog_list_activity eq 'y'}
				{assign var=numbercol value=$numbercol+1}
				<th><a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'activity_desc'}activity_asc{else}activity_desc{/if}">{tr}Activity{/tr}</a></th>
			{/if}
			{assign var=numbercol value=$numbercol+1}
			<th></th>
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
					<td class="integer">{$listpages[changes].posts}</td>
				{/if}
				{if $prefs.blog_list_visits eq 'y'}
					<td class="integer">{$listpages[changes].hits}</td>
				{/if}
				{if $prefs.blog_list_activity eq 'y'}
					<td class="integer">{$listpages[changes].activity}</td>
				{/if}
				<td class="action">
					{capture name=blog_actions}
						{strip}
							{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_read_blog eq 'y' )}
								{$libeg}<a href="{$listpages[changes].blogId|sefurl:blog}">
									{icon name="view" _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
								</a>{$liend}
							{/if}
							{if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y')}
								{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_blog_create_blog eq 'y' )}
									{$libeg}<a href="tiki-edit_blog.php?blogId={$listpages[changes].blogId}">
										{icon name="edit" _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
									</a>{$liend}
								{/if}
							{/if}
							{if $tiki_p_blog_post eq 'y'}
								{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_blog_post eq 'y' )}
									{if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y') or ($listpages[changes].public eq 'y')}
										{$libeg}<a href="tiki-blog_post.php?blogId={$listpages[changes].blogId}">
											{icon name="post" _menu_text='y' _menu_icon='y' alt="{tr}Post{/tr}"}
										</a>{$liend}
									{/if}
								{/if}
							{/if}
							{if $tiki_p_blog_admin eq 'y' and $listpages[changes].allow_comments eq 'y'}
								{$libeg}<a href='tiki-list_comments.php?types_section=blogs&amp;blogId={$listpages[changes].blogId}'>
									{icon name="comments" _menu_text='y' _menu_icon='y' alt="{tr}Comments{/tr}"}
								</a>{$liend}
							{/if}
							{if $tiki_p_admin eq 'y' || $tiki_p_assign_perm_blog eq 'y'}
								{$libeg}{permission_link mode=text type="blog" permType="blogs" id=$listpages[changes].blogId}{$liend}
							{/if}
							{if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y')}
								{if ($tiki_p_admin eq 'y') or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_tiki_p_blog_create_blog eq 'y' )}
									{$libeg}<a href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].blogId}">
										{icon name="delete" _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
									</a>{$liend}
								{/if}
							{/if}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.blog_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.blog_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=$numbercol}
		{/section}
	</table>
</div>
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
