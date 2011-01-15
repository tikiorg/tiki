{* $Id$ *}

{title help="forums" admpage="forums"}{tr}Forums{/tr}{/title}

{if $tiki_p_admin_forum eq 'y'}
  <div class="navbar">
		{button href="tiki-admin_forums.php" _text="{tr}Admin forums{/tr}"}
  </div>
{/if}

{if $channels or ($find ne '')}
	{if $prefs.feature_forums_search eq 'y' or $prefs.feature_forums_name_search eq 'y'}
		<table class="findtable">
			<tr>
				<td class="findtable">{tr}Find{/tr}</td>
				{if $prefs.feature_forums_name_search eq 'y'}
					<td class="findtable">
						<form method="get" action="tiki-forums.php">
							<input type="text" name="find" value="{$find|escape}" />
							<input type="submit" value="{tr}Search by name{/tr}" name="search" />
							<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
						</form>
					</td>
				{/if}

				{if $prefs.feature_forums_search eq 'y' and $prefs.feature_search eq 'y'}
					<td>
						<form class="forms" method="get" action="{if $prefs.feature_forum_local_tiki_search eq 'y'}tiki-searchindex.php{else}tiki-searchresults.php{/if}">
							<input name="highlight" size="30" type="text" />
							<input type="hidden" name="where" value="forums" />
							<input type="submit" class="wikiaction" name="search" value="{tr}Search in content{/tr}"/>
						</form>
					</td>
				{/if}
			</tr>
		</table>
	{/if}
{/if}
<table class="normal">
	<tr>
		{assign var=numbercol value=1}
		<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
		 
		{if $prefs.forum_list_topics eq 'y'}
			{assign var=numbercol value=`$numbercol+1`}
			<th>{self_link _sort_arg='sort_mode' _sort_field='threads'}{tr}Topics{/tr}{/self_link}</th>
		{/if}	

		{if $prefs.forum_list_posts eq 'y'}
			{assign var=numbercol value=`$numbercol+1`}
			<th>{self_link _sort_arg='sort_mode' _sort_field='comments'}{tr}Posts{/tr}{/self_link}</th>
		{/if}	

		{if $prefs.forum_list_ppd eq 'y'}
			{assign var=numbercol value=`$numbercol+1`}
			<th>{tr}PPD{/tr}</th>
		{/if}	

		{if $prefs.forum_list_lastpost eq 'y'}	
			{assign var=numbercol value=`$numbercol+1`}
			<th>{self_link _sort_arg='sort_mode' _sort_field='lastPost'}{tr}Last Post{/tr}{/self_link}</th>
		{/if}

		{if $prefs.forum_list_visits eq 'y'}
			{assign var=numbercol value=`$numbercol+1`}
			<th>{self_link _sort_arg='sort_mode' _sort_field='hits'}{tr}Visits{/tr}{/self_link}</th>
		{/if}	
		
		{assign var=numbercol value=`$numbercol+1`}
		<th>{tr}Actions{/tr}</th>
	</tr>

	{assign var=section_old value=""}
	{section name=user loop=$channels}
		{cycle values="odd,even" print=false}
		{assign var=section value=$channels[user].section}
		{if $section ne $section_old}
			{assign var=section_old value=$section}
			{if ($tiki_p_admin eq 'y' or $tiki_p_admin_forum eq 'y')} 
				<tr>
					<td class="third" colspan="7">{$section|escape}</td>
				</tr>
			{else}
				<tr>
					<td class="third" colspan="6">{$section|escape}</td>
				</tr>
			{/if}
		{/if}

		<tr class="{cycle}">
			<td>
				<span style="float:left">
					{if ($channels[user].individual eq 'n') or ($tiki_p_admin eq 'y') or ($channels[user].individual_tiki_p_forum_read eq 'y')}
						<a class="forumname" href="tiki-view_forum.php?forumId={$channels[user].forumId}">{$channels[user].name|escape}</a>
					{else}
						{$channels[user].name|escape}
					{/if}
				</span>
				{if $prefs.forum_list_desc eq 'y'}
					<br />
					<div class="subcomment">
						{capture name="parsedDesc"}{wiki}{$channels[user].description}{/wiki}{/capture}
						{if strlen($smarty.capture.parsedDesc) < $prefs.forum_list_description_len}
							{$smarty.capture.parsedDesc}
						{else}
							{$smarty.capture.parsedDesc|strip_tags|truncate:$prefs.forum_list_description_len:"...":true}
						{/if}
					</div>
				{/if}
			</td>
			{if $prefs.forum_list_topics eq 'y'}
				<td class="integer">{$channels[user].threads}</td>
			{/if}
			{if $prefs.forum_list_posts eq 'y'}
				<td class="integer">{$channels[user].comments}</td>
			{/if}
			{if $prefs.forum_list_ppd eq 'y'}
				<td class="integer">{$channels[user].posts_per_day|string_format:"%.2f"}</td>
			{/if}
			{if $prefs.forum_list_lastpost eq 'y'}	
				<td>
					{if isset($channels[user].lastPost)}
						{$channels[user].lastPost|tiki_short_datetime}<br />
						{if $prefs.forum_reply_notitle neq 'y'}<small><i>{$channels[user].lastPostData.title|escape}</i>{/if}
						{tr}by{/tr} {$channels[user].lastPostData.userName|username}</small>
					{/if}
				</td>
			{/if}
			{if $prefs.forum_list_visits eq 'y'}
				<td class="integer">{$channels[user].hits}</td>
			{/if}	

			<td class="action">
				<a class="admlink" title="{tr}View{/tr}" href="tiki-view_forum.php?forumId={$channels[user].forumId}">{icon _id='table' alt="{tr}View{/tr}"}</a>
				{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
					<a class="admlink" title="{tr}Configure Forum{/tr}" href="tiki-admin_forums.php?forumId={$channels[user].forumId}&amp;cookietab=2">{icon _id='page_edit'}</a>
				{/if}
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan="$numbercol"}
	{/section}
</table>

{pagination_links cant=$cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
