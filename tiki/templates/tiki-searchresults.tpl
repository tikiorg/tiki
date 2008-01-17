{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-searchresults.tpl,v 1.31.2.3 2008-01-17 23:52:44 sylvieg Exp $ *}
{if !( $searchNoResults ) }
<h1>{tr}Search results{/tr}
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=search" title="{tr}Admin Feature{/tr}">{html_image file='pics/icons/wrench.png' border='0'  alt="{tr}Admin Feature{/tr}"}</a>
{/if}
</h1>
{/if}
<div class="nohighlight">
{if !( $searchStyle eq "menu" )}
<div class="nohighlight">
{tr}Search in{/tr}:<br />
<a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=pages">{tr}All{/tr}</a>
{if $prefs.feature_wiki eq 'y'}
 <a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=wikis">{tr}Wiki{/tr}</a>
{/if}
{if $prefs.feature_galleries eq 'y'}
 <a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=galleries">{tr}Galleries{/tr}</a>
 <a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=images">{tr}Images{/tr}</a>
{/if}
{if $prefs.feature_file_galleries eq 'y'}
 <a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=files">{tr}Files{/tr}</a>
{/if}
{if $prefs.feature_forums eq 'y'}
 <a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=forums">{tr}Forums{/tr}</a>
{/if}
{if $prefs.feature_faqs eq 'y'}
 <a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=faqs">{tr}FAQs{/tr}</a>
{/if}
{if $prefs.feature_blogs eq 'y'}
 <a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=blogs">{tr}Blogs{/tr}</a>
 <a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=posts">{tr}Blog Posts{/tr}</a>
{/if}
{if $prefs.feature_directory eq 'y'}
 <a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=directory">{tr}Directory{/tr}</a>
{/if}

{if $prefs.feature_articles eq 'y'}
 <a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=articles">{tr}Articles{/tr}</a>
{/if}
{if $prefs.feature_trackers eq 'y'}
 <a class="linkbut" href="tiki-searchresults.php?highlight={$words}&amp;where=trackers">{tr}Trackers{/tr}</a>
{/if}
</div><!--nohighlight-->
<br /><br />
{tr}Found{/tr} "{$words}" {tr}in{/tr} {if $where3}{$where2}: {$where3}{else}{$cant_results} {$where2}{/if}
{/if}
<form class="forms" method="get" action="tiki-searchresults.php">
    {tr}Find{/tr} <input id="fuser" name="highlight" size="14" type="text" accesskey="s" value="{$words}"/>
{if ( $searchStyle eq "menu" )}
    {tr}in{/tr}
    <select name="where">
    <option value="pages">{tr}Entire Site{/tr}</option>
    {if $prefs.feature_wiki eq 'y'}
       <option value="wikis">{tr}Wiki Pages{/tr}</option>
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
    </select>
{else}
    <input type="hidden" name="where" value="{$where|escape}" />
	{if $forumId}<input type="hidden" name="forumId" value="{$forumId}" />{/if}
{/if}
    <input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}"/>
</form>
</div><!--nohighlight-->

<div class="searchresults">
{if !($searchNoResults) }
<br /><br />
{section  name=search loop=$results}
<a href="{$results[search].href}&amp;highlight={$words}" class="wiki">{$results[search].pageName|strip_tags}</a> ({tr}Hits{/tr}: {$results[search].hits})
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
<div class="searchdesc">{$results[search].data|strip_tags}</div>
<div class="searchdate">{tr}Last modification date{/tr}: {$results[search].lastModif|tiki_long_datetime}</div><br />
{sectionelse}
{tr}No pages matched the search criteria{/tr}
{/section}
</div>

{if $cant_pages gt 0}<div class="mini">
{if $prev_offset >= 0}
[<a class="linkbut" href="tiki-searchresults.php?where={$where}&amp;highlight={$words}&amp;offset={$prev_offset}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="linkbut" href="tiki-searchresults.php?where={$where}&amp;highlight={$words}&amp;offset={$next_offset}">{tr}Next{/tr}</a>]
{/if}
</div>{/if}
{/if}
