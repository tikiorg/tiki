{if !isset($show_heading) or $show_heading neq "n"}
<div class="breadcrumbs"><a class="link" href="tiki-list_blogs.php">{tr}Blogs{/tr}</a> {$prefs.site_crumb_seper} {$title}</div>
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
		<input type="text" name="find" value="{$find|regex_replace:"/\"/":"'"}" /> 
		<input type="submit" name="search" value="{tr}Find{/tr}" />
		</form>

       {* <!--
          {tr}Sort posts by:{/tr}
          <a class="bloglink" href="tiki-view_blog.php?blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Date{/tr}</a>
        -->	*}
	</div>
{/if}
{/if}

{section name=ix loop=$listpages}
<div class="post{if !empty($container_class)} {$container_class}{/if}">
	<div class="clearfix postbody">
		<div class="author_actions clearfix">
			<div class="actions">
            {if ($ownsblog eq 'y') or ($user and $listpages[ix].user eq $user) or $tiki_p_blog_admin eq 'y'}
              <a class="blogt" href="tiki-blog_post.php?blogId={$listpages[ix].blogId}&amp;postId={$listpages[ix].postId}">{icon _id='page_edit'}</a> 
              &nbsp;
              <a class="blogt" href="tiki-view_blog.php?blogId={$blogId}&amp;remove={$listpages[ix].postId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
            {/if}

            {if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
              <a title="{tr}Save to notepad{/tr}" href="tiki-view_blog.php?blogId={$blogId}&amp;savenotepad={$listpages[ix].postId}">{icon _id='disk'
							alt='{tr}Save to notepad{/tr}'}</a>
            {/if}
			</div>
			<div class="author_info">
				{if $use_title eq 'y'}
					{tr}By{/tr} {$listpages[ix].user|userlink}
				{if $show_avatar eq 'y'}
					{$listpages[ix].avatar}
				{/if}
				{tr}on{/tr} {$listpages[ix].created|tiki_short_datetime}
				{else}
					{tr}By{/tr} {$listpages[ix].user}
					{if $show_avatar eq 'y'}
						{$listpages[ix].avatar}
					{/if}
				{/if}
			</div>
		</div>

		<a name="postId{$listpages[ix].postId}"></a> {* ?? *}

		<div class="clearfix postbody-title">
			<div class="title"> {* because used in forums, but I don't know purpose *}
				{if $use_title eq 'y'}
					<h2>{$listpages[ix].title}</h2>
				{else}
					<h2>{$listpages[ix].created|tiki_short_datetime}</h2>
				{/if}
			</div>
	  
			{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y'}
				{if $listpages[ix].freetags.data|@count >0}
				<div class="freetaglist">{tr}Tags{/tr}:
					{foreach from=$listpages[ix].freetags.data item=taginfo}
						{capture name=tagurl}{if (strstr($taginfo.tag, ' '))}"{$taginfo.tag}"{else}{$taginfo.tag}{/if}{/capture}
						<a class="freetag" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$taginfo.tag|escape}</a>
					{/foreach}
				</div>
				{/if}
			{/if}
	</div> <!-- posthead -->
	{*<div class="content">
	<div class="postbody-content">*}
		{$listpages[ix].parsed_data}
		{if $listpages[ix].pages > 1}
			<a class="link" href="{$listpages[ix].postId|sefurl:blogpost}">
			{tr}Read more{/tr} ({$listpages[ix].pages} {tr}pages{/tr})</a>
		{/if}
		{if $prefs.blogs_feature_copyrights  eq 'y' and $prefs.wikiLicensePage}
        	{if $prefs.wikiLicensePage == $page}
        		{if $tiki_p_edit_copyrights eq 'y'}
					<div class="editdate">
						{tr}To edit the copyright notices{/tr} 
						<a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.
					</div>
				{/if}
			{else}
				<div class="editdate">
					{tr}The content on this page is licensed under the terms of the{/tr} 
            		<a href="tiki-index.php?page={$prefs.wikiLicensePage}&amp;copyrightpage={$page|escape:"url"}">
					{$prefs.wikiLicensePage}</a>.
				</div>
			{/if}
		{/if}
		<div class="postfooter">
			<div class="status"> {* renamed to match forum footer layout *}
				<a href='tiki-print_blog_post.php?postId={$listpages[ix].postId}'>{icon _id='printer' alt='{tr}Print{/tr}'}</a>
				<a href='tiki-send_blog_post.php?postId={$listpages[ix].postId}'>{icon _id='email' alt='{tr}Email This Post{/tr}'}</a>
			</div>
			<div class="actions"> {* renamed to match forum footer layout *}
				<a class="link" href="{$listpages[ix].postId|sefurl:blogpost}">{tr}Permalink{/tr}</a>
				{if $allow_comments eq 'y' and $prefs.feature_blogposts_comments eq 'y'}
					<a class="link" href="tiki-view_blog_post.php?find={$find}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;postId={$listpages[ix].postId}&amp;show_comments=1">
					{$listpages[ix].comments}
					{if $listpages[ix].comments == 1}
						{tr}comment{/tr}
					{else}
						{tr}comments{/tr}</a>
					{/if}
				{/if}
			</div>
		</div>
	</div> <!-- postbody -->
</div> <!--blogpost -->
{/section}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

{if $prefs.feature_blog_comments == 'y'
  && (($tiki_p_read_comments  == 'y'
  && $comments_cant != 0)
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}

  <div id="page-bar">
  	   {include file=comments_button.tpl}
  </div>

  {include file=comments.tpl}
{/if}
