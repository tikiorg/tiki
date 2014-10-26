{* $Id$ *}

{title help="forums" admpage="forums"}{tr}Forums{/tr}{/title}

{if $tiki_p_admin_forum eq 'y'}
  <div class="t_navbar form-group">
		{button href="tiki-admin_forums.php" class="btn btn-default" _text="{tr}Admin forums{/tr}"}
  </div>
{/if}

{if $channels or ($find ne '')}
	{if $prefs.feature_forums_search eq 'y' or $prefs.feature_forums_name_search eq 'y'}

				{if $prefs.feature_forums_name_search eq 'y'}
                    <div class="row form-group">
					    <form method="get" class="col-md-12 form-inline form-horizontal" role="form" action="tiki-forums.php">
                            <label class="control-label col-sm-2">{tr}Find{/tr}</label>
                            <div class="input-group col-sm-6">
                                <input type="text" name="find" class="form-control" value="{$find|escape}">
                            </div>
                            <div class="col-sm-2">
				    		    <input type="submit" class="btn btn-default" value="{tr}Search by name{/tr}" name="search">
					    	    <input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
                            </div>
                        </form>
                    </div>
				{/if}

				{if $prefs.feature_forums_search eq 'y' and $prefs.feature_search eq 'y'}
                    <div class="row form-group">
					    <form class="col-md-12 form-inline form-horizontal" method="get" role="form" action="{if $prefs.feature_search_fulltext neq 'y'}tiki-searchindex.php{else}tiki-searchresults.php{/if}">
                            {if $prefs.feature_forums_name_search neq 'y'}
                                <label class="control-label col-sm-2">{tr}Find{/tr}</label>
                            {else}
                                <div class="col-sm-2">&nbsp;</div>
                            {/if}
                            <div class="input-group col-sm-6">
                                <input name="highlight" type="text" class="form-control">
                            </div>
                            <div class="col-sm-2">
						        <input type="hidden" name="where" value="forums">
							    <input type="hidden" name="filter~type" value="forum post">
							    <input type="submit" class="wikiaction btn btn-default" name="search" value="{tr}Search in content{/tr}">
                            </div>
                        </form>
                    </div>
				{/if}
		{/if}
{/if}
<div class="table-responsive">
<table class="table table-striped normal">
	<tr>
		{assign var=numbercol value=1}
		<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
		 
		{if $prefs.forum_list_topics eq 'y'}
			{assign var=numbercol value=$numbercol+1}
			<th>{self_link _sort_arg='sort_mode' _sort_field='threads'}{tr}Topics{/tr}{/self_link}</th>
		{/if}	

		{if $prefs.forum_list_posts eq 'y'}
			{assign var=numbercol value=$numbercol+1}
			<th>{self_link _sort_arg='sort_mode' _sort_field='comments'}{tr}Posts{/tr}{/self_link}</th>
		{/if}	

		{if $prefs.forum_list_ppd eq 'y'}
			{assign var=numbercol value=$numbercol+1}
			<th>{tr}PPD{/tr}</th>
		{/if}	

		{if $prefs.forum_list_lastpost eq 'y'}	
			{assign var=numbercol value=$numbercol+1}
			<th>{self_link _sort_arg='sort_mode' _sort_field='lastPost'}{tr}Last Post{/tr}{/self_link}</th>
		{/if}

		{if $prefs.forum_list_visits eq 'y'}
			{assign var=numbercol value=$numbercol+1}
			<th>{self_link _sort_arg='sort_mode' _sort_field='hits'}{tr}Visits{/tr}{/self_link}</th>
		{/if}	
		
		{assign var=numbercol value=$numbercol+1}
		<th>{tr}Actions{/tr}</th>
	</tr>

	{assign var=section_old value=""}
	{section name=user loop=$channels}

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

		<tr>
			<td class="text">
				<span style="float:left">
					{if (isset($channels[user].individual) and $channels[user].individual eq 'n')
						or ($tiki_p_admin eq 'y') or ($channels[user].individual_tiki_p_forum_read eq 'y')}
						<a class="forumname" href="{$channels[user].forumId|sefurl:'forum'}">{$channels[user].name|addongroupname|escape}</a>
					{else}
						{$channels[user].name|addongroupname|escape}
					{/if}
				</span>
				{if $prefs.forum_list_desc eq 'y'}
					<br>
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
				<td class="text">
					{if isset($channels[user].lastPost)}
						{$channels[user].lastPost|tiki_short_datetime}<br>
						{if $prefs.forum_reply_notitle neq 'y'}<small><i>{$channels[user].lastPostData.title|escape}</i>{/if}
						{tr}by{/tr} {$channels[user].lastPostData.userName|username}</small>
					{/if}
				</td>
			{/if}
			{if $prefs.forum_list_visits eq 'y'}
				<td class="integer">{$channels[user].hits}</td>
			{/if}	

			<td class="action">
				{if $prefs.mobile_mode eq 'y'}<div class="navbar" data-role="controlgroup" data-type="horizontal">{/if} {* mobile *}
					<a {if $prefs.mobile_mode eq 'y'} data-role="button"{/if} class="admlink" title="{tr}View{/tr}" href="{$channels[user].forumId|sefurl:'forum'}">{icon _id='table' alt="{tr}View{/tr}"}</a> {* mobile *}
					{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
						<a {if $prefs.mobile_mode eq 'y'} data-role="button"{/if} class="admlink" title="{tr}Configure Forum{/tr}" href="tiki-admin_forums.php?forumId={$channels[user].forumId}&amp;cookietab=2">{icon _id='page_edit'}</a> {* mobile *}
					{/if}
				{if $prefs.mobile_mode eq 'y'}</div>{/if} {* mobile *}
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=$numbercol}
	{/section}
</table>
</div>

{pagination_links cant=$cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
