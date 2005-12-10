{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-freetag_list_objects.tpl,v 1.1 2005-12-10 16:07:11 amette Exp $ *}
<h1><a class="pagetitle" href="tiki-freetag_list_objects.php">{tr}Tags{/tr}</a></h1>
{tr}Browse in{/tr}:<br />
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;sort_mode={$sort_mode}">{tr}All{/tr}</a>
{if $feature_wiki eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=wiki+page&amp;sort_mode={$sort_mode}">{if $type eq 'wiki page'}<span class="highlight">{/if}{tr}Wiki pages{/tr}{if $type eq 'wiki page'}</span>{/if}</a>
{/if}
{if $feature_galleries eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=image+gallery&amp;sort_mode={$sort_mode}">{if $type eq 'image gallery'}<span class="highlight">{/if}{tr}Image galleries{/tr}{if $type eq 'image gallery'}</span>{/if}</a>
{/if}
{if $feature_galleries eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=image&amp;sort_mode={$sort_mode}">{if $type eq 'image'}<span class="highlight">{/if}{tr}Images{/tr}{if $type eq image}</span>{/if}</a>
{/if}
{if $feature_file_galleries eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=file+gallery&amp;sort_mode={$sort_mode}">{if $type eq 'file gallery'}<span class="highlight">{/if}{tr}File galleries{/tr}{if $type eq 'file gallery'}</span>{/if}</a>
{/if}
{if $feature_blogs eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=blog&amp;sort_mode={$sort_mode}">{if $type eq 'blog'}<span class="highlight">{/if}{tr}Blogs{/tr}{if $type eq 'blog'}</span>{/if}</a>
{/if}
{if $feature_trackers eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=tracker&amp;sort_mode={$sort_mode}">{if $type eq 'tracker'}<span class="highlight">{/if}{tr}Trackers{/tr}{if $type eq 'tracker'}</span>{/if}</a>
{/if}<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=trackerItem&amp;sort_mode={$sort_mode}">{if $type eq 'trackerItem'}<span class="highlight">{/if}{tr}Trackers Items{/tr}{if $type eq 'trackerItem'}</span>{/if}</a>
{if $feature_quizzes eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=quiz&amp;sort_mode={$sort_mode}">{if $type eq 'quiz'}<span class="highlight">{/if}{tr}Quizzes{/tr}{if $type eq 'quiz'}</span>{/if}</a>
{/if}
{if $feature_polls eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=poll&amp;sort_mode={$sort_mode}">{if $type eq 'poll'}<span class="highlight">{/if}{tr}Polls{/tr}{if $type eq 'poll'}</span>{/if}</a>
{/if}
{if $feature_surveys eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=survey&amp;sort_mode={$sort_mode}">{if $type eq 'survey'}<span class="highlight">{/if}{tr}Surveys{/tr}{if $type eq 'survey'}</span>{/if}</a>
{/if}
{if $feature_directory eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=directory&amp;sort_mode={$sort_mode}">{if $type eq 'directory'}<span class="highlight">{/if}{tr}Directory{/tr}{if $type eq 'directory'}</span>{/if}</a>
{/if}
{if $feature_faqs eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=faq&amp;sort_mode={$sort_mode}">{if $type eq 'faq'}<span class="highlight">{/if}{tr}FAQs{/tr}{if $type eq 'faq'}</span>{/if}</a>
{/if}
{if $feature_sheet eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=sheet&amp;sort_mode={$sort_mode}">{if $type eq 'sheet'}<span class="highlight">{/if}{tr}Sheets{/tr}{if $type eq 'sheet'}</span>{/if}</a>
{/if}
{if $feature_articles eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?find={$find|escape}&amp;type=article&amp;sort_mode={$sort_mode}">{if $type eq 'article'}<span class="highlight">{/if}{tr}Articles{/tr}{if $type eq 'article'}</span>{/if}</a>
{/if}
<br />
<div class="treetitle">{tr}Current tag{/tr}: 
<a href="tiki-freetag_list_objects.php?type={$tag|escape}" class="categpath">{$tag}</a>
</div>

<table width="100%">
<tr><td>
<div class="tree">
{$tree}
</div>
</td
<td width="20">&nbsp;</td>
<td>

<h3>{tr}Objects{/tr} ({$cantobjects})</h3>
{if $cantobjects > 0}
<table class="normal">
{cycle values="odd,even" print=false}
{section name=ix loop=$objects}
<tr class="{cycle}" >
<td>{tr}{$objects[ix].type|replace:"wiki page":"Wiki"|replace:"article":"Article"|regex_replace:"/tracker [0-9]*/":"tracker item"}{/tr}</td>
<td><a href="{$objects[ix].href}" class="catname">{$objects[ix].name}</a></td>
<td>{$objects[ix].description}&nbsp;</td>
</tr>
{/section}
</table>
<br />

<div align="center">
  <div class="mini">
    {if $prev_offset >= 0}
      [<a class="prevnext" href="tiki-freetag_list_objects.php?find={$find}&amp;type={$type}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
    {/if}
    {tr}Page{/tr}: {$actual_page}/{$cant_pages}
    {if $next_offset >= 0}
      &nbsp;[<a class="prevnext" href="tiki-freetag_list_objects.php?find={$find}&amp;type={$type}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
    {/if}
    {if $direct_pagination eq 'y'}
      <br />
      {section loop=$cant_pages name=foo}
        {assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
        <a class="prevnext" href="tiki-freetag_list_objects.php?find={$find}&amp;type={$type}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
          {$smarty.section.foo.index_next}
        </a>&nbsp;
      {/section}
    {/if}
 </div>
</div>
{/if}
</td></tr></table>
