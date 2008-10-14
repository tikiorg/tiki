{* $Id$ *}
{popup_init src="lib/overlib.js"}
{if !( $searchNoResults ) }
  {title admpage="search"}{tr}Search results{/tr}{/title}
{/if}

<div class="nohighlight">
{if !( $searchStyle eq "menu" )}
<div class="navbar">
{tr}Search in{/tr}:<br />
<a href="{$smarty.server.PHP_SELF}?{query where=pages}"{if $where eq 'pages'} class='highlight'{/if}>{tr}All{/tr}</a>
{if $prefs.feature_wiki eq 'y'}
 <a href="{$smarty.server.PHP_SELF}?{query where=wikis}"{if $where eq 'wikis'} class='highlight'{/if}>{tr}Wiki{/tr}</a>
{/if}
{if $prefs.feature_galleries eq 'y'}
 <a href="{$smarty.server.PHP_SELF}?{query where=galleries}"{if $where eq 'galleries'} class='highlight'{/if}>{tr}Galleries{/tr}</a>
 <a href="{$smarty.server.PHP_SELF}?{query where=images}"{if $where eq 'images'} class='highlight'{/if}>{tr}Images{/tr}</a>
{/if}
{if $prefs.feature_file_galleries eq 'y'}
 <a href="{$smarty.server.PHP_SELF}?{query where=files}{if $where eq 'files'} class='highlight'{/if}">{tr}Files{/tr}</a>
{/if}
{if $prefs.feature_forums eq 'y'}
 <a href="{$smarty.server.PHP_SELF}?{query where=forums}"{if $where eq 'forums'} class='highlight'{/if}>{tr}Forums{/tr}</a>
{/if}
{if $prefs.feature_faqs eq 'y'}
 <a href="{$smarty.server.PHP_SELF}?{query where=faqs}"{if $where eq 'faqs'} class='highlight'{/if}>{tr}FAQs{/tr}</a>
{/if}
{if $prefs.feature_blogs eq 'y'}
 <a href="{$smarty.server.PHP_SELF}?{query where=blogs}"{if $where eq 'blogs'} class='highlight'{/if}>{tr}Blogs{/tr}</a>
 <a href="{$smarty.server.PHP_SELF}?{query where=posts}"{if $where eq 'posts'} class='highlight'{/if}>{tr}Blog Posts{/tr}</a>
{/if}
{if $prefs.feature_directory eq 'y'}
 <a href="{$smarty.server.PHP_SELF}?{query where=directory}"{if $where eq 'directory'} class='highlight'{/if}>{tr}Directory{/tr}</a>
{/if}

{if $prefs.feature_articles eq 'y'}
 <a href="{$smarty.server.PHP_SELF}?{query where=articles}"{if $where eq 'articles'} class='highlight'{/if}>{tr}Articles{/tr}</a>
{/if}
{if $prefs.feature_trackers eq 'y'}
 <a href="{$smarty.server.PHP_SELF}?{query where=trackers}"{if $where eq 'trackers'} class='highlight'{/if}>{tr}Trackers{/tr}</a>
{/if}
</div><!-- navbar -->
{/if}
<form class="forms" method="get" action="tiki-searchresults.php">
	{if !( $searchStyle eq "menu" )}
		<label for="boolean">{tr}Boolean search:{/tr}<input type="checkbox" name="boolean"{if $boolean eq 'y'} checked="checked"{/if} /></label>
		<a {popup text="<ul><li>+ : {tr}A leading plus sign indicates that this word must be present in every object returned.{/tr}</li>
		<li>- : {tr}A leading minus sign indicates that this word must not be present in any row returned.{/tr}</li>
    	<li>{tr}By default (when neither plus nor minus is specified) the word is optional, but the object that contain it will be rated higher.{/tr}</li>
		<li>< > : {tr}These two operators are used to change a word's contribution to the relevance value that is assigned to a row.{/tr}</li>
		<li>( ) : {tr}Parentheses are used to group words into subexpressions.{/tr}</li>
		<li>~ : {tr}A leading tilde acts as a negation operator, causing the word's contribution to the object relevance to be negative. It's useful for marking noise words. An object that contains such a word will be rated lower than others, but will not be excluded altogether, as it would be with the - operator.{/tr}</li>
		<li>* : {tr}An asterisk is the truncation operator. Unlike the other operators, it should be appended to the word, not prepended.{/tr}</li>
		<li>&quot; : {tr}The phrase, that is enclosed in double quotes &quot;, matches only objects that contain this phrase literally, as it was typed. {/tr}</li></ul>" width=300 center=true}>
			  {icon _id='help' style="vertical-align:middle"}
		</a>
		<br />
	{/if}
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

{if $searchStyle ne "menu" }
	<div class="highlight simplebox">
		 {tr}Found{/tr} "{$words}" {tr}in{/tr} {if $where3}{$where2}: {$where3}{else}{$cant_results} {$where2}{/if}
	</div>
{/if}

{if !($searchNoResults) }
<div class="searchresults">
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
{if !empty($results[search].parentName)}{tr}in{/tr} <a href="{$results[search].parentHref}">{$results[search].parentName|escape}</a> {/if}

<br />
<div class="searchdesc">{$results[search].data|strip_tags}</div>
<div class="searchdate">{tr}Last modification date{/tr}: {$results[search].lastModif|tiki_long_datetime}</div><br />
{sectionelse}
{tr}No pages matched the search criteria{/tr}
{/section}
</div>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

{/if}
