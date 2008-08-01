{* $Id$ *}
{if !( $searchNoResults ) }
<h1>{tr}Search results{/tr}:</h1>
{/if}

{if !( $searchStyle eq "menu" )}
  <div class="nohighlight navbar">
    <p>{tr}Search in{/tr}:</p>
	{foreach item=name key=k from=$where_list}
      	<a class="linkbut" {if $where eq $k}id="highlight"{/if} href="tiki-searchindex.php?highlight={$words}&amp;where={$k}">{tr}{$name}{/tr}</a>
   	{/foreach}
  </div><!--nohighlight-->
  {* do not change the comment below, since smarty 'highlight' outputfilter is hardcoded to find exactly this... instead you may experience white pages as results *}

{/if}
<form class="forms" method="get" action="tiki-searchindex.php">
    {tr}Find{/tr} <input id="fuser" name="highlight" size="14" type="text" accesskey="s" value="{$words}"/>
{if ( $searchStyle eq "menu" )}
    {tr}in{/tr}
    <select name="where">
	{if empty($where_list)}
		<option value="pages">{tr}Entire Site{/tr}</option>
		{if $prefs.feature_wiki eq 'y'}<option value="wikis">{tr}Wiki Pages{/tr}</option>{/if}
		{if $prefs.feature_galleries eq 'y'}<option value="galleries">{tr}Galleries{/tr}</option>{/if}
		{if $prefs.feature_galleries eq 'y'}<option value="images">{tr}Images{/tr}</option>{/if}
		{if $prefs.feature_file_galleries eq 'y'}<option value="files">{tr}Files{/tr}</option>{/if}
		{if $prefs.feature_faqs eq 'y'}<option value="faqs">{tr}Faqs{/tr}</option>{/if}
		{if $prefs.feature_forums eq 'y'}<option value="forums">{tr}Forums{/tr}</option>{/if}
		{if $prefs.feature_blogs eq 'y'}<option value="blogs">{tr}Blogs{/tr}</option>{/if}
		{if $prefs.feature_directory eq 'y'}<option value="directory">{tr}Directory{/tr}</option>{/if}
		{if $prefs.feature_articles eq 'y'}<option value="articles">{tr}Articles{/tr}</option>{/if}
		{if $prefs.feature_trackers eq 'y'}<option value="trackers">{tr}Trackers{/tr}</option>{/if}
	{else}
	    {foreach item=name key=k from=$where_list}
    		<option value="{$k}">{tr}{$name}{/tr}</option>
    	{/foreach}
	{/if}
    </select>
{else}
    <input type="hidden" name="where" value="{$where|escape}" />
{/if}
    <input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}"/>
</form>

<br />
{if $words neq ''}
  {tr}Found{/tr} "{$words}" {tr}in{/tr} {$cant_results} {$where2}
{/if}

{if $words neq ''}
<div class="searchresults">
{if !($searchNoResults) }
<br /><br />
{section name=search loop=$results}
<b>{tr}{$results[search].location}{/tr}:&nbsp;<a href="{$results[search].href}&amp;highlight={$words}{$results[search].anchor}" class="wiki">{$results[search].pageName|strip_tags|escape}</a> ({tr}Hits{/tr}: {$results[search].hits})</b>
{if $prefs.feature_search_fulltext eq 'y'}
	{if $results[search].relevance <= 0}
		&nbsp;({tr}Simple search{/tr})
	{else}
		&nbsp;({tr}Relevance{/tr}: {$results[search].relevance})
	{/if}
{/if}
{if $results[search].type > ''}
&nbsp;({$results[search].type})
{/if}

<br />
<div class="searchdesc">{$results[search].data|strip_tags|truncate:250:'...'}</div>
<div class="searchdate">{tr}Last modification date{/tr}: {$results[search].lastModif|tiki_long_datetime}</div><br />
{sectionelse}
{tr}No pages matched the search criteria{/tr}
{/section}
{/if}
</div>

{* PAGINATION *}
{pagination_links cant=$cant_results step=$maxRecords offset=$offset}{/pagination_links} 
{* END OF PAGINATION *}

{/if}
