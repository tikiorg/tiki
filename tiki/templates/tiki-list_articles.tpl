<a class="pagetitle" href="tiki-list_articles.php">{tr}Articles{/tr}</a><br/><br/>
[{if $tiki_p_edit_article eq 'y'}<a class="link" href="tiki-edit_article.php">edit new article</a>|{/if}
<a class="link" href="tiki-view_articles.php">view articles</a>]
<br/><br/>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_articles.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
     <select name="type">
     <option value='' {if $find_type eq ''}selected="selected"{/if}>{tr}all{/tr}</option>
     <option value='Article' {if $find_type eq 'Article'}selected="selected"{/if}>{tr}Article{/tr}</option>
     <option value='Review' {if $find_type eq 'Review'}selected="selected"{/if}>{tr}Review{/tr}</option>
     </select>
     <select name="topic">
     <option value='' {if $find_topic eq ''}selected="selected"{/if}>{tr}all{/tr}</option>
     {section name=ix loop=$topics}
     <option value="{$topics[ix].topicId}" {if $find_topic eq $topics[ix].topicId}selected="selected"{/if}>{tr}{$topics[ix].name}{/tr}</option>
     {/section}
     </select>
   </form>
   </td>
</tr>
</table>

<table class="listarticles">
<tr>
<td class="listartheading"><a class="llistart" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
<td class="listartheading"><a class="llistart" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'topicName_desc'}topicName_asc{else}topicName_desc{/if}">{tr}Topic{/tr}</a></td>
<td class="listartheading"><a class="llistart" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'publishDate_desc'}publishDate_asc{else}publishDate_desc{/if}">{tr}PublishDate{/tr}</a></td>
<td class="listartheading"><a class="llistart" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'authorName_desc'}authorName_asc{else}authorName_desc{/if}">{tr}AuthorName{/tr}</a></td>
<td class="listartheading"><a class="llistart" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'reads_desc'}reads_asc{else}reads_desc{/if}">{tr}Reads{/tr}</a></td>
<td class="listartheading"><a class="llistart" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}Size{/tr}</a></td>
<td class="listartheading">{tr}Img{/tr}</td>
<!--<td class="listartheading"><a class="llistart" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'useImage_desc'}useImage_asc{else}useImage_desc{/if}">{tr}UseImg{/tr}</a></td>-->
<!--<td class="listartheading"><a class="llistart" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'author_desc'}author_asc{else}author_desc{/if}">{tr}User{/tr}</a></td>-->
<td class="listartheading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="listarttitleodd">&nbsp;
{if $tiki_p_read_article eq 'y'}
<a class="artname" href="tiki-read_article.php?articleId={$listpages[changes].articleId}">
{/if}
{$listpages[changes].title|truncate:20:"(...)":true}
{if $listpages[changes].type eq 'Review'}(r){/if}
{if $tiki_p_read_article eq 'y'}
</a>
{/if}
&nbsp;</td>
<td class="listarttopicodd">&nbsp;{$listpages[changes].topicName}&nbsp;</td>
<td class="listartpublishDateodd">&nbsp;{$listpages[changes].publishDate|date_format:"%a %d of %b [%H:%M]"}&nbsp;</td>
<td class="listartauthorodd">&nbsp;{$listpages[changes].authorName}&nbsp;</td>
<td class="listartreadsodd">&nbsp;{$listpages[changes].reads}&nbsp;</td>
<td class="listartsizeodd">&nbsp;{$listpages[changes].size}&nbsp;</td>
<td class="listarthasimageodd">&nbsp;{$listpages[changes].hasImage}/{$listpages[changes].useImage}&nbsp;</td>
<!--<td class="listartuseimageodd">&nbsp;{$listpages[changes].useImage}&nbsp;</td>-->
<!--<td class="listartauthorodd">&nbsp;{$listpages[changes].author}&nbsp;</td>-->
<td class="listartactionsodd">
{if $tiki_p_edit_article eq 'y'}
<a class="link" href="tiki-edit_article.php?articleId={$listpages[changes].articleId}">{tr}Edit{/tr}</a>
{/if}
{if $tiki_p_remove_article eq 'y'}
<a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].articleId}">{tr}Remove{/tr}</a>
{/if}
</td>
{else}
<td class="listarttitleeven">&nbsp;
{if $tiki_p_read_article eq 'y'}
<a class="artname" href="tiki-read_article.php?articleId={$listpages[changes].articleId}">
{/if}
{$listpages[changes].title|truncate:20:"(...)":true}
{if $listpages[changes].type eq 'Review'}(r){/if}
{if $tiki_p_read_article eq 'y'}
</a>
{/if}
&nbsp;</td>
<td class="listarttopiceven">&nbsp;{$listpages[changes].topicName}&nbsp;</td>
<td class="listartpublishDateeven">&nbsp;{$listpages[changes].publishDate|date_format:"%a %d of %b [%H:%M]"}&nbsp;</td>
<td class="listartauthoreven">&nbsp;{$listpages[changes].authorName}&nbsp;</td>
<td class="listartreadseven">&nbsp;{$listpages[changes].reads}&nbsp;</td>
<td class="listartsizeeven">&nbsp;{$listpages[changes].size}&nbsp;</td>
<td class="listarthasimageeven">&nbsp;{$listpages[changes].hasImage}/{$listpages[changes].useImage}&nbsp;</td>
<!--<td class="listartuseimageeven">&nbsp;{$listpages[changes].useImage}&nbsp;</td>-->
<!--<td class="listartauthoreven">&nbsp;{$listpages[changes].author}&nbsp;</td>-->
<td class="listartactionseven">
{if $tiki_p_edit_article eq 'y'}
<a class="link" href="tiki-edit_article.php?articleId={$listpages[changes].articleId}">{tr}Edit{/tr}</a>
{/if}
{if $tiki_p_remove_article eq 'y'}
<a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].articleId}">{tr}Remove{/tr}</a>
{/if}
</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="artprevnext" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="artprevnext" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_articles.php?topic={$find_topic}&amp;type={$find_type}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
