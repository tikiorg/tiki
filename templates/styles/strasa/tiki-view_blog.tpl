{* $Id$ *}
<a class="link" href="tiki-list_blogs.php">{tr}Blogs{/tr}</a> {$prefs.site_crumb_seper} {$title|escape}
{if strlen($heading) > 0}
	{eval var=$heading}
{else}
	{include file="blog-heading.tpl"}
{/if}
{if $use_find eq 'y'}
	<div class="blogtools">
		<form action="tiki-view_blog.php" method="get">
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
			<input type="hidden" name="blogId" value="{$blogId|escape}" />
	{tr}Find:{/tr} 
			<input type="text" name="find" value="{$find|escape}" /> 
			<input type="submit" name="search" value="{tr}Find{/tr}" />
		</form>
	</div>
{/if}

{section name=ix loop=$listpages}
<div class="post">
	{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y'}
		{if $listpages[ix].freetags.data|@count >0}
	<div class="freetaglist">
			{foreach from=$listpages[ix].freetags.data item=taginfo}
			{capture name=tagurl}{if (strstr($taginfo.tag, ' '))}"{$taginfo.tag}"{else}{$taginfo.tag}{/if}{/capture}
		<a class="freetag" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$taginfo.tag}</a>
			{/foreach}
	</div>
	    {/if}
	{/if}
			
	<div class="clearfix postbody">
		
		<div class="clearfix postbody-title">
	{if $show_avatar eq 'y'}
		{$listpages[ix].avatar}
	{/if}
			<div class="title">
				<h2>{$listpages[ix].title|escape}</h2>
			</div>
			
		</div><!-- postbody-title -->

		<div class="author_actions clearfix">
			<div class="actions">
	{if ($ownsblog eq 'y') or ($user and $listpages[ix].user eq $user) or $tiki_p_blog_admin eq 'y'}
				<a class="blogt" href="tiki-blog_post.php?blogId={$listpages[ix].blogId}&amp;postId={$listpages[ix].postId}">{icon _id='page_edit'}</a> 
				<a class="blogt" href="tiki-view_blog.php?blogId={$blogId}&amp;remove={$listpages[ix].postId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
	{/if}

	{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
				<a title="{tr}Save to notepad{/tr}" href="tiki-view_blog.php?blogId={$blogId}&amp;savenotepad={$listpages[ix].postId}">{icon _id='disk'	alt='{tr}Save to notepad{/tr}'}</a>
	{/if}
			</div>
			
			<div class="author_info">
		{tr}By{/tr} {$listpages[ix].user|userlink}
		{tr}on{/tr} {$listpages[ix].created|tiki_short_datetime}

			</div>
		</div><!-- author_actions -->

		<div class="content">
			<div class="postbody-content">
	{$listpages[ix].parsed_data}
	{if $listpages[ix].pages > 1}
				<a class="link" href="{$listpages[ix].postId|sefurl:blogpost}">{tr}Read more{/tr} ({$listpages[ix].pages} {tr}pages{/tr})</a>
	{/if}
	{if $prefs.blogs_feature_copyrights  eq 'y' and $prefs.wikiLicensePage}
		{if $prefs.wikiLicensePage == $page}
			{if $tiki_p_edit_copyrights eq 'y'}
				<div class="editdate">
				{tr}To edit the copyright notices{/tr} 
					<a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.
				</div><!-- editdate -->
			{/if}
		{else}
				<div class="editdate">
			{tr}The content on this page is licensed under the terms of the{/tr} 
					<a href="tiki-index.php?page={$prefs.wikiLicensePage}&amp;copyrightpage={$page|escape:"url"}">{$prefs.wikiLicensePage}</a>.
				</div><!-- editdate -->
		{/if}
	{/if}
				<div class="postfooter">
					<div class="status">{* renamed to match forum footer layout *}
						<a href='tiki-print_blog_post.php?postId={$listpages[ix].postId}'>{icon _id='printer' alt='{tr}Print{/tr}'}</a>
					</div><!-- status -->
					<div class="actions">{* renamed to match forum footer layout *}
						<a class="link" href="{$listpages[ix].postId|sefurl:blogpost}">{tr}Permalink{/tr}</a>
	{if $allow_comments eq 'y' and $prefs.feature_blogposts_comments eq 'y'}
						<a class="link" href="tiki-view_blog_post.php?find={$find|escape:url}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;postId={$listpages[ix].postId}&amp;show_comments=1">{$listpages[ix].comments}
						{if $listpages[ix].comments == 1}{tr}comment{/tr}{else}{tr}comments{/tr}{/if}</a>
	{/if}
					</div><!-- actions -->
				</div><!-- postfooter -->
		
			</div><!-- postbody-content -->
		</div><!--content -->
	</div><!-- postbody -->
</div><!--post-->
{/section}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

