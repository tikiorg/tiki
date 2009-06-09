{* $Id$ *}
{popup_init src="lib/overlib.js"}

{capture name=advanced_search_help}
		<ul><li>+ : {tr}A leading plus sign indicates that this word must be present in every object returned.{/tr}</li>
		<li>- : {tr}A leading minus sign indicates that this word must not be present in any row returned.{/tr}</li>
    	<li>{tr}By default (when neither plus nor minus is specified) the word is optional, but the object that contain it will be rated higher.{/tr}</li>
		<li>&lt; &gt; : {tr}These two operators are used to change a word's contribution to the relevance value that is assigned to a row.{/tr}</li>
		<li>( ) : {tr}Parentheses are used to group words into subexpressions.{/tr}</li>
		<li>~ : {tr}A leading tilde acts as a negation operator, causing the word's contribution to the object relevance to be negative. It's useful for marking noise words. An object that contains such a word will be rated lower than others, but will not be excluded altogether, as it would be with the - operator.{/tr}</li>
		<li>* : {tr}An asterisk is the truncation operator. Unlike the other operators, it should be appended to the word, not prepended.{/tr}</li>
		<li>&quot; : {tr}The phrase, that is enclosed in double quotes &quot;, matches only objects that contain this phrase literally, as it was typed.{/tr}</li></ul>
{/capture}


	<div class="nohighlight">
		{if !( $searchStyle eq "menu" )} 
			{title admpage="search" help="Search+User"}{tr}Search results{/tr}{/title}
        	{if $prefs.feature_search_show_object_filter eq 'y'}
			<div class="navbar">
                {tr}Search in{/tr}:<br />
									{button _auto_args='where,highlight,date' href="?where=pages" _text="{tr}Entire Site{/tr}" _selected_class='highlight' _selected="'$where'=='pages'"}
                {if $prefs.feature_calendar eq 'y'}
									{button _auto_args='where,highlight,date' href="?where=calendars" _text="{tr}Calendars{/tr}" _selected_class='highlight' _selected="'$where'=='calendars'"}
                {/if}
                {if $prefs.feature_wiki eq 'y'}
									{button _auto_args='where,highlight,date' href="?where=wikis" _text="{tr}Wiki Pages{/tr}" _selected_class='highlight' _selected="'$where'=='wikis'"}
                {/if}
                {if $prefs.feature_galleries eq 'y'}
									{button _auto_args='where,highlight,date' href="?where=galleries" _text="{tr}Galleries{/tr}" _selected_class='highlight' _selected="'$where'=='galleries'"}
									{button _auto_args='where,highlight,date' href="?where=images" _text="{tr}Images{/tr}" _selected_class='highlight' _selected="'$where'=='images'"}
                {/if}
                {if $prefs.feature_file_galleries eq 'y'}
									{button _auto_args='where,highlight,date' href="?where=files" _text="{tr}Files{/tr}" _selected_class='highlight' _selected="'$where'=='files'"}
                {/if}
                {if $prefs.feature_forums eq 'y'}
									{button _auto_args='where,highlight,date' href="?where=forums" _text="{tr}Forums{/tr}" _selected_class='highlight' _selected="'$where'=='forums'"}
                {/if}
                {if $prefs.feature_faqs eq 'y'}
									{button _auto_args='where,highlight,date' href="?where=faqs" _text="{tr}Faqs{/tr}" _selected_class='highlight' _selected="'$where'=='faqs'"}
                {/if}
                {if $prefs.feature_blogs eq 'y'}
									{button _auto_args='where,highlight,date' href="?where=blogs" _text="{tr}Blogs{/tr}" _selected_class='highlight' _selected="'$where'=='blogs'"}
									{button _auto_args='where,highlight,date' href="?where=posts" _text="{tr}Blogs Post{/tr}" _selected_class='highlight' _selected="'$where'=='posts'"}
                {/if}
                {if $prefs.feature_directory eq 'y'}
									{button _auto_args='where,highlight,date' href="?where=directory" _text="{tr}Directory{/tr}" _selected_class='highlight' _selected="'$where'=='directory'"}
                {/if}
                
                {if $prefs.feature_articles eq 'y'}
									{button _auto_args='where,highlight,date' href="?where=articles" _text="{tr}Articles{/tr}" _selected_class='highlight' _selected="'$where'=='articles'"}
                {/if}
                {if $prefs.feature_trackers eq 'y'}
									{button _auto_args='where,highlight,date' href="?where=trackers" _text="{tr}Trackers{/tr}" _selected_class='highlight' _selected="'$where'=='trackers'"}
                {/if}
			</div><!-- navbar -->
		{/if}
      {/if} 



{if $prefs.feature_search_show_search_box eq 'y'}
<form class="forms" method="get" action="tiki-searchresults.php">
    <label class="searchhighlight" for="highlight">{tr}Find{/tr} <input id="fuser" name="highlight" size="14" type="text" accesskey="s" value="{$words}" /></label>
		{if !( $searchStyle eq "menu" )} 
		<label class="searchboolean" for="boolean">{tr}Advanced search:{/tr}<input type="checkbox" name="boolean"{if $boolean eq 'y'} checked="checked"{/if} /></label>
		{add_help show='n' title="{tr}Help{/tr}" id="advanced_search_help"}
			{$smarty.capture.advanced_search_help}
		{/add_help}
		<label class="searchdate" for="date">{tr}Date Search:{/tr}
		<select name="date" onchange="javascript:submit()">
		{section name=date start=0 loop=12 step=1}	
		<option value="{$smarty.section.date.index}" {if $smarty.section.date.index eq $date}selected="selected"{/if}>{if $smarty.section.date.index eq 0}{tr}All dates{/tr}{else}{$smarty.section.date.index} {tr}Month{/tr}{/if}</option>
		{/section}
		</select>
		</label>
		{/if}

{if $prefs.feature_search_show_object_filter eq 'y'}
{if ( $searchStyle eq "menu" )}
<span class='searchMenu'>
    {tr}in{/tr}
    <select name="where">
    <option value="pages">{tr}Entire Site{/tr}</option>
    {if $prefs.feature_wiki eq 'y'}
       <option value="wikis">{tr}Wiki Pages{/tr}</option>
    {/if}
    {if $prefs.feature_calendar eq 'y'}
       <option value="calendars">{tr}Calendar Items{/tr}</option>
    {/if}
    {if $prefs.feature_galleries eq 'y'}
       <option value="galleries">{tr}Galleries{/tr}</option>
       <option value="images">{tr}Images{/tr}</option>
    {/if}
    {if $prefs.feature_file_galleries eq 'y'}
       <option value="files">{tr}Files{/tr}</option>
    {/if}
    {if $prefs.feature_forums eq 'y'}
       <option value="forums">{tr}Forums{/tr}</option>
    {/if}
    {if $prefs.feature_faqs eq 'y'}
       <option value="faqs">{tr}FAQs{/tr}</option>
    {/if}
    {if $prefs.feature_blogs eq 'y'}
       <option value="blogs">{tr}Blogs{/tr}</option>
       <option value="posts">{tr}Blog Posts{/tr}</option>
    {/if}
    {if $prefs.feature_directory eq 'y'}
       <option value="directory">{tr}Directory{/tr}</option>
    {/if}
    {if $prefs.feature_articles eq 'y'}
       <option value="articles">{tr}Articles{/tr}</option>
    {/if}
	{if $prefs.feature_trackers eq 'y'}
		<option value="trackers">{tr}Trackers{/tr}</option>
	{/if}
    </select>
   </span> 
{else}
    <input type="hidden" name="where" value="{$where|escape}" />
	{if $forumId}<input type="hidden" name="forumId" value="{$forumId}" />{/if}
{/if}
{/if}
    <label class="searchsubmit"><input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}" /></label>
</form>
{/if}

</div><!--nohighlight-->





{if $searchStyle ne "menu" and  ! $searchNoResults }
	<div class="highlight simplebox">
		 {tr}Found{/tr} "{$words}" {tr}in{/tr} {if $where3}{$where2}: {$where3}{else}{$cant_results} {$where2}{/if}
	</div>
{/if}

{if ! $searchNoResults }
	<div class="searchresults">
	<br /><br />
	{section  name=search loop=$results}
		{strip}
		{if $prefs.feature_search_show_object_type eq 'y'}
		  {if $results[search].type > ''}
				<b>{$results[search].type}::</b>
			{/if}
		{/if}
		{if !empty($results[search].parentName)}
			::<a href="{$results[search].parentHref}">{$results[search].parentName|escape}</a>&nbsp;-&gt;
		{/if}
		{/strip}
		<a href="{$results[search].href}&amp;highlight={$words}" class="wiki">{$results[search].pageName|strip_tags}</a>
		{if $prefs.feature_search_show_visit_count eq 'y'}
			<b>({tr}Hits{/tr}: {$results[search].hits})</b>
		{/if}

		{if $prefs.feature_search_show_pertinence eq 'y'}
    	{if $prefs.feature_search_fulltext eq 'y'}
				{if $results[search].relevance <= 0}
					&nbsp;({tr}Simple search{/tr})
        {else}
					&nbsp;({tr}Relevance{/tr}: {$results[search].relevance})
        {/if}
			{/if}
		{/if}    
		<br />
		<div class="searchdesc">{$results[search].data|strip_tags}</div>

		{if $prefs.feature_search_show_last_modification eq 'y'}
			<div class="searchdate">{tr}Last modification date{/tr}: {$results[search].lastModif|tiki_long_datetime}</div>
		{/if}
		<br/>
	{sectionelse}
		{tr}No pages matched the search criteria{/tr}
	{/section}
</div>
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{/if}
